<?php
$this->pageTitle = Yii::app()->name . ' - 代驾宝司机分布查询';

/**
 * @tutorial 为页面注册jquery以及其他js脚本
 */
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile("http://api.map.baidu.com/api?v=2.0&ak=ECfffb5d16a4f1b23c885c0527e91774",CClientScript::POS_HEAD);

$city = isset($_POST['city']) ? $_POST['city'] : '北京';
$address = isset($_POST['address']) ? $_POST['address'] : '天安门';
?>
<style>

#map_container{height:500px;width:100%;padding:0;margin:0;font-size:14px;border:3px solid gray;padding-bottom:2px;}

</style>

<h1>司机分布图</h1>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-form',
	'method'=>'post',
	'enableAjaxValidation'=>false,
)); 

/**
 * {"passwd":"张兆泉",
 * "createTime":"2012-06-13 17:56:53.0",
 * "coopId":1514,
 * "carFlag":null,
 * "estateNumber":"37142519781130555x",
 * "contact":null,
 * "city":"济南",
 * "serviceTimes":null,
 * "photoVersion":0,
 * "id":2186,
 * "editTime":null,
 * "province":"山东",
 * "longitude":11694818,
 * "latitude":3664324,
 * "license":"37142519781130555x",
 * "driverName":"张兆泉",
 * "guarantorEstateId":"37142519781130555x",
 * "loginName":"张兆泉",
 * "fullTime":null,
 * "praiseCount":null,
 * "idCard":"37142519781130555x",
 * "imei":"356993020505380",
 * "runNum":"37142519781130555x",
 * "origin":"山东齐河",
 * "guarantorId":"37142519781130555x",
 * "guarantor":null,
 * "commentCount":null,
 * "photo":"data/driver/86/21/00/head.jpg",
 * "driveYears":7,
 * "smallPhoto":"data/driver/86/21/00/_head.jpg",
 * "address":"王舍人镇南洋馨苑小区",
 * "userStatus":0,
 * "workStatus":0,
 * "showStar":3,
 * "mobile":"15554151721"} 
 */

?>
    <label>城市：</label>
    <select class="span2" id='city' name="city">
		<option value="北京">北京</option>
		<option value="太原">太原</option>
		<option value="济南">济南</option>
		<option value="重庆">重庆</option>
		<option value="天津">天津</option>
		<option value="西安">西安</option>
		<option value="包头">包头</option>
		<option value="晋中">晋中</option>
    </select>
    <input type="text" name="address" size="40" id="address" value="<?php echo $address;?>" />
    <input type="submit" name="button" id="button" value="重新查询地址" />
<?php $this->endWidget(); ?>
<div id="map_container" class="span12"></div>

<script type="text/javascript">
var map;// 创建Map实例

function finddriver(longitude,latitude,city){
	var log,lat;
	$.ajax({
		type: 'post',
		url: '<?php echo Yii::app()->createUrl('/dai/postion');?>',
		dataType : 'json',
		data:{lng:longitude,lat:latitude,city:city},
		success: function(json){
			for(i=0;i<json.length;i++){
				adddriver(json[i]);
			}
	}});	
}

function adddriver(driver){
	var point = new BMap.Point(driver['longitude']/100000, driver['latitude']/100000);
	switch(driver['workStatus']){
		case 0:
			var myIcon = new BMap.Icon("<?php echo SP_URL_IMG . 'us_cursor.gif';?>", new BMap.Size(22, 24), {
				offset: new BMap.Size(22, 22),
				imageOffset: new BMap.Size(0, -22)
			});
			break;
		case 1:
			var myIcon = new BMap.Icon("<?php echo SP_URL_IMG . 'us_cursor.gif';?>", new BMap.Size(22, 24), {
				offset: new BMap.Size(22, 22),
				imageOffset: new BMap.Size(-44, -22)
			});
			break;
		case 2:
			var myIcon = new BMap.Icon("<?php echo SP_URL_IMG . 'us_cursor.gif';?>", new BMap.Size(22, 24), {
				offset: new BMap.Size(22, 22),
				imageOffset: new BMap.Size(-22, -22)
			});
			break;
	}
	
    var marker = new BMap.Marker(point, {icon:myIcon});
    
	message = driver['driverName']+' '+driver['mobile']+ '<br/>'+ '签约时间' + driver['createTime'] + '<br/>服务次数' + driver['serviceTimes'];
	
    var infoWindow = new BMap.InfoWindow(message);  // 创建信息窗口对象
    
    marker.addEventListener("click", function(){          
       this.openInfoWindow(infoWindow);
    });	
		
    map.addOverlay(marker);
}


$(document).ready(function(){
	map = new BMap.Map("map_container");
	map.addControl(new BMap.NavigationControl(),options); 

	var all_count = 0;
	var all_longtitude = 0;
	var all_latitude = 0;	

	var options = {
	  	onSearchComplete: function(results){
		console.log(results);
	    // 判断状态是否正确
	    var i = 0;
	    if(local.getStatus() == BMAP_STATUS_SUCCESS){
	        for(i = 0; i < results.getCurrentNumPois(); i++){
	            finddriver(results.getPoi(i).point.lng,results.getPoi(i).point.lat, '<?php echo $city;?>');
	            map.centerAndZoom(results.getPoi(i).point, 12);
	            break;
	        }
	    }
	  }
	};

	var local = new BMap.LocalSearch("<?php echo $city;?>", options);
	local.search("<?php echo $address;?>");
});

</script>
