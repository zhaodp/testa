<?php 
	$this->pageTitle = '在线预约代驾 - e代驾';
?>

<style type="text/css">
.btn {
	padding:4px 10px;
}

.btn.active, .btn:active {
	background-color:#2E7BCC;
	color:#ffffff;
}
</style>
<?php 
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'order-form', 
	//'focus'=>array ($model, 'order_number'),
	'errorMessageCssClass'=>'alert alert-error',
	'enableClientValidation'=>true,
	'clientOptions'=>array( 
	        'validateOnSubmit'=>true,  // 这个是设置是否把提交按钮也做成客户端验证。 
	),
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>"form-horizontal")
));

?>
	<fieldset>
		<div class="control-group">
            <label for="input01" class="control-label">所在城市：</label>
            <div class="controls">
				<?php
				$citys = Dict::items('city');
				//unset($citys[0]);
				$citys[0] = '不能确定您所在城市，请选择';
				unset($citys[2]);
				echo CHtml::dropDownList('Order[city_id]', $city_id, $citys,array('id'=>'Order_city','class'=>'input-xlarge'));?>
			</div>
		</div>
		<div class="control-group">
            <label for="input01" class="control-label">上车地点：</label>
            <div class="controls">
				<input type="text" id="Order_address" class="input-xlarge" name="Order[address]" placeholder="正在定位..." >
			</div>
		</div>
		<div class="control-group">
            <label for="input01" class="control-label">联系电话：</label>
            <div class="controls">
				<input type="text" id="Order_phone" class="input-xlarge" name="Order[phone]" value="<?php echo $phone;?>" readonly >
			</div>
		</div>
		<div class="control-group">
            <label for="input01" class="control-label">代驾人数：</label>
            <div class="controls">
			    <div id="number" class="btn-group" data-toggle="buttons-radio">
			    <button type="button" class="btn active" number='1'>1位</button>
			    <button type="button" class="btn" number='2'>2位</button>
			    <button type="button" class="btn" number='3'>3位</button>
			    <button type="button" class="btn" number='4'>4位</button>
			    <button type="button" class="btn" number='5'>5位</button>
			    </div>
			</div>
		</div>
		<div class="control-group">
            <label for="input01" class="control-label">出发时间：</label>
            <div class="controls">
			    <div id="booking_time" class="btn-group" data-toggle="buttons-radio">
			    <button type="button" class="btn active" timespan='30'>30分钟后</button>
			    <button type="button" class="btn" timespan='60'>1小时后</button>
			    <button type="button" class="btn" timespan='120'>2小时后</button>
			    </div>
			</div>
		</div>
		<div class="form-actions">
            <button class="btn btn-primary btn-large" type="submit">确认无误，提交预约</button>
		</div>
		<input type="hidden" id="Order_booking_time" name="Order[booking_time]" value="30">
		<input type="hidden" id="Order_number" name="Order[number]" value="1">
		<input type="hidden" id="Order_callid" name="Order[callid]" value="<?php echo $call_id;?>">
	</fieldset>
<?php $this->endWidget();?>
      
<script type="text/javascript">
	function getLocation(){
		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(getPositionSuccess,LocationError,{enableHighAccuracy:true, maximumAge:5000, timeout:5000});
		}else{
			alert("您的浏览器不支持获取地理位置服务");
		}
	}
	
	function getPositionSuccess(position){
		$.ajax({
			url: "/ajax/?",
			data: {method:'gps_location', lng:position.coords.longitude, lat:position.coords.latitude},
			success: function(data){
				var city_id = data.location.street.component.city_id;
				console.log(city_id);
				$("#Order_city option[value="+city_id+"]").attr("selected", true);
				$('#Order_address').val(data.location.street.component.street);

				$('.form-actions button').each(function(e){
					$(this).removeAttr("disabled");
				});
			},
			dataType: "json"
		});		
	}
	
	function LocationError(error){
		switch(error.code){
			case error.TIMEOUT :
				alert("定位失败，我们暂时无法确定您当前的位置");
				$('#Order_address').attr('placeholder','定位失败，请输入您的位置');
				break;
			case error.PERMISSION_DENIED :
				alert("您拒绝了使用位置共享服务，查询已取消");
				break;
			case error.POSITION_UNAVAILABLE : 
				alert("非常抱歉，定位失败，我们暂时无法确定您当前的位置");
				break;
		}
		$('.form-actions button').each(function(e){
			$(this).removeAttr("disabled");
		});
	}
	
	function init(){
		getLocation();
	}
	
	$(document).ready(function(){
		$('#relocation').click(function(e){
			getLocation();
		});

		$('#booking_time button').each(function(e){
			$(this).click(function(e){
				$('#Order_booking_time').val($(this).attr('timespan'));
			});
		});
		
		$('#number button').each(function(e){
			$(this).click(function(e){
				$('#Order_number').val($(this).attr('number'));
			});
		});

		$('.form-actions button').each(function(e){
			$(this).attr("disabled","disabled");
		});

		$('#order-form').submit(function(e){
			flag = true;
			if($('#Order_city').val()=='0'){
				flag = false;
				alert('不能确定您所在城市，请选择城市');
				return false;
			}

			if(!$('#Order_address').val()){
				flag = false;
				alert('请输入您的位置，我们将为您派最近的司机');
				$('#Order_address').focus();
				return false;
			}

			if(flag == false){
				e.preventDefault();
			}
		});
		
		$('#Order_address').focus();
		window.onload = init;
	});

	
</script>