<?php
$this->pageTitle = Yii::app()->name . ' - 当前司机分布图';

/**
 * @tutorial 为页面注册jquery以及其他js脚本
 */
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile("http://api.map.baidu.com/api?v=2.0&ak=ECfffb5d16a4f1b23c885c0527e91774",CClientScript::POS_END);
$cs->registerScriptFile("http://dev.baidu.com/wiki/static/map/API/examples/script/convertor.js",CClientScript::POS_END);
//$url= SP_URL_JS .'map.js';
//$cs->registerScriptFile($url,CClientScript::POS_HEAD);

$city_id = isset($_GET['city_id']) ? $_GET['city_id'] : 1;

$params =  array('city_id'=>$city_id,'ac'=>'map');
$sig = Api::createSig($params);
$params['sig'] = $sig;

//echo Yii::app()->user->getCity();

?>

<h1>当前司机分布图</h1>
<div style="height:480px;border:3px solid gray;margin:0 auto;padding-bottom:2px;" id="map_container" class="span12"></div>

<script type="text/javascript">
var city_id = <?php echo $city_id;?>;
var map;// 创建Map实例

function finddriver(city_id){
	var log,lat;
	$.ajax({
		type: 'get',
		url: '<?php echo Yii::app()->createUrl('/api/driver',$params);?>',
		dataType : 'json',
		success: function(json){
			var point = new BMap.Point(116.404, 39.915);
			map.centerAndZoom(point,12);
			
			for(i=0;i<json.length;i++){
				adddriver(json[i]['longitude'],json[i]['latitude']);
			}
	}});	
}

function adddriver(longitude,latitude){
	var point = new BMap.Point(longitude, latitude);
	var myIcon = new BMap.Icon("<?php echo SP_URL_IMG . 'us_cursor.gif';?>", new BMap.Size(22, 24), {
		offset: new BMap.Size(22, 22),
		imageOffset: new BMap.Size(0, -22)
	});
	
	
	translateCallback = function (point){
	    var marker = new BMap.Marker(point, {icon:myIcon});
	    map.addOverlay(marker);
	}    
	
	BMap.Convertor.translate(point,2,translateCallback);    
}

$(document).ready(function(){
	map = new BMap.Map("map_container");
	map.addControl(new BMap.NavigationControl()); 
	finddriver(city_id);
});

</script>
