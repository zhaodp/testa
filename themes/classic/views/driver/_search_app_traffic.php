<div class="span12">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
        'htmlOptions'=>array('class'=>'form')
    )); ?>

    <div class="row-fluid">

        <div class="span2">
            <?php echo $form->label($model,'driver_id'); ?>
            <?php echo CHtml::textField('DriverAppTraffic[driver_id]',$model->driver_id,array('placeholder'=>'工号','class'=>'span8')); ?>
        </div>

        <div class="span2">
            <?php echo $form->label($model,'in_date'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'DriverAppTraffic[in_date]',
                'model'=>$model,  //Model object
                'value'=>date("Y-m-d"),
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),  // jquery plugin options
                'language'=>'zh',
                'htmlOptions'=>	array('class'=>"span12")
            ));
            ?>
        </div>


    </div>

    <div class="span3">
        <?php echo CHtml::submitButton('搜索',array('class'=>"btn btn-success")); ?>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- search-form -->
