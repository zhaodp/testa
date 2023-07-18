<div class="wide form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div>
        <div class="row-fluid">
            <div class="span3">
                <?php echo $form->label($model, 'id'); ?>
                <?php echo $form->textField($model, 'id'); ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, 'title'); ?>
                <?php echo $form->textField($model, 'title'); ?>
            </div>
            <div class="span3">
                <?php echo CHtml::label('开始时间', 'start_time');
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->beginWidget('CJuiDateTimePicker', array(
                    'name' => 'Invest[start_time]',
                    'value' =>'',
                    'mode' => 'datetime',  //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ),
                    'language' => 'zh',
                    'htmlOptions' => array(
                        'placeholder' => "开始时间",
                    ),
                ));
                $this->endWidget();
                ?>
            </div>
            <div class="span3">
                <?php echo CHtml::label('结束时间', 'create_time');
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->beginWidget('CJuiDateTimePicker', array(
                    'name' => 'Invest[end_time]',
                    'value' => '',
                    'mode' => 'datetime',  //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ),
                    'language' => 'zh',
                    'htmlOptions' => array(
                        'placeholder' => "结束时间",
                    ),
                ));
                $this->endWidget();
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span3">
                <?php echo $form->label($model, '&nbsp;'); ?>
                <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- search-form -->