<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver version'=>array('index'),
	'Create',
);

?>

<h2>司机版本添加</h2>

<div class="form span11">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-version-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation' => true,
    'clientOptions' => array(
    'validateOnSubmit' => true  //在这个位置做验证
    ),


)); ?>
	<?php echo $form->errorSummary($model); ?>

	<input type='hidden' name='DriverClientVersion[id]' value='<?php echo $model->id?>'/>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'deprecated'); ?>
		<?php echo $form->textField($model,'deprecated',array('size'=>10,'maxlength'=>10)); ?>
			<?php echo $form->error($model,'deprecated'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'deprecated_int'); ?>
		<?php echo $form->textField($model,'deprecated_int',array('size'=>9,'maxlength'=>9)); ?>
			<?php echo $form->error($model,'deprecated_int'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'latest'); ?>
		<?php echo $form->textField($model,'latest',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'latest'); ?>


	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'latest_int'); ?>
		<?php echo $form->textField($model,'latest_int',array('size'=>9,'maxlength'=>9)); ?>
		<?php echo $form->error($model,'latest_int'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textField($model,'url',array('size'=>255,'maxlength'=>255)); ?>
				<?php echo $form->error($model,'url'); ?>


	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'beta_latest'); ?>
		<?php echo $form->textField($model,'beta_latest',array('size'=>10,'maxlength'=>10)); ?>
				<?php echo $form->error($model,'beta_latest'); ?>


	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'beta_url'); ?>
		<?php echo $form->textField($model,'beta_url',array('size'=>255,'maxlength'=>255)); ?>
				<?php echo $form->error($model,'beta_url'); ?>


	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'updatetime'); ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'DriverClientVersion[updatetime]', 
				'model'=>$model,  //Model object
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'value'=>$model->updatetime,
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('size'=>60,'maxlength'=>100)
			));
		?>
				<?php echo $form->error($model,'updatetime'); ?>


	</div>
    <div class="row">
		<?php echo $form->labelEx($model,'up_desc'); ?>
		<?php echo $form->textArea($model,'up_desc',array('size'=>500,'maxlength'=>500)); ?>
				
        <?php echo $form->error($model,'up_desc'); ?>

	</div>
	

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '保存' : '修改',array('name' => 'save','class'=>'btn')); ?>
		<?php 
		if($model->isNewRecord){
			echo CHtml::link('返回列表',Yii::app()->createUrl('driver/version'),array('class'=>'btn'));
		}?>
	</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'tc-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
        	array(
                'name' => 'ID',
                'value' => '$data->id'
            ),
            array(
                'name' => '版本名',
                'value' => '$data->name'
            ),
            array(
                'name' => '过期版本',
                'value' =>'$data->deprecated'
            ),
        	array(
        				'name' => '整数过期版本',
        				'value' =>'$data->deprecated_int'
        	),
	    array(
                'name' => '当前版本',
                'value' =>'$data->latest'
            ),
        array(
        				'name' => '整数当前版本',
        				'value' =>'$data->latest_int'
        ),
		array(
                'name' => '当前版本下载Url',
                'value' =>'$data->url'
            ),
	    array(
                'name' => '测试版本',
                'value'=>'$data->beta_latest'
            ),
	    array(
                'name' => '测试版本Url',
                'value' =>'$data->beta_url'
            ),
            array(
                'name' => '更新时间',
                'value' =>'$data->updatetime'
            ),
        	array(
        		'name' => '升级文案',
        		'value' =>'$data->up_desc'
        	),
	    array(
            	'name' => '操作',
            	'value' => array($this, 'versionOpt')
            ),
       ),
    ));?>
<?php $this->endWidget(); ?>
</div><!-- form -->

