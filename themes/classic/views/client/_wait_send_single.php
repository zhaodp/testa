<style>
    table{
        width: 550px;
    }
    table tr td{
        padding-left: 20px;
    }
</style>
<h3 style="margin-left: 40px;">修改 <?php echo $model->name;?> 订单信息 </h3>
<div style="margin-left: 40px;">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'change-queue-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <table>
        <tr>
            <td>
                <div class="row">
                    <?php echo $form->labelEx($model,'city_id'); ?>
                    <?php
                    $city = Dict::items('city');
                    $city[0] = '无法定位城市';
                    echo $form->dropDownlist($model,'city_id',$city);
                    ?>
                    <?php echo $form->error($model,'city_id'); ?>
                </div>
            </td>
            <td>
                <div class="row">
                    <?php echo $form->labelEx($model,'contact_phone'); ?>
                    <input type="text"  maxlength="100" id="contact_phone">
                    <?php echo $form->hiddenField($model,'contact_phone'); ?>
                    <a  href="#" style= 'color:#ff0000; text-decoration : underline' id="update_btn">修改电话</a>
                    <?php echo $form->error($model,'contact_phone'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <?php echo $form->labelEx($model,'address'); ?>
                    <?php echo $form->textField($model,'address'); ?>
                    <?php echo $form->error($model,'address'); ?>
                </div>
            </td>
            <td>
                <div class="row">
                    <?php echo $form->labelEx($model,'name'); ?>
                    <?php echo $form->textField($model,'name'); ?>
                    <?php echo $form->error($model,'name'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <?php echo $form->labelEx($model,'number'); ?>
                    <?php echo $form->textField($model,'number'); ?>
                    <?php echo $form->error($model,'number'); ?>
                </div>
            </td>
            <td>
                <div class="row input-append">
                    <?php echo $form->labelEx($model,'booking_time'); ?>
                    <?php echo $form->textField($model,'booking_time_day',array('style'=>'width:100px')); ?>&nbsp;
                    <?php echo $form->textField($model,'booking_time_time',array('style'=>'width:92px')); ?>
                    <?php echo $form->error($model,'booking_time'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <?php echo $form->labelEx($model,'comments'); ?>
                    <?php echo $form->textField($model,'comments'); ?>
                    <?php echo $form->error($model,'comments'); ?>
                </div>
                <div class="row buttons" >
                    <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '更新',array('class'=>'btn btn-primary')); ?>
                </div>
            </td>
        </tr>

    </table>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">

    $(document).ready(function(){
        //1.页面刷新后将联系人电话真实的值隐藏，将带*的值显示在文本框 2.当用户修改电话后将值赋给隐藏域提交
        $("#contact_phone").val('<?php echo AdminSpecialAuth::model()->haveSpecialAuth("user_phone") ? $model->contact_phone : trim(substr_replace($model->contact_phone, "*****", 3, 5))?>');
        $("#contact_phone").attr("readonly","readonly");
    });
    $("#update_btn").click(function(){
        $("#contact_phone").attr("readonly",false);
        $("#contact_phone").val('');
        $("#OrderQueue_contact_phone").val('');
    });
    $("#contact_phone").blur(function(){
        //当不是屏蔽状态填写电话后失去光标的时候将值复制给隐藏域
        if($("#contact_phone").attr("readonly") != "readonly"){
            $("#OrderQueue_contact_phone").val($("#contact_phone").val());
        }
    });
    $("input[value='更新']").click(function(){
        if(!$("#OrderQueue_contact_phone").val()){
            $("#contact_phone").focus();
            return false;
        }
    });
</script>
