<div class="form">

<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'form-horizontal', 
	'focus'=>array ($model, 'order_number'), 
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal', "onSubmit" => "checkSubmit()")
));
?>
<?php echo $form->hiddenField($model, 'user_id');?>
<fieldset>
	<table class="well span12">
		<tbody>
		<tr>
			<td nowrap="nowrap"><label>订单来源：</label></td>
			<td><span><?php echo $model->description;?></span></td>
	    </tr>
		<tr>
			<td><label>客户电话:</label></td>
			<td><span><?php echo $model->phone; ?></span></td>
	    </tr>
        <?php
        switch ($model->cost_type) {
            case 1:
                echo '<tr>
			<td><label>客户类型：</label></td>
			<td><span>VIP卡号：' . $model->vipcard . '</span></td>
	    </tr>';
                break;
            case 2:
                echo '<tr>
			<td><label>客户类型：</label></td>
			<td><span>优惠劵用户</span></td>
	    </tr>';
                break;  
            case 4:
                echo '<tr>
			<td><label>客户类型：</label></td>
			<td><span>优惠劵用户</span></td>
	    </tr>';
                break;
            case 8:
                echo '<tr>
			<td><label>客户类型：</label></td>
			<td><span>预付费用户</span></td>
	    </tr>';
                break;
        }
        ?>

	    <tr>
			<td><label>呼叫时间:</label></td>
			<td><span><?php echo date('Y-m-d H:i', $model->call_time);?></span></td>
	    </tr>
	    <tr>
			<td><label>预约时间:</label></td>
			<td><span><?php echo date('Y-m-d H:i', $model->booking_time);?></span></td>
		</tr>
		<?php echo $form->hiddenField($model,'bonus_code',array('placeholder'=>'仅限微信优惠码')); ?>
		<?php echo $form->hiddenField($model,'vipcard',array('placeholder'=>'VIP客户卡号')); ?>
	    <tr>
			<td><label>单号:</label></td>
			<td><?php echo $form->textField($model, 'order_number', array('class'=>'require','placeholder'=>'填入完整单号，包括带A的单号', 'autocomplete' => 'off' ));?>&nbsp;&nbsp;*必填
				<?php echo $form->error($model,'order_number',array('style'=>'width:210px;'));?>
			</td>
		</tr>
	    
	    <tr>
			<td><label>客户名称:</label></td>
			<td><?php echo $form->textField($model, 'name', array ('size'=>20,'maxlength'=>20,'class'=>'require', 'autocomplete' => 'off' ));?>&nbsp;&nbsp;*必填
			<?php echo $form->error($model,'name',array('style'=>'width:210px;')); ?>
			</td>
		</tr>
		<tr>
			<td><label>车牌号:</label></td>
			<td><input type="text" value="<?php echo $parameter['car_number']; ?>" id="Order_car_number" name="Order[car_number]" maxlength="20" autocomplete = "off" placeholder="车牌号码" class="require">&nbsp;&nbsp;*必填
			<?php echo $form->error($model,'car_number',array('style'=>'width:210px;'));?>
			</td>
		</tr>
		<tr style="display:none">
			<td colspan = '2'><label><input type="checkbox" name="isComplaint" id="isComplaint" >
		是否投诉</label></td>
		</tr>
		<tr style="display:none">
			<td><label>投诉类型:</label></td>
			<td><?php 
		$dict = Dict::items('confirm_c_type');
		echo CHtml::dropDownList('status',1 , $dict)?>
			</td>
		</tr>
		<tr style="display:none">
			<td><label>投诉描述:</label></td>
			<td>
				<textarea rows="2" cols="35" id="complaint" name="complaint"></textarea>
			</td>
		</tr>
		
		
	    <tr>
			<td><label>出发地点:</label></td>
			<td><?php echo $form->textField($model, 'location_start', array ('size'=>20,'maxlength'=>20,'class'=>'require', 'autocomplete' => 'off' ));?>&nbsp;&nbsp;*必填
			<?php echo $form->error($model,'location_start',array('style'=>'width:210px;'));?>
			</td>
		</tr>
	    <tr>
			<td><label>到达地点:</label></td>
			<td><?php echo $form->textField($model, 'location_end', array ('size'=>20,'maxlength'=>20,'class'=>'require', 'autocomplete' => 'off' ));?>&nbsp;&nbsp;*必填
			<?php echo $form->error($model,'location_end',array('style'=>'width:210px;'));?></td>
		</tr>
		
		 <tr>
			<td><label>出发时间:</label></td>
			<td><?php
			$days = (time() - $model->call_time)/3600/24+1;
			$call_time = date('Y-m-d',$model->call_time);
			$order_date[$call_time] = $call_time;
			for($i=$days;$i>=0;$i--){
				$curr_date = date('Y-m-d', $model->call_time+($i-1)*3600*24);
				$order_date[$curr_date] = $curr_date;
			}
			echo CHtml::dropDownList('Order[start_time]', '', $order_date,array('style'=>'width:120px'))."&nbsp;";
			echo CHtml::textField('Order[start_hour]',($model->start_time)?Date('H',$model->start_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:18px','class'=>'require', 'autocomplete' => 'off' )) . '时';
			echo CHtml::textField('Order[start_min]',($model->start_time)?Date('i',$model->start_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:18px','class'=>'require', 'autocomplete' => 'off' )). '分 &nbsp;&nbsp;*必填';
			?>
			</td>
		</tr>
	    <tr>
			<td><label>到达时间:</label></td>
			<td>
			<?php
			echo CHtml::dropDownList('Order[end_time]', '', $order_date,array('style'=>'width:120px')) . "&nbsp;";
			echo CHtml::textField('Order[end_hour]',($model->end_time)?Date('H',$model->end_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:18px','class'=>'require', 'autocomplete' => 'off' )) . '时';
			echo CHtml::textField('Order[end_min]',($model->end_time)?Date('i',$model->end_time):'',array('size'=>2,'maxlength'=>2,'style'=>'width:18px','class'=>'require', 'autocomplete' => 'off' )). '分 &nbsp;&nbsp;*必填';
			echo $form->error($model,'end_time',array('style'=>'width:210px;'));
			?>	
			</td>
		</tr>
		
		<tr>
			<td><label>等候时间:</label></td>
			<td>
				<?php
				if (isset($modelExt))
				{
					echo CHtml::textField('OrderExt[wait_time]', $parameter['wait_time'],array('style'=>'width:210px;','placeholder'=>'等候时间', 'autocomplete' => 'off' ));
				}
				else{
					echo CHtml::textField('OrderExt[wait_time]', $parameter['wait_time'],array('style'=>'width:210px;','placeholder'=>'等候时间', 'autocomplete' => 'off' ));
				}
				?>
				分钟
				<?php echo $form->error($model,'wait_time',array('style'=>'width:210px;'));?>
			</td>		
		</tr>
		
	   
	    <tr>
			<td><label>里程:</label></td>
			<td>
				<?php echo $form->textField($model, 'distance', array('class'=>'require','placeholder'=>'代驾里程，可以为0', 'autocomplete' => 'off' ));?>
				<?php echo $form->error($model,'distance',array('style'=>'width:210px;'));?>
			</td>
		</tr>
		<tr>
		<td>&nbsp;</td>
            <td>
            <?php
            if ($model->cost_type > 0) {
                switch ($model->cost_type) {
                    case 1:
                        echo '<label>';
                        if ($model->cost_type == 1 || $model->income == 0) {
                            echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                        } else {
                            echo $form->checkBox($model, 'cost_type');
                        }
                        echo '&nbsp;从客户账户中扣除代驾费</label>';
                        break;
                    case 2:
                        echo '<label>';
                        if ($model->cost_type == 2 || $model->income == 0) {
                            echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                        } else {
                            echo $form->checkBox($model, 'cost_type');
                        }
                        echo '&nbsp;使用客户优惠券抵扣代驾费</label>';
                        break;
                    case 4:
                        echo '<label>';
                        if ($model->cost_type == 2 || $model->income == 0) {
                            echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                        } else {
                            echo $form->checkBox($model, 'cost_type');
                        }
                        echo '&nbsp;使用客户优惠券抵扣代驾费</label>';
                        break;
                    case 8:
                        echo '<label>';
                        if ($model->cost_type == 2 || $model->income == 0) {
                            echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                        } else {
                            echo $form->checkBox($model, 'cost_type');
                        }
                        echo '&nbsp;预付费用户</label>';
                        break;
                }
            }
            ?>
            </td>
		</tr>
		<tr id = "controls_income">
			
		</tr>
		
	    <tr>
			<td><label>代驾服务费:</label></td>
			<td><?php echo $form->textField($model, 'price', array('class'=>'require','placeholder'=>'实际收取的代驾费用', 'autocomplete' => 'off' ));
					echo $form->error($model,'price',array('style'=>'width:210px;'));?></td>
		</tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <b style="color:#ff0000">注意：此处费用为收到的现金，请如<br/>实填写，如没有收到现金，可不输入</b>
            </td>
        </tr>

	    <tr>
			<td><label>备注:</label></td>
			<td><textarea rows="2" cols="35" id="Order_log" name="Order[log]"
                          placeholder="<?php if (empty($model->vipcard)) {
                              echo "客人额外支付的费用（包括小费），需描述清楚";
                          } else {
                              echo "客人额外支付的现金费用（包括小费），需描述清楚，此处填写的费用不会从VIP账户里面扣除";
                          } ?>"></textarea></td>
		</tr>
		</tbody>
	</table>
</fieldset>	

<div style="padding-top: 5px; text-align: center">
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : '保存',array('class'=>'btn span1 btn-success'));?>
</div>
</div>

<?php
$this->endWidget();
?>

<script>
window.onload = function(){
	Order_cost();
}

$("fieldset").find("input[type='text']").each(function(i){
	if($(this).attr('value') =='0'){
		$(this).val("");
	}
	if($(this).attr('value') =='00'){
		$(this).val("");
	}
});

$('body').on('keyup', '#Order_distance', function () {
    var order_start_hour = $("#Order_start_hour").val();
    var order_start_min = $("#Order_start_min").val();
    if (order_start_hour != '' || order_start_min != '') {
        Order_cost();
    } else {
        alert("请先填写开始时间");
    }
});


$('body').on('keyup', '#Order_start_hour', function () {
    var order_distance = $("#Order_distance").val();
    if (order_distance != '') {
        Order_cost();
    }
});

$('body').on('keyup', '#Order_start_min', function () {
    var order_distance = $("#Order_distance").val();
    if (order_distance != '') {
        Order_cost();
    }
});


function Order_cost(){



	var city_id = '<?php echo Yii::app()->user->city;?>';
	var distance = $('#Order_distance').val();
	var booking_time = '<?php echo $model->booking_time; ?>';
	var vipcard = '<?php echo $model->vipcard; ?>';
    var cost_type = '<?php echo $model->cost_type;?>';
	var money = '<?php echo $money;?>';
	var wait_time = $('#OrderExt_wait_time').val();
    var order_start_hour = $("#Order_start_hour").val();
    var order_start_min = $("#Order_start_min").val();
    var order_start_time = $("#Order_start_time").val();

	var pars = 'cost_type=' + cost_type + '&city_id='+ city_id + '&distance=' + distance + '&booking_time=' + booking_time +
        '&wait_time=' + wait_time + '&vipcard=' + vipcard + '&money=' + money +
        '&type=1' + '&order_start_hour=' + order_start_hour + '&order_start_min=' + order_start_min + '&order_start_time=' + order_start_time;
		$.ajax({
			type: 'get',
			url: '<?php echo Yii::app()->createUrl('/order/income');?>',
			data: pars,
			dataType:'json',
			success: function(data){
				str = '';
				str_fee = '';
				if(data['income'] > 0){
					str += "应付代驾费用:<br/>";
					str_fee += data['income']+"元<br/>";
				}
                switch(cost_type){
                    case '1':
                        if ($("#Order_cost_type").attr("checked") == 'checked'){
                            str += "<span class='show_label'>vip可用抵扣金额:</span>";
                            str_fee += "<span class='show_label'>"+data['vip']+"元</span>";
                        }else{
                            str += "<span class='show_label' style='display:none;'>vip可用抵扣金额:</span>";
                            str_fee += "<span class='show_label' style='display:none;'>"+data['vip']+"元</span>";
                        }
                        break;
                    default:
                        if ($("#Order_cost_type").attr("checked") == 'checked'){

                            str += "<span class='show_label'>优惠劵可用抵扣金额:</span>";
                            str_fee += "<span class='show_label'>"+data['bonus']+"元</span>";
                        }else{
                            str += "<span class='show_label' style='display:none;'>优惠劵可用抵扣金额:</span>";
                            str_fee += "<span class='show_label' style='display:none;'>"+data['bonus']+"元</span>";
                        }
                        break;
                }
				$("#controls_income").html("<td><label>"+str+"</label></td> <td>"+str_fee+"</td>");
				
		}});
}

$('body').on('keyup','#OrderExt_wait_time',function(){
	var distance = $('#OrderExt_wait_time').val();
	if(distance != ''){
		$("#Order_distance").keyup();
	}
});

$('body').on('change','#Order_cost_type',function(){
	$(".show_label").toggle();
})

function checkSubmit() {
    $("#ceshi").attr("disabled", true);
}


</script>
<!-- form -->