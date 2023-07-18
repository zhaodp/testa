<?php
/* @var $this AdminRolesController */
/* @var $model AdminRoles */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'admin-roles-update-form',
    'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->errorSummary($model); ?>

	<?php echo $form->labelEx($model,'name'); ?>
	<?php echo $form->textField($model,'name'); ?>

	<?php echo $form->labelEx($model,'controller'); ?>
	<?php echo $form->textField($model,'controller'); ?>
	
	<?php echo $form->labelEx($model,'action'); ?>
	<?php echo $form->textField($model,'action'); ?>
	
	<?php echo $form->labelEx($model,'roles'); ?>
	<?php //echo $form->textField($model,'roles'); ?>
	<h4>权限设置：</h4>
	<?php 
		$group = AdminGroup::model()->getGroups();
		$group_list = is_array($model->roles) ? $model->roles : explode(",",$model->roles);
		
		foreach($group as $item) {
			if (isset($item['children'])) {
				echo '<legend><div style="margin-bottom:0px;width:120px;padding:4px 0px 0px 8px" class="alert alert-success">'.CHtml::label($item['name'], null).'</div></legend>';
				echo '<div style="border-radius:4px;border:1px solid #eeeeee;padding-bottom:6px;margin:-9px 0px 5px 0px">';
				foreach($item['children'] as $child) {
					echo '<label class="checkbox inline">';
					echo CHtml::checkBox('AdminRoles[roles][]', in_array($child['id'],$group_list), array (
						'id'=>'AdminUser_roles_' . $child['id'], 
						'value'=>$child['id'], 
						'name'=>$child['name'], 
						'separator'=>''));
					echo $child['name'].'</label>';
				}
				echo '</div>';
			}
		}
	?>	

    <div class="buttons">
		<?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?>
    </div>

<?php $this->endWidget(); ?>

</div>