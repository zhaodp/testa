<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zengzhihai
 * Date: 13-8-23
 * Time: 下午1:31
 * To change this template use File | Settings | File Templates.
 */
class ServiceVisitAction extends CAction{
    public function run(){

        $param=array(':status' => '1');
        $command = Yii::app()->db_readonly->createCommand();
        $recoupArr=$command->select('*')
            ->from('{{customer_complain_recoup}}')
            ->where('status = :status')
            ->order('id DESC')->queryAll(true,$param);


        $command->reset();
        $newArr=array();
        foreach($recoupArr as $item){

           $complain= $command->select('order_id')
               ->from('{{customer_complain}}')
               ->where('id=:complain_id',array(':complain_id'=>$item['complain_id']))
               ->queryRow();

            $item['order_id']=$complain['order_id'];
            $newArr[]=$item;

        }

        $dataProvider=new CArrayDataProvider($newArr, array(
            'id'=>'id',
            'pagination'=>array(
                'pageSize'=>20,
            ),
        ));

        $this->controller->render('service_visit',array(
            'dataProvider'=>$dataProvider,
        ));

    }
}