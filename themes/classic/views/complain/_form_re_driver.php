<?php
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
)); ?>
    <div class="control-group">
        <label class="control-label" for="inputEmail">信息费退款</label>
        <div class="controls">
            <?php echo CHtml::textField('cast','',array('placeholder'=>'信息费退还金额')); ?>
            <input type="hidden" name="re" value="<?php echo $re ?>">
            <input type="hidden" name="cid" value="<?php echo $cid ?>">
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">工服</label>
            <div class="controls">
                <?php echo CHtml::textField('clothing_fee','',array('placeholder'=>'工服退还金额')); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">胸卡</label>
            <div class="controls">
                <?php echo CHtml::textField('card_fee','',array('placeholder'=>'胸卡退还金额')); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">其它</label>
            <div class="controls">
                <?php echo CHtml::textField('other_fee','',array('placeholder'=>'其它费用')); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">备注</label>
            <div class="controls">
                <textarea rows="3" name="mark"></textarea>
            </div>
        </div>
        <div class="control-group">
            <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large btn-primary','name'=>'driver_save')); ?>
        </div>
    </div>


<?php $this->endWidget(); ?>