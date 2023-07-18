<?php
$this->pageTitle = '充值卡 激活';
?>

<h1><?php echo $this->pageTitle; ?></h1>
<?php
if (!empty($error))
{
?>
<div class="well alert-error">
<?php
echo $error; 
?>
</div>
<?php
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'vip-form',
	'enableAjaxValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>

<div class="row-fluid">
	<div class="span4">
		<label for="id">请输入充值卡号(8位卡号)</label>
		<input type="text" id="id" name="id" value="<?php echo $id;?>" />
	</div>
</div>
<div class="row-fluid">
	<div class="span4">
		<label for="pass">请输入密码(6位密码)</label>
		<input type="text" id="pass" name="pass" value="<?php echo $pass;?>" />
	</div>
</div>
<div class="row-fluid">
	<div class="span4">
		<label for="phone">请用户称谓</label>
		<input type="text" id="name" name="name" value="<?php echo $name;?>" />
	</div>
</div>
<div class="row-fluid">
	<div class="span4">
		<label for="phone">请输入手机号(11位手机号)</label>
		<input type="text" id="phone" name="phone" value="<?php echo $phone;?>" />
	</div>
</div>
<div class="row-fluid">
	<div class="span4">
		<?php echo CHtml::submitButton('激活',array('class'=>'btn btn-success')); ?>
	</div>
</div>
<?php $this->endWidget(); ?>
</div>



	