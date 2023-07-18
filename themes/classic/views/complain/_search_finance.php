<?php
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>


    <div class="row-fluid">

        <div class="span3">
            <?php echo CHtml::label('客户电话','customer_phone');?>
            <?php echo CHtml::textField('customer_phone',$model->customer_phone) ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('司机工号','driver_id');?>
            <?php echo CHtml::textField('driver_id',$model->driver_id) ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('处理状态','status');?>
            <?php echo CHtml::dropDownList('status',$model->finance_process,array('-1'=>'全部','0'=>'未处理','1'=>'已处理')); ?>
        </div>

    </div>
    <div class="row-fluid">
        <div class="span10">
            <button class="btn btn-primary" type="submit" name="search" value="search">搜索</button>

        </div>

    </div>


<?php $this->endWidget(); ?>