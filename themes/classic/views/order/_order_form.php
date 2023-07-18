<div class="form-vertical">

<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'order-form', 
	'focus'=>array ($model, 'order_number'), 
	'enableAjaxValidation'=>false,
));
?>
<fieldset>
	<div class="control-group">
        <label for="optionsCheckbox" class="control-label">订单来源：</label>
        <div class="controls">
            <?php
	    $list = array();
			$source = array(
				Order::SOURCE_CALLCENTER_INPUT,
				Order::SOURCE_CLIENT_INPUT,
			);
	    foreach($source as $i) {
	        $list[$i] = Order::SourceToString($i);
	    }
	    echo $form->dropDownList($model, 'source', $list);
	    ?>
        </div>
    </div>
	<div class="control-group">
        <label for="optionsCheckbox" class="control-label">客户电话:</label>
        <div class="controls">
		<?php echo $form->textField($model, 'phone', array('class'=>'require'));?>
		<?php echo $form->error($model,'phone'); ?>
        </div>
    </div>
	<div class="control-group">
        <label for="optionsCheckbox" class="control-label">呼叫时间:</label>
        <div class="controls">
		<?php
		$days = 2;
		$choose_date = '';
		for($i=$days;$i>=0;$i--){
			$curr_date = date('Y-m-d', time()-$i*3600*24);
			$order_date[$curr_date] = $curr_date;
			if($i==1){
				$choose_date = $curr_date;
			}
		}
		echo CHtml::dropDownList('Order[call_time]', $choose_date, array_reverse($order_date),array('style'=>'border:1px solid red;width:110px'));
		
		
//		$this->widget('zii.widgets.jui.CJuiDatePicker', array (
//			'name'=>'Order[call_time]', 
//			'model'=>$model,  //Model object
//			//'attribute'=>'start_time', //attribute name
//			'value'=>(!$model->call_time)?'':date('Y-m-d', $model->call_time), 
//			'options'=>array (
//				'dateFormat'=>'yy-mm-dd',
//				'currentText'=>'当前时间',
//			),  // jquery plugin options
//			'language'=>'zh',
//			'htmlOptions'=>array('style'=>'border:1px solid red;width:80px','readonly'=>true)
//		));
		echo '时间：';
		echo CHtml::textField('Order[call_hour]',($model->call_time)?Date('H',$model->call_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:13px','class'=>'require')) . '时';
		echo CHtml::textField('Order[call_min]',($model->call_time)?Date('i',$model->call_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:13px','class'=>'require')). '分';
		?>
		<?php echo $form->error($model,'call_time'); ?>			
        </div>
    </div>
	<div class="control-group">
        <label for="optionsCheckbox" class="control-label">预约时间:</label>
        <div class="controls">
		<?php
		echo CHtml::dropDownList('Order[booking_time]', $choose_date, array_reverse($order_date),array('style'=>'border:1px solid red;width:110px'));
		
//		$this->widget('zii.widgets.jui.CJuiDatePicker', array (
//			'name'=>'Order[booking_time]', 
//			'model'=>$model,  //Model object
//			//'attribute'=>'start_time', //attribute name
//			'value'=>(!$model->booking_time)?'':date('Y-m-d', $model->booking_time), 
//			'options'=>array (
//				'dateFormat'=>'yy-mm-dd',
//				'currentText'=>'当前时间',
//			),  // jquery plugin options
//			'language'=>'zh',
//			'htmlOptions'=>array('style'=>'border:1px solid red;width:80px','readonly'=>true)
//		));
		echo '时间：';
		echo CHtml::textField('Order[booking_hour]',($model->booking_time)?Date('H',$model->booking_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:13px','class'=>'require')) . '时';
		echo CHtml::textField('Order[booking_min]',($model->booking_time)?Date('H',$model->booking_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:13px','class'=>'require')). '分';
		?>
		<?php echo $form->error($model,'booking_time'); ?>	
        </div>
    </div>
</fieldset>


<div style="margin-top: 5px;">
	<?php
	echo CHtml::submitButton($model->isNewRecord ? '创建订单' : '保存', array (
		'class'=>'btn-large'
	));
	?>
</div>
<div style="clear: both"></div>
</div>

<?php
$this->endWidget();
?>

<script>
$("fieldset").find("input[type='text']").each(function(i){
	if($(this).attr('value') =='0' || $(this).attr('value') =='00'){
		$(this).val("");
	}
});


</script>
<!-- form -->
