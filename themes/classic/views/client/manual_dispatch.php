<?php $this->pageTitle = '派单 '.date('H:i', strtotime($data['booking_time'])) .' '. trim($data['name']);?>
<?php $data['city'] = Dict::item('city', $data['city_id']);?>
<?php
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'jquery.md5.js',CClientScript::POS_BEGIN);
?>
<div class="span9">
    <div>
    重新加载周围司机:
    <input class="btn" type="button" id="load_driver_10" onclick="load_driver(10);" value="加载10位司机" />
    <input class="btn" type="button" id="load_driver_30" onclick="load_driver(30);" value="加载30位司机" />
    <input class="btn" type="button" id="load_driver_50" onclick="load_driver(50);" value="加载50位司机" />
    </div>
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
        <!-- <p>临时功能：<a href="/v2/index.php?r=client/map&id=<?php echo $data['id']; ?>" title="老地图派单" target="_blank">老地图派单</a></p> -->
    </div>
    <div id="cart-menu" style="visibility: hidden; display: block;"></div>

    <form class="navbar-form pull-left span12">

        <input type="hidden" name="id" id="id" value="<?php echo $data['id'];?>">
        <input type="hidden" name="phone" id="phone" value="<?php echo trim($data['phone']); ?>"/>
        <input type="hidden" name="r" id="r" value="client/manual_dispatch"/>
        <input type="hidden" id="cart" name="cart" value="<?php echo $data['cart'];?>">

        <label>客户详细地址：</label>
        <input type="text" name="address" id="address" value="<?php echo trim($data['address']); ?>" class="span11"/>
        <label>地图查询地址：</label>
        <input type="text" name="map_address" id="map_address" value="<?php echo trim($data['map_address']); ?>" class="span11" autocomplete="off" />
        <button type="submit" class="btn">重新查询</button>
        <a url="<?php echo Yii::app()->createUrl('CallCenter/error',array('qid'=>$data['id']));  ?>" data-toggle="modal" data-target="" mewidth="400" style="display:inline-block;cursor:pointer;">申报错误</a>
        <?php if ($data['bonus'] != '') {?>
            <label><?php echo $data['bonus'];?></label>
        <?php } else {?>
            <hr class="divider" />
            <label>绑定优惠券：</label>
            <input type="text" name="bonus" id="bonus" value="" class="span8"/>&nbsp;&nbsp;
            <input class="btn" type="button" name="bondbonusbtn" id="bondbonusbtn" onclick="validateBonus();" value="绑定" />
        <?php } ?>
    </form>
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="modal-body">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->


<script type="text/javascript">
var MD5KEY = '9dfa6bba-b49f-11e1-8814-f7bbbf8e8b0c';
var idel_count = 10;

var CU = {
    dateFormat: function (date, format) {
        format = format || 'yyyy-MM-dd hh:mm:ss';
        var o = {
            "M+": date.getMonth() + 1,
            "d+": date.getDate(),
            "h+": date.getHours(),
            "m+": date.getMinutes(),
            "s+": date.getSeconds(),
            "q+": Math.floor((date.getMonth() + 3) / 3),
            "S": date.getMilliseconds()
        };
        if (/(y+)/.test(format)) {
            format = format.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
            }
        }
        return format;
    },
    getSig: function (param) {
        var paramStr = [], paramStrSorted = [];
        for (var n in param) {
            paramStr.push(n);
        }
        paramStr = paramStr.sort();
        $(paramStr).each(function (index) {
            paramStrSorted.push(this + param[this]);
        });
        var text = paramStrSorted.join('') + MD5KEY;
        return $.md5(text);
    }
};

var markers = [];
$(document).ready(function(){

    $("a[data-toggle=modal]").click(function(){
        var target = $(this).attr('data-target');
        var url = $(this).attr('url');
        var mewidth = $(this).attr('mewidth');
        if(mewidth==null) mewidth='850px';
        if(url!=null){
            $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
            $('#myModal').modal('show');
            $('#modal-body').load(url);
        }
        return true;
    });
});

var dataURL = "http://api.edaijia.cn/rest/";

var config = {
    appkey:"<?php echo Yii::app()->params['edj_api_key'];?>",
    ver:3,
    gps_type:"baidu",
    macaddress:'12:34:56:78:9A:BC',
    from:'backend',
    method:"b.nearby",
    udid:"backend__1c91564f4e33d8a3430fac9c781290bb",
}

var stringify = function (data) {
    var value = "";
    for (prop in data) {
        value += prop + "=" + data[prop] + "&";
    }
    return value.substr(0, value.length - 1);
}

