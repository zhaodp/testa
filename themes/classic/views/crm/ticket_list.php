<?php
/* @var $this CrmController */
/* @var $model SupportTicket */
/**
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('#search-form').submit(function(){
	$('#support-ticket-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
**/
?>
<h3>工单列表</h3>
<div class="btn-group">
        <?php echo CHtml::link('工单处理', array('crm/ticketList'),array('class'=>'btn-primary btn'));?>
        <?php echo CHtml::link('工单补扣款处理', array('crm/feeList'),array('class'=>'btn'));?>
</div>
<div class="search-form" style="display:block">
    <?php $this->renderPartial('_search',array(
        'model'=>$model,
    )); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'support-ticket-grid',
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        array (
            'name'=>'id',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->id'
        ),
        array(
            'name' => 'city_id',
            'value'=>'Dict::item("city",$data->city_id)',
        ),
       array(
           'name'=> 'type',
           'value'=>'Dict::item("ticket_category", $data->type)',
       ),
	array(
	   'name'=>'class',
	   'value'=>'$data->class==0?"":SupportTicketClass::model()->findByPk($data->class)->name',
	
	),
        array (
            'name'=>'content',
            'headerHtmlOptions'=>array (
                'width'=>'300px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->content'
        ),
        array(
            'header'=>'申报人',
            'type'=>'raw',
            'value'=>array($this,'getApplicant'),
        ),
		array(
            'header'=>'设备',
            'type'=>'raw',
            'value'=>'$data->device',
        ),
		array(
            'header'=>'操作系统版本',
            'type'=>'raw',
            'value'=>'$data->os',
        ),
		array(
            'header'=>'版本号',
            'type'=>'raw',
            'value'=>'$data->version',
        ),
        array(
            'name' => 'status',
            'value' => 'SupportTicket::$statusList[$data->status]'
        ),
        'follow_user',
        array(
            'header'=>'处理部门',
            'name'=>'group',
            'value' => 'Dict::item("support_ticket_group", $data->group)',
        ),
        'operation_user',
        array(
            'header'=>'创建信息',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => array($this,'getCreateInfo'),
        ),
        array(
            'header'=>'最后回复',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => array($this,'getLastReplyInfo'),
        ),
        array(
            'header'=>'操作',
            'class'=>'CButtonColumn',
            'template'=>'{view}',
            'viewButtonImageUrl'=> false,
            'buttons'=>array(
                'view'=> array(
                    'label'=>'查看',
                    'url' => 'Yii::app()->createUrl("crm/ticketView", array("id"=>$data->id))',
                    'options'=>array('target' => '_blank'),
                ),

            ),
        ),
    ),
)); ?>



