<?php
/** 
 * 分公司司管投诉KPI action
 * 加权分数=投诉次数*分类权重系数
 * 加权投诉率=投诉次数*分类权重系数/总订单数(当前城市、当前月份的所有状态订单数)*100％
 * 
 */
class ComplainKpiAction extends CAction
{
	public function run()
	{
        $city_id=$start_time=$end_time='';
        $flag=date('w');
        switch($flag){
            case 0:
                $start_time=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-13,date('Y')));
                $end_time=date('Y-m-d',mktime(23,59,59,date('m'),date('d')-7,date('Y')));
                break;
            case 1:
                $start_time=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-7,date('Y')));
                $end_time=date('Y-m-d',mktime(23,59,59,date('m'),date('d')-1,date('Y')));
                break;
            case 2:
                $start_time=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-8,date('Y')));
                $end_time=date('Y-m-d',mktime(23,59,59,date('m'),date('d')-2,date('Y')));
                break;
            case 3:
                $start_time=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-9,date('Y')));
                $end_time=date('Y-m-d',mktime(23,59,59,date('m'),date('d')-3,date('Y')));
                break;
            case 4:
                $start_time=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-10,date('Y')));
                $end_time=date('Y-m-d',mktime(23,59,59,date('m'),date('d')-4,date('Y')));
                break;
            case 5:
                $start_time=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-11,date('Y')));
                $end_time=date('Y-m-d',mktime(23,59,59,date('m'),date('d')-5,date('Y')));
                break;
            case 6:
                $start_time=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-12,date('Y')));
                $end_time=date('Y-m-d',mktime(23,59,59,date('m'),date('d')-6,date('Y')));
                break;
        }
        $params = $params_order = array();
        $criteria = new CDbCriteria();
        $criteria_order = new CDbCriteria();
        if(isset($_GET['search'])){
            $city_id=isset($_GET['city_id'])?trim($_GET['city_id']):0;
            //by 曾志海  改
            $start_time=isset($_GET['start_time'])?trim($_GET['start_time']):'';
            $end_time=isset($_GET['end_time'])?trim($_GET['end_time']):'';
        }else{
            $city_id=Yii::app()->user->city;
        }

        if($city_id){

            $criteria->addCondition('city_id=:city_id');
            $criteria_order->addCondition('city_id=:city_id');
            $params[':city_id']=$city_id;
            $params_order[':city_id']=$city_id;
        }else{
            $criteria->addCondition('city_id >:city_id');
            $criteria_order->addCondition('city_id >:city_id');
            $params[':city_id']=0;
            $params_order[':city_id']=0;
        }

        if($start_time&&$end_time){
            $start_time_stamp = strtotime($start_time.' 00:00:00');
            $end_time_stamp = strtotime($end_time.' 23:59:59');
            $criteria->addCondition('DATE_FORMAT(create_time, \'%Y-%m-%d\') BETWEEN :s_time AND :e_time');
            $criteria_order->addCondition('booking_time  BETWEEN :s_time AND :e_time');
            $params[':s_time']=$start_time;
            $params_order[':s_time']=$start_time_stamp;
            $params[':e_time']=$end_time;
            $params_order[':e_time']=$end_time_stamp;
        }
        $criteria_order->params=$params_order;
        // $criteria->addCondition('status>:s ');
        // $params[':s']=1;
        // $criteria->addCondition('status<:s2 ');
        // $params[':s2']=5;
        //changed by aiguoxin 2014-07-16 具体含义见CustomerComplain
        $criteria->addInCondition('status', array(2,3,4,8));
        $params=array_merge($params,$criteria->params);


        $criteria->params=$params;

        $criteriaArr=$criteria->toArray();
        $criteria_orderArr=$criteria_order->toArray();

        $command=Yii::app()->db_readonly->createCommand();
        $complainData=$command->select('count(id) as cnt,complain_type')
                                    ->from('t_customer_complain')
                                    ->where($criteriaArr['condition'],$criteriaArr['params'])
                                    ->group('complain_type')->queryAll();

        $command->reset();
        $complainArr=array();
        foreach($complainData as $item){
            $complainArr[$item['complain_type']]=$item['cnt'];
        }
        //当前城市、当前月份的所有状态订单数
        $orderCount = Order::model()->getCityMonthAllStatusOrders($criteria_orderArr);

        $typeArr= CustomerComplainType::model()->getComplainTypeList();
        $dataArr=array();
        $i=1;
        $total_cnt=$total_num=$total_rate=0;
        if($orderCount<=0) $orderCount=1;
		foreach($typeArr as $v) {
            $name='';
            $tmpArr=array();
            $tmpArr['id']=$v['id'];
            $tmpArr['parent_id']=$v['parent_id'];
            $tmpArr['type_count']=$tmpArr['weight_num']=$tmpArr['weight_rate']='';
            if($v['parent_id']==0){
                $name=$i.'.'.$v['name'];
                $i++;
                $total_type=$this->getParentDataBytype($complainArr,$v['id'],$typeArr);
                $tmpArr['type_count']=$total_type['type_count'];
                $tmpArr['weight_num']=$total_type['weight_num'];
                $tmpArr['weight_rate']=round(($tmpArr['weight_num']/$orderCount)*10000,4);

            }else{
                $tmpArr['type_count']=isset($complainArr[$v['id']])?$complainArr[$v['id']]:0;  //某分类投诉数量
                $tmpArr['weight_num']=$tmpArr['type_count']*$v['weight']; //加权分数
                //加权投诉率
                // 加权投诉率=投诉次数*分类权重系数/总订单数(当前城市、当前月份的所有状态订单数)*100％
                $tmpArr['weight_rate']=round(($tmpArr['weight_num']/$orderCount)*10000,4);
                $total_rate+=intval($tmpArr['weight_rate']);
                
                $total_cnt+=intval($tmpArr['type_count']);
                $total_num+=intval($tmpArr['weight_num']);
            }
            $tmpArr['type_name']=$v['parent_id']==0?$name:$v['name'];
            $dataArr[]=$tmpArr;
        }

        $total_arr=array(
                         'id'=>0,'parent_id'=>0,
                         'type_name'=>'总计',
                         'type_count'=>$total_cnt,
                         'weight_num'=>$total_num,
                         'weight_rate'=>round(($total_num/$orderCount)*10000,4));
        $dataArr[]=$total_arr;

		$city_arr = Dict::items('city');
		$dataProvider=new CArrayDataProvider($dataArr, array(
            'id'=>'id',
            'sort'=>array(),
            'pagination'=>array(
                'pageSize'=>100,
            ),
        ));
        $dataProvider->keyField = false;

		$this->controller->render('kpi',array(
			'typeArr' => $typeArr,
            'data'=>$dataProvider,
			'city_list'=>$city_arr,
            'city_id'=>$city_id,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
		));
	}

    protected function getParentDataBytype($complaint_data,$parent_type_id,$type_data){
        $sub_type_count=$sub_weight_num=0;
        foreach($type_data as $type){
            if($type['parent_id']==$parent_type_id){
                $t_count=isset($complaint_data[$type['id']])?$complaint_data[$type['id']]:0;
                $sub_type_count+=$t_count;
                $sub_weight_num+=$t_count*$type['weight'];
            }
        }
        return array('type_count'=>$sub_type_count,'weight_num'=>$sub_weight_num);
    }

}
