<div class="form span2">
    <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'customer-complain-cs-add-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <?php $openCity = RCityList::model()->getOpenCityList(); ?>
    <div class="row">
        <?php echo $form->labelEx($model, 'city_id'); ?>
        <?php echo $form->dropDownList($model, 'city_id', $openCity, array('empty' => '请选择')); ?>
        <?php echo $form->error($model, 'city_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name'); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'phone'); ?>
        <?php echo $form->textField($model, 'phone'); ?>
        <?php echo $form->error($model, 'phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'customer_phone'); ?>
        <?php echo $form->textField($model, 'customer_phone'); ?>
        <?php echo $form->error($model, 'customer_phone'); ?>
    </div>


    <div class="row">
        <?php echo $form->labelEx($model, 'complain_type', array('class' => 'control-label')); ?>
        <?php
            echo CHtml::dropDownList('complain_maintype', $model->complain_type, $typelist, array(
                    'ajax' => array(
                        'type' => 'POST', //request type
                        'url' => Yii::app()->createUrl('complain/getsubtype'),
                        'update' => '#sub_type', //selector to update
                        'data' => array('complain_maintype' => 'js:$("#complain_maintype").val()')
                    ))
            );
            ?>
            <?php echo CHtml::dropDownList('sub_type', '-1', array('-1' => '全部')); ?>

    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'detail'); ?>
        <?php echo $form->textArea($model, 'detail', array('class' => 'input-xlarge', 'rows' => '3')); ?>
        <?php echo $form->error($model, 'detail'); ?>
    </div>
    <div class="row buttons">
        <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-large btn-primary')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form --> 