<?php
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'tc-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
                <div>
                    <?php echo $form->hiddenField($model, 'id'); ?>
                </div>
            <div>
                <?php echo $form->labelEx($model, 'type_id'); ?>
                <?php
                $ticket_category = Dict::items('ticket_category');
                ksort($ticket_category);
                echo $form->dropDownList($model,
                    'type_id',
                    $ticket_category,
                    array()
                );
                ?>
                <?php echo $form->error($model, 'type_id'); ?>
		
            </div>

            <div>
                <?php echo $form->labelEx($model, 'name'); ?>
                <?php echo $form->textField($model, 'name', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'name'); ?>
            </div>



        </div>
    </div>
    <div class="row-fluid">
        <div class="span2">
            <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-success btn-block')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
