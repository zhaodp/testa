<?php $this->pageTitle = '司机位置实时分布图';?>

<div class="span9">
	<div id="map_canvas"></div>
	<div class="shoppingcart" id="shoppingcart"></div>
</div>
<div class="span3" style="margin-top:20px;">
    <img src="<?php echo SP_URL_IMG; ?>e_300.png" class="span12">
</div>
<div class="span3">
</div>
<div class="span3">
	<label class="alert alert-info">当前城市：<?php echo $data['city'];?></label> 
<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'driver-map-form', 
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'navbar-form pull-left span12')
));
?>
<?php echo CHtml::dropDownList('city_id', $data['city_id'], Dict::items('city'));?>
        <label>司机工号：</label>
        <input type="text" name="driver_id" id="driver_id" value="<?php echo isset($data['driver_id'])?$data['driver_id']:''; ?>" class="span11" autocompl
ete="off"></input>
	<label>地图查询地址：</label>
	<input type="text" name="address" id="address" value="<?php echo isset($data['address'])?$data['address']:$data['city']; ?>" class="span11" autocomplete="off"></input>
	<button type="submit" class="btn">查询</button>
	<?php if(isset($data['mess'])){echo $data['mess']; }?>
<?php 
$this->endWidget();
?>
        <div class="span3" style="height:auto;position: absolute;bottom: 105px;right: 15px;width:240px;">
            <script src="<?php echo SP_URL_JS; ?>jquery.flipcountdown.js"></script>
            <link rel="stylesheet" type="text/css" href="<?php echo SP_URL_CSS; ?>jquery.flipcountdown.css">
            <div id="retroclockbox1" style="height:auto;position: absolute;"></div>
            <script>
            $(function(){
                $("#retroclockbox1").flipcountdown({
//                    size:"lg"
                });
            });
            </script>
        </div>
</div>


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

	    //map.addOverlay(marker);
	    marker.addEventListener("click", function(){
	    	$.ajax({
	    		url: "index.php",
	    		data: {r:'client/ajax',method:'driver_get', driver_id:driver_id},
				success: function(data){
					infoWindow.setTitle('<span style="font-size:16px;color:#0A8021">' + data.driverInfo.name+ ' ' + driver_id + '</span>');
	    			switch(data.driverInfo.state){
	    				case "0":
	    					infoWindow.setContent($message);
		    			  	break;
	    			  	case "1":
	    			  		infoWindow.setContent(data.driverInfo.name + '工作中');
		    			  	break;
	    			  	case "2":
	    			  		infoWindow.setContent(data.driverInfo.name + '已下班');
	    			  		break;
	    			}
				},
	    		dataType: "json"
	    	});
			this.openInfoWindow(infoWindow);  
	    });
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

    address = $('input#address').prop("defaultValue");
    $('input#address').val(address);
  }
};

var map = new BMap.Map("map_canvas");
var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10)};
map.addControl(new BMap.NavigationControl(opts));
map.enableScrollWheelZoom();
map.addEventListener("tilesloaded", addMarkers);
map.addEventListener("zoomend", addMarkers);
map.addEventListener("moveend", addMarkers);

var ac = new BMap.Autocomplete(
	{"input" : "address",
	 "location" : "<?php echo $data['city']; ?>"
});

ac.addEventListener("onhighlight", function(e) {
	var str = "";
	if(e.fromitem.value){
    	var _value = e.fromitem.value;
	}
    var value = "";
    if (e.fromitem.index > -1) {
        value = _value.district +  _value.street +  _value.business;
    }    
    
    value = "";
    if (e.toitem.index > -1) {
        _value = e.toitem.value;
        value = _value.district +  _value.street +  _value.business;
    }    
});

var myValue;
//鼠标点击下拉列表后的事件
ac.addEventListener("onconfirm", function(e) {
	var _value = e.item.value;
	myValue = _value.district +  _value.street +  _value.business;
});

var local = new BMap.LocalSearch("<?php echo $data['city']; ?>", options);
local.search("<?php echo isset($data['address'])?$data['address']:$data['city']; ?>");

//定时刷新
$().ready(function(){
    if (self == top) {
        setInterval(function(){location.href = '<?php echo Yii::app()->request->url; ?>'}, 300000);
    }
})
</script>