var load_driver =  function(i) {
    i = parseInt(i);
    if (i>50) i=50;
    if (i<10) i=10;
    idel_count = i;
    addMarkers();
}

function addMarkers(){
    //清除所有点
    //map.clearOverlays();
    if (markers!=null) {
        for(var i=0;i<markers.length;i++){
            map.removeOverlay(markers[i]);
        }
        markers = [];
    }
    var req = $.extend(true, {}, config);

    req.idel_count = idel_count;
    req.queue_id = queue_id;
    req.longitude = lng;
    req.latitude = lat;
    req.timestamp = CU.dateFormat(new Date());
    req.sig = CU.getSig(req);

    return $.ajax({
        url: dataURL,
        type: 'GET',
        data: stringify(req),
        crossDomain:true,
        dataType: 'jsonp',
        error: function (x, h, r) {
            alert("ajaxError");
        },
        success: function (data) {
            if(data.code == 0){
                var driver_list = data.driverList;
                for(var i=0; i<driver_list.length; i++) {
                    var d = driver_list[i];
                    if (number=="1") {
                        var sms_link = "<a href='"+ d.url +"'>发短信</a>";
                    } else {
                        var sms_link="<a href=\"javascript:add_group('"+ queue_id +"','"+ d.driver_id +"');\">多人预约</a>";
                    }

                    //message = "";

                    addDriver(d.lat, d.lng, d.driver_id, 0, sms_link, d.recommand);
                }

                bds = map.getBounds();
                if (markers!=null)
                {
                    for(i=0;i<markers.length;i++){
                        var result = BMapLib.GeoUtils.isPointInRect(markers[i].getPosition(), bds);
                        if(result == true)
                            map.addOverlay(markers[i]);
                        else
                            map.removeOverlay(markers[i]);
                    }
                }
                return false;
            }
        }
    });

/*
    $.get("index.php",
        {
            r :'client/nearby',
            lng : lng,
            lat: lat,
            id : queue_id
        },
        function(data){
            var driver_list=eval("("+data+")");
            for(var i=0; i<driver_list.length; i++) {
                var d = driver_list[i];
                if (number=="1") {
                    var sms_link = "<a href='"+ d.url +"'>发短信</a>";
                } else {
                    var sms_link="<a href=\"javascript:add_group('"+ queue_id +"','"+ d.driver_id +"');\">多人预约</a>";
                }

                //message = "";

                addDriver(d.lat, d.lng, d.driver_id, 0, sms_link, d.recommand);
            }

            bds = map.getBounds();
            if (markers!=null)
            {
                for(i=0;i<markers.length;i++){
                    var result = BMapLib.GeoUtils.isPointInRect(markers[i].getPosition(), bds);
                    if(result == true)
                        map.addOverlay(markers[i]);
                    else
                        map.removeOverlay(markers[i]);
                }
            }
            return false;
        });
*/


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
                        var msg = "";
                        if (is_agent=="1") {
                            msg="【<a href='#' onclick='window.parent.call_driver("+ data.driverInfo.phone +");'>呼叫司机(天润)</a>】&nbsp;&nbsp;&nbsp;&nbsp;"+ message +"<br/>";
                        }
                        infoWindow.setContent(msg);
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

<?php //echo $data['addPoint']; ?>

var number = "<?php echo $data['number'];?>";
var is_agent = "<?php echo $data['is_agent'];?>";
var queue_id = "<?php echo $data['id'];?>";

var all_count = 0;
var all_longtitude = 0;
var all_latitude = 0;

var lng = null;
var lat = null;
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
            lng = all_longtitude/all_count;
            lat = all_latitude/all_count;
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

var f_titlesloaded = function() {
    console.log('titlesloaded');
    addMarkers();
}

var f_zoomend = function() {
    console.log('zoomend');
    addMarkers();
}

var f_moveend = function() {
    console.log('moveend');
    addMarkers();
}

var f_load = function() {
    console.log('load');
    addMarkers();

}

var map = new BMap.Map("map_canvas");
var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10)};
map.addControl(new BMap.NavigationControl(opts));
map.enableScrollWheelZoom();
map.addControl(new BMap.ScaleControl());                    // 添加默认比例尺控件
map.addControl(new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT}));                    // 左下
//map.addEventListener("tilesloaded", f_titlesloaded);
map.addEventListener("zoomend", f_zoomend);
//map.addEventListener("moveend", f_moveend);
//map.addEventListener("load", f_load);

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
