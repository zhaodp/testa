<?php
$this->pageTitle = Yii::app()->name . ' - 品质监管短信历史';
Yii::app()->clientScript->registerScript('search', "

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('sms-content-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>品质监管短信历史</h1>
<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'sms-content-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table',
	
	'columns'=>array(
		array (
			'name'=>'phone', 
			'headerHtmlOptions'=>array (
				'width'=>'160px', 
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link(Common::parseCustomerPhone($data->phone), array("smsContent/admin", "SmsContent[phone]"=>$data->phone))'
		),
			array (
					'name'=>'评价详情 ',
					'headerHtmlOptions'=>array (
							'width'=>'460px',
							'nowrap'=>'nowrap'
					),
					'type'=>'raw',
					//'value'=>'$data->getCommentComments()'
                    'value'=>'isset($data->comment)?$data->comment->comments:""'
			),
			
			array (
					'name'=>'评价时间',
					'headerHtmlOptions'=>array (
							'width'=>'160px',
							'nowrap'=>'nowrap'
					),
					'type'=>'raw',
					//'value'=>'$data->getCommentInsertTime()'
                    'value'=>'isset($data->insert_time)?$data->comment->insert_time:""'
			),
		'create_time',
		array(
			'header'=>'操作',
			'class'=>'CButtonColumn',
			'template'=>'{view} {delete}',
				'buttons'=>array(
						'view'=>
							array(
								'label'=>'查看',
                            	'url'=>'$this->grid->controller->createUrl("view", array("id"=>$data->id,"asDialog"=>1))',
								'click'=>'function(){$("#cru-frame").attr("src",$(this).attr("href")); $("#cru-dialog").dialog("open"); return false;}',
							)
						
						 
				),
		),
	),
)); 

//--------------------- begin new code --------------------------
   // add the (closed) dialog for the iframe
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'cru-dialog',
    'options'=>array(
        'title'=>'查看详情',
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
