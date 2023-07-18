
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'admin-group-user_group_form-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'parentid'); ?>
		<?php echo $form->textField($model,'parentid'); ?>
		<?php echo $form->error($model,'parentid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'code'); ?>
		<?php echo $form->textField($model,'code'); ?>
		<?php echo $form->error($model,'code'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'position'); ?>
		<?php echo $form->textField($model,'position'); ?>
		<?php echo $form->error($model,'position'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('保存'); ?>
	</div>
	<legend>&nbsp;</legend>
	
	<div class="row">
		<h4>功能模块列表：</h4>
		<?php 
			$mods = AdminRoles::model()->getValidMods();
			
			$group_info = $model->attributes;
			$group_id = empty($group_info['id']) ? '' : $group_info['id'];
			
			$group_mods = empty($group_info['mods']) ? array() : explode(',',$group_info['mods']);
			
			$new_mods = array();
			foreach( $mods as $item )
			{
				$item['view_name'] = $item['name']."({$item['controller']}/{$item['action']})";
				$new_mods[$item['controller']][] = $item;
			}
			
			foreach( $new_mods as $g => $item_mods ) 
			{
				//echo '<legend>'.CHtml::label($g, null, array ( 'style'=>'display:inline')).'</legend>';
				$all_select = " <input type='checkbox'  style='vertical-align:top;' name='chk_all' data='{$g}' />全选/取消";
				echo '<legend><div style="margin-bottom:0px;width:200px;padding:4px 0px 0px 8px" class="alert alert-success">'.CHtml::label($g.$all_select, null)." </div></legend>";
				echo "<div id = '{$g}' >";
				
				$i=0;
				foreach($item_mods as $item) {
					if($i==0){
						echo "&nbsp;&nbsp;";
						$i++;
					}
					echo '<label class="checkbox inline">';
					echo CHtml::checkBox('AdminGroup[mods][]', in_array( $item['id'],$group_mods ), array (
						'id'=>'AdminGroup_mods_' . $item['id'], 
						'value'=>$item['id'], 
						'name'=>$item['controller']."_".$item['action'], 
						'separator'=>''));
					echo $item['view_name'].'</label>';
				}
				echo "</div>";
			}

		?>
		
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">

$(document).ready(function(){

	$("input[name='chk_all']").click(function(){
		var attr = "checked" == $(this).attr("checked") ? true : false ;
		var id = $(this).attr('data');
		$("#"+id+" label :checkbox").attr("checked",attr );
	});
});

</script>