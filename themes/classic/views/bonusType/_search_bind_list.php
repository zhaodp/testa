<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'id'=>'CustomerBonusSearch'
));

if (isset($_GET['CustomerBonus']))
{
	$param = $_GET['CustomerBonus'];
}
			
$created = isset($param['created'])? $param['created'] : '';
$used = isset($param['used'])? $param['used'] : '';

$bind_type = 0;
if (isset($_GET['bind_type']) && $_GET['bind_type'] > 0)
	$bind_type = 1;

?>

	<div class="row-fluid">
		<div class="span4">
			<?php echo $form->label($model,'bonus_type_id'); ?>
			<?php 
				$bonusTypeList = $this->getBonusTypeList();
				$bonusTypeList[0] = '全部';
				ksort($bonusTypeList);
				echo $form->dropDownList($model, 'bonus_type_id', $bonusTypeList); 
			?>
		</div>
		<div class="span4">
			<?php echo CHtml::label('绑定类型', 'bind_type'); ?>
			<?php 
				$bindType[0] = '已绑定';
				$bindType[1] = '已消费';
				echo CHtml::dropDownList('bind_type', $bind_type, $bindType); 
			?>
		</div>
		<div class="span4">
			<?php echo $form->label($model,'created'); ?>
			<?php
				Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
				$this->widget('CJuiDateTimePicker', array (
					'name'=>'CustomerBonus[created]', 
//					'model'=>$model,  //Model object
					'value'=>$created,
					'mode'=>'date',  //use "time","date" or "datetime" (default)
					'options'=>array (
						'dateFormat'=>'yy-mm-dd'
					),  // jquery plugin options
					'language'=>'zh',
				));
			?>			
		</div>		
	</div>
	<div class="row-fluid">
		<div class="span4">
			<?php echo $form->label($model,'customer_phone'); ?>
			<?php echo $form->textField($model,'customer_phone',array('size'=>50,'maxlength'=>50)); ?>
		</div>
		<div class="span4">
			<?php echo $form->label($model,'bonus_sn'); ?>
			<?php echo $form->textField($model,'bonus_sn',array('size'=>50,'maxlength'=>50)); ?>
		</div>			
		<div class="span4">
			<?php echo $form->label($model,'used'); ?>
			<?php
				Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
				$this->widget('CJuiDateTimePicker', array (
					'name'=>'CustomerBonus[used]', 
//					'model'=>$model,  //Model object
					'value'=>$used,
					'mode'=>'date',  //use "time","date" or "datetime" (default)
					'options'=>array (
						'dateFormat'=>'yy-mm-dd'
					),  // jquery plugin options
					'language'=>'zh',
				));
			?>
		</div>		
	</div>
	<div class="row-fluid">
		<div class="span3">
			<?php echo $form->label($model,'order_id'); ?>
			<?php echo $form->textField($model,'order_id',array('size'=>50,'maxlength'=>50)); ?>
		</div>		
	</div>
	<div class="row-fluid">		
		<div class="span3">
			<?php echo CHtml::submitButton('搜索'); ?>
		</div>
		<div class="span3">		
		<input type="button" value="导出手机号列表" onClick="exportlist();" name="export">
		</div>
		<div class="span3">		
		<input type="button" value="导出新客邀请码报表" onClick="exportdriverlist();" name="exportdriver">
		</div>		
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->

<SCRIPT LANGUAGE="JavaScript">

function exportdriverlist(){
	var url = '<?php echo Yii::app()->createUrl('/BonusType/bindlist');?>' + '&export=2';
	window.location.href=url;
}

function exportlist(){
	var url = '<?php echo Yii::app()->createUrl('/BonusType/bindlist');?>' + '&export=1&CustomerBonus[bonus_type_id]=' + $('select#CustomerBonus_bonus_type_id').val() 
	+ '&CustomerBonus[customer_phone]='	+ $('input#CustomerBonus_customer_phone').val()
	+ '&bind_type=' + $("select#bind_type option:selected").val()
	+ '&CustomerBonus[created]=' + $('input#CustomerBonus_created').val()
	+ '&CustomerBonus[used]=' + $('input#CustomerBonus_used').val();
	window.location.href=url;
/*	
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/BonusType/bindlist');?>',
		'data':'export=1&CustomerBonus[bonus_type_id]=' + $('select#CustomerBonus_bonus_type_id').val() 
					+ '&CustomerBonus[customer_phone]='	+ $('input#CustomerBonus_customer_phone').val()
					+ '&bind_type=' + $("select#bind_type option:selected").val()
					+ '&CustomerBonus[created]=' + $('input#CustomerBonus_created').val()
					+ '&CustomerBonus[used]=' + $('input#CustomerBonus_used').val(),
		'type':'get',
		'success':function(data){
		},
		'cache':false		
	});
	return false;
*/
	
}
</SCRIPT>
