<div class="well span12">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <div class="span3">
        <?php echo $form->labelEx($model, 'city_id'); ?>
        <?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
        <?php echo $form->error($model, 'city_id'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->label($model, 'datestart'); ?>
        <?php

        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array(
            'name' => 'DriverBankResult[datestart]',
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
    至
    <div class="row span3">
        <?php echo $form->label($model, 'dateend'); ?>
        <?php

        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array(
            'name' => 'DriverBankResult[dateend]',
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

    <div class="row buttons">
        <br>
        <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->
