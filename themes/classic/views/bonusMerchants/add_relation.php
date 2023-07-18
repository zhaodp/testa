<?php
$this->breadcrumbs=array(
        'BonusMerchants'=>array('index'),
        'Create',
);
?>
<h1>新增优惠劵</h1>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bm-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>
    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
	    <div> 
                <?php echo '商家名称：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';?>
		<?php echo $model->name; ?>
            </div>
            <div>
                <?php echo '关联优惠劵id：';?>
		<?php echo  CHtml::textField("bonus_ids", ''); ?>
		<?php echo '(关联多张优惠券时，请用","隔开)'?>
            </div>
	    <div class="span2">
		<?php echo CHtml::link('提交', 'javascript:void(0)', array('class'=>'btn btn-success btn-block')); ?>
            </div>
     </div>
    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
   $('.btn-success').click(function() {
	if ($('#bonus_ids').val()==''){
                alert('优惠劵id不能为空');
                return false;
        }
        if($('#bonus_ids').val().indexOf('，')>0){
                alert('请使用英文","');
                return false;
        }
	var bonus_ids = $('#bonus_ids').val();
        $.ajax({
                type: 'GET',
                async: false,
                url: "<?php echo Yii::app()->createUrl('/bonusMerchants/checkBonusIds');?>",
                data: 'bonus_ids='+bonus_ids,
                success: function(msg){
                        if(msg!=''){
                           alert(msg);
                        }else{
                          $('#bm-form').submit();
                        }
                }
        }); 
    });
 document.onkeydown = function(event) {
    var target, code, tag;
    if (!event) {
    event = window.event; //针对ie浏览器
    target = event.srcElement;
    code = event.keyCode;
    if (code == 13) {
    tag = target.tagName;
    if (tag == "TEXTAREA") { return true; }
    else { return false; }
    }
    }
    else {
    target = event.target; //针对遵循w3c标准的浏览器，如Firefox
    code = event.keyCode;
    if (code == 13) {
    tag = target.tagName;
    if (tag == "INPUT") { return false; }
    else { return true; }
    }
    }
    };
</script>
