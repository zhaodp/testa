<?php $this->pageTitle = '地址设置';?>

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
    <?php echo CHtml::dropDownList('city_id', $data['city_id'], Dict::items('city'));?>
    <label>地图查询地址：</label>
    <input type="text" name="address" id="address" value="<?php echo isset($data['address'])?$data['address']:$data['city']; ?>" class="span11" autocomplete="off"></input>
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
                    var href = "<br><div style='text-align:center'>【<a href=javascript:set_address_pool(<?php echo $data["city_id"];?>,'<?php echo urlencode(trim($data['address'])); ?>',"+results.getPoi(i).point.lng+","+results.getPoi(i).point.lat+",'<?php echo $data['id'];?>');>保存地址</a>】</div>";
                    addPointWithPic(results.getPoi(i).point.lat, results.getPoi(i).point.lng,results.getPoi(i).title+":"+results.getPoi(i).address+href, 4);
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


    /**
     * 设置地址
     * @author zhanglimin 2013-05-08
     */
    function set_address_pool(city_id , address , lng , lat , id ){
        if( address=="" || lng == "" || lat == ""){
            alert("数据获取有误，请重试!");
            return false;
        }
        $.get("index.php", {r :'addressPool/setAddressPool', city_id : city_id , address : address, lng : lng ,lat:lat ,id : id },
            function(data){
                alert(data);
                return false;
            });
    }
</script>