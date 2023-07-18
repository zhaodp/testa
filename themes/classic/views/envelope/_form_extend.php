<div class="well span12">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'id' => 'form-submit',
        'method' => 'get',
    ));
    ?>

    <div class="row span12">

        <div class="span3">
            <?php echo $form->label($model, '城市'); ?>
            <?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
            <?php echo $form->error($model, 'city_id'); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, '开始时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'EnvelopeExtend[start_date]',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ),
                'language' => 'zh'
            ));

            ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, '结束时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'EnvelopeExtend[end_date]',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh'
            ));

            ?>
        </div>


        <div class="span3">
            <?php echo $form->labelEx($model, '金额'); ?>
            <?php
            echo $form->dropDownList($model, 'amount', array('0' => '全部') + $arr_amount); ?>
        </div>
    </div>
    <div class="row span12">
        <div class="span9">
            <?php echo $form->labelEx($model, 'drive_id'); ?>
            <?php echo $form->textField($model, 'drive_id', array('class' => 'span3')); ?>
            <?php echo $form->error($model, 'drive_id'); ?>
        </div>
        <div class="row buttons">
            <br>
            <?php echo CHtml::button('搜索', array('class' => 'btn', 'onclick' => 'searchSubmit()')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>

</div><!-- search-form -->


<script type="text/javascript">
    function searchSubmit() {
        if ($('#EnvelopeInfo_start_date').val() > $('#EnvelopeInfo_end_date').val()) {
            alert('开始时间不能大于结束时间!');
            return false;
        }
        $('#form-submit').submit();
    }
</script>
