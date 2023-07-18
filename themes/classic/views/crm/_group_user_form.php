
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'ticket-group-user-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <div>
        <p><?php echo CHtml::checkBox("is_admin",false)?>  <span>添加为管理员</span></p>
    </div>

    <div>
        <?php echo $form->labelEx($model,'group'); ?>
        <?php
             $groups =  Dict::items('support_ticket_group');
             //unset($groups[5]);
             unset($groups[7]);
             echo $form->dropDownList($model,'group',$groups); ?>
        <?php echo $form->error($model,'group'); ?>
    </div>
    <?php echo $form->labelEx($model,'city_id'); ?>
    <?php
        $citys = Dict::items('city');
        unset($citys[0]);
        echo $form->dropDownList($model,'city_id',$citys); ?>
    <?php echo $form->error($model,'city_id'); ?>

    <div>

    </div>
    <div>
        <?php echo $form->labelEx($model,'user'); ?>
        <?php echo $form->textField($model,'user'); ?>
        <?php echo $form->error($model,'user'); ?>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <?php echo CHtml::submitButton($model->isNewRecord ? '添 加' : '保 存',array('class'=>'btn btn-large  btn-success')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $("#is_admin").click(function (){
        if(!this.checked){
            $("#TicketUser_group").attr('disabled',false);
        }else{
            $("#TicketUser_group").attr('disabled',true);
        }
    });
    $("#ticket-group-user-form").submit(function (){
        if($("#TicketUser_user").val().trim() == ""){
            alert("处理人不能为空！");
            return false;
        }
    });
</script>