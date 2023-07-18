<h1>处理司机投诉</h1>

<div class="form span6">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'driver-index-form',
    )); ?>
    <input type="hidden" name="re" value="<?php echo isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:''?>"/>
    <fieldset>
    <legend></legend>
    <?php echo $form->errorSummary($model); ?>
    <div class="control-group">
        <div class="span2">投诉类型：</div>
        <?php echo CHtml::dropDownList('complaint_type',$model->complaint_type, $data,array('class'=>'span6')); ?>
    </div>
    <div class="control-group">
        <div class="span2">投诉处理：</div>
        <?php echo CHtml::dropDownList('complaint_status','', DriverComplaint::$customer_pulish_type,array('class'=>'span6')); ?>
    </div>
    <div class="control-group">
        <div class="span2">处理说明：</div>
        <?php echo CHtml::textArea('mark','',array('class'=>'input-xlarge','rows'=>'6','style'=>'width: 360px;'));?>
    </div>
    <div class="control-group">
        <div class="span2"></div>
        <?php echo CHtml::checkBox('is_to_customer')?> 给客户发短信
    </div>
    <div class="control-group">
        <div class="span2">短信内容：</div>
        <?php echo CHtml::textArea('content','',array('class'=>'input-xlarge','rows'=>'6','style'=>'width: 360px;'));?>
    </div>
    <div class="control-group">
        <div class="span2"></div>
        <?php echo CHtml::submitButton('提交',array('class'=>'btn btn-large btn-primary')); ?>
    </div>

    <?php $this->endWidget(); ?>
    </fieldset>

</div><!-- form -->