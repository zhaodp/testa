<?php
$this->pageTitle = '司机结账';
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
    	<label><?php echo $form->label($model,'user'); ?></label>
    	<?php echo $form->textField($model,'user',array('width'=>20,'maxlength'=>20,'class'=>"span12")); ?>
    </div>
    <div class="span3">
    	<label>结账月</label>
    	<?php 
    	$currentMonth = date('Ym', time() - 480 * 3600);
    	$minYear = 2012;
    	$minMonth = 4;
    	
    	//$monthArray = array($currentMonth=>$currentMonth);
    	
    	for($year = date('Y'); $year >= $minYear; $year--){
    		if (date('Y') <> $minYear){
    			
    		} else {
    			for($month = date('n', time() - 480 * 3600); $month >= $minMonth; $month--){
    				$yearmonth = strlen($month) == 1 ? $year.'0'.$month : $year.$month;
    				$monthArray[$yearmonth] = $yearmonth;
    			}
    		}
    	}
    	echo $form->dropDownList($model,
			'created',
			$monthArray,
			array('class'=>"span12")
		); ?>
    </div>
</div>
<hr class="divider"/>
<?php 
echo CHtml::submitButton('查询'); 


$this->endWidget();
if (isset($account)&&$account){
	$accountArray = explode('=', $account['cast']);
	$form = $this->beginWidget('CActiveForm', array (
		'id'=>'settle-form', 
		'action'=>array('account/dosettle'),
		'enableAjaxValidation'=>false,
	));

?>
<div class="row span12">
    <div class="span3">
    	<label>工号：<?php echo $user->user;?></label>
    	<label>姓名：<?php echo $user->name;?></label>
    	<label>单数：<?php echo $account['order_id'];?></label>
    	<label>订单现金收入：<?php echo $accountArray[0];?></label>
    	<label>订单信息费：<?php echo $accountArray[1];?></label>
    	<label>订单发票扣税：<?php echo $accountArray[2];?></label>
    	<label>VIP订单司机收入：<?php echo $accountArray[3];?></label>
    	<label>罚金扣费：<?php echo $accountArray[4];?></label>
    	<label>信息费充值：<?php echo $accountArray[5];?></label>
    	<label>上月结余：<?php echo $account['lastAccount'];?></label>
    	<label>本月结余：<?php echo $account['lastAccount'] + $accountArray[1] + $accountArray[2] +$accountArray[3] + $accountArray[4] + $accountArray[5];?></label>
    	<input type="hidden" name='EmployeeAccount[created]' value='<?php echo $account['currentMonth'];?>'>
    	<input type="hidden" name='EmployeeAccount[user]' value='<?php echo $user->user;?>'>
    	<input type="hidden" name='EmployeeAccount[cast]' value='<?php echo $account['lastAccount'] + $accountArray[1] + $accountArray[2] +$accountArray[3] + $accountArray[4] + $accountArray[5];?>'>
    </div>
</div>
<?php 

	if ($account['is_settle'] == 0)
		echo '未结账';
		//echo CHtml::submitButton('结账'); 

	$this->endWidget();
}
?>

