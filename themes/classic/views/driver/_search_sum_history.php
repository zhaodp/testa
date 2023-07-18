<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('class'=>'form')
)); ?>

<div class="row-fluid">
	<div class="span2">
		<label>结账月：</label>
		<?php 
			$settle_month[0] = '--请选择结账月--';
			$month = Yii::app()->db_finance->createCommand()
							->select('settle_date')
							->from('t_employee_account_settle')
							->group('settle_date')
							->queryAll();
			foreach ($month as $value){
				$month_short = str_replace('-', '', $value['settle_date']);
				$settle_month[$month_short] = $month_short;
			}
		
			echo CHtml::dropDownList('Account[city_id]','city_id',$settle_month,array('class'=>'span11'));
		?>
	</div>
    <div class="span2">
		<?php echo $form->label($model,'user'); ?>
		<?php echo CHtml::textField('Account[user]',$model->user,array('placeholder'=>'工号','class'=>'span11')); ?>
    </div>

</div>
<div class="row-fluid">
    <div class="span12">
	    <?php echo CHtml::submitButton('搜索',array('class'=>"btn btn-success span1")); ?>
    </div>
</div>

<?php $this->endWidget(); ?>
</div><!-- search-form -->
