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

$selCityId = isset($_REQUEST['city_id'])? $_REQUEST['city_id'] : Yii::app()->user->city;
$selDistrictId = isset($_REQUEST['district_id'])? $_REQUEST['district_id'] : 0;
$selStatus = isset($_REQUEST['status'])? $_REQUEST['status'] : 0;
$id_card = isset($_REQUEST['id_card']) ? $_REQUEST['id_card'] : '';	
?>

<h1>司机回收站</h1>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get'
)); ?>
<div class="row-fluid">
	<div class="span4">
	
<?php
	$city = Dict::items('city');
			if(Yii::app()->user->city!=0){
				foreach($city as $k=>$v){
					if($k!==Yii::app()->user->city){
						unset($city[$k]);
					}
				}
				$city[-1] ='--选择城市--';
			}
			echo CHtml::label('城市选择','city'); 
			echo CHtml::dropDownList('city_id',
						$selCityId,
						$city,
				array(
					'ajax' => array(
					'type'=>'POST', //request type
					'url'=>Yii::app()->createUrl('recruitment/district'),
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
	$status = Dict::items('recruitment_status');
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
echo '<iframe id="view_informexam_frame" width="100%" height="100%" style="border:0px"></iframe>';
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
                'value'=>'CHtml::link(Yii::app()->controller->getRecruitmentQueueNumber($data->id, $data->city_id), "javascript:void(0);", array (
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
            'name' => '手机号',
            'value' => 'Common::parseDriverPhone($data->mobile)'
        ),
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
                'name'=>'是否需要担保',
                'value'=>'$data->status ? "是"  :"否"',
        ),
		array(
                'name'=>'回收时间',
                'value'=>'($data->apply_time > 0) ?date("Y.m.d",$data->discard_time) : ""',
        ),
                                        
		array(
                'name'=>'回收前状态',
                'value'=>'Yii::app()->controller->getRecruitmentStatus($data->status)',		
        ), 

		'recycle_reason',
		array(
			'class'=>'CButtonColumn',
            'header'=>'操作',
            'template'=>'{active}',
            'buttons'=>array(
                    'active' => array(
                            'label'=>'激活',     // text label of the button
                            'url'=>'Yii::app()->controller->createUrl("recruitment/active",array("id"=>$data->primaryKey))',       // a PHP expression for generating the URL of the button
                            'options'=>array('style'=>'cursor:pointer;'), // HTML options for the button tag
                    ),
				)				
		),
	),
)); ?>
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
</script>