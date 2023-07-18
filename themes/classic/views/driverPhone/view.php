<?php if(empty($phone_right)){
	echo "<h3>请输入正确的手机号</h3>";
	exit;
}?>


<?php if(isset($driver)){?>
<h3 style = "padding-top:10px; padding-bottom:10px;"><?php echo "司机姓名：".$driver['name']."&nbsp;&nbsp;司机工号：".$driver['user']."&nbsp;&nbsp;手机类型：".$equipment; ?></h3>
<table class="table table-condensed">
  <tr>
    <th>工作电话</th>
    <td><?php echo $driver['phone']; ?></td>
    <th>替换为</th>
    <td><?php echo $driverPhone['phone']; ?></td>
  </tr>
  <tr>
    <th>imei</th>
    <td><?php echo $driver['imei']; ?></td>
    <th>替换为</th>
    <td><?php echo $driverPhone['imei']; ?></td>
  </tr>
  <tr>
    <th colspan = '4' style="padding-top:10px;"><a class="btn" href="javascript:void(0);" onclick="{DriverIDblur();}"> 替换 </a></th>
  </tr>
</table>
<?php }else{
	echo "<h3>此工号不存在，司机未签约</h3>";
}?>
<script type="text/javascript">
function DriverIDblur(){
	$.ajax({
		url:'<?php echo Yii::app ()->createUrl ( '/driverPhone/ajaxDriverPhone' );?>',
		data:{driver_id:'<?php echo $driverPhone['driver_id']; ?>',phone:'<?php echo $driverPhone['phone']; ?>',imei:'<?php echo $driverPhone['imei']; ?>',imei_old:'<?php echo $_GET['id']; ?>',simcard:'<?php echo $driverPhone['simcard']; ?>'},
		dataType:"html",
		success: function(data){
			if(data == 0){
				
				alert("修改失败");
			}else{
				alert("修改成功");
				parent.closedDialog("update_driver_phone_dialog");
			}
		}
	});
}
</script>