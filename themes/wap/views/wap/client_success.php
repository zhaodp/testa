<?php 
	$this->pageTitle = '在线预约成功 - e代驾';
?>
<h5 class="text-center">
<div class="text-center alert alert-success">
您的订单信息已经提交，请保持手机畅通<br/>司机会提前半小时跟您联系，谢谢您使用e代驾！</h5>
</div>
</h5>	
	<center><input type="button" value="确认" id="success_btn"></center>

<script>
$(function(){
	$("#success_btn").click(function(){
		window.location.href="<?php echo Yii::app()->createUrl('clientOrder#tab3');?>";
	});
});
</script>