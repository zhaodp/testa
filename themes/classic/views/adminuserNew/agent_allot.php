<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'agent-allot-update-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->labelEx($model,'agent_num'); ?>
    <?php echo $form->textField($model,'agent_num',array('readonly'=>'readonly')); ?>

    <?php echo $form->labelEx($model,'phone'); ?>
    <?php
    if($model->is_lock==1){
        echo $form->textField($model,'phone',array('disabled'=>'disabled'));
    }else{
        echo $form->textField($model,'phone');
    }
    ?>

    <?php echo $form->labelEx($model,'user_id'); ?>
    <?php
    echo CHtml::activeDropDownList($model, 'user_id', AdminUserNew::model()->getAgentUsers());
    ?>

    <div class="buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-success')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>