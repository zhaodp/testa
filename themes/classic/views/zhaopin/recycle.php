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
	return false;
});
");

$selCityId = isset($_POST['city_id'])? $_POST['city_id'] : 0;
$selDistrictId = isset($_POST['district_id'])? $_POST['district_id'] : 0;
$selStatus = isset($_POST['status'])? $_POST['status'] : 0;
$id_card = isset($_REQUEST['id_card']) ? $_REQUEST['id_card'] : '';	
?>

<h1>司机回收站</h1>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>
<div class="row-fluid">
	<div class="span4">
	
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
<div class="span4">
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
<div class="span4">
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
</div>
<div class="row-fluid">
		<div class="span4">
			<label for="id_card">身份证号</label>
			<input type="text" id="id_card" name="id_card" value="<?php echo $id_card;?>" />		
		</div>
<div style="float:left;margin-top:20px;margin-left:50px;">
<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
</div>
</div>
<?php $this->endWidget(); ?>
<div style="clear:both" />		
<div>
<?php echo $this->getRecycleItemCountString();?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array(
                'name'=>'报名流水号',
                'value'=>'Yii::app()->controller->getZhaopinQueueNumber($data->id, $data->city_id)',
        ),
		'name',
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
                'name'=>'是否需要担保',
                'value'=>'$data->status ? "是"  :"否"',
        ),
		array(
                'name'=>'回收时间',
                'value'=>'($data->ctime > 0) ?date("Y.m.d",$data->rtime) : ""',
        ),
                                        
		array(
                'name'=>'回收前状态',
                'value'=>'Yii::app()->controller->getZhaopinStatus($data->status)',		
        ), 

		'recycle_reason',
		array(
			'class'=>'CButtonColumn',
            'header'=>'操作',
            'template'=>'{active}',
            'buttons'=>array(
                    'active' => array(
                            'label'=>'激活',     // text label of the button
                            'url'=>'Yii::app()->controller->createUrl("zhaopin/active",array("id"=>$data->primaryKey))',       // a PHP expression for generating the URL of the button
                            'options'=>array('style'=>'cursor:pointer;'), // HTML options for the button tag
                    ),
				)				
		),
	),
)); ?>
