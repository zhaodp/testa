<?php
$this->pageTitle = '司机信息';


Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    'options'=>array(
        'title'=>'屏蔽原因',
        'autoOpen'=>false,
		'width'=>'750',
		'height'=>'350',
		'modal'=>true,
		'buttons'=>array(
            'OK'=>'js:function(){dialogClose($("#DriverExt_driver_id").val(), $("#DriverExt_mark").val(), $("#DriverExt_mark_reason").val())}',    
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<h1><?php echo $this->pageTitle;?></h1>

<div class="btn-group">
	<?php echo CHtml::link('高级搜索', array("#"),array('class'=>"search-button btn-primary btn"));?>
</div>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search_list',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-grid',
	'dataProvider'=>$model->search(),
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'driverMark'),
	'htmlOptions'=>array('class'=>'row span11'),
	'columns'=>array(
		 
		array (
			'name'=>'city_id', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'Dict::item("city", $data->city_id);'
		), 
		array (
			'name'=>'user', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link($data->user, array("driver/update", "id"=>$data->user))'
		), 
		array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			)
		), 
		array (
			'name'=>'phone', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			)
		),	
		array (
			'name'=>'ext_phone', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			)
		),
		array (
			'name'=>'level', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			)
		),
		array (
			'name'=>'mark', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'($data->mark == Employee::MARK_DISNABLE) ? "已屏蔽" : (($data->mark == Employee::MARK_LEAVE) ? "已解约" : "正常")'
		),
	),
)); ?>

<script>
function dialogInit(id, mark){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/driver/mark');?>',
		'data':{'id':id, 'mark':mark},
		'type':'get',
		'success':function(data){
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	$("#mydialog").dialog("open");
	return false;
}

function dialogClose(id, mark, mark_reason){
	if (mark_reason == '') {
		alert ("请填写原因。");
		return false;
	} else {
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/driver/domark');?>',
			'data':{'id':id, 'mark':mark, 'reason':mark_reason},
			'type':'get',
			'success':function(data){
				$.fn.yiiGridView.update('driver-grid');
			},
			'cache':false		
		});	
		$("#mydialog").dialog("close");
		return false;
	}
}
</script>
