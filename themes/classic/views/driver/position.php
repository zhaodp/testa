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
        <button type="button" onclick="beforeSubmit(this);" class="btn">查询</button>
<?php
$this->endWidget();
?>

<div style="margin-top: 190px; margin-left: 10px; display: none;">
    <div>轨迹数量：<?php echo isset($data['count']) ? $data['count'] : '';?></div>
    <div>查询用时：<?php echo isset($data['time']) ? $data['time'] : '';?></div>
    <div>查询速度：<?php echo isset($data['speed']) ? $data['speed'] : '';?></div>
    <div>数据来源：<?php echo isset($data['source']) ? $data['source'] : '';?></div>
    <div>Marker创建用时：<span id="marker-create-time"></span>ms</div>
    <div>Line创建用时：<span id="line-create-time"></span>ms</div>
</div>

</div>


<script type="text/javascript">

// init baidu map
var map = new BMap.Map("map_canvas");
map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10)}));
map.enableScrollWheelZoom();

addMarkers();
centerMap();


function addMarkers() {
    var points = [];
    var startTime = new Date();    
    <?php
    if(isset($data['positions'])){
        foreach($data['positions'] as $position) {
            echo sprintf("points.push(new BMap.Point(%s, %s));\n", $position['lng'], $position['lat']);
            echo sprintf("addMarker(%s, %s, %d, '%s');\n",
                $position['lat'],
                $position['lng'],
                isset($position['state']) ? $position['state'] : 1,
                is_numeric($position['created']) ? date("Y-m-d H:i:s", $position['created']) : $position['created'] 
            );
    ?>

    // any later logic
    
    <?php
        }
        }
    ?>

    $("#marker-create-time").text(new Date().getTime() - startTime.getTime());

    startTime = new Date();
    var polyline = new BMap.Polyline(points, {strokeColor:"blue", strokeWeight:6, strokeOpacity:0.5});
    map.addOverlay(polyline);
    $("#line-create-time").text(new Date().getTime() - startTime.getTime());
}

function addMarker(latitude, longitude, status, tips) {
    var point = new BMap.Point(longitude, latitude);
    var myIcon = new BMap.Icon("/v2/sto/classic/i/us_cursor.gif", new BMap.Size(23, 25), {
        offset: new BMap.Size(10, 25),
        imageOffset: new BMap.Size(0-status*23,-21)
    });

    var marker = new BMap.Marker(point, {icon: myIcon});
    var infoWindow = new BMap.InfoWindow('', {title : '<span style="font-size:16px;color:#0A8021">' + tips + '</span>'});  // 创建信息窗口对象

    marker.addEventListener("click", function(){
        this.openInfoWindow(infoWindow);  
    });
    
    map.addOverlay(marker);
}

function centerMap() {
    <?php
        $count = isset($data['positions']) ? count($data['positions']) : 0;
        if($count > 0) {
            $center_position = $data['positions'][$count/2];
            echo sprintf("map.centerAndZoom(new BMap.Point(%s, %s), 15);\n", $center_position['lng'], $center_position['lat']);
        } else {
            echo("map.centerAndZoom(new BMap.Point(116.39633672727,39.922375818182), 5);\n");
        }
    ?>
}


Date.prototype.format =function(format)
{
        var o = {
                "M+" : this.getMonth()+1, //month
                "d+" : this.getDate(), //day
                "h+" : this.getHours(), //hour
                "m+" : this.getMinutes(), //minute
                "s+" : this.getSeconds(), //second
                "q+" : Math.floor((this.getMonth()+3)/3), //quarter
                "S" : this.getMilliseconds() //millisecond
                }
        if(/(y+)/.test(format)){
                format=format.replace(RegExp.$1,(this.getFullYear()+"").substr(4- RegExp.$1.length));
        }
        for(var k in o){
                if(new RegExp("("+ k +")").test(format)){
                        format = format.replace(RegExp.$1,RegExp.$1.length==1? o[k] :("00"+ o[k]).substr((""+ o[k]).length));
                }
        }
        return format;
}

function beforeSubmit(obj){
    var cStartDate = new Date(document.getElementById("startDate").value);
    var maxStartDate = new Date(document.getElementById("endDate").value);
    var cEndDate = new Date(document.getElementById("endDate").value);
    var maxEndDate = new Date(document.getElementById("startDate").value);
    maxEndDate = maxEndDate.setDate(cStartDate.getDate()+2);
    maxStartDate = maxStartDate.setDate(cEndDate.getDate()-2);
    if(cEndDate>maxEndDate){
        //限定查询开始时间和结束时间差在2天以内，钟福海修改于2015/4/27
        document.getElementById("endDate").value=(new Date(maxEndDate)).format('yyyy-MM-dd hh:mm:ss');
    }else if(cStartDate>cEndDate){
    	//限定查询开始时间和结束时间差在2天以内，钟福海修改于2015/4/27
    	document.getElementById("startDate").value=(new Date(maxStartDate)).format('yyyy-MM-dd hh:mm:ss');
    }else{
        document.getElementById("driver-map-form").submit();
    }
}

</script>