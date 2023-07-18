<div class="well span12">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'id' => 'form-submit',
        'method' => 'get',
    ));
    ?>

    <div class="row span12">
        <div class="row span3">
            <?php echo $form->label($model, '开始时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateStart',
                'model' => '', //Model object
                'value' => $dateStart,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh'
            ));

            ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, '结束时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateEnd',
                'model' => '', //Model object
                'value' => $dateEnd,
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
    </div>
    <?php $this->endWidget(); ?>

</div><!-- search-form -->
