<?php
$this->pageTitle = '司机对单数';
?>

<h1><?php echo $this->pageTitle;?></h1>
<hr class="divider"/>

<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'accountsettle-form', 
	'method'=>'post',
	'enableAjaxValidation'=>false,
));
?>
<div class="row span12">
    <div class="span3">
    	<label><?php echo $form->label($model,'driver_id'); ?></label>
    	<?php echo $form->textField($model,'driver_id',array('width'=>20,'maxlength'=>20,'class'=>"span12")); ?>
    </div>
    <div class="span3">
    	<label>对单月</label>
    	<?php 
    	
    	$currentMonth = date('Ym', time() - 480 * 3600);
    	/*
    	$minYear = 2012;
    	$minMonth = 4;
    	
    	//$monthArray = array($currentMonth=>$currentMonth);
    	
    	for($year = date('Y'); $year >= $minYear; $year--){
    		if (date('Y') <> $minYear){
    			
    		} else {
    			for($month = date('n'); $month >= $minMonth; $month--){
    				$yearmonth = strlen($month) == 1 ? $year.'0'.$month : $year.$month;
    				$monthArray[$yearmonth] = $yearmonth;
    			}
    		}
    	}
    	*/
    	$monthArray = array($currentMonth=>$currentMonth);
    	echo $form->dropDownList($model,
			'booking_time',
			$monthArray,
			array('class'=>"span12")
		); ?>
    </div>
</div>
<hr class="divider"/>
<?php 
echo CHtml::submitButton('查询'); 


$this->endWidget();
if (isset($orderCount)){
	$form = $this->beginWidget('CActiveForm', array (
		'id'=>'settle-form', 
		'action'=>array('driver/dosettle'),
		'enableAjaxValidation'=>false,
	));

?>
<div class="row span12">
    <div class="span3">
    	<label>工号：<?php echo $user['user'];?></label>
    	<label>姓名：<?php echo $user['name'];?></label>
    	<label>单数：<?php echo $orderCount;?></label>
    	<input type="hidden" name='OrderCount[currentMonth]' value='<?php echo $model->booking_time;?>'>
    	<input type="hidden" name='OrderCount[user]' value='<?php echo $user['user'];?>'>
    	<input type="hidden" name='OrderCount[count]' value='<?php echo $orderCount;?>'>
    </div>
</div>
<?php 
	if ($settleCount == 0 && $orderCount > 0)
		echo CHtml::submitButton('对单'); 

	$this->endWidget();
}
?>

