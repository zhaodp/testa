<div class="well span12">
<?php 
	$form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'post')); ?>

	<div class="span3"><?php echo $form->label($model,'phone'); ?>		
		<?php echo $form->textField($model,'phone',array('size'=>30,'maxlength'=>30)); ?>
	</div>
<br>
<div class="row">
	<?php
	echo CHtml::submitButton ( 'Search', array ('class' => 'btn span2' ) );
	?>
</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->