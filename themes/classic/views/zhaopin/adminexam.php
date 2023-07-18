<?php
$this->pageTitle = '考试试题';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-exam-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'查看试题信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){closedDialog("view_exam_dialog")}'))));
echo '<div id="view_exam_dialog"></div>';
echo '<iframe id="view_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_view = <<<EOD
function(){
	$("#view_exam_frame").attr("src",$(this).attr("href"));
	$("#view_exam_dialog").dialog("open");
	return false;
}
EOD;

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'update_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'修改试题信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function() {closedDialog("update_exam_dialog")}'))));
echo '<div id="update_exam_dialog"></div>';
echo '<iframe id="update_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_update = <<<EOD
function(){
	$("#update_exam_frame").attr("src",$(this).attr("href"));
	$("#update_exam_dialog").dialog("open");
	return false;
}
EOD;
?>

<h1><?php echo $this->pageTitle; ?></h1>

<div class="btn-group">
	<?php echo CHtml::link('考试搜索', array("#"),array('class'=>"search-button btn-primary btn"));?>
	<?php echo CHtml::link('添加试题', array("zhaopin/createexam"),array('class'=>"btn"));?>
</div>

<div class="search-form span12" style="display:none">
<?php $this->renderPartial('_esearch',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-exam-grid',
	'dataProvider'=>$dataDriverExam,
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
		array (
				'name'=>'type',
				'headerHtmlOptions'=>array (
						'width'=>'40px',
						'nowrap'=>'nowrap'
				),
				'value'=>'Dict::item("exam_type", $data->type)'
		),
		array (
				'name'=>'city_id',
				'headerHtmlOptions'=>array (
						'width'=>'40px',
						'nowrap'=>'nowrap'
				),
				'value'=>'Dict::item("city", $data->city_id)'
		),		
		array (
				'name'=>'title',
				'headerHtmlOptions'=>array (
						'width'=>'200px',
						'nowrap'=>'nowrap'
				),
				'value'=>'$data->title'
		),
		array (
					'name'=>'correct',
					'headerHtmlOptions'=>array (
							'width'=>'50px',
							'nowrap'=>'nowrap'
					),
					'value'=>'strtoupper($data->correct)'
			),
		array (
					'name'=>'status',
					'headerHtmlOptions'=>array (
							'width'=>'40px',
							'nowrap'=>'nowrap'
					),
					'type'=>'raw',	
					'value'=>'($data->status == 1) ? CHtml::link("正常", "javascript:void(0);",array("id"=>"status_$data->id","onclick"=>"{ShieldorNormal($data->id,0);}")) : CHtml::link("屏蔽", "javascript:void(0);",array("id"=>"status_$data->id","onclick"=>"{ShieldorNormal($data->id,1);}"))'
			),
		array(
			'class'=>'CButtonColumn',
            'header'=>'操作',
            'template'=>'{update} {view}',
			'headerHtmlOptions'=>array (
							'width'=>'	30px',
							'nowrap'=>'nowrap'
					),
				'buttons'=>array(
						'update' => array(
								'label'=> '修改',
								'url'=>'Yii::app()->controller->createUrl("zhaopin/updateexam",array("id"=>$data->id))',
								'click'=>$click_update,
						),
						'view' => array(
								'label'=> '查看',
								'url'=>'Yii::app()->controller->createUrl("zhaopin/viewexam",array("id"=>$data->id))',
								'click'=>$click_view,
						),
				)
		),
	),
)); 
?>
<script type="text/javascript">
function Dialogdivview(id){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/zhaopin/viewexam');?>',
		'data':'id='+id,
		'type':'get',
		'success':function(data){
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	jQuery("#mydialog").dialog("open");
	return false;
}
function Dialogdivupdate(id){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/zhaopin/updateexam');?>',
		'data':'id='+id,
		'type':'get',
		'success':function(data){
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	jQuery("#mydialog").dialog("open");
	return false;
}
function closedDialog(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
}

function ShieldorNormal(id,status){

	if (status !==""){
		var alert_str = status==0 ? '确认把该题目设置为屏蔽？' : '确认把该题目设置为正常？';
		var u_status = status == 0 ? 1 : 0;
		var u_html = status == 0 ? "屏蔽" : "正常";
		
		if(!confirm(alert_str)) return false;
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/zhaopin/ajaxstatus');?>',
			'data':{'id':id,'status':status},
			'type':'get',
			'success':function(data){
				$("#status_"+id).attr("onclick","{ShieldorNormal("+id+","+u_status+")}").html(u_html);
			},
			'cache':false
		});
	}
	return false;
}
</script>

