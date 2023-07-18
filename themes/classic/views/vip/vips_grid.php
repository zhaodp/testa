<?php
$criteria = new CDbCriteria();
$criteria->condition = 'vipcard=:vipcard';
$criteria->params = array(':vipcard'=>$model->vipcard);
$vips = VipS::model()->findAll($criteria);
$arrStatus = Vips::model()->arrStatus;
?>

<table class="table table-striped">
	<thead>
		<tr class="row">
			<th class="span1">序号</th>
			<th class="span3">副卡人姓名</th>
			<th class="span3">手机号码</th>
			<th class="span3">状态</th>
			<th class="span2">操作</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if (!empty($vips))
	{
		$i = 0;
		foreach ($vips as $value) {
			$i ++;
	?>
			<tr class="row odd">
				<td  class="span1"><?php echo $i;?></td>
				<td  class="span3"><?php echo CHtml::textField("vips_name_" . $value->id, $value->name, array('style'=>'width:150px;')); ?></td>
				<td  class="span3"><?php echo CHtml::textField("vips_phone_" . $value->id, $value->phone, array('style'=>'width:150px;')); ?></td>
				<td  class="span3"><?php 
				echo CHtml::dropDownList("vips_status_" . $value->id,  $value->status, $arrStatus, array('style'=>'width:150px;')); 
				?>
				</td>
				<td  class="span2"><a href="#" id="vips_update_<?php echo $value->id;?>" name="vips_udpate_<?php echo $value->id;?>">修改</a>
<script type="text/javascript">
jQuery('#vips_update_<?php echo $value->id;?>').live('click',function() {

	if ($('#vips_name_<?php echo $value->id;?>').attr('value').length <=0)
	{
		alert('姓名不能为空');
		$('#vips_name_<?php echo $value->id;?>').focus();
		return false;
	}
	if(!(/^1[3|4|5|8][0-9]\d{4,8}$/.test($('#vips_phone_<?php echo $value->id;?>').attr('value')))){
		alert("不是完整的11位手机号或者正确的手机号前七位");
		$('#vips_phone_<?php echo $value->id;?>').focus();
		return false;
	} 
	

	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/vip/updatevips');?>',
		'data': 'id=' + <?php echo $value->id;?> + '&vipcard='+ <?php echo $value->vipcard;?> + '&name=' + $('#vips_name_<?php echo $value->id;?>').attr('value') + '&phone=' + $('#vips_phone_<?php echo $value->id;?>').attr('value') + '&status=' + $("#vips_status_<?php echo $value->id;?> option:selected").val(),
		'type':'get',
		'success':function(data){
		    if (data.length > 0)
		    {
			    if (data.length > 100)
			    	alert("修改成功");					    
//					$('#vips_grid').html(data);
			    else
			    {
			    	alert(data);
			    	$('#vips_phone_<?php echo $value->id;?>').focus();			    	
			    }
		    }
		},
		'cache':false		
	});
	return false;
});

</script>			
				</td>
			</tr>
	<?php 
		}
	}
	
	?>
	<tr class="row odd">
		<td  class="span1">&nbsp;</td>
		<td  class="span3"><?php echo CHtml::textField("vips_name_0", '', array('style'=>'width:150px;')); ?></td>
		<td  class="span3"><?php echo CHtml::textField("vips_phone_0", '', array('style'=>'width:150px;')); ?></td>
		<td  class="span3"><?php echo CHtml::dropDownList("vips_status_0", 1, $arrStatus, array('style'=>'width:150px;')); ?></td>
		<td  class="span2"><a href="#" id="vips_create">添加</a></td>		
	</tr>	
	</tbody>
</table>
<script>
<?php echo "alert('$alert')";?>
</script>