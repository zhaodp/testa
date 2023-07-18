<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zengzhihai
 * Date: 13-8-1
 * Time: 下午2:18
 * To change this template use File | Settings | File Templates.
 */
class ComplainDeductAction extends CAction
{
    public function run()
    {
        $model = new CustomerComplainDeduct();
        $where = $allMonthDriver = '';
        $params = $findData = $temp = $result = $score_driver = $rate_driver = $findDataResult = array();
        //初始化时间和城市
        $time = date('Ym', time());
        $city_id = 0;
        $model->unsetAttributes();

        //搜索
        if (isset($_GET['search'])) {
            if (!empty($_GET['city_id'])) {
                $city_id = $_GET['city_id'];
            }
            $time = $_GET['datetime'];
        }

        if(!empty($city_id)){
            $where='city_id=:city_id';
            $params=array(':city_id'=>$city_id);
        }
        //统计这个月这个城市的所有的司机
        $allMonthDriver = Yii::app()->db_readonly->createCommand()
                                    ->select('count(1)')
                                    ->from('{{driver}}')
                                    ->where($where, $params)
                                    ->queryScalar();

        //获取所有的数据
        $allMonthDriver = round($allMonthDriver * 0.05);

        //查找的数据
        if(!empty($city_id)){
            $where="city_id=:city_id and DATE_FORMAT(create_time, '%Y%m')=:t and driver_id<>'' and driver_id is not null and mark<>'0.0'";
            $params=array(':city_id'=>$city_id,':t'=>$time);
        }else{
            $where="DATE_FORMAT(create_time, '%Y%m')=:t and driver_id<>'' and driver_id is not null and mark<>'0.0'";
            $params=array(':t'=>$time);
        }
        $findData = Yii::app()->db_readonly->createCommand()
                            ->select('count(1) as c,driver_id,id,city_id,sum(mark) as m')
                            ->from('{{customer_complain_deduct}}')
                            ->where($where, $params)
                            ->group('driver_id')
                            ->order('m desc')
                            ->queryAll();
        //获取按投诉率排序的数据
        for($k=0;$k<count($findData);$k++){
            $tempCount=MonthOrderReport::model()->getModelByDriverDate($findData[$k]['driver_id'],$time);
            if($tempCount){
                if($tempCount->complete<=0){
                    $tempCount->complete=1;
                }
                $rate_driver[$k]['p']=round(($findData[$k]['c']/$tempCount->complete)*100,2);
            }else{
                $rate_driver[$k]['p']=round(($findData[$k]['c']/1)*100,2);
            }
            $rate_driver[$k]['driver_id']=$findData[$k]['driver_id'];
        }

        //投诉率排序和扣分排序获取5%
        $score_driver=$this->array_sort($findData,'m','desc',$allMonthDriver);
        $rate_driver=$this->array_sort($rate_driver,'p','desc',$allMonthDriver);
        //取交集
        for($i=0;$i<count($score_driver);$i++){
            for($j=0;$j<count($rate_driver);$j++){
                if(!empty($score_driver[$i])&&!empty($rate_driver[$j])){
                    if($score_driver[$i]['driver_id']==$rate_driver[$j]['driver_id']){
                        $score_driver[$i]['p']=$rate_driver[$j]['p'];
                        $findDataResult[]=$score_driver[$i];
                    }
                }
            }
        }

        if($findDataResult){
            foreach($findDataResult as $key=>$value){
                $temp['id']=$value['id'];
                $temp['city_id']=$value['city_id']?$value['city_id']:0;
                $temp['driver_id']=$value['driver_id'];
                //查找姓名
                $driverInfo=Driver::model()->getDriverInfoByDriverId($value['driver_id']);
                $temp['driver_name']=$driverInfo['name'];
                //司机状态
                if($driverInfo['mark']==Driver::MARK_ENABLE){
                    $temp['status']='正常';
                }elseif($driverInfo['mark']==Driver::MARK_DISNABLE){
                    $temp['status']='已屏蔽';
                }elseif($driverInfo['mark']==Driver::MARK_CHANGE){
                    $temp['status']='已换手机';
                }elseif($driverInfo['mark']==Driver::MARK_LEAVE){
                    $temp['status']='已解约';
                }
                $temp['point']=$value['p'];
                $temp['mark']=100-$value['m'];
                $result[]=$temp;
            }
        }

        $dataProvider = new CArrayDataProvider($result, array(
            'id' => 'id',
            'sort' => array(),
            'pagination' => array(
                'pageSize' => 20,
            ),
        ));

        $this->controller->render('deduct', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
            'date_time' => $time,
            'city_id' => $city_id,
        ));
    }

    //排序获取指定的数据
    private function array_sort($arr,$keys,$type='asc',$lengh=0){
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
            $keysvalue[$k] = $v[$keys];
        }
        if($type == 'asc'){
            asort($keysvalue);
        }else if($type == 'desc'){
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
            if(count($new_array)>=($lengh-1)){
                break;
            }
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }


}