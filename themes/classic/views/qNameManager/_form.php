<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'crontab-form',
    'enableAjaxValidation'=>false,
    'errorMessageCssClass'=>'alert alert-error'
)); ?>
<div class="row-fluid">
   <div class="span3">
       <?php echo $form->labelEx($model,'base_qname'); ?>
       <?php echo $form->textField($model,'base_qname', array('size'=>60,'maxlength'=>255)); ?>
       <?php echo $form->error($model,'base_qname'); ?>
   </div>

   <div class="span3">
       <?php echo $form->labelEx($model,'level'); ?>
       <?php echo $form->textField($model,'level', array('size'=>10)); ?>
       <?php echo $form->error($model,'level'); ?>
   </div>

   <div class="span3">
       <?php echo $form->labelEx($model,'max'); ?>
       <?php echo $form->textField($model,'max', array('size'=>10)); ?>
       <?php echo $form->error($model,'max'); ?>
   </div>

   <div class="span2">
       <?php echo $form->labelEx($model,'owner'); ?>
       <?php echo $form->textField($model,'owner',array('size'=>60,'maxlength'=>128)); ?>
       <?php echo $form->error($model,'owner'); ?>
   </div>

   <div class="span2">
       <input name="no_hash_qname" id="no_hash_qname" value="1" type="checkbox">白名单队列
   </div>

</div>

<div class="row-fluid text-center">
    <div class="margin:0 auto;width:400px;">
       <?php echo CHtml::submitButton('提交',array('class'=>'btn btn-primary','style'=>'width:200px;height:50px')); ?>
       <input type="button" class="btn btn-primary" onclick="window.history.back(); return false;" value="返  回" hidefocus style="width:200px;height:50px"/>
    </div>
</div>
<?php $this->endWidget(); ?>

</div>
