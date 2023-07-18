<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
    <?php
        Yii::app()->getClientScript()->registerScriptFile(SP_URL_IMG . "WdatePicker/WdatePicker.js");
    ?>
<div class="span12">
    <div class="span2">
        <?php echo $form->label($model, 'department'); ?>
        <?php echo $form->dropDownList($model, 'department', AdminDepartment::model()->getAllDepartment(true),array('style'=>'width:150px;','ajax'=>array(
                    'type'=>'get',
                    'url'=>Yii::app()->createUrl('adminWorkLog/admin'),
                    'update'=>'#AdminWorkLog_category',
                    'data'=>array('department'=>'js:$(this).val()','getCategory'=>'1'),
                )));
        ?>
    </div>
        
    <div class="span2">
        <?php echo $form->labelEx($model,'category'); ?>
        <?php echo $form->dropDownList($model,'category',isset(AdminWorkLog::$categorys[$model->department]) ? AdminWorkLog::$categorys[$model->department] : array(),array('empty'=>'全部','style'=>'width:150px;')); ?>
        <?php echo $form->error($model,'category'); ?>
    </div>
    
    <div class="span2">
		<?php echo $form->label($model,'city'); ?>
        <?php echo $form->dropDownList($model,'city',Dict::items('city'),array('style'=>'width:120px;')); ?>
    </div>
    
    <div class="span2">
		<?php echo CHtml::label('日期','btime'); ?>
        <?php echo $form->textField($model,'btime',array('onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
    </div>

    <div class="span2" style="width:20px;">
        <?php echo CHtml::label('&nbsp;',''); ?>
        --
    </div>
    
    <div class="span2">
        <?php echo CHtml::label('&nbsp;','etime'); ?>
        <?php echo $form->textField($model,'etime',array('onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
    </div>
</div>
<div class="span12">
	<div class="span2">
            <?php echo $form->label($model,'author'); ?>
            <?php echo $form->textField($model,'author',array('style'=>'width:150px;')); ?>
        </div>
	
	<div class="span2">
            <?php echo $form->labelEx($model,'type'); ?>
            <?php echo $form->dropDownList($model,'type',AdminWorkLog::$type,array('style'=>'width:80px;')); ?>
            <?php echo $form->error($model,'type'); ?>
        </div>

	<div class="span3 buttons">
        <?php echo CHtml::label('&nbsp;',' '); ?>
		<?php echo CHtml::submitButton('搜索', array('class'=>'btn btn-primary')); ?>
	</div>
</div>

<?php $this->endWidget(); ?>

</div>

<script>
    $().ready($('#AdminWorkLog_department').change());
</script>
