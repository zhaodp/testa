
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'complain-order-search',
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>

    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('司机工号','driver_id');?>
            <?php echo CHtml::textField('driver_id',$model->driver_id,array('class'=>'input-large','placeholder'=>'司机工号'));?>

            <?php echo CHtml::hiddenField('id',$model->id); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('司机电话','driver_phone');?>
            <?php echo CHtml::textField('driver_phone',$model->driver_phone,array('class'=>'input-large','placeholder'=>'司机电话'));?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('客人电话','phone');?>
            <?php echo CHtml::textField('phone',$model->phone,array('class'=>'input-large','placeholder'=>'客人电话'));?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('代驾时间','service_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'service_time',
                'value'=>$model->service_time,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"使用代驾时间",
                ),
            ));
            ?>
        </div>

    </div>

    <div class="row-fluid">
        <div class="span10">
            <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-primary', 'type'=>'button','name'=>'search')); ?>
        </div>

    </div>
 <?php $this->endWidget(); ?>

