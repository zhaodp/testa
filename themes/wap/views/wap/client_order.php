<?php
	$this->pageTitle = 'e代驾手机端首页';
	$citys = Dict::items('city');
	$city_id= Yii::app()->request->cookies['edaijia_city_id']->value;
	$lng = Yii::app()->session['lng'];
	$lat = Yii::app()->session['lat'];
	$gps_address = Yii::app()->session['gps_address'];
?>
<style type="text/css">
.list_box{ width:99%; height:80px; border:1px solid #66CCCC; margin-top:3px; }
#more{ width:100%; text-align:center; height:20px;  color:#FF0000; margin:0 auto; cursor:pointer}
.loading{ width:100%; text-align:center; height:20px;  color:#FF0000; margin:0 auto; }
.list_box_item{ width:70%;float:left}
.list_status{ width:25%; height:80px;  float:right;}
h3{ margin:25px;}
.clear{ clear:both};
</style>
<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">预约代驾</a></li>
    <li><a href="#tab2" data-toggle="tab">价格表</a></li>
    <li><a href="#tab3" data-toggle="tab">订单列表</a></li>
  </ul>
  <div class="tab-content">
    <!--FORM START-->
    <div class="tab-pane active" id="tab1">
      <!---->
      <?php 
		$form = $this->beginWidget('CActiveForm', array (
			'id'=>'order-form', 
			'errorMessageCssClass'=>'alert alert-error',
			'enableClientValidation'=>true,
			'clientOptions'=>array( 
					'validateOnSubmit'=>true,  // 这个是设置是否把提交按钮也做成客户端验证。 
			),
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('class'=>"form-horizontal")
		));
		
		?>
      <div class="control-group">
        <label class="control-label" for="inputEmail">当前城市：</label>
        <div class="controls"> <?php echo $citys[$city_id];?> </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputEmail">预约手机号：</label>
        <div class="controls">
          <input type="text" id="contect_mobile" name="contect_mobile" value="<?php echo Yii::app()->request->cookies['edaijia_mobile'];?>">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputPassword">预约地点：</label>
        <div class="controls">
          <input type="text" id="address" name="address" placeholder="请输入预约地点">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputPassword">预约时间：</label>
        <div class="controls">
          <div class="btn-group" id="time">
            <button class="btn">30分钟后</button>
            <button class="btn">1小时后</button>
            <button class="btn">2小时后</button>
          </div>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputPassword">预约人数：</label>
        <div class="controls">
          <div class="btn-group" id="num">
            <button class="btn">1人</button>
            <button class="btn">2人</button>
            <button class="btn">3人</button>
            <button class="btn">4人</button>
          </div>
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <button type="button" class="btn" id="submit_btn">预约代驾</button>
        </div>
      </div>
      <input type="hidden" name="num" id="num_hid" value="0"/>
      <input type="hidden" name="time" id="time_hid" value="0"/>
      <input type="hidden" name="city_id" id="city_id" value="<?php echo Yii::app()->request->cookies['edaijia_city_id']->value;?>"/>
      <?php $this->endWidget();?>
      <!----->
    </div>
    <!--FORM END-->
    <div class="tab-pane" id="tab2">
      <p>
	  价格表<hr>
	  <!--START-->
	  <?php 
	  	if($city_id==1||$city_id==2||$city_id==3||$city_id==4){
			echo '<dl id="time1">
                	<dt><span>时间段</span><span>代驾费</span></dt>
                    <dd><span>07:00 - 22:00</span><span>39元</span></dd>
                    <dd><span>22:00 - 23:00</span><span>59元</span></dd>
                    <dd><span>23:00 - 00:00</span><span>79元</span></dd>
                    <dd><span>00:00 - 07:00</span><span>99元</span></dd>
                </dl><br>';
			echo '
					<b>注：</b><br>
					1.不同时间段的代驾起步费用以约定
					  时间为准，默认最短约定时间为客户
					  呼叫时间延后20分钟。<br>
					2.按照车内里程总表计算公里数，
					  代驾距离超过10公里后，
					  每超过10公里加收20元，
					  不足10公里按10公里计算。<br>
					3.约定时间前到达客户指定位置，从
					  约定时间开始，每满30分钟收费20
					  元等候费，不满30分钟不收费；约定
					  时间之后到达客户指定位置，从司机
					  到达时间后，每满30分钟收费20元等
					  候费，不满30分钟不收费。';
		}else if($city_id==5){
			echo ' <dl id="time3" style="display:none;">
                	 <dd><span>时间段</span><span>代驾费</span></dd>
                	<dd><span>07:00 - 22:00</span><span>39元</span></dd>
                    <dd><span>22:00 - 07:00</span><span>59元</span></dd>
                </dl><br>';
			echo '<b>注：</b><br>
					1.不同时间段的代驾起步费用以约定
					  时间为准，默认最短约定时间为客户
					  呼叫时间延后20分钟。<br>
					2.按照车内里程总表计算公里数，
					  代驾距离超过10公里后，
					  每超过5公里加收20元，
					  不足5公里按5公里计算。<br>
					3.约定时间前到达客户指定位置，从
					  约定时间开始，每满30分钟收费20
					  元等候费，不满30分钟不收费；约定
					  时间之后到达客户指定位置，从司机
					  到达时间后，每满30分钟收费20元
					  等候费，不满30分钟不收费。';
		}else if($city_id==6){
			echo '<dl id="time2" style="display:none;">
                    <dd><span>时间段</span><span>起步价(5公里以内)</span></dd>
                    <dd><span>全天</span><span>39元</span></dd>
                </dl><br>';
			echo '<b>注：</b><br>
					1.不同时间段的代驾起步费用以约定
					  时间为准，默认最短约定时间为客户
					  呼叫时间延后20分钟。<br>
					2.按照车内里程总表计算公里数，
					  代驾距离超过5公里后，
					  每超过5公里加收20元，
					  不足5公里按5公里计算。<br>
					3.约定时间前到达客户指定位置，从
					  约定时间开始，每满30分钟收费20
					  元等候费，不满30分钟不收费；约定
					  时间之后到达客户指定位置，从司机
					  到达时间后，每满30分钟收费20元
					  等候费，不满30分钟不收费。';
		}
	  ?>
	  
	  <!--END-->
	  </p>
    </div>
    <div class="tab-pane" id="tab3">
      <p id="page_list"></p>
    </div>
  </div>
</div>
<!-- 解析当前地址 -->
<script language="javascript">
var lat = "<?php echo $lat?>";
var lng = "<?php echo $lng?>";
var gps_address = "<?php echo $gps_address;?>";
if(lat!=""&&lat!=""&&gps_address!=""){
	$("#address").val(gps_address);
}else{
	//重新获取当前地址
	getLocation();
}

function getLocation(){
	if (navigator.geolocation){
		navigator.geolocation.getCurrentPosition(getPositionSuccess);
	}
}
function getPositionSuccess(position){
	$.ajax({
		url: "/ajax/?",
		dataType: "json",
		data: {method:'gps_location', lng:position.coords.longitude, lat:position.coords.latitude},
		success: function(data){
			var address = data.location.street.component.city+" "+data.location.street.component.district+" "+data.location.street.component.province+" "+data.location.street.component.street+" "+data.location.street.component.street_number;
				$("#address").val(address);
			},
		
	});		
}
</script>
<script language="javascript">
$(function(){

	//判断URL转向
	var url = window.location.href;
	if(url.indexOf("#")>1){
		urlArr = url.split("#");
		tab = urlArr[urlArr.length-1];
		if(tab=="tab3"){
			$(".nav-tabs li").removeClass("active");
			$(".nav-tabs li").eq(2).addClass("active");
			$("#tab1").removeClass("active");
			$("#tab3").addClass("active");
		}
	}
	//END
	var c_time = 0;
	var c_num = 0;
	var c_page=0;
	//设置默认选中
	setBtnGroup(1,c_time);
	setBtnGroup(2,c_num);
	
	//改变btn group的背景颜色
	$("#time .btn").click(function(){
		c_time = $("#time .btn").index($(this));
		setBtnGroup(1,c_time);
		return false;
	});
	$("#num .btn").click(function(){
		c_num = $("#num .btn").index($(this));
		setBtnGroup(2,c_num);
		return false;
	});
	$("#submit_btn").click(function(){
		//检查输入正确性
		if(!checkVal()){
			alert("输入有误，请确认后重新提交");
			return false;
		}else{
			if($("#city_id").val()==''||$("#city_id").val()==0){
				alert("系统错误，请确刷新页面然后重新提交");
				return false;
			}else{
				$("#order-form").submit();
			}
		}
	});
	//ajax获取订单列表
	$.ajax({
			   type: "POST",
			   url: "<?php echo Yii::app()->createUrl('wap/customerOrderPage')?>",
			   data: "page="+c_page,
			   dataType:"html",
			   success: function(data){
				 $("#page_list").html(data);
				 c_page++;
			  }
	});
	$("#more").live('click',function(){
		
		var loading = '<div class="loading">加载中...</div>';
		$("#more").replaceWith(loading);
		////
		$.ajax({
			   type: "POST",
			   url: "<?php echo Yii::app()->createUrl('wap/customerOrderPage')?>",
			   data: "page="+c_page,
			   dataType:"html",
			   success: function(data){
			   		if(data!=0){
				 		$("#page_list").append(data);
				 		c_page++;
					}
				 $(".loading").replaceWith('');
			  }
		});
		///
	});
});
function checkVal(){
	//mobile reg
	var mobileReg = /^1[3|8|5]\d{9}$/;
	if($("#contect_mobile").val()==''||!mobileReg.test($("#contect_mobile").val())||$("#address").val()==''){
		return false;
	}else{
		return true;
	}
}
function setBtnGroup(type,val){
	if(type==1){
		$("#time .btn").css("background","");
		$("#time .btn").eq(val).css("background","#33CCFF");
		$("#time_hid").val(val);
	}else if(type==2){
		$("#num .btn").css("background","");
		$("#num .btn").eq(val).css("background","#33CCFF");
		$("#num_hid").val(val);
	}
}
</script>
