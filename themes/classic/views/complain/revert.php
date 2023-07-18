<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'complain-confirm-form',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
)); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <!--左侧-->
        <div class="span4">
            <!--Body content-->
            <div class="row-fluid">
                <div class="span4">
                    <label>订单编号</label>
                    <?php echo CHtml::textField('order_id',$model->order_id,array('class'=>'span10','placeholder'=>'订单编号','disabled'=>'disabled')); ?>
                </div>
                <div class="span4">
                    <label>司机工号</label>
                    <?php echo CHtml::textField('driver_id',$model->driver_id,array('class'=>'span10','placeholder'=>'司机工号','disabled'=>'disabled')); ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <label>投诉人电话</label>
                    <?php echo CHtml::textField('driver_id',$model->phone,array('class'=>'span12','placeholder'=>'投诉人电话','disabled'=>'disabled')); ?>
                </div>
                <div class="span4">
                    <label>投诉人姓名</label>
                    <?php echo CHtml::textField('name',$model->name,array('class'=>'span12','placeholder'=>'客人姓名','disabled'=>'disabled')); ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <label>预约电话</label>
                    <?php echo CHtml::textField('customer_phone',$model->customer_phone,array('class'=>'span12','placeholder'=>'预约电话','disabled'=>'disabled')); ?>
                </div>
                <div class="span4">
                    <label>投诉来源</label>
                    <?php echo CHtml::dropDownList('source',$model->source, array_merge(array('-1'=>'全部'),CustomerComplain::$source),array('class'=>'span12','disabled'=>'disabled')); ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label>投诉详情</label>
                    <?php echo CHtml::textArea('detail',$model->detail,array('class'=>'input-xlarge','rows'=>'6','style'=>'width: 360px;','disabled'=>'disabled'));?>
                </div>
                <br />
                <?php
                    if($model->source == 8){  //从APP司机端过来的投诉

                        if(isset($model->support_ticket_id)){
                            echo CHtml::link('工单详情',array('crm/ticketView','id'=>$model->support_ticket_id),array('style'=>"display:inline-block;cursor:pointer;",'target'=>'_blank','class'=>'btn btn-info','disabled'=>'disabled'));
                        }

                    }
                ?>
            </div>


        </div>
        <!--右侧-->
        <div class="span6">
            <div class="row-fluid">
                <div class="span4">
                    <label>一级分类</label>
                    <label><?php echo $firstComplain ?></label>
                </div>
                <div class="span5">
                    <label>二级分类</label>
                    <label><?php echo $secondComplain ?></label>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span12">
                    <label>处理意见</label>
                    <?php echo CHtml::textArea('mark',$handleContent,array('class'=>'input-xlarge','rows'=>'6','style'=>'width: 498px; height: 160px;','disabled'=>'disabled'));?>
                </div>
            </div>

            <div class="row-fluid">
                <ul class="thumbnails">
                    <li class="span5">
                        <div class="thumbnail">
                            <div class="caption">
                                <div class="row-fluid">
                                   <div class="span12">
                                    <label>投诉撤销原因</label>
                                    <?php echo CHtml::textArea('reason','',array('class'=>'input-xlarge','rows'=>'6','style'=>'width: 180px;'));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="span5">
                        <div class="thumbnail">
                            <div class="caption">
                                <div class="row-fluid" >
                                    <div class="span10">
                                        <label>司机</label>
                                        <input type="text" class="input span10" disabled="disabled" name="new_driver_id" id="new_driver_id" placeholder="司机工号" value="<?php echo $model->driver_id ?>">
                                    </div>
                                    </div>
                                <div class="row-fluid">
                                        <div class="span8">
                                            <label>金额(品监请注意：撤销生效时，金额为正是补偿，金额为负是扣款)</label>
                                            <input type="text" class="input span8" disabled="disabled" name="driver_cash" id="driver_cash" value="<?php echo $money ?>
                                                   placeholder="信息费充扣">
                                        </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <p>
                <div class="row-fluid">
                <div class="span4">
                    <input type="hidden" name="confirm_btn_d" value="" id="confirm_btn_d_id">
                    <a  class="btn btn-success span9" name="confirm_btn" id="confirm_btn">确认撤销</a>
                </div>
                <div class="span4">
                    <input type="hidden" name="re" value="<?php echo $re ?>">
                    <input type="hidden" name="cid" value="<?php echo $cid ?>">
                    <a class="btn btn-info span9" name="close_btn" id="close_btn">取消</a>
                </div>
            </div>
            </p>
        </div>

    </div>
</div>


<?php $this->endWidget(); ?>
<script type="text/javascript">
<!--
$(function(){
    //关闭
	$('#close_btn').click(function(){
        $('#close_btn').attr('disabled',true);
        history.back();
	});
    //确认撤销
    $('#confirm_btn').click(function () {
        if (!confirm('撤消后不可更改，确认撤销该投诉？'))
            return false;
        $('#confirm_btn').attr('readonly','readonly');
        $('#confirm_btn').attr('disabled','disabled');
        //alert('<?php echo Yii::app()->createUrl('complain/revert') ?>'); return false;
        $("#complain-confirm-form").attr("action", "<?php echo Yii::app()->createUrl('complain/revert') ?>");
        $('#complain-confirm-form').submit();
    });

});
//-->
</script>


