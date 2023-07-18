<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
    <?php
        Yii::app()->getClientScript()->registerScriptFile(SP_URL_IMG . "WdatePicker/WdatePicker.js");
    ?>
    <div class="span2">
		<?php echo CHtml::label('日期','btime'); ?>
        <?php echo $form->textField($model,'btime',array('onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
    </div>

    <div class="span1" style="width:20px;">
        <?php echo CHtml::label('&nbsp;',''); ?>
        --
    </div>
    
    <div class="span2">
        <?php echo CHtml::label('&nbsp;','etime'); ?>
        <?php echo $form->textField($model,'etime',array('onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
    </div>

	<div class="span3 buttons">
        <?php echo CHtml::label('&nbsp;',' '); ?>
		<?php echo CHtml::submitButton('搜索', array('class'=>'btn btn-primary')); ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo CHtml::link('我的日志',array('adminWorkLog/index'),array('class'=>'btn btn-primary',)); ?>
	</div>

<?php $this->endWidget(); ?>

</div>