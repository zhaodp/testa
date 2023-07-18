<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

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
?>

<h1>批次:<?php echo $_GET['batch']?> 司机管理</h1>

<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get'
)); ?>
	<div class="row-fluid">
		<div class="span3">
			<?php echo CHtml::label('姓名','');?>
			<?php echo $form->textField($model,'name');?>
		</div>
		<div class="span3">
			<?php echo CHtml::label('工号','');?>
			<?php echo $form->textField($model,'driver_id');?>
		</div>
		<div class="span3">
			<?php echo CHtml::label('未签约状态','');?>
			<?php echo $form->dropDownList($model,'noentry',array('0'=>'全部','1'=>'imei问题','2'=>'工号问题'));?>
		</div>
		<div class="span3">
		<?php echo CHtml::label("&nbsp;",'');?>
		<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
		</div>		
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
<?php echo CHtml::button('签约',array('class'=>'btn','onclick'=>'entry()','id'=>'entry','data-loading-text'=>'加载中...')); ?>&nbsp;
<?php echo CHtml::button('激活',array('class'=>'btn','onclick'=>'activation()','id'=>'activation','data-loading-text'=>'加载中...')); ?>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$data,
	'itemsCssClass'=>'table table-striped',
	'pager'=>false,
	'columns'=>array(
		array(
			'name' =>CHtml::checkBox("batchall",false,array('onclick'=>'checkedall()')),
			'type' => 'raw',
			'value' =>'$data->status != 5 ? CHtml::checkBox("batch",false,array("value"=>"$data->id")) : CHtml::checkBox("batch",false,array("value"=>"$data->id","disabled"=>"disabled"))',
		),
		array(
			'name'=>'报名流水号',
			'type' => 'raw',
			'value'=>'$data->id',
        ),
        array (
			'name' => 'name',
			'type' => 'raw',
			'value' => '$data->name'
		),
        array(
			'name'=>'工号',
			'type' => 'raw',
			'value'=>'$data->driver_id',
        ),
        array(
			'name'=>'工作号码',
			'type' => 'raw',
			'value'=>'$data->driver_phone',
        ),
        'imei',
        'id_card',
		array(
			'name'=>'居住城市',
			'value'=>'Yii::app()->controller->getRecruitmentCity($data->city_id)',
        ),
		array(
			'name'=>'居住地区',
			'value'=>'Yii::app()->controller->getRecruitmentDistrict($data->district_id)',
		),
		array(
			'name'=>'担保',
			'value'=>'$data->assure > 0 ? "是"  :"否"',
		),
		array(
			'name'=>'报名时间',
			'value'=>'($data->apply_time > 0) ?date("Y.m.d",$data->apply_time) : ""',
        ),
		array(
			'name'=>'通知培训',
			'type'=>'raw',		
			'value'=>'($data->inform_time == 0)? CHtml::link("通知培训", "javascript:void(0);", array (
			"onclick"=>"{changestatus($data->id, 2);}")) : date("Y-m-d", $data->inform_time);',		
        ), 
		array(
			'name'=>'已培训考核',
			'type'=>'raw',
			'value'=>'($data->inform_time > 0 && $data->cultivate_time == 0)? CHtml::link("已培训考核", "javascript:void(0);", array (
			"onclick"=>"{changestatus($data->id, 3);}")) : ($data->cultivate_time == 0?"" : date("Y-m-d", $data->cultivate_time));',	
        ),
		array(
			'name'=>'签约',
			'type'=>'raw',		
			'value'=>'($data->cultivate_time > 0 && $data->entrant_time == 0)? CHtml::link("签约", array("driver/create", "Driver[name]"=>$data->name, "Driver[domicile]"=>$data->domicile, "Driver[address]"=>$data->address, "Driver[id_card]"=>$data->id_card, "Driver[car_card]"=>$data->driver_card, "Driver[ext_phone]"=>$data->mobile, "Driver[license_date]"=> date("Y-m-d", $data->driver_year))) : ($data->entrant_time == 0?"" : date("Y-m-d", $data->entrant_time));',
        ),
        array(
			'name'=>'签约状态',
			'value'=>'$data->noentry == 0 ? "正常" : ($data->noentry == 1 ? "imei问题" : "工号问题")',
        ),
        array(
			'name' => '修改',
			'headerHtmlOptions' => array(
				'nowrap' => 'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link("修改", array("recruitment/updateRecruitment","id"=>$data->id))',
		),
	),
)); 
?>

<script>
function checkedall(){
	var arrChk=$("input[name='batchall']").attr("checked");
	if(arrChk){
		$("input[name='batch']:not(input[disabled='disabled'])").attr("checked",true);
	}else{
		$("input[name='batch']").attr("checked",false);
	}
}

function entry(){
	var arrID = new Array();
    var arrChk=$("input[name='batch']:checked");
    for (var i=0;i<arrChk.length;i++)
    {
    	arrID.push(arrChk[i].value);
    }
    if(arrID.length > 0){
	 $.ajax({
			'url':'<?php echo Yii::app()->createUrl('/recruitment/driverentry');?>',
			'data':'batch='+<?php echo $_GET['batch'];?>+'&id='+arrID,
			'type':'get',
			'beforeSend':function(){
				$("#entry").button('loading');
			},
			'success':function(data){
				$("#entry").button("reset");
				if(data==0)
					alert("没有找到要签约的司机");
				else if(data == -1)
					alert("司机IMei或工号有问题");
				else
					alert("已有"+data+"个司机签约。");
				$.fn.yiiGridView.update('driver-batch-grid');
			},
			'cache':false		
		});
    }
}

function activation(batch){
	var arrID = new Array();
    var arrChk=$("input[name='batch']:checked");
    for (var i=0;i<arrChk.length;i++)
    {
    	arrID.push(arrChk[i].value);
    }
    if(arrID.length > 0){
	 $.ajax({
			'url':'<?php echo Yii::app()->createUrl('/recruitment/batchactivation');?>',
			'data':'batch='+<?php echo $_GET['batch'];?>+'&id='+arrID,
			'type':'get',
			'beforeSend':function(){
				$("#activation").button('loading');
			},
			'success':function(data){
				$("#activation").button('reset');
				if(data==0)
					alert("没有激活的司机！");
				else
					alert("已激活。");
				
			},
			'cache':false		
		});
    }
}
</script>
