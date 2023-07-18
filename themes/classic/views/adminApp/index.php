<h1>信息查询</h1>
<div class="row">

<?php $form=$this->beginWidget('CActiveForm', array(
    #'action'=>Yii::app()->createUrl($this->route),
    'action'=>"index.php?r=adminApp/show",
    'method'=>'get',
)); ?>

   
    <div class="row span3">
        <?php echo $form->label($model,'描述'); ?>
        <?php echo $form->textField($model,'description',array('size'=>20,'maxlength'=>20)); ?>
    </div>

    <div class="row span2">
    	<label>&nbsp;</label>
        <?php echo CHtml::submitButton('search',array('class'=>'btn')); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
    
