<h2>品监工具</h2>
<?php echo CHtml::link('新优惠卷绑定','javascript:void(0);',array('onClick'=>'DialogInit()')); ?>
<br/>
<?php echo CHtml::link('手动结账','javascript:void(0);',array('onClick'=>'OrderDialogInit()')); ?>

<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_driver_dialog', 
	'options'=>array (
		'title'=>'优惠券绑定', 
		'autoOpen'=>false, 
		'width'=>'480', 
		'height'=>'380', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<script>
function DialogInit(){
	var src = '<?php echo Yii::app()->createUrl('/bonusType/manualUse');?>';
	$("#view_driver_frame").attr("src",src);
	$("#view_driver_dialog").dialog("open");
	return false;
}

function OrderDialogInit(){
	var src = '<?php echo Yii::app()->createUrl('/order/manualInvoice');?>';
	$("#view_driver_frame").attr("src",src);
	$("#view_driver_dialog").dialog("open");
	return false;
}
</script>
