<h1>财务确认收款</h1>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-zhaopin-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$selCityId = isset($_REQUEST['city_id'])? $_REQUEST['city_id'] : Yii::app()->user->city;
$id_card = isset($_REQUEST['id_card']) ? $_REQUEST['id_card'] : '';	
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';	
$mobile = isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : '';

?>

<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get'
)); ?>
	<?php 
if(Yii::app()->user->city==0||empty(Yii::app()->user->city)){
?>
	<div class="row-fluid">
		<div class="span3">
		<?php
			$city = Dict::items('city'); 
			
			echo CHtml::label('城市选择','city'); 
			echo CHtml::dropDownList('city_id',
						$selCityId,
						$city,
				array()
			); 
		?>		
		</div>
	</div>
	<?php }?>
	<div class="row-fluid">
		<div class="span3">
			<label for="id_card">身份证号</label>
			<input type="text" id="id_card" class="span12" name="id_card" value="<?php echo $id_card;?>" />		
		</div>
		<div class="span3">
			<label for="name">姓名</label>
			<input type="text" id="name" class="span12" name="name" value="<?php echo $name;?>" />		
		</div>
		<div class="span3">
			<label for="mobile">手机号</label>
			<input type="text" id="mobile" class="span12" name="mobile" value="<?php echo $mobile;?>" />		
		</div>
						
	</div>
	<div class="row-fluid">
		<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
		
		</div>
		
</div>
<?php $this->endWidget(); ?>




<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'报名信息',
        'autoOpen'=>false,
		'width'=>'800',
		'height'=>'600',
		'modal'=>true,
		'buttons'=>array(
        	'Close'=>'js:function(){$("#mydialog").dialog("close");}'
		),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array(
			'name'=>'driver_id',
			'type' => 'raw',
			'value'=>'CHtml::link($data->driver_id, "javascript:void(0);", array (
			"onclick"=>"{zhaopinDialogdivInit($data->id);}"));',
        ),
		array (
			'name' => 'name',
			'type' => 'raw',
			'value' => 'CHtml::link($data->name, "javascript:void(0);", array (
			"onclick"=>"{zhaopinDialogdivInit($data->id);}"));'
		),
		//'mobile',
        array(
            'name' => '申请人电话',
            'value' => 'Common::parseDriverPhone($data->mobile)'
        ),
        'id_card',
		array(
			'name'=>'居住城市',
			'value'=>'Yii::app()->controller->getRecruitmentCity($data->city_id)',
        ),
		array(
			'name'=>'担保',
			'value'=>'$data->assure > 0 ? "是"  :"否"',
		),
		array(
			'name'=>'状态',
			'value'=>'Yii::app()->controller->getRecruitmentStatus($data->status)',
		),
		array(
			'name'=>'操作',
			'type'=>'raw',
			'value'=>'CHtml::link("确认收款", "javascript:void(0)", array (
			"onclick"=>"{changestatus($data->id, 6);}"));',	
        ),
	),
)); 
?>

<script>

function zhaopinDialogdivInit(zhaopinId){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/recruitment/view');?>',
		'data':'id='+zhaopinId,
		'type':'get',
		'success':function(data){
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	jQuery("#mydialog").dialog("open");
	return false;
}

function changestatus(zhaopinId, status) {
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/recruitment/changestatus');?>',
		'data':'id='+zhaopinId + '&status=' + status,
		'type':'get',
		'success':function(data){
			if(data==1){
				alert("操作成功");
				$.fn.yiiGridView.update('driver-zhaopin-grid', {
					data: data
				});
			}else{
				alert("操作失败,请重试");
			}			
		},
		'cache':false		
	});
	return false;	
}

</script>
