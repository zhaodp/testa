<?php
/**
 * 司机处罚列表
 * User: Bidong
 * Date: 13-6-20
 * Time: 上午12:43
 * To change this template use File | Settings | File Templates.
 */

class ComplainDriverPunishAction extends CAction{
    public function run(){

        if(isset($_GET['did'])){
            $driver_id=$_GET['did'];

            $data=array();
            $condition='driver_id=:did';
            $params=array(':did'=>$driver_id);
            $punishList=DriverPunish::model()->findAll($condition,$params);
            foreach($punishList as $item){
                $temp=array();
                $temp['create_time']=$item->create_time;
                $temp['result']=CustomerComplain::$driver_spro[$item->result];
                $temp['operator']=$item->operator;
                $temp['mark']=$item->mark;
                $temp['limit_time']=$item->un_punish_time;

                $temp['order_id']='';
                $data[]=$temp;

            }

            $dataProvider=new CArrayDataProvider($data, array(
                'id'=>'id',
                'sort'=>array(),
                'pagination'=>array(
                    'pageSize'=>20,
                ),
            ));
            $dataProvider->keyField=false;

            $this->controller->renderPartial('driver_punish_list',array('data'=>$dataProvider));

        }



    }
}