<?php

$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerScriptFile('http://api.map.baidu.com/api?v=2.0&ak=ECfffb5d16a4f1b23c885c0527e91774',CClientScript::POS_HEAD);
$driver_id = isset($driver->user)?$driver->user:'';
if(!empty($driver_id)){
    $driver = DriverStatus::model()->get($driver_id);
    if($driver->position){
?>

<div class="row-fluid">
    <h4>司机位置</h4>
    <div id="divMap" class="row-fluid span12" style="height:300px;border:solid 1px gray;margin-left: 0px;"></div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    var myMap = new BMap.Map("divMap");
    var lat = <?php echo $driver->position['baidu_lat'];?>;
    var lng = <?php echo $driver->position['baidu_lng']?>;
    myMap.centerAndZoom(new BMap.Point(lng,lat), 16);

    //可以转化gps坐标
    var mapWforGPS = new BMapLib.MapWrapper(myMap, BMapLib.COORD_TYPE_GPS); 
    //添加gps坐标mkr
    var longitude = <?php echo $driver->position['longitude'];?>;
    var latitude = <?php echo $driver->position['latitude'];?>;
    var point = new BMap.Point( longitude + ',' + latitude);
    var gpsMkr = new BMap.Marker(point);
	mapWforGPS.addOverlay(gpsMkr);

})
</script>
<?php
    }
}
?>
