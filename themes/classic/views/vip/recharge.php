<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	$model->name,
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('admin-user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>
<div class="row-fluid">
<div class="span8">
<h1>VIP卡充值  卡号<?php echo $model->id; ?></h1>
</div>
<div class="span2"><?php echo CHtml::link("编辑主卡", Yii::app()->controller->createUrl("vip/update",array("id"=>$model->id)), array('target'=>'_main')); ?></div>
</div>
<div class="row">
<div class="span4">
账户余额:
</div>
<div class="span4">
<?php echo $model->balance; ?>
</div>
</div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'vip-charge-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
	'method'=>'post'
)); ?>

<div class="row">
<div class="span4">
充值金额:
</div>
<div class="span4">
<input type="text" name="amount" id="amount" value="0.00" />
<input type="hidden" name="id" id="id" value="<?php echo $model->id; ?>" />
</div>
</div>
<div class="row-fluid">
<div class="span4">
</div>
<div class="span6">
<?php echo CHtml::submitButton("确定充值", array('class'=>'span3 btn-large btn-success btn-block'));?>
</div>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
    $('input[type="submit"]').click(function(){
        var naem_id = "<?php echo Yii::app()->user->getId();?>";
        if($('#amount').val()>=0 || naem_id == '孟欣' || naem_id == '李定才' || naem_id == '邓小明'){

            /*$('#vip-charge-form').submit();
             $('input[type="submit"]').attr('disabled',true);
             alert('充值成功！');*/
            var vipId = $("input[name='id']").val();
            var vipAmount = $("input[name='amount']").val();
            $.ajax({
                'url':'<?php echo Yii::app()->createUrl('/vip/recharge');?>',
                'type':'post',
                'data':{'id':vipId, 'amount':vipAmount},
                'dataType':'json',
                'cache':false,
                'beforeSend':function(){
                    $('input[type="submit"]').val('loading...').attr('disabled', true);
                },
                'success':function(data){
                    window.parent.$('#mydialog').dialog('close');
                    alert(data.msg);
                    window.parent.$('#mydialog').dialog('close');
                    window.parent.$('.search-form form').submit()
                },
                'complete':function(){
                    $(this).val('确定充值').attr('disabled', false);
                }
            });
            return false;
        }else{
            alert('充值金额不能为空或为0！');
            return false;
            //$('input[type="submit"]').attr('disabled',true);
        }
    });
</script>



