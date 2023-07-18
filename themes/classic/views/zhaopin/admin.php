<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
	'Driver Zhaopins'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List DriverZhaopin', 'url'=>array('index')),
	array('label'=>'Create DriverZhaopin', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-zhaopin-grid', {
		data: $(this).serialize()
	});
	getItemCountString($('#city_id').val());
	return false;
});
");

$selCityId = isset($_REQUEST['city_id'])? $_REQUEST['city_id'] : 0;
$selDistrictId = isset($_REQUEST['district_id'])? $_REQUEST['district_id'] : 0;
$selStatus = isset($_REQUEST['status'])? $_REQUEST['status'] : 0;
$selSrc = isset($_REQUEST['src'])? $_REQUEST['src'] : '';
$id_card = isset($_REQUEST['id_card']) ? $_REQUEST['id_card'] : '';	
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';	
$mobile = isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : '';
$selExam = isset($_REQUEST['exam'])? $_REQUEST['exam'] : 0;
?>

<h1>司机报名管理</h1>

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
			<?php
			echo CHtml::label('城市选择','city_id'); 
			$citys = Dict::items('city');
			$citys[0] = '--全部--';
			
			echo CHtml::dropDownList(
						'city_id',
						$selCityId,
						$citys,
			array(
				'ajax' => array(
				'type'=>'POST', //request type
				'url'=>Yii::app()->createUrl('zhaopin/district'),
				'update'=>'#district_id', //selector to update
				'data'=>array('city_id'=>'js:$("#city_id").val()', 'admin'=>'1')
				))
			);
		?>
		</div>
		<div class="span3">
		<?php
			echo CHtml::label('地区选择','city_id'); 
			$districts = District::model()->findAll('city_id=:city_id', array(':city_id' => $selCityId));
			$districts = CHtml::listData($districts,'id','name');
			$districts[0] = '--全部--';
			echo CHtml::dropDownList('district_id',
						$selDistrictId,
						$districts,
				array()
			); 
		?>		
		</div>
		<div class="span3">
		<?php
			$status = array('全部','已报名', '已通知培训', '已培训考核', '已签约');
			$status[0] = '--全部--';
			echo CHtml::label('状态选择','status'); 
			echo CHtml::dropDownList('status',
						$selStatus,
						$status,
				array()
			); 
		?>		
		</div>
		<div class="span3">
		<?php
			$src = DriverZhaopin::model()->zhaopin_src;
			$src[''] = '--全部--';
			ksort($src);
			echo CHtml::label('来源渠道选择','src'); 
			echo CHtml::dropDownList('src',
						$selSrc,
						$src,
				array()
			); 
		?>		
		</div>		
	</div>
	<div class="row-fluid">
		<div class="span3">
			<label for="id_card">身份证号</label>
			<input type="text" id="id_card" name="id_card" value="<?php echo $id_card;?>" />		
		</div>
		<div class="span3">
			<label for="name">姓名</label>
			<input type="text" id="name" name="name" value="<?php echo $name;?>" />		
		</div>
		<div class="span3">
			<label for="mobile">手机号</label>
			<input type="text" id="mobile" name="mobile" value="<?php echo $mobile;?>" />		
		</div>
		<div class="span3">
		<?php
			$exam = array(''=>'全部', '0'=>'未考试','1'=>'已考试');
			echo CHtml::label('是否考试','exam'); 
			echo CHtml::dropDownList('exam',
						$selExam,
						$exam,
				array()
			); 
		?>	
		</div>					
	</div>
	<div class="row-fluid">
		<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
	</div>
</div>
<?php $this->endWidget(); ?>

