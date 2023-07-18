<?php
/**
 *  工单分类列表
 * @author wanglonghuan
 * @date 2013/12/24
 */
class DynamicClassAction extends CAction
{
    public function run()
    {
	$type_id=$_GET['type_id'];
	$from=$_GET['from'];
	$model = SupportTicketClass::model()->getClasses($type_id,$from);
        foreach($model as $value=>$name)
        {
            echo CHtml::tag('option',array('value'=>$value),CHtml::encode($name),true);
        }
     }
}
