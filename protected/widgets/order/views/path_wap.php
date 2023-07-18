<?php
$cs = Yii::app()->clientScript;
$cs->coreScriptPosition = CClientScript::POS_HEAD;
$cs->scriptMap = array();
$cs->registerCoreScript('jquery');
//$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/bootstrap.min.js',CClientScript::POS_HEAD);
//$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/bootstrap-dropdown.js',CClientScript::POS_HEAD);
//$cs->registerScriptFile('http://api.map.baidu.com/api?v=1.3',CClientScript::POS_HEAD);
$cs->registerScriptFile('http://api.map.baidu.com/api?type=quick&v=1.0&ak=ECfffb5d16a4f1b23c885c0527e91774', CClientScript::POS_HEAD);  //更换手机api
//$cs->registerScriptFile('http://api.map.baidu.com/library/GeoUtils/1.2/src/GeoUtils_min.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(SP_URL_JS . 'map.js', CClientScript::POS_HEAD);
//$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.css');
//$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.css');
//$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
?>

<div <?php echo CHtml::renderAttributes($htmlOptions); ?>>
    <input type="hidden" name="address" id="address" value="<?php echo $data['city']; ?>" ></input>
    <div id="map_canvas" style="width: 100%;height: 100%;"></div>
</div>	

<script type="text/javascript">
    var markers = [];

    function addMarkers() {
        bds = map.getBounds();
        for (i = 0; i < markers.length; i++) {
            var result = 1;
            if (result == true)
                map.addOverlay(markers[i]);
            else
                map.removeOverlay(markers[i]);
        }
    }

    function addDriver(latitude, longitude, driver_id, status, $message) {
        var marker;
        var point = new BMap.Point(longitude, latitude);
        var myIcon = new BMap.Icon("<?php echo SP_URL_IMG; ?>us_cursor.gif", new BMap.Size(23, 25), {
            offset: new BMap.Size(10, 25),
            imageOffset: new BMap.Size(0 - status * 23, -21)
        });

        var marker = new BMap.Marker(point, {icon: myIcon});
        message = '';
        var opts = {title: '<span style="font-size:16px;color:#0A8021">' + driver_id + '</span>'};
        var infoWindow = new BMap.InfoWindow('', opts);  // 创建信息窗口对象

        //map.addOverlay(marker);
        marker.addEventListener("click", function() {

            this.openInfoWindow(infoWindow);
        });
        markers.push(marker);
    }

    function getIcon($status) {
        myIcon = new BMap.Icon("<?php echo SP_URL_IMG; ?>us_cursor.gif", new BMap.Size(23, 25), {
            offset: new BMap.Size(10, 25),
            imageOffset: new BMap.Size(0 - status * 23, -21)
        });
        return myIcon;
    }

<?php echo isset($data['addPoint']) ? $data['addPoint'] : ''; ?>


    var all_count = 0;
    var all_longtitude = 0;
    var all_latitude = 0;

    var options = {
        onSearchComplete: function(results) {
            // 判断状态是否正确
            var i = 0;
            if (local.getStatus() == BMAP_STATUS_SUCCESS) {
                for (i = 0; i < results.getCurrentNumPois(); i++) {
                    all_count++;
                    all_longtitude += results.getPoi(i).point.lng;
                    all_latitude += results.getPoi(i).point.lat;

                    addPointWithPic(results.getPoi(i).point.lat, results.getPoi(i).point.lng, results.getPoi(i).title + ":" + results.getPoi(i).address, 4);
                }
            }

//            if (all_count > 0) {
//                var point = new BMap.Point(<?php // echo isset($data['centerLng']) ? $data['centerLng'] : 'all_longtitude/all_count'; ?>, <?php // echo isset($data['centerLat']) ? $data['centerLat'] : 'all_latitude/all_count'; ?>);
//                map.centerAndZoom(point, 12);
//            } else {
//                var point = new BMap.Point(116.39633672727, 39.922375818182);
//                map.centerAndZoom(point, 5);
//            }

            address = $('input#address').prop("defaultValue");
            $('input#address').val(address);
        }
    };

    var map = new BMap.Map("map_canvas");
    var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10)};
map.addControl(new BMap.ZoomControl(opts));          //添加地图缩放控件
//    map.addControl(new BMap.NavigationControl(opts));
//    map.enableScrollWheelZoom();      //启用滚轮放大缩小
    map.addControl(new BMap.ScaleControl());                    // 添加默认比例尺控件
    map.addEventListener("tilesloaded", addMarkers);
    map.addEventListener("zoomend", addMarkers);
    map.addEventListener("moveend", addMarkers);

//    var ac = new BMap.Autocomplete(
//            {"input": "address",
//                "location": "<?php // echo $data['city']; ?>"
//            });
//
//    ac.addEventListener("onhighlight", function(e) {
//        var str = "";
//        if (e.fromitem.value) {
//            var _value = e.fromitem.value;
//        }
//        var value = "";
//        if (e.fromitem.index > -1) {
//            value = _value.district + _value.street + _value.business;
//        }
//
//        value = "";
//        if (e.toitem.index > -1) {
//            _value = e.toitem.value;
//            value = _value.district + _value.street + _value.business;
//        }
//    });

    var myValue;
//鼠标点击下拉列表后的事件
//    ac.addEventListener("onconfirm", function(e) {
//        var _value = e.item.value;
//        myValue = _value.district + _value.street + _value.business;
//    });

    var polyline = new BMap.Polyline([
<?php echo isset($data['linePoint']) ? substr($data['linePoint'], 0, strlen($data['linePoint'])-2) : ''; ?>], {strokeColor: "blue",
        strokeWeight: 6, strokeOpacity: 0.5});
    map.addOverlay(polyline);
    map.setViewport([<?php echo isset($data['linePoint']) ? substr($data['linePoint'], 0, strlen($data['linePoint'])-2) : ''; ?>]);


    var local = new BMap.LocalSearch("<?php echo $data['city']; ?>", options);
    local.search("<?php echo $data['city']; ?>");
    setTimeout(function(){map.setZoom(map.getZoom()-1);}, 1);
</script>
