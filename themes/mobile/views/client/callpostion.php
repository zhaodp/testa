<?php
$this->pageTitle = Yii::app()->name . ' - 客户最近24小时呼叫位置及当前司机分布图';

/**
 * @tutorial 为页面注册jquery以及其他js脚本
 */
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile("http://api.map.baidu.com/api?v=2.0&ak=ECfffb5d16a4f1b23c885c0527e91774",CClientScript::POS_HEAD);
$cs->registerScriptFile("http://dev.baidu.com/wiki/static/map/API/examples/script/convertor.js",CClientScript::POS_HEAD);
$url= SP_URL_JS .'map.js';
$cs->registerScriptFile($url,CClientScript::POS_HEAD);

$city_id = isset($_GET['city_id']) ? $_GET['city_id'] : 1;

$params =  array('city_id'=>$city_id,'ac'=>'map');
$sig = Api::createSig($params);
$params['sig'] = $sig;

//echo Yii::app()->user->getCity();

?>

<h3>客户呼叫位置及司机分布</h3>
<br/>
<div align="center" style="margin:0 auto;width:95%;">
	<?php 
		if(Yii::app()->user->type == AdminUserNew::USER_TYPE_ADMIN){
			echo ' 管理员高级查询:昨天 前天 本周 上周 选择日期：';
		}
	?>
	<?php if(Yii::app()->user->type == AdminUserNew::USER_TYPE_ADMIN){?>
	<div style="text-align:left;">选择时间区间:</div>
	<div id="slider-range"></div>
	<?php }?>
	<?php
    if(Yii::app()->user->type == AdminUserNew::USER_TYPE_ADMIN){
		$this->widget('zii.widgets.jui.CJuiSlider', array(
				'id'=>'slider-range',
	            'options' => array(
	                'min' => 0,
	                'max' => 23,
	                'range' => true,
	                'step' => 1,
	                'values' => array(0,23),
	                'stop' => 'js:function (event, ui) {
	                	$("span#amount").text(ui.values[0]+"点至"+ui.values[1] + "点");
	                	map.clearOverlays();
	                	findclient(ui.values[0],ui.values[1]);
	                }',
					'slide' => 'js:function (event, ui) {
	                	$("span#amount").text(ui.values[0]+"点至"+ui.values[1] + "点");
	                }'
	            ),
	            'htmlOptions' => array(
	                'style' => 'height:14px;width:95%;margin:0 auto;'
	            ),
	        ));
	}
	?>
	<div style="height:480px;border:3px solid gray;margin:0 auto;padding-bottom:5px;" id="container"></div>
</div>

<script type="text/javascript">
var city_id = <?php echo $city_id;?>;
var map;// 创建Map实例


$(document).ready(function(){
	map = new BMap.Map("container");
	map.addControl(new BMap.NavigationControl()); 
	findclient(0,23);
	finddriver(city_id);
});

function findclient(start,end){
	var pars = 'start='+start+'&end='+end+'&city_id='+ city_id +'&format=json';
	$.ajax({
		type: 'get',
		url: '<?php echo Yii::app()->createUrl('/client/callpostion');?>',
		data: pars,
		dataType : 'json',
		success: function(json){
			var point = new BMap.Point(116.404, 39.915);
			map.centerAndZoom(point,12);
			if(json){
			for(i=0;i<json.length;i++){
				addclient(json[i]['longitude'],json[i]['latitude']);
			}}
	}});	
}

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

function addclient(longitude,latitude){
	var point = new BMap.Point(longitude, latitude);
	var myIcon = new BMap.Icon("<?php echo SP_URL_IMG . 'us_cursor.gif';?>", new BMap.Size(20, 40), {
		offset: new BMap.Size(20, 22),
		imageOffset: new BMap.Size(-40, -46)
	});
	
	
	translateCallback = function (point){
	    var marker = new BMap.Marker(point, {icon:myIcon});
	    map.addOverlay(marker);
	}    
	
	BMap.Convertor.translate(point,2,translateCallback);    
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

</script>
