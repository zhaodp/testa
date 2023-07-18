<?php 
$this->pageTitle = Yii::app()->name . ' - 个人信息';

$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile("http://api.map.baidu.com/api?v=2.0&ak=ECfffb5d16a4f1b23c885c0527e91774",CClientScript::POS_HEAD);
$cs->registerScriptFile("http://api.map.baidu.com/library/MapWrapper/1.2/src/MapWrapper.min.js",CClientScript::POS_HEAD);

$ext = DriverExt::model()->find('driver_id=:driver_id', array(':driver_id'=>$driver->user));
//print_r($employee->position->attributes);
?>
<h1>个人信息</h1>
<div class="view">
	<div class="table-view">
		<table class="table table-striped">
			<tr>
				<td rowspan="17" width="200px"><img width='200px' src='<?php echo $driver->picture;?>'>
					<table class="table table-striped">
						<tr>
							<td><?php echo $driver->getAttributeLabel('user');?></td>
							<td><?php echo $driver->user;?></td>
						</tr>
						<tr>
							<td><?php echo $driver->getAttributeLabel('name');?></td>
							<td><?php echo $driver->name;?></td>
						</tr>
						<tr>
							<td><?php echo $driver->getAttributeLabel('phone');?></td>
							<td><?php echo $driver->phone;?></td>
						</tr>
						<tr>
							<td><?php echo $driver->getAttributeLabel('ext_phone');?></td>
							<td><?php echo $driver->ext_phone;?></td>
						</tr>						
						<tr>
							<td><?php echo $driver->getAttributeLabel('year');?></td>
							<td><?php echo $driver->year;?></td>
						</tr>
						<tr>
							<td><?php echo $driver->getAttributeLabel('level');?></td>
							<td><?php echo $driver->level;?></td>
						</tr>
						<tr>
							<td>未报单</td>
							<td><?php echo Driver::getDriverReadyOrder($driver->user);?></td>
						</tr>
					</table>				
				
				</td>
			</tr>
			<tr>
				<td>
				当前位置：(上次更新时间<?php echo $driver->position->created;?>)
				<?php echo $statusLable;?>			
				<div id="divMap" style="width:500px;height:400px;border:solid 1px gray"></div>
				</td>
			</tr>
		</table>
	</div>
</div>
<input name="OrderLog_description" id="OrderLog_description" type="hidden" value="" />
<input name="OrderLog_status" id="OrderLog_status" type="hidden" value="0" />
<input name="OrderLog_order_id" id="OrderLog_order_id" type="hidden" value="0" />
<script type="text/javascript">
 <!--    

    var myMap = new BMap.Map("divMap");
    myMap.centerAndZoom(new BMap.Point(<?php echo $driver->position->baidu_lng.','.$driver->position->baidu_lat;?>), 16);
    
    //可以转化gps坐标
    var mapWforGPS = new BMapLib.MapWrapper(myMap, BMapLib.COORD_TYPE_GPS); 
    //添加gps坐标mkr
    var point = new BMap.Point(<?php echo $driver->position->longitude.','.$driver->position->latitude;?>);
    var gpsMkr = new BMap.Marker(point);
	mapWforGPS.addOverlay(gpsMkr);

	var gc = new BMap.Geocoder();    

    gc.getLocation(point, function(rs){
        var addComp = rs.addressComponents;
        //alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
    });        

	
	//-->
 </script>
