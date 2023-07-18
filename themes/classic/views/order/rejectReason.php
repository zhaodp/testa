<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'reject-form',
	'enableAjaxValidation'=>false,
)); 
switch ($_GET['status']){
	case Order::ORDER_NOT_COMFIRM:
		$title = '拒绝销单';
		break;
	case Order::ORDER_COMFIRM:
		$title = '同意销单';
		break;
	case Order::ORDER_READY:
		$title = '退单补信息';
		break;
}
?>
<h1><?php echo $title; ?></h1>
	<div class="grid-view">		
		<textarea rows="5" style="width:500px;height:150px" cols="50" name="OrderLog[description]" id="OrderLog_description"></textarea>
		<input name="OrderLog[order_id]" id="OrderLog_order_id" type="hidden" value="<?php echo $model->order_id; ?>" />
		<input name="OrderLog[status]" id="OrderLog_status" type="hidden" value="<?php echo $_GET['status']; ?>" />
	</div>
<?php $this->endWidget(); ?>