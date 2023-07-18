<?php
$this->pageTitle = Yii::app()->name . ' - 设置投诉分类';
?>

<h1>设置投诉分类</h1>
<div class="search-form">

</div><!-- search-form -->
投诉类型：
<?php echo $data['top_type'].'--'.$data['second_type']; ?><br /><br />

投诉详情：
订单号：<?php echo $data['order_id']; ?> <?php echo $data['detail']; ?><br /><br />

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'reset-complain-user-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('class' => "form-horizontal"),
));
?>
<?php echo CHtml::hiddenField('id',$data['id']); ?>
<?php echo CHtml::label('投诉任务组','label_group');?>
<?php  echo CHtml::dropDownList('group',
    $gid,
    $grouplist
);?>
<?php echo CHtml::label('投诉任务人','label_user');?>
<?php echo CHtml::dropDownList('user',$uid,$userlist); ?><br /><br /><br />

<?php echo CHtml::Button('确定', array('class' => 'btn btn-large btn-primary', 'id'=>'setuser', 'type' => 'button', 'name' => 'save')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo CHtml::Button('取消', array('class' => 'btn btn-large btn-primary', 'data-dismiss'=>'modal', 'aria-hidden'=>'true', 'type' => 'button', 'name' => 'cancel')); ?>

<?php $this->endWidget(); ?>

<script>
    $('#group').bind('change',function(){
        $.ajax({
            'type':'POST',
            'url':'/v2/index.php?r=complain/getgroupuser',
            'data':{'group_id':$("#group").val()},
            'cache':false,
            'success':function(html) {
                jQuery("#user").html(html)
            }
        });
        return false;
    });
    $('#setuser').click(function(){
        var group = $('#group').val();
        var user = $('#user').val();

        if (user == -1) {
            alert('请选择投诉任务人');
            return false;
        }
        $.post('index.php?r=complain/manualdispatch',$('#reset-complain-user-form').serialize(),function(data){
            if (data.succ==1) {
                window.location.reload();
            } else {
                alert('修改投诉任务人失败');
            }
        },'json');
    });
</script>