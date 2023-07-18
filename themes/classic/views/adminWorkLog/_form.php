<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'admin-work-log-form',
	'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->errorSummary($model); ?>

     <div class="row">
        <div class="span3">
            <?php echo $form->labelEx($model,'city'); ?>
            <?php echo $form->dropDownList($model,'city',Dict::items('city'),array('style'=>'width:120px;')); ?>
            <?php echo $form->error($model,'city'); ?>
        </div>

        <div class="span3">
            <?php echo $form->labelEx($model,'work_date'); ?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'AdminWorkLog[work_date]',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
        </div>

        <?php $department = Yii::app()->user->department;if(isset(AdminWorkLog::$categorys[$department])){ ?>
        <div class="span3">
            <?php echo $form->labelEx($model,'category'); ?>
            <?php echo $form->dropDownList($model,'category',AdminWorkLog::$categorys[$department],array('empty'=>'请选择')); ?>
            <?php echo $form->error($model,'category'); ?>
        </div>
        <?php } ?>
    	 <div class="span3">
            <?php echo $form->labelEx($model,'type'); ?>
            <?php echo $form->dropDownList($model,'type',AdminWorkLog::$type,array('style'=>'width:120px;')); ?>
            <?php echo $form->error($model,'type'); ?>
        </div>
     </div>
	<div class="row">
		<?php echo $form->labelEx($model,'work_log'); ?>
		<?php echo $form->textArea($model,'work_log',array('rows'=>15, 'cols'=>100, 'style'=>'width:800px;')); ?>
		<?php echo $form->error($model,'work_log'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '提交' : '保存', array('class'=>'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
