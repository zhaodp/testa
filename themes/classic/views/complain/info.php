<?php
$this->pageTitle = Yii::app()->name . ' - 设置投诉分类';
?>

<h1>设置投诉分类</h1>
<div class="search-form">

</div><!-- search-form -->

投诉详情：
订单号：<?php echo $data['order_id']; ?> <?php echo $data['detail']; ?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'reset-complain-type-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('class' => "form-horizontal"),
));
?>
<?php echo CHtml::hiddenField('id',$data['id']); ?>
<?php echo CHtml::label('投诉类型','first');?>
<?php  echo CHtml::dropDownList('top_type',
    $parent_id,
    $typelist,
    array(
        'ajax' => array(
            'type'=>'POST', //request type
            'url'=>Yii::app()->createUrl('complain/getsubtypeall'),
            'update'=>'#second_type', //selector to update
            'data'=>array('top_type'=>'js:$("#top_type").val()')
        ))
);?>
<?php echo CHtml::label('投诉二级分类','second');?>
<?php echo CHtml::dropDownList('second_type',$child_id,$subtypelist); ?><br /><br /><br />

<?php echo CHtml::Button('确定', array('class' => 'btn btn-large btn-primary', 'id'=>'settype', 'type' => 'button', 'name' => 'save')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo CHtml::Button('取消', array('class' => 'btn btn-large btn-primary', 'data-dismiss'=>'modal', 'aria-hidden'=>'true', 'type' => 'button', 'name' => 'cancel')); ?>

<?php $this->endWidget(); ?>

<script>
    $('#top_type').bind('change',function(){
        $.ajax({
            'type':'POST',
            'url':'/v2/index.php?r=complain/getsubtypeall',
            'data':{'complain_maintype':$("#top_type").val()},
            'cache':false,
            'success':function(html) {
                jQuery("#second_type").html(html)
            }
        });
        return false;
    });
    $('#settype').click(function(){
        var t_type = $('#top_type').val();
        var s_type = $('#second_type').val();
        if (t_type == -1) {
            alert('请选择投诉类型');
            return false;
        }
        if (s_type == -1) {
            alert('请选择投诉二级分类');
            return false;
        }
        $.post('index.php?r=complain/info',$('#reset-complain-type-form').serialize(),function(data){
            if (data.succ==1) {
                window.location.reload();
            } else {
                alert('修改分类失败');
            }
        },'json');
    });
</script>