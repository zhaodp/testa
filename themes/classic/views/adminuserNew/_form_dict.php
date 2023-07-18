<div class="span12">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'bonus-type-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <p class="note">带 <span class="required">*</span> 必填</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'dictname'); ?>
        <?php echo $form->textField($model,'dictname'); ?>
        <?php echo $form->error($model,'dictname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>60)); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'code'); ?>
        <?php echo $form->textField($model,'code'); ?>
        <?php echo $form->error($model,'code'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'postion'); ?>
        <?php echo $form->textField($model,'postion'); ?>
        <?php echo $form->error($model,'postion'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '更新',array('class'=>'btn btn-success span12','name'=>'dictcreate')); ?>
    </div>
    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(function(){
        $('#bonus-type-form').attr('target','_parent');
        $("#Dict_dictname").change(function(){
            var dictname = $(this).val();
            $.ajax({
                'url':'<?php echo Yii::app()->createUrl('/adminuser/getDictCode');?>',
                'data':{'dictname':dictname},
                'type':'get',
                'dataType':'json',
                'cache':false,
                'success':function(data){
                    if(data.status == 1){
                        $('#Dict_code').val(data.code);
                        $('#Dict_postion').val(data.code);
                    }else{
                        $('#Dict_code').val(0);
                        $('#Dict_postion').val(0);
                    }
                }
            });
        });
    });
</script>