<?php
$this->pageTitle = Yii::app()->name . ' - 添加投诉任务人';
?>

<h1>设置投诉任务人</h1>
<div class="search-form">

</div><!-- search-form -->

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'groupuser-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('class' => "form-horizontal"),
));
?>
<?php echo CHtml::hiddenField('gid',$gid); ?>
<?php echo CHtml::hiddenField('ouid',$uid); ?>
<?php echo CHtml::label('投诉任务组','group');?>
<?php  echo CHtml::textField('gname', $gname, array('id'=>'gname','disabled'=>true)) ?><br /><br /><br />

<?php echo CHtml::label('投诉任务人','user');?>
<?php  echo CHtml::dropDownList('department',
    $did,
    $dlist
    );
?>&nbsp;&nbsp;
<?php echo CHtml::dropDownList('user',$uid,$ulist); ?><br />
<?php echo CHtml::label('角色','role');?>
<?php echo CHtml::dropDownList('role',$role,array(2=>'组员',1=>'组长')); ?><br /><br />

<?php echo CHtml::Button('保存任务投诉人', array('class' => 'btn btn-large btn-primary', 'id'=>'setuser', 'type' => 'button', 'name' => 'save')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo CHtml::Button('取消', array('class' => 'btn btn-large btn-primary', 'data-dismiss'=>'modal', 'aria-hidden'=>'true', 'type' => 'button', 'name' => 'cancel')); ?>

<?php $this->endWidget(); ?>

<script>
    $('#department').bind('change',function(){
        $.ajax({
            'type':'POST',
            'url':'/v2/index.php?r=complain/getdepartmentuser',
            'data':{'did':$("#department").val()},
            'cache':false,
            'success':function(html) {
                jQuery("#user").html(html)
            }
        });
        return false;
    });

    $('#setuser').click(function(){
        var user = $('#user').val();
        var role = $('#role').val();
        if (user == -1) {
            alert('请填写投诉任务人');
            return false;
        }
        if (role == '') {
            alert('请填写投诉任务人角色');
            return false;
        }
        $.post('index.php?r=complain/groupuseradd',$('#groupuser-edit-form').serialize(),function(data){
            if (data.succ==1) {
                window.location.reload();
            } else {
                alert('设置投诉任务人失败');
            }
        },'json');
    });
</script>