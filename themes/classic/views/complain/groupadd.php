<?php
$this->pageTitle = Yii::app()->name . ' - 添加投诉任务组';
?>

<h1>设置投诉任务组</h1>
<div class="search-form">

</div><!-- search-form -->

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'group-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('class' => "form-horizontal"),
));
?>
<?php echo CHtml::hiddenField('gid',$gid); ?>
<?php echo CHtml::label('投诉任务组','first');?>
<?php  echo CHtml::textField('gname', $gname, array('id'=>'gname')) ?><br /><br /><br />

<?php echo CHtml::Button('保存任务投诉组', array('class' => 'btn btn-large btn-primary', 'id'=>'setgroup', 'type' => 'button', 'name' => 'save')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo CHtml::Button('取消', array('class' => 'btn btn-large btn-primary', 'data-dismiss'=>'modal', 'aria-hidden'=>'true', 'type' => 'button', 'name' => 'cancel')); ?>

<?php $this->endWidget(); ?>

<script>
    $('#setgroup').click(function(){
        var gname = $('#gname').val();
        if (gname == '') {
            alert('请填写投诉任务组名');
            return false;
        }
        $.post('index.php?r=complain/groupadd',$('#group-edit-form').serialize(),function(data){
            if (data.succ==1) {
                window.location.reload();
            } else {
                alert('设置投诉任务组失败');
            }
        },'json');
    });
</script>