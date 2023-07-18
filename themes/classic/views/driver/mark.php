<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'mark-form',
	'enableAjaxValidation'=>false,
));
$isBlock=false;
switch ($_GET['mark']){
	case Employee::MARK_DISNABLE:
		$title = '屏蔽用户';
        $isBlock=true;
		break;
	case Employee::MARK_ENABLE:
		$title = '激活用户';
		break;
	case Employee::MARK_LEAVE:
		$title = '用户解约';
		break;
}
?>
<h1><?php echo $title; ?></h1>
	<div class="grid-view">
        <?php if($isBlock)
        {echo CHtml::dropDownList('limit_day','', array('1'=>'屏蔽1天','3'=>'屏蔽3天','7'=>'屏蔽7天','3600'=>'永久屏蔽'));}
        else{
            echo CHtml::hiddenField('limit_day','0');
        }?>
        <?php echo CHtml::label('备注','mark');?>
        <textarea rows="10" class="span6" cols="50" style="width:80%;" name="DriverExt[mark_reason]" id="DriverExt_mark_reason"></textarea>
		<input name="DriverExt[driver_id]" id="DriverExt_driver_id" type="hidden" value="<?php echo $model->driver_id; ?>" />
		<input name="DriverExt[mark]" id="DriverExt_mark" type="hidden" value="<?php echo $_GET['mark']; ?>" />
	</div>
<?php 
$currentReason = $model->mark_reason;

$this->endWidget(); ?>
