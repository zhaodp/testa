<?php $this->pageTitle = '派单 '.date('H:i', strtotime($data['booking_time'])) .' '. trim($data['name']);?>
<?php $data['city'] = Dict::item('city', $data['city_id']);?>

<div class="span9">
	<div id="map_canvas"></div>
	<div class="shoppingcart" id="shoppingcart"></div>
</div>				
<div class="span3">
    <div class="alert alert-info span11">
    	<p><?php echo trim($data['city']);?>  <?php echo trim($data['name']);?></p>
    	<p>电话：<a href="#" onclick="javascript:softphoneBar.dialout(<?php echo trim($data['phone']);?>);return false;">
    		<?php
				echo AdminSpecialAuth::model()->haveSpecialAuth('user_phone') ? $data['phone'] : trim(substr_replace($data['phone'], "*****", 3, 5));
    		?>
    	</a></p>
    	<p>地址：<?php echo trim($data['address']);?></p>
		<p>预约时间：<?php echo date('m-d H:i', strtotime($data['booking_time']));?></p>
		<p>司机人数：<?php echo isset($data['number'])?$data['number']:1;?>人</p>
		<?php if(isset($data['comments'])){?>
		<p class='alert'><?php echo isset($data['comments'])?$data['comments']:'';?></p>
		<?php }?>
		<?php if ($data['bonus'] != '') {?>
		<p>绑定优惠券：<?php echo $data['bonus'];?></p>
		 <?php }?>
	</div>
	<div id="cart-menu" style="visibility: hidden; display: block;"></div>	    
    <form class="navbar-form pull-left span12">
				
		<input type="hidden" name="id" id="id" value="<?php echo $data['id'];?>">
		<input type="hidden" name="phone" id="phone" value="<?php echo trim($data['phone']); ?>"/>
		<input type="hidden" name="r" id="r" value="client/map"/>
		<input type="hidden" id="cart" name="cart" value="<?php echo $data['cart'];?>">

    	<label>客户详细地址：</label>
		<input type="text" name="address" id="address" value="<?php echo trim($data['address']); ?>" class="span11"/>
    	<label>地图查询地址：</label>
		<input type="text" name="map_address" id="map_address" value="<?php echo trim($data['map_address']); ?>" class="span11" autocomplete="off"></input>
		<button type="submit" class="btn">重新查询</button>
		<?php if ($data['bonus'] != '') {?>
		<label><?php echo $data['bonus'];?></label>
		 <?php } else {?>
		<hr class="divider"></hr>
		<label>绑定优惠券：</label>
		<input type="text" name="bonus" id="bonus" value="" class="span11"/>
		<input type="button" name="bondbonusbtn" id="bondbonusbtn" onclick="validateBonus();" value="绑定优惠券" />
		<?php } ?>
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

