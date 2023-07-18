<?php
$this->pageTitle = Yii::app()->name . ' - 客户最近24小时呼叫位置及当前司机分布图';

/**
 * @tutorial 为页面注册jquery以及其他js脚本
 */
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile("http://api.map.baidu.com/api?v=2.0&ak=ECfffb5d16a4f1b23c885c0527e91774",CClientScript::POS_END);
$cs->registerScriptFile("http://api.map.baidu.com/library/MapWrapper/1.2/src/MapWrapper.min.js",CClientScript::POS_END);

$city_id = isset(Yii::app()->user->city) ? Yii::app()->user->city : 1;
$city_name =  Dict::item('city', $city_id);
$params =  array('city_id'=>$city_id,'ac'=>'map');
?>

<h2>客户最近24小时呼叫及派单位置(绿：app，红：呼叫中心)</h2>
<br/>
<div align="center" style="margin:0 auto;width:95%;">
	<div id="labMyCity"></div>
	<div style="height:480px;border:3px solid gray;margin:0 auto;padding-bottom:5px;" id="map_container" class="span12"></div>
</div>

<script type="text/javascript">
var city_id = <?php echo $city_id;?>;
var city_name = '<?php echo $city_name;?>';
var map;// 创建Map实例

function clientCall(city_id){
	$.ajax({
		url: "index.php",
		data: {r:'client/ajax',method:'customer_position', city_id:city_id},
		success: function(data){
			map.centerAndZoom(city_name);
		    mapWforGPS = new BMapLib.MapWrapper(map, BMapLib.COORD_TYPE_GPS);

			for(i=0;i<data.length;i++){
				gpsMkr = addClient(data[i]['longitude'],data[i]['latitude']);
				mapWforGPS.addOverlay(gpsMkr);
			}
		},
		dataType: "json"
	});	
}

function callCenter(city_id){
	$.ajax({
		url: "index.php",
		data: {r:'client/ajax',method:'customer_positionofcallcenter', city_id:city_id},
		success: function(data){
			map.centerAndZoom(city_name);

			for(i=0;i<data.length;i++){
				addCallCenter(data[i]['location_start']);
			}
		},
		dataType: "json"
	});	
}

function addClient(longitude, latitude){
    point = new BMap.Point(longitude, latitude);
	myIcon = new BMap.Icon("/v2/sto/classic/i/location-icon.png", new BMap.Size(15, 18), {
		offset: new BMap.Size(15, 18),
		imageOffset: new BMap.Size(0- 1*15,0)  
	});

	return marker = new BMap.Marker(point, {icon: myIcon});	
}

function addCallCenter(poi){
    mapWforGPS = new BMapLib.MapWrapper(map, BMapLib.COORD_TYPE_GPS);
	
	var myGeo = new BMap.Geocoder();
	myGeo.getPoint(poi, function(point){
	if (point) {
		myIcon = new BMap.Icon("/v2/sto/classic/i/location-icon.png", new BMap.Size(18, 28), {
			offset: new BMap.Size(18, 28),
			imageOffset: new BMap.Size(-0,-18)  
		});
		console.log(point);
		marker = new BMap.Marker(point, {icon: myIcon});
		mapWforGPS.addOverlay(marker);
  		}
	}, city_name);
		
}

$(document).ready(function(){
	map = new BMap.Map("map_container");
	map.addControl(new BMap.NavigationControl()); 
	clientCall(city_id);
	callCenter(city_id);
});

</script>
