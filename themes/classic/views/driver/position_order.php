<?php $this->pageTitle = '司机历史位置地图'?>
<div class="span9">
	<div id="map_canvas"></div>
	<div class="shoppingcart" id="shoppingcart"></div>
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
<?php 
/*
 * @author libaiyang 2013-05-06
 * 修改为自动定位到城市，去掉选择城市功能
 */
?>
<input type="hidden" name="address" id="address" value="<?php echo $data['city'];?>" ></input>
<label>订单号：</label>
<input size="50" maxlength="50" name="Orderid" id="Order_id" type="text" value="" />

<label>司机工号：</label>
<?php echo $form->textField($driver,'user',array('size'=>50,'maxlength'=>50)); ?>
	<label>开始时间：</label>
	<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'startDate', 
				'model'=>$driver,  //Model object
				'value'=>$data['startDate'], 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
			));
			?>
	<label>结束时间：</label>
			<?php 
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'endDate', 
				'model'=>$driver,  //Model object
				'value'=>$data['endDate'], 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
			));
		?>
	<button type="submit" class="btn">查询</button>
<?php 
$this->endWidget();
?>
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

<?php echo isset($data['addPoint']) ? $data['addPoint'] : ''; ?>


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

var polyline = new BMap.Polyline([ 
<?php echo isset($data['linePoint']) ? $data['linePoint'] : '';?> ], {strokeColor:"blue", 
strokeWeight:6, strokeOpacity:0.5} );   
map.addOverlay(polyline);


var local = new BMap.LocalSearch("<?php echo $data['city']; ?>", options);
local.search("<?php echo $data['city']; ?>");
</script>