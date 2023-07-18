<?php
$this->pageTitle = Yii::app()->name . ' - 当前司机分布图';
?>

<h1>当前司机分布图</h1>
<div style="height:480px;border:3px solid gray;margin:0 auto;padding-bottom:2px;" id="map_container" class="span12"></div>

<script type="text/javascript">
var markers = [];

function addMarkers(){
	bds = map.getBounds();
    for(i=0;i<markers.length;i++){
        var result = BMapLib.GeoUtils.isPointInRect(markers[i].getPosition(), bds);
        if(result == true)
            map.addOverlay(markers[i]);
        else 
            map.removeOverlay(markers[i]);
    }
}

function addDriver(latitude, longitude, driver_id, status, $message){
	var marker;
    var point = new BMap.Point(longitude, latitude);
	var myIcon = new BMap.Icon("/v2/sto/classic/i/us_cursor.gif", new BMap.Size(23, 25), {
		offset: new BMap.Size(10, 25),
		imageOffset: new BMap.Size(0-status*23,-21)  
	});

		var marker = new BMap.Marker(point, {icon: myIcon});
    	message = '';
		var opts = {title : '<span style="font-size:16px;color:#0A8021">' + driver_id + '</span>'};
		var infoWindow = new BMap.InfoWindow('', opts);  // 创建信息窗口对象
	    markers.push(marker);
}

function getIcon($status){
	myIcon = new BMap.Icon("/v2/sto/classic/i/us_cursor.gif", new BMap.Size(23, 25), {
		offset: new BMap.Size(10, 25),
		imageOffset: new BMap.Size(0-status*23,-21)  
	});	
	return myIcon;
}

<?php echo $data['addPoint']; ?>

var all_count = 0;
var all_longtitude = 0;
var all_latitude = 0;

var options = {
  onSearchComplete: function(results){
    // 判断状态是否正确
    var i = 0;
    if(local.getStatus() == BMAP_STATUS_SUCCESS){
        for(i = 0; i < results.getCurrentNumPois(); i++){
            all_count++;
            all_longtitude += results.getPoi(i).point.lng;
            all_latitude += results.getPoi(i).point.lat;

            addPointWithPic(results.getPoi(i).point.lat, results.getPoi(i).point.lng,results.getPoi(i).title+":"+results.getPoi(i).address, 4);
        }
    }

    if(all_count>0){
		var point = new BMap.Point(all_longtitude/all_count,all_latitude/all_count);
        map.centerAndZoom(point, 12);
    }else{
        var point = new BMap.Point(116.39633672727,39.922375818182);
        map.centerAndZoom(point, 5);
    }
  }
};

var map = new BMap.Map("map_container");
var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10)};
map.addControl(new BMap.NavigationControl(opts));
map.enableScrollWheelZoom();
map.addEventListener("tilesloaded", addMarkers);
map.addEventListener("zoomend", addMarkers);
map.addEventListener("moveend", addMarkers);

var local = new BMap.LocalSearch("<?php echo $data['city']; ?>", options);
local.search("<?php echo isset($data['address'])?$data['address']:$data['city']; ?>");

</script>