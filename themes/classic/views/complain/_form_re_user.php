<?php
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
)); ?>

    <div class="control-group">
        <label class="control-label" for="inputEmail">申请补偿金额</label>
        <div class="controls">
            <?php echo CHtml::textField('recoup_amount',$amount,array('placeholder'=>'申请补偿金额','disabled'=>'disabled')); ?>
            <input type="hidden" name="re" value="<?php echo $re ?>">
            <input type="hidden" name="cid" value="<?php echo $cid ?>">
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">金额</label>
            <div class="controls">
                <?php echo CHtml::textField('recoup_amount_real','',array('placeholder'=>'实际补偿用户金额')); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">备注</label>
            <div class="controls">
                <?php echo CHtml::textArea('mark','',array('row'=>3)); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large btn-primary','name'=>'user_save')); ?>
        </div>
    </div>



<?php $this->endWidget(); ?>