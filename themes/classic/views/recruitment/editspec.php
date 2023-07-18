<?php

$city_id = isset($_POST['city_id']) ? $_POST['city_id'] : 1;
$spec = DictContent::item('zhaopin_spec', $city_id);

?>
<h1>服务规范编辑</h1>
<?php
$form = $this->beginWidget('CActiveForm', array(
	'id'=>'driver-spec-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); 
?>
<div class="row">
<div class="span4">
<?php
	echo CHtml::label('城市选择','city_id'); 
	$citys = Dict::items('city');
	echo CHtml::dropDownList(
				'city_id',
				$city_id,
				$citys,
	array('ajax' => array(
			'type'=>'POST', //request type
			'url'=>Yii::app()->createUrl('recruitment/getspec'),
			'update'=>'#spec', //selector to update
			'data'=>array('city_id'=>'js:$("#city_id").val()')
			)
		)
	);
?>
</div>
<div class="span4">

</div>
</div>
<div class="row">
<?php

echo CHtml::textArea("spec", $spec, array('style'=>"width:700px;height:1000px;"));
echo CHtml::submitButton('保存',array('class'=>'btn btn-success'));

?>
</div>

<?php $this->endWidget();?>