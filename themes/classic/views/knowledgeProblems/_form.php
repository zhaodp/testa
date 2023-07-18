<?php
/* @var $this ProblemsCollectController */
/* @var $model ProblemsCollect */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'problems-collect-form',
        'enableAjaxValidation' => false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'driver_id'); ?>
        <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10, 'class' => 'span6', 'class' => 'driver_id')); ?>
        <?php echo CHtml::button('检查司机', array('class' => 'span4 btn', 'id' => 'checked')); ?>

        <?php echo $form->error($model, 'driver_id'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 100, 'class' => 'span10')); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'phone'); ?>
        <?php echo $form->textField($model, 'phone', array('size' => 60, 'maxlength' => 100, 'class' => 'span10 phone')); ?>
        <?php echo $form->error($model, 'phone'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 100, 'class' => 'span10')); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'content'); ?>
        <?php echo $form->textArea($model, 'content', array('size' => 60, 'maxlength' => 255, 'class' => 'span10')); ?>
        <?php echo $form->error($model, 'content'); ?>
    </div>

    <div class="row-fluid buttons" style="display: none;">
        <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->


<script type="text/javascript">

    $(function () {
        $("#checked").click(function () {
            var driver_id = $('.driver_id').val();
            $.ajax({
                type: 'get',
                url: '<?php echo Yii::app()->createUrl('/KnowledgeProblems/ajaxCheck');?>',
                data: 'driver_id=' + driver_id,
                dataType: "json",
                success: function (e) {
                    if (e != 0) {
                        $('#KnowledgeProblems_name').val(e.name);
                        $(".phone").val(e.phone);
                    } else {
                        alert("司机工号错误！");
                    }
                }
            });
        });
    });
</script>