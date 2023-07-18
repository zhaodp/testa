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
        
    <?php $department = Yii::app()->user->department;if(isset(AdminWorkLog::$categorys[$department])){ ?>
    <div class="span2">
        <?php echo $form->labelEx($model,'category'); ?>
        <?php echo $form->dropDownList($model,'category',AdminWorkLog::$categorys[$department],array('empty'=>'全部','style'=>'width:150px;')); ?>
        <?php echo $form->error($model,'category'); ?>
    </div>
    <div class="span2">
            <?php echo $form->labelEx($model,'type'); ?>
            <?php echo $form->dropDownList($model,'type',AdminWorkLog::$type,array('style'=>'width:80px;')); ?>
            <?php echo $form->error($model,'type'); ?>
    </div>
  </div>
    <?php } ?>
    <div class="span12">
	<div class="span1 buttons">
        <?php echo CHtml::label('&nbsp;',' '); ?>
		<?php echo CHtml::submitButton('搜索', array('class'=>'btn btn-primary')); ?>
	</div>
	<div class="span2 buttons">
        <?php echo CHtml::label('&nbsp;',' '); ?>
        <?php echo CHtml::link('添加日志',array('adminWorkLog/create'),array('class'=>'btn btn-primary',)); ?>
	</div>
	
	<div class="span2 buttons">
        <?php echo CHtml::label('&nbsp;',' '); ?>
        <?php echo CHtml::link('查看本部门日志',array('adminWorkLog/Depworklog'),array('class'=>'btn btn-primary',)); ?>
	</div>
     </div>
<?php $this->endWidget(); ?>

</div>
