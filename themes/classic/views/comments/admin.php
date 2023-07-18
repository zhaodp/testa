<?php
$this->pageTitle = Yii::app()->name . ' - 司机评价管理';
Yii::app()->clientScript->registerScript('search', "

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('comments-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>司机评价管理</h1>
<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'comments-grid', 
	'dataProvider'=>$model->search(), 
	'itemsCssClass'=>'table',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'rowCssClassExpression'=>'($data->level==1)?"alert-error":""',
	'columns'=>array (
		array (
			'header'=>'司机工号', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'getDriverUser'),
		),
		array (
			'header'=>'司机', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data->uuid',
		), 
		array (
			'header'=>'城市', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'getDriverCityId'),
		), 
		array (
			'name'=>'comments', 
			'headerHtmlOptions'=>array (
				'width'=>'250px', 
				'nowrap'=>'nowrap'
			)
		), 
		array (
			'name'=>'level', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data->level'
		),
		array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link(AdminSpecialAuth::model()->haveSpecialAuth("user_phone") ? $data->name : trim(substr_replace($data->name, "*****", 3, 5)), array("comments/admin", "Comments[name]"=>$data->name))'
		),
		array(
			'name'=>'order_status',
			'headerHtmlOptions'=>array(
				'width' => '60px',
				'nowrap' =>'nowrap'
			),
			'value'=>'($data->order_status)?"销单":"报单"',
		), 
		array(
				'name'=>'处理情况',
				'headerHtmlOptions'=>array(
						'width' => '60px',
						'nowrap' =>'nowrap'
				),
				'value'=>'($data->status==0)?"未处理":"已处理"',
		),
		array (
			'name'=>'insert_time', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			)
		),
		array(
			'header'=>'操作',
            'class'=>'CButtonColumn',
            'template'=>'{operate} {order} {sms}',
            'buttons'=>array(		
                         'operate'=>
                            array(
                                'label'=>'处理',    
                            	'url'=>'$this->grid->controller->createUrl("reply", array("id"=>$data->id,"asDialog"=>1,"gridId"=>$this->grid->id))',
                                'click'=>'function(){$("#cru-frame").attr("src",$(this).attr("href")); $("#cru-dialog").dialog("open");$("#ui-id-1").html("差评处理");  return false;}',
                            	'visible'=>'AdminActions::model()->havepermission("comments", "reply")'
                                ),
                         'order'=>
                             array(
                             	'label' =>'订单'	,
                            	'url'=>'$this->grid->controller->createUrl("order/admin", array("Order[driver]"=>isset($data->uuid)?$data->uuid:"","Order[phone]"=>$data->name))',
                            	'options'=>array('target'=>'_blank')
                             ),  
                             'sms'=>
                             array(
                             		'label' =>'短信'	,
                             		'url'=>'$this->grid->controller->createUrl("SmsContent/Create", array("id"=>$data->id,"phone"=>$data->name))',
                             		'click'=>'function(){$("#cru-frame").attr("src",$(this).attr("href")); $("#cru-dialog").dialog("open");$("#ui-id-1").html("发送短信");  return false;}',
                             ),
                             
                      ),
        ),	
	)
)
);

//--------------------- begin new code --------------------------
   // add the (closed) dialog for the iframe
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'cru-dialog',
    'options'=>array(
        'title'=>'差评处理',
        'autoOpen'=>false,
	    'modal'=>true,
        'width'=>750,
        'height'=>450,
		'buttons'=>array(
        	'关闭'=>'js:function(){$("#cru-dialog").dialog("close");}'
		)
    ),
    ));
?>
<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>
<?php
 
$this->endWidget();
//--------------------- end new code --------------------------
?>
