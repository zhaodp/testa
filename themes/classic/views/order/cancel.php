<?php
$this->pageTitle = '销单';
?>
<h1>销单</h1>
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'order-form',
		'focus'=>array($model,'order_number'),
		'enableAjaxValidation'=>false,
	)); ?>
	<?php echo $form->hiddenField($model,'order_id'); ?>
	<fieldset>
		<legend>订单基本信息</legend>
		<table summary="Order form">
			<tbody>
			<tr>
				<td class="first"><label>客户电话:</label></td>
				<td><?php echo $model->phone; ?></td>
		    </tr>
		    <tr>
				<td>呼叫时间:</td>
				<td><?php echo date('m-d H:i',$model->call_time); ?></td>
			</tr>
			<tr>
				<td>销单类型:</td>
				<td><?php 
					$cancel_type = Dict::items('cancel_type');
					$cancel_type = array(
						0 => '请选择原因',
						1 => '客户单方面取消',
						2 => '客人咨询或拨错电话',
						3 => '未接听到客人电话',
						4 => '骚扰电话',
						5 => '其它',
					); 
					echo $form->dropDownList($model,
						'cancel_type',
						$cancel_type,array('style'=>'border:1px solid red;'))
				?></td>
			</tr>
		    <tr>
				<td>销单原因:</td>
				<td><textarea rows="5" cols="60" id="Order_log" name="Order[log]" style="border:1px solid red;"></textarea></td>
			</tr>
			 <tr>
				<td>是否投诉:<input type="checkbox" name="isComplaint" id="isComplaint" ></td>
				<td>
						<div class="controls" id="complaintBox" style="display:none">
						<?php 
						$dict = Dict::items('cancel_c_type');
						$dict[0] = '请选择投诉类型';
						rsort($dict);
						echo CHtml::dropDownList('status',0 , $dict)?>
						<br>
							<textarea rows="2" cols="35" id="complaint" name="complaint"></textarea>
						</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : '保存',array('class'=>'btn btn-large')); ?>
				</td>
			</tr>
		    </tbody>
		</table>
	</fieldset>
	<?php $this->endWidget(); ?>
</div>
<script>
$(function(){
	$("#isComplaint").change(function(){
		if($("#isComplaint").attr("checked")=="checked"){
			$("#complaintBox").slideDown(200);
		}else{
			$("#complaintBox").slideUp(200);
		}
	});
	$(".btn-large").click(function(){
		
			if($("#isComplaint").attr("checked")=="checked"){
				if($("#complaint").val()==''){
					alert("请输入投诉内容");
					return false;
				}
			}
	});
});
</script>