function addDriver(latitude, longitude, driver_id, status, message, recommand){
	var marker;
    var point = new BMap.Point(longitude, latitude);
	var myIcon = new BMap.Icon("/v2/sto/classic/i/us_cursor.gif", new BMap.Size(23, 25), {
		offset: new BMap.Size(10, 25),
		imageOffset: new BMap.Size(0-status*23,-21)  
	});

	if(status ==0 && recommand ==1){
		var myIcon = new BMap.Icon("/v2/sto/classic/i/mark_driver.gif", new BMap.Size(24, 29), {
			offset: new BMap.Size(0, 0),
			imageOffset: new BMap.Size(-114,0)
		});
	}

	marker = new BMap.Marker(point, {icon: myIcon});
    
	var opts = {title : '<span style="font-size:16px;color:#0A8021">更新司机状态....</span>'};
	var infoWindow = new BMap.InfoWindow('', opts);  // 创建信息窗口对象

	    //map.addOverlay(marker);
	    marker.addEventListener("click", function(){
	    	$.ajax({
	    		url: "index.php",
	    		data: {r:'client/ajax',method:'driver_get', driver_id:driver_id},
				success: function(data){
					infoWindow.setTitle('<span style="font-size:16px;color:#0A8021">' + data.driverInfo.driverID + ' ' + data.driverInfo.phone + '</span>');
	    			switch(data.driverInfo.state){
	    				case "0":
	    					infoWindow.setContent(message);
		    			  	break;
	    			  	case "1":
	    			  		infoWindow.setContent('此司机工作中');
		    			  	break;
	    			  	case "2":
	    			  		infoWindow.setContent('此司机已下班');
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

function validateBonus(){
	var phone = $("#phone").val();
	var bonus = $("#bonus").val();

	if (phone == ''){
		alert ('电话信息不正确，请重新派单。');
		return false;
	}
	
	if (bonus == ''){
		alert ('请填写优惠码。');
		return false;
	}
	
	$.get("index.php", {r :'client/validatebonus', phone : phone, bonus : bonus},
	   function(data){   
		var tst=eval("("+data+")");
		alert(tst.message);
	   });
}

function sendprice(phone){
	if (phone == ''){
		alert ('电话信息不正确，请重新派单。');
		return false;
	}

	$('#sendpricebtn').attr("disabled",true);

	$.get("index.php", {r :'client/sendprice', phone : phone, city : $('#city').val()},
   function(data){   
     if (data == phone){
         alert ('价格表成功发送到手机' + phone);
     } else {
         alert ('价格表发送不成功。');
         $('#sendpricebtn').attr("disabled",false);
     }
   });
}

function add_group(queue_id, message){
	$.get("index.php", {r :'client/insertcart', queue_id: queue_id, user : message, cart : $("#cart").val()},
   function(data){   
     $('#cart-menu').html( data ); 
   });
   cartShow();
}

function remove_group(queue_id, message){
	$.get("index.php", {r :'client/removecart',queue_id: queue_id, user : message, cart : $("#cart").val()},
   function(data){   
     $('#cart-menu').html( data ); 
   });
   cartShow();
}

/**
 * 派单
 * @author zhanglimin 2013-05-08
 */
function push_order(queue_id , address , lng , lat){
    if(queue_id == "" , address=="" || lng == "" || lat == ""){
        alert("数据获取有误，请重试!");
        return false;
    }
    $.get("index.php", {r :'client/setOrderQueueStatus', queue_id : queue_id , address : address, lng : lng ,lat:lat},
        function(data){
           alert(data);
           return false;
        });
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
            var href = "<br><div style='text-align:center'>【<a href=\"javascript:push_order(<?php echo $data['id'];?>,'<?php echo urlencode(trim($data['address'])); ?>',"+results.getPoi(i).point.lng+","+results.getPoi(i).point.lat+");$.get('?r=client%2Frelocationlog&orderid=<?php echo $data['id'];?>&orgaddr='+$('#address').val()+'&newaddr='+$('#map_address').val()+'&omg=omgAb39fe');\">确认派单</a>】</div>";
            addPointWithPic(results.getPoi(i).point.lat, results.getPoi(i).point.lng,"<b>"+results.getPoi(i).title+"</b><br/>"+results.getPoi(i).address+href, 4);
        }
    }

    if(all_count>0){
		var point = new BMap.Point(all_longtitude/all_count,all_latitude/all_count);
        map.centerAndZoom(point, 15);
    }else{
    	markers = null;
        var point = new BMap.Point(116.39633672727,39.922375818182);
        map.centerAndZoom(point, 5);
    }

    map_address = $('input#map_address').prop("defaultValue");
    $('input#map_address').val(map_address);
  }
};

var map = new BMap.Map("map_canvas");
var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10)};
map.addControl(new BMap.NavigationControl(opts));
map.enableScrollWheelZoom();
map.addEventListener("tilesloaded", addMarkers);
map.addEventListener("zoomend", addMarkers);
map.addEventListener("moveend", addMarkers);
map.addControl(new BMap.ScaleControl());                    // 添加默认比例尺控件
map.addControl(new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT}));                    // 左下

var ac = new BMap.Autocomplete(
	{"input" : "map_address",
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
local.search("<?php echo $data['map_address']; ?>");


//购物车开始
function cartShow()
{
	var cartCont=document.getElementById("cart-menu");
	var cartZ=document.getElementById("shoppingcart");
	var intTime;
	var z=parseInt(cartZ.offsetLeft);				
	var t=cartZ.offsetTop;
	var zAdd=Math.ceil((document.body.clientWidth-980)/2);
	var zMore=z+313-980;
	
	//cartCont.style.left=z+zAdd-zMore+"px";
	//cartCont.style.top=96+"px";
	cartCont.className="cart_open alert alert-warning span11";
	 
	intTime=setTimeout(function(){
		cartCont.style.visibility="visible";
		cartCont.style.display="block";
	});
}

</script>