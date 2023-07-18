<?php
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
)); ?>

    <div class="control-group">

        <div class="control-group">
            <label class="control-label" for="inputEmail">处理备注</label>
            <div class="controls">
                <input type="hidden" name="re" value="<?php echo $re ?>">
                <input type="hidden" name="cid" value="<?php echo $cid ?>">
                <?php echo CHtml::textArea('mark','',array('rows'=>6,'style'=>'width: 290px; height: 120px;')); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo CHtml::submitButton('提交',array('class'=>'btn btn-large btn-primary','name'=>'user_save')); ?>
        </div>
    </div>



<?php $this->endWidget(); ?>