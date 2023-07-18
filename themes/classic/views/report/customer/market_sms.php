<?php
$this->pageTitle = '新增发送短信';
echo "<h1>".$this->pageTitle."</h1><br />";
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
));

Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

echo "<table><tr><td>城市：";
$city = Dict::items('city');
echo "<select id='sms_city_id' name='city_id' style='width:80px;'>";
foreach ($city as $k=>$v)
{
	echo "<option value='".$k."'>".$v."</option>";
}
echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;司机状态:<select id='sms_mark' name='mark' style='width:80px;'>
      <option value='0'>全部</option>
      <option value='1'>正常</option>
      <option value='2'>屏蔽</option>
      <option value='3'>上班</option>
      <option value='4'>下班</option>
      </select></td></tr>";
echo "<tr><td>预计发送时间：</td><td>";
$this->widget('CJuiDateTimePicker', array (
    'id' => 'report_pre_send_time',
	'name'=>'pre_send_time', 
	'mode'=>'datetime',
	'options'=>array (
	    'width' => '60',
	    'mode'=>'datetime',
		'dateFormat'=>'yy-mm-dd'
	),
	'htmlOptions'=>array(
         'style'=>'width:100px;'
     ),
	'language'=>'zh'
));
echo "</td></tr>";
echo "<tr id='sms_phone'><td>电话号：<br/>(一行一号码)</td><td><textarea id='sms_phones' name='sms_phones' style='width:400px;height:300px;'></textarea><br /></td></tr>";
echo "<tr><td>短信内容：</td><td><textarea name='content' style='width:400px;height:100px;'></textarea><br /></td></tr>";
echo "<tr><td colspan=2 align='center'>".CHtml::submitButton('提交')."</td></tr></table>";
$this->endWidget();
?>
<script>
$(document).ready(function(){
	$("#sms_city_id").change(function(){
		if($("#sms_city_id").val() == 0 && $("#sms_mark").val() == 0){
			$("#sms_phone").show();
		}else {
			$("#sms_phone").hide();
			$("#sms_phones").attr("value",'');
		}
	});
	
	$("#sms_mark").change(function(){
		if($("#sms_city_id").val() == 0 && $("#sms_mark").val() == 0){
			$("#sms_phone").show();
		}else {
			$("#sms_phone").hide();
			$("#sms_phones").attr("value",'');
		}
	});
});
</script>