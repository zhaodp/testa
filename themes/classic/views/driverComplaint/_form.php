<div class="form">
<?php 
;
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-complaint-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'complaint_type'); ?>
		<?php
		if($orderstatus==1){
					$dict = Dict::items('confirm_c_type');
					$dict[0] = '请选择投诉类型';
					rsort($dict);
					echo CHtml::dropDownList('complaint_type',0 , $dict);
		}else if($orderstatus==2||$orderstatus==3||$orderstatus==4){
					$dict = Dict::items('cancel_c_type');
					$dict[0] = '请选择投诉类型';
					rsort($dict);
					echo CHtml::dropDownList('complaint_type',0 , $dict);
		}
		?>
		<?php echo $form->error($model,'complaint_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'complaint_content'); ?>
		<?php echo $form->textArea($model,'complaint_content',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'complaint_content'); ?>
	</div>
	<input type="hidden" name="'orderId" id="orderId" value="<?php echo $orderId;?>"/>
	<div class="row buttons">
		<?php echo CHtml::button('提交',array('id'=>'save_btn')); ?>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->
<script>
$("#save_btn").click(function(){
	if($("#DriverComplaint_complaint_content").val()==''){
		alert("请输入投诉内容");
		return false;
	}
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/driverComplaint/create');?>',
		'data':'complaint_type='+$("#complaint_type").val() + '&complaint_content=' + $("#DriverComplaint_complaint_content").val()+"&orderId="+$("#orderId").val(),
		'type':'post',
		'success':function(data){
			if(data==1){
				alert("操作成功");
				parent.location.reload();
				$(window.parent.document).find(".ui-dialog-buttonset button").click();
			}else{
				alert("操作失败");
				parent.location.reload();
				$(window.parent.document).find(".ui-dialog-buttonset button").click();
				
			}
		},
		'cache':false		
	});
	return false;
});
</script>