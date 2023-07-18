<h1>司机出勤统计查询</h1>

<section>
<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'order-form', 
	'enableAjaxValidation'=>false,
));
?>
司机工号：<?php echo CHtml::textField('Employee[user]','',array('class'=>'span3'))?>
IMEI号：<?php echo CHtml::textField('Employee[imei]','',array('class'=>'span3'))?>
<?php 
echo CHtml::submitButton('查询'); 

$this->endWidget();
?>

<?php 
foreach($work as $month){
	foreach($month as $item){
		echo '<label>'.$item['date'].' <span class="label">' .$item['c'].'</span><label>';
	}
}

?>
</section>