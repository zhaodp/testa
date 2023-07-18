<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-7-8
 * Time: 下午6:28
 * To change this template use File | Settings | File Templates.
 */

class ComplainPunishListAction extends CAction{

    public  function  run(){

        $city_id = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : 0;
        $criteria = new CDbCriteria();
        $criteria->addCondition('status=:s');
        $criteria->addCondition('DATE_FORMAT(un_punish_time,\'%Y-%m-%d %H:%i:%s\')<= :t');
        $criteria->params = array(':s'=>'1',':t'=>date('Y-m-d H:i:s',time()));
        if ($city_id > 0) {
            $city_prefix_list = Dict::items('city_prefix');
            $city_prefix = $city_prefix_list[$city_id];
            $criteria->addCondition("driver_id like '{$city_prefix}%'");
        }

        $dataProvider=new CActiveDataProvider('DriverPunish', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20,
            ),
        ));

        $this->controller->render('active_driver',array(
            'model'=>$dataProvider,
            'city_id' => $city_id,
        ));


    }

}