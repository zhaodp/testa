<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'complain-driver-confirm-form',
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'post',
    )); ?>
    <div class="control-group">
    <label class="control-label" for="inputEmail">处理结果</label>
    <div class="controls">
        <?php echo CHtml::dropDownList('dm_process','0',CustomerComplain::$driver_spro); ?>
        <input type="hidden" name="re" value="<?php echo $re ?>">
        <input type="hidden" name="cid" value="<?php echo $cid ?>">
    </div>
    <div class="control-group">
        <label class="control-label" for="inputEmail">备注</label>
            <div class="controls">
                <textarea rows="3" name="mark"></textarea>
            </div>
    </div>
        <div class="control-group">
            <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large btn-primary')); ?>
        </div>
</div>

    <?php $this->endWidget(); ?>
</div>