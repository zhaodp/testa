<div class="well span12">

    <?php
        $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        ));
    ?>

    <div class="span2">
        <?php echo $form->label($model, 'driverID'); ?>
        <?php echo $form->textField($model, 'driverID', array('size' => 60, 'maxlength' => 64, 'style' => 'width:150px;')); ?>
    </div>

    <div class="span2">
        <?php echo $form->label($model, 'os'); ?>
        <?php //echo $form->textField($model, 'os', array('size' => 60, 'maxlength' => 64, 'style' => 'width:150px;'));
        echo CHtml::dropDownList('os',$model->os,$os_arr,array('style' => 'width:150px;'));

        ?>
    </div>

        <?php
Yii::app()->getClientScript()->registerScriptFile(SP_URL_IMG . "WdatePicker/WdatePicker.js");
        ?>
    <div class="span2">
        <?php echo $form->label($model, 'call_time'); ?>
        <?php echo $form->textField($model,'call_time',array('value'=>$stime,'name' => 'btime','onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
    </div>

    <div class="span2" style="width:20px;">
        <?php echo CHtml::label('&nbsp;',''); ?>
        --
    </div>
    
    <div class="span2">
        <?php echo CHtml::label('&nbsp;','etime'); ?>
        <?php echo $form->textField($model,'call_time',array('value'=>$etime,'name' => 'etime','onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
    </div>

    <br>
    <div class="span2">
        <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
		<input type="button" name="yt0" class="btn" id="export_btn" value="导出 excel">
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->