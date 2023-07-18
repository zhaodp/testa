<div class="well row-fluid">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-submit',
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <div class="span12">


        <div class="span3">
            <?php echo $form->labelEx($model, 'bonus_sn'); ?>
            <?php echo $form->textField($model, 'bonus_sn', array('size' => 30, 'maxlength' => 30)); ?>
        </div>


        <div class="span3">
            <?php echo $form->labelEx($model, '实体卷名称'); ?>
            <?php echo $form->textField($model, 'bonus_id', array('size' => 30, 'maxlength' => 30)); ?>
        </div>

        <div class="span3">
            <?php echo $form->labelEx($model, 'password'); ?>
            <?php echo $form->textField($model, 'password', array('size' => 30, 'maxlength' => 30)); ?>
            <?php echo $form->error($model, 'password'); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '开始时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'created',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh'
            ));

            ?>
        </div>


        <div class="span3">
            <?php echo $form->label($model, '结束时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'update',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh'
            ));

            ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '&nbsp'); ?>
            <?php echo CHtml::button('搜索', array('class' => 'btn', 'onclick' => 'searchSubmit()')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>


<script type="text/javascript">
    function searchSubmit() {
        if ($('#created').val() > $('#update').val()) {
            alert('开始时间不能大于结束时间!');
            return false;
        }
        $('#form-submit').submit();
    }
</script>