<div class="row-fluid" id ="item_count_string" name="item_count_string">	
<h3><?php echo $this->getItemCountString($selCityId);?></h3>
</div>

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
			'name'=>'报名流水号',
			'type' => 'raw',
			'value'=>'CHtml::link(Yii::app()->controller->getZhaopinQueueNumber($data->id, $data->city_id), "javascript:void(0);", array (
			"onclick"=>"{zhaopinDialogdivInit($data->id);}"));',
        ),
		array (
			'name' => 'name',
			'type' => 'raw',
			'value' => 'CHtml::link($data->name, "javascript:void(0);", array (
			"onclick"=>"{zhaopinDialogdivInit($data->id);}"));'
		),
		'mobile',
        'id_card',
		array(
			'name'=>'居住城市',
			'value'=>'Yii::app()->controller->getZhaopinCity($data->city_id)',
        ),
		array(
			'name'=>'居住地区',
			'value'=>'Yii::app()->controller->getZhaopinDistrict($data->district_id)',
		),
		array(
			'name'=>'担保',
			'value'=>'$data->assure > 0 ? "是"  :"否"',
		),
		array(
			'name'=>'报名时间',
			'value'=>'($data->ctime > 0) ?date("Y.m.d",$data->ctime) : ""',
        ),
		array(
			'name'=>'通知培训',
			'type'=>'raw',		
			'value'=>'($data->ttime == 0)? CHtml::link("通知培训", "javascript:void(0);", array (
			"onclick"=>"{changestatus($data->id, 2);}")) : date("Y-m-d", $data->ttime);',		
        ), 
        array(
			'name'=>'考试',
			'value'=>'$data->exam == 0 ? "未通过"  :"已通过"',
        ), 
		array(
			'name'=>'已培训考核',
			'type'=>'raw',
			'value'=>'($data->ttime > 0 && $data->etime == 0)? CHtml::link("已培训考核", "javascript:void(0);", array (
			"onclick"=>"{changestatus($data->id, 3);}")) : ($data->etime == 0?"" : date("Y-m-d", $data->etime));',	
        ),
		array(
			'name'=>'签约',
			'type'=>'raw',		
			'value'=>'($data->etime > 0 && $data->htime == 0)? CHtml::link("签约", array("driver/create", "Driver[name]"=>$data->name, "Driver[domicile]"=>$data->domicile, "Driver[address]"=>$data->address, "Driver[id_card]"=>$data->id_card, "Driver[car_card]"=>$data->driver_card, "Driver[ext_phone]"=>$data->mobile, "Driver[license_date]"=> date("Y-m-d", $data->driver_year))) : ($data->htime == 0?"" : date("Y-m-d", $data->htime));',
        ), 
		/*
		'gender',
		'age',
		'id_card',
		'domicile',
		'assure',
		'marry',
		'political_status',
		'edu',
		'pro',
		'driver_type',
		'driver_card',
		'driver_year',
		'driver_cars',
		'contact',
		'contact_phone',
		'contact_relate',
		'experience',
		'status',
		'recyle',
		'recycle_reason',
		'ip',
		'ttime',
		'etime',
		'htime',
		'rtime',
		'ctime',
		*/
		array(
			'class'=>'CButtonColumn',
            'header'=>'操作',
            'template'=>'{update} {delete}<br/>{resign}',
            'buttons'=>array(
                    'resign' => array(
                            'label'=> '重新排队',     // text label of the button
                            'url'=>'Yii::app()->controller->createUrl("zhaopin/resign",array("id"=>$data->primaryKey))',       // a PHP expression for generating the URL of the button
                            'options'=>array('style'=>'cursor:pointer;'), // HTML options for the button tag
                    ),
				)				
		),
	),
)); 
?>

<script>
function getItemCountString(cityId){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/zhaopin/getitemcount');?>',
		'data':'id='+cityId,
		'type':'get',
		'success':function(data){
			$('#item_count_string').html(data);
		},
		'cache':false		
	});
	return false;
}

function zhaopinDialogdivInit(zhaopinId){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/zhaopin/view');?>',
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
		'url':'<?php echo Yii::app()->createUrl('/zhaopin/changestatus');?>',
		'data':'id='+zhaopinId + '&status=' + status + '&city_id=' + $('#city_id').val() + 
				'&id_card=' + $('#id_card').val() + '&name=' + $('#name').val() + '&mobile=' + $('#mobile').val() +
				'&exam=' + $('#exam').val() + '&src=' + $('#src').val(),
		'type':'get',
		'success':function(data){
			$.fn.yiiGridView.update('driver-zhaopin-grid', {
				data: data
			});			
		},
		'cache':false		
	});
	return false;	
}

</script>
