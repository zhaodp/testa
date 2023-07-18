<?php 
$this->pageTitle = Yii::app()->name . ' - 修改密码';
?>
<h1>修改密码</h1>
<hr class="divider"/>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'passwd-form',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="grid-view">		
		<?php echo $form->errorSummary($model); ?>
		<label>原密码：</label>
		<input size="60" maxlength="60" name="Employee[old_password]" id="Employee_old_password" type="password" value="" />
		<label>新密码：</label>
		<input size="60" maxlength="60" name="Employee[password]" id="Employee_password" type="password" value="" />
                <span style="color:rgb(162,162,162);">(八位以上的字母、数字组合)</span>
		<label>重新输入新密码：</label>
		<input size="60" maxlength="60" name="Employee[re_password]" id="Employee_re_password" type="password" value="" />
	</div>
	<?php echo CHtml::submitButton('保存',array('class'=>'btn')); ?>
<?php $this->endWidget(); ?>
