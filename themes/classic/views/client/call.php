<?php $this->pageTitle = '电话接单 ';?>
<div class="span7">
	<div id="map_canvas" style="height:80%;"></div>
	<div>历史订单</div>
</div>				
<div class="span5">
    <h3 class="span11">电话号码：<?php echo trim($data['callerid']); ?>来电</h3>
    <div class="alert alert-info span11" >
	    	<p>已经订单 但还没有派单</p>
	    	<p>可修改和删除</p>
    </div>
    <div class="alert alert-success span11" >
	    	已经派出单的信息
    </div>
	<div id="cart-menu" style="visibility: hidden; display: block;"></div>	    
    <form class="form-horizontal span11">
     <input type="hidden" name="actiontype" id="actiontype" value="<?php echo  trim($data['actiontype']);?>"/>
     <input type="hidden" name="callerid" id="callerid" value="<?php echo trim($data['callerid']); ?>"/>
     <input type="hidden" name="calleeid" id="calleeid" value="<?php echo trim($data['calleeid']);?>"/>
     <input type="hidden" name="calltype" id="calltype" value="<?php echo  trim($data['calltype']);?>"/>
	  <fieldset>
	    <legend>客户资料</legend>
	      <div class="control-group">
			      <label class="control-label span3" >性别：</label>
			      <div class="controls span9">
			        	<input type="radio" name="sex" value="0"  checked="checked" />先生
						<input type="radio" name="sex" value="1" />女士
			      </div>
		    </div>
		    <div class="control-group">
		      	  <label class="control-label span3">客户姓名：</label>
			      <div class="controls span9">
			        	<input type="text" name="name"  id="name"  value="" class="span10"/>
			      </div>
		    </div>
		    <div class="control-group">
			      <label class="control-label span3" >搜索地址：</label>
			      <div class="controls span9">
			        	<input type="text" name="map_address" id="map_address" value="" class="span10" autocomplete="off"></input>
			      </div>
		    </div>
		    <div class="control-group">
			      <label class="control-label span3" >详细地址：</label>
			      <div class="controls span9">
			      	<textarea rows="2" cols="" class="span10" name="address" id="address" ></textarea>
			      </div>
		    </div>
		    <div class="control-group">
			      <label class="control-label span3" >司机人数：</label>
			      <div class="controls span9">
			        	<input type="text" name=""  id=""  value="" class="span10"/>
			      </div>
		    </div>
		     <div class="control-group">
			      <label class="control-label span3" >预约时间：</label>
			      <div class="controls span9">
			      		<input type="text" name=""  id=""  value="<?php echo date('Y-m-d H:i',time()); ?>" class="span10"/>
			      </div>
		    </div>
		    <div class="control-group">
			      <label class="control-label span3" >&nbsp;</label>
			      <div class="controls span9">
						<button type="submit" class="btn">提交订单</button>
			      </div>
		    </div>
	  </fieldset>
	</form>
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
    markers.push(marker);
}
<?php echo $data['addPoint']; ?>

var map = new BMap.Map("map_canvas");
var point = new BMap.Point(116.39633672727,39.922375818182);
map.centerAndZoom(point, 11);
var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10)};
map.addControl(new BMap.NavigationControl(opts));
map.enableScrollWheelZoom();
map.addEventListener("tilesloaded", addMarkers);
map.addEventListener("zoomend", addMarkers);
map.addEventListener("moveend", addMarkers);

var ac = new BMap.Autocomplete(
		{"input" : "map_address",
		 "location" : "北京"
});
var options = {
		  onSearchComplete: function(results){
		  
			var all_count = 0;
			var all_longtitude = 0;
			var all_latitude = 0;
		    // 判断状态是否正确
		    if (local.getStatus() == BMAP_STATUS_SUCCESS){
		        for (var i = 0; i < results.getCurrentNumPois(); i ++){
				   	all_count++;
					all_longtitude += results.getPoi(i).point.lng;
					all_latitude += results.getPoi(i).point.lat;
					var marker_y = addMarker_addr(results.getPoi(i).point,i);
					addInfoWindow_addr(marker_y,results.getPoi(i),i);
		        }
				if(all_count>0){
					var point = new BMap.Point(all_longtitude/all_count,all_latitude/all_count);
		        	map.centerAndZoom(point, 15);
				}else{
					var point = new BMap.Point(116.39633672727,39.922375818182);
					map.centerAndZoom(point, 5);
				}
		    }
		  }
		};
		
//添加标注
var marker_addrs=[];
function addMarker_addr(point, index){
  var myIcon = new BMap.Icon("/v2/sto/classic/i/us_cursor.gif", new BMap.Size(23, 25), {
    offset: new BMap.Size(10, 25),
    imageOffset: new BMap.Size(-95,4)
  });
  var marker = new BMap.Marker(point, {icon: myIcon});
  map.addOverlay(marker);
  marker_addrs.push(marker);
  return marker;
}

//添加信息窗口
function addInfoWindow_addr(marker,poi,index){
    var maxLen = 10;
    // infowindow的显示信息
    var infoWindowHtml = [];
    infoWindowHtml.push('<table cellspacing="0" style="table-layout:fixed;width:100%;font:12px arial,simsun,sans-serif"><tbody>');
    infoWindowHtml.push('<tr>');
    infoWindowHtml.push('<td>' + poi.title + ':'+ poi.address+'</td>');
    infoWindowHtml.push('</tr>');
    infoWindowHtml.push('</tbody></table>');
    var infoWindow = new BMap.InfoWindow(infoWindowHtml.join(""),{width:200}); 
    var openInfoWinFun = function(){
        marker.openInfoWindow(infoWindow);
        for(var cnt = 0; cnt < maxLen; cnt++){
            if(!document.getElementById("list" + cnt)){continue;}
            if(cnt == index){
                document.getElementById("list" + cnt).style.backgroundColor = "#f0f0f0";
            }else{
                document.getElementById("list" + cnt).style.backgroundColor = "#fff";
            }
        }
    }
    marker.addEventListener("click", openInfoWinFun);
    return openInfoWinFun;
}

ac.addEventListener("onhighlight", function(e) {
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
var local = new BMap.LocalSearch(map, options);
var myValue;
//鼠标点击下拉列表后的事件
ac.addEventListener("onconfirm", function(e) {
	var _value = e.item.value;
	myValue = _value.district +  _value.street +  _value.business;
	removeMarkers();
	local.search(myValue);
 	$("#address").val(myValue);
});
function removeMarkers(){
	 for(i=0;i<marker_addrs.length;i++){
	            map.removeOverlay(marker_addrs[i]);
	    }
	 marker_addrs =[];
}
 $("#map_address").keyup(function(e){
	 var myEvent = e.keyCode;
	 if(myEvent==13)
	 {
	 	removeMarkers();
	 	local.search($(this).val());
	 	$("#address").val($(this).val());
	 }
})

<?php if(trim($data['map_address']) !=""){ ?>
	local.search("<?php echo trim($data['map_address']);?>");
	<?php }?>
</script>