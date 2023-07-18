<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row span12">
		<div class="span3">
			<?php echo $form->label($model,'city_id'); ?>
			<?php 
			echo $form->dropDownList($model,
						'city_id',
						Dict::items('city'),
						array('class'=>'span12')
			);
			?>
		</div>
			
		<div class="span3">
			<?php echo $form->label($model,'mark'); ?>
			<?php echo $form->dropDownList($model,
						'mark',
						array(
							'' =>'全部',
							'0'=>'正常',
							'1'=>'已屏蔽',
							'3'=>'已解约',
						),
			array('class'=>"span12")
		); ?>
		</div>
	</div>
	
	<div class="row span12">
		<div class="span3">
			<?php echo $form->label($model,'user'); ?>
			<?php echo $form->textField($model,'user',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
		</div>
		
		<div class="span3">
			<?php echo $form->label($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
		</div>
	</div>
	
	<div class="row span12">
		<div class="span3">
			<?php echo $form->label($model,'phone'); ?>
			<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20,'class'=>"span12")); ?>
		</div>
		
		<div class="span3">
			<?php echo $form->label($model,'ext_phone'); ?>
			<?php echo $form->textField($model,'ext_phone',array('size'=>60,'maxlength'=>255,'class'=>"span12")); ?>
		</div>
	</div>
	
	<div class="row span12">
		<?php echo CHtml::submitButton('Search',array('class'=>'btn span2')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->