<?php
$this->pageTitle = '配置开关';
echo "<h1>".$this->pageTitle."</h1><br />";
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
));

Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
echo "<table><tr><td>配置名称:</td><td><input type='checkbox' name='config[sms]' value='1'>短信&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='config[blacklist]' value='1'>黑名单</td></tr>";
echo "<tr><td>配置开关：</td><td><select id='push_status' name='status' style='width:120px;'>
      <option value='0'>选择状态</option>
      <option value='1'>开启</option>
      <option value='2'>关闭</option>
      </select></td></tr>";
echo "<tr><td colspan=2 align='center'>".CHtml::submitButton('提交')."</td></tr></table>";
$this->endWidget();
?>