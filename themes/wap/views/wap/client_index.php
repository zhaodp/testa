<?php
	$this->pageTitle = 'e代驾手机端首页';
	
	$cookie_mobile = isset(Yii::app()->request->cookies['edaijia_mobile']->value) ? Yii::app()->request->cookies['edaijia_mobile']->value : '';
	$cookie_city_id = isset(Yii::app()->request->cookies['edaijia_city_id']->value) ?  Yii::app()->request->cookies['edaijia_city_id']->value : '';
	if($cookie_mobile!=''&&$cookie_city_id!=''&&preg_match('/^1[3|8|5]\d{9}$/', $cookie_mobile)){
		$this->redirect(Yii::app()->createUrl('wap/clientOrder'));
	}
	/**
	 * 需要在最后增加一个遮罩，假装loading....
	 * 用户进入APP后首先调用html5API的定位功能，设置city_id,如果用户手机不支持html5，则改为手动设置所处城市
	 * 用cookie记录用户第一次输入的手机号码
	 */
	$citys = Dict::items('city');
	$citys[0] = '不能确定您所在城市，请手动选择';	
?>
<script>
$(function(){
	popup();
	getLocation();
	//获取验证码
	$("#validata_btn").click(function(){
		var mobileReg = /^1[3|8|5]\d{9}$/;
		if($("#city_id").val()<=0||!mobileReg.test($("#mobile").val())){
			alert("您输入的信息有误,请重试");
			return false;
		}else{
			//START
			$.ajax({
				   type: "POST",
				   url: "<?php echo Yii::app()->createUrl('wap/sendClientSms')?>",
				   data: "city_id="+$("#city_id").val()+"&mobile="+$("#mobile").val(),
				   success: function(data){
				     if(data==-1){
						alert("您输入的信息有误，请确认有重试");
						return false;
					 }else if(data==0){
						alert("验证码已成功发送，请注意查收信息，该验证码将在十分钟后失效");
						return false;
					 }else if(data==1){
						alert(" 系统延迟，请稍后再试。");
						return false;
					 }else if(data==2){
						 alert("您已经连续三次请求获取E代驾验证码。如有疑问，请拨打4006-91-3939联系客服为您服务。");
						 return false;
					 }else if(data==3){
						 alert("十分钟之内只能请求一次预登录密码。");
						 return false;
					 }else{
						 alert("系统错误，请稍后重试");
						 return false;
					 }
				   }
				});
			//END
		}
	});
	//调用发送短信接口
	
});
function getLocation(){

	if (navigator.geolocation){
		navigator.geolocation.getCurrentPosition(getPositionSuccess,LocationError,{enableHighAccuracy:true, maximumAge:5000, timeout:5000});
	}else{
		alert("您的浏览器不支持获取地理位置服务,请手动选择所处城市");
		winclose();
	}
}
function getPositionSuccess(position){
	
	if(position.coords.longitude&&position.coords.latitude){
		$("#lng").val(position.coords.longitude);
		$("#lat").val(position.coords.latitude);
	}
	$.ajax({
		url: "/ajax/?",
		dataType: "json",
		data: {method:'gps_location', lng:position.coords.longitude, lat:position.coords.latitude},
		success: function(data){
			var address = data.location.street.component.city+" "+data.location.street.component.district+" "+data.location.street.component.province+" "+data.location.street.component.street+" "+data.location.street.component.street_number;
			var city_id=data.location.street.component.city_id;
			if(checkCity(city_id)==false){
				alert("您所在的城市暂未开通E代驾!"); winclose();return false;
			}else{
				$("#city_id").val(city_id);
				$("#gps_address").val(address);
				winclose();return false;
			}
		},
	});		
}
function LocationError(error){
	switch(error.code){
		case error.TIMEOUT :
			alert("定位失败，我们暂时无法确定您当前的位置,请手动选择所处城市");
			winclose()
			break;
		case error.PERMISSION_DENIED :
			alert("您拒绝了使用位置共享服务，查询已取消,请手动选择所处城市");
			winclose()
			break;
		case error.POSITION_UNAVAILABLE : 
			alert("非常抱歉，定位失败，我们暂时无法确定您当前的位置,请手动选择所处城市");
			winclose()
			break;
	}
}
function popup(){	
		var maskHeight = $(document).height(); 
		var maskWidth = $(window).width();
		var dialogTop =  $(window).scrollTop() + ($('#dialog-box').height()/3 );
		var dialogLeft = (maskWidth/2) - ($('#dialog-box').width()/2);
		$('#dialog-overlay').css({height:maskHeight, width:maskWidth}).show();
		$('#dialog-box').css({top:dialogTop, left:dialogLeft}).show(100);
}
	
	
function winclose(){
		$('#dialog-overlay,#dialog-box').hide();
}
function checkCity(city_id){
	var cityList = Array(1,2,3,4,5,6,7);
	for(i=0;i<cityList.length;i++){
		if(city_id==cityList[i]){
			return true;
		}
	}
	return false;
}

$(function(){
	$("#main_1_next_btn").click(function(){
		if($("#city_id").val()<=0){
				alert("请手动选择您所在城市");
			}else{
				$(".main_1").slideUp('slow');
				$(".main_2").slideDown('slow');
			}
	});
	$("#login_btn").click(function(){
		checkSubmit();
	});
});

function checkSubmit(){
	var mobileReg = /^1[3|8|5]\d{9}$/;
	if($("#validata").val()==''||$("#mobile").val()==''||!mobileReg.test($("#mobile").val())){
		alert('输入有误，请重新输入!');
		return false;
	}else{
		//ajax 验证手机号和验证码
		$.ajax({
			 type: "POST",
			url: "<?php echo Yii::app()->createUrl('wap/checkValiDateCode')?>",
			dataType: "html",
			 data: "validata="+$("#validateCode").val()+"&mobile="+$("#mobile").val(),
			success: function(data){
				if(data==-1){
					alert("您的输入有误，请重新输入!"); 
					return false;
				}else if(data==1){
					alert("您输入的验证码有误，或已过期，请重新输入!"); 
					return false;
				}else if(data==2){
					$("#customer-login-form").submit();
				}
			},
		});		
		//End Ajax
		
	}
	
}

</script>

<style type="text/css">
#dialog-overlay{width:100%;height:100%; background:#000000;position:absolute;top:0;left:0;z-index:4000;filter:alpha(opacity=60);opacity:0.7;display:none;}
#dialog-box{display:none;position:absolute;z-index:5000}
h5{ margin-top:50px;}
.main_1{ width:100%; height:500px;; text-align:center; display:block; border:1px solid #FF0000}
.main_2{ width:100%; height:500px; text-align:center; display:none;border:1px solid #FF0000}
</style>
<div id="dialog-overlay">
	<div id="dialog-box">
		<h5 style="color:#FFFFFF"><img src="<?php echo SP_URL_IMG;?>/loading.gif" />正在定位，请稍后......</h5>
	</div>
</div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-login-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>
<div class="main_1">
	<?php echo CHtml::dropDownList('city_id', 0, $citys);?>
	<br>
	<input type="button" value="下一步" id="main_1_next_btn"/>
</div>

<div class="main_2">
	 手机号码：<input type="text" name='mobile' id='mobile' value="<?php echo Yii::app()->request->cookies['edaijia_mobile'];?>" /><br>
	 验证码：<input type="text" name = 'validata' id='validateCode' /><br>
	 <input type="button" value="获取验证码" id='validata_btn'/><br>
	 <input type="button" value="登录" id='login_btn'/>
	 <input type='hidden' value='' name='lng' id='lng'>
	 <input type='hidden' value='' name='lat' id='lat'>
	 <input type='hidden' value='' name='gps_address' id='gps_address'>
</div>
<?php $this->endWidget(); ?>