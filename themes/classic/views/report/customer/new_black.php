<?php
$this->pageTitle = '新增黑名单用户';
echo "<h1>".$this->pageTitle."</h1><br />";
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
));
echo "<table>";
echo "<tr><td>电话号：<br/>(一行一号码)</td><td><textarea name='phones' style='width:400px;height:400px;'></textarea><br /></td></tr>";
echo "<tr><td colspan=2 align='center'>".CHtml::submitButton('提交')."</td></tr></table>";
$this->endWidget();
?>