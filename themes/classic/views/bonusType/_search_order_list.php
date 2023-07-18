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
	</div>
	<div class="row span12">
		<div class="span3">
			<?php echo CHtml::submitButton('搜索'); ?>
		</div>	
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
