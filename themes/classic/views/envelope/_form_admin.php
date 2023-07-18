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
            <?php echo $form->label($model_map, '城市'); ?>
            <?php echo $form->dropDownList($model_map, 'city_id', Dict::items('city')); ?>
            <?php echo $form->error($model_map, 'city_id'); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, '时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'EnvelopeInfo[start_date]',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ),
                'language' => 'zh'
            ));

            ?>
        </div>


        <div class="span3">
            <?php echo $form->labelEx($model, '发放方式'); ?>
            <?php
            $arr_type= Dict::items('envelope_type');
            echo $form->dropDownList($model, 'envelope_type', array('0' => '请选择') + $arr_type); ?>
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
        if ($('#EnvelopeInfo_start_date').val()>$('#EnvelopeInfo_end_date').val()) {
            alert('开始时间不能大于结束时间!');
            return false;
        }
        $('#form-submit').submit();
    }
</script>
