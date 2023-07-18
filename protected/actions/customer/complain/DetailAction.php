<?php
/**
 * Created by JetBrains PhpStorm.
 * User: daiyihui
 * Date: 13-10-30
 * Time: 下午5:58
 * To change this template use File | Settings | File Templates.
 */

class DetailAction extends CAction
{
    public function run()
    {
        if(isset($_GET['driver']) && !empty($_GET['driver'])){
            $dataProvider=new CActiveDataProvider('CustomerComplainDeduct', array(
                'criteria'=>array(
                    'condition'=> 'driver_id = :driver_id AND mark <> :mark',
                    'params' => array(':driver_id' => $_GET['driver'], ':mark' => '0.0'),
                    'order'=>'create_time DESC',
                ),
                'pagination'=>array(
                    'pageSize'=>20,
                ),
            ));

            $this->controller->renderPartial('view_detail',array('data'=>$dataProvider));
        }
    }
}