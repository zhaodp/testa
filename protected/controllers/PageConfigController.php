<?php
class PageConfigController extends Controller {

    public function actionAdmin() {
	    $model = new PageConfig();
        $dataProvider = PageConfig::model()->search();
        $this->render('admin', array(
            'dataProvider' => $dataProvider,
		    'model' => $model,
            )
        );
    }
	
    //获取活动时间
    protected function getActTime($data){
	    $begintime = date('Y年m月d日H时i分', strtotime($data->begintime));
	    $endtime = date('Y年m月d日H时i分', strtotime($data->endtime));
	    return $begintime.'至'.$endtime;
    }

    protected function getOrderTime($data){
        $begintime = date('Y年m月d日H时i分', strtotime($data->order_begin));
        $endtime = date('Y年m月d日H时i分', strtotime($data->order_end));
        return $begintime.'至'.$endtime;
    }

    protected function getUrl($data){
        $url=$data->url;
        return "<a href='$url' target='_blank'>预览<a>";
   }

    protected function getTriggerTime($data){
        $trigger = $data->trigger_time;
        $trigger_name = '';
        $trigger_array = explode(',', $trigger);
        $times =  PageConfig::$trigger_time;
        if(in_array(PageConfig::TRIGGER_RECEIVE, $trigger_array)){
            $trigger_name .=  $times[PageConfig::TRIGGER_RECEIVE].',';
        }
        if(in_array(PageConfig::TRIGGER_START, $trigger_array)){
            $trigger_name .=  $times[PageConfig::TRIGGER_START].',';
        }
        if(in_array(PageConfig::TRIGGER_COMMENT, $trigger_array)){
            $trigger_name .=  $times[PageConfig::TRIGGER_COMMENT].',';
        }
        if(in_array(PageConfig::TRIGGER_DETAILS, $trigger_array)){
            $trigger_name .=  $times[PageConfig::TRIGGER_DETAILS].',';
        }
        $trigger_name = rtrim($trigger_name, ',');
        return $trigger_name;
    }
   protected function getCityName($data){
        $viewCity = CHtml::link('查看', 'javaScript:void(0);', array('onClick' => 'viewCityDialogdivInit(\'' . Yii::app()->createUrl("pageConfig/viewCity", array("id" => $data->primaryKey)) . '\')'));
	    return $viewCity;
   }


   public function actionViewCity($id) {
	    $this->layout = '//layouts/main_no_nav';
        $config = PageConfig::model()->findByPk($id);
	    $city_ids = $config->city_ids;
	    $city = explode(',', $city_ids);
        $city_name = '';
        foreach ($city as $items){
            $city_name .= Dict::item('city', $items).',';
        }
	    $city_name = rtrim($city_name,',');
        $this->render('view_city', array(
                        'city_name' => $city_name,
                    )
        );
  }

   protected function enablePageConfig($data){
      if($data->status == 0){
          $link = CHtml::link('终止活动', Yii::app()->createUrl("pageConfig/enable", array("id" => $data->id)));
      }else{
          $link = '已终止';
      }
      echo $link;
   }


    public function actionCreate() {
        $model = new PageConfig();
        if (isset($_POST['PageConfig']) && $_POST['city']) {
            $model->attributes = $_POST['PageConfig'];
            $model->created = date("Y-m-d H:i:s", time());
            $model->city_ids = implode(',', $_POST['city']);
            $model->trigger_time = implode(',', $_POST['trigger_time']);
            $res = $model->save();
            if($res){
                //加入缓存,同一个城市 同一段时间只能有一个活动
                /*foreach($_POST['city'] as $city_id){
                   $cache_key = 'pageConfig_'.$city_id;
                   Yii::app()->cache->set($cache_key, serialize($model));
                }*/
                $cache_key = 'page_Config';
                $obj = Yii::app()->cache->get($cache_key);
                if(!$obj){//缓存不存在
                    $obj_array = array();
                    $obj_array[0] = $model;
                }else{
                    $obj_array = unserialize($obj);
                    $obj_array[count($obj_array)]=$model;
                }
                Yii::app()->cache->set($cache_key, serialize($obj_array));
                $this->redirect(Yii::app()->createUrl('pageConfig/admin'));
            }else{
                throw new CHttpException(500, 'create failed.');
            }
	    }
    }
    //校验活动是否重叠
    public function actionCheckAct() {   
        $model = new PageConfig();
        if (isset($_POST)) {
             $begintime = $_POST['begintime'];
             $endtime = $_POST['endtime'];
             $city_ids = $_POST['city_ids'];
             $city_ids = rtrim($city_ids,',');
             $city_array = explode(',', $city_ids);
             $city_name = '';
             $configs = $model->findAllByAttributes(array('status'=>0));
             if($configs){
                foreach($configs as $config){
                    if(($begintime>=$config->begintime && $begintime<=$config->endtime)
                        || ($endtime>=$config->begintime && $endtime<=$config->endtime)){
                        $cities = array_intersect($city_array, explode(',', $config->city_ids));
                        if(!empty($cities)){
                            foreach ($cities as $items){
                                $city_name .= Dict::item('city', $items).',';
                            }
                            $city_name = rtrim($city_name,',');
			                break;
                        }
                    }
                }
            }
            if(empty($city_name)){
                echo '0';
            }else{
                echo '['.$city_name.']有重叠的活动';
            }
        }
    }

  
    public function actionEnable($id) {
	    $res = PageConfig::model()->updateByPk($id, array('status'=>1));
	    if($res){
            /*$city_ids = $model->city_ids;
            $city_array = explode(',', $city_ids);
            if(!empty($city_array)){
                foreach($city_array as $city_id){
                   $cache_key = 'pageConfig_'.$city_id;
                   Yii::app()->cache->delete($cache_key);
                }
	        }*/
            $cache_key = 'page_Config';
            $obj = Yii::app()->cache->get($cache_key);
            if($obj){
                $obj_array = unserialize($obj);
                foreach($obj_array as $model){
                    if($model->id == $id){
                        $key = array_search($model, $obj_array);
                        array_splice($obj_array, $key, 1);//将被禁用的活动从缓存清除
                        break;
                    }
                }
                Yii::app()->cache->set($cache_key, serialize($obj_array));
            }
	        $this->redirect(Yii::app()->createUrl('pageConfig/admin'));
	    }
    }

}
