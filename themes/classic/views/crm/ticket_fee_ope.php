<?php
$this->breadcrumbs=array(
    '列表'=>array('ticketList'),
    $model->id,
);
?>
<div class="span9" style="margin-left: 30px;">
    <h4>工单补扣款处理</h4>
</div>

<div class="well span9" style="height: auto;">
    <span style="padding: 10px;">
        <span style="padding: 5px;"><b><?php echo ($model->source == 1)?"400接线员" :"司机";?></b></span>
        <span style="padding: 5px;"><?php echo $model->create_user;?></span>
        <span style="padding: 5px;"><?php echo $model->create_time ;?></span> 录入
        <span style="padding: 10px;margin-left: 20px;">
            <?php
            if($model->type == 4){
                $complaint_type = SupportTicket::$driver_complaint_type[$model->complaint_type];
                echo "投诉：";
                echo isset($complaint_type)?$complaint_type:'-';
                echo  isset($model->complaint_target)?$model->complaint_target:'-';
            }
            if($model->type == 6){
                echo " 订单号：" . $model->order_id;
            }
            if($model->type==5){
                echo " 被投诉id：" . $model->customer_complain_id;
            }
            if($model->source != 1){
                echo "<br /><span style='padding: 10px;'>设备:".$model->device . " 操作系统版本:".$model->os ." App 版本:". $model->version . "</span>";
            }
            ?>
        </span>
        <div style="width: 500px;padding:10px;">
            <?php echo htmlentities($model->content,ENT_QUOTES,'UTF-8');?>
            <p/><p/>
        </div>
    </span>
</div>
<?php foreach($msg_models as $k => $v):?>
<div class="well span9" style="height: auto;display: <?php echo count($msg_models)>0?"block":"none";?>;">
    <div style='padding: 5px; width: 550px;<?php echo $v->reply_user_type==1?"text-align: left;margin-left:5px;":"text-align: right;margin-left:-10px;";?>'>
    <?php
        $reply_type = '';
        if($v->reply_user_type != 1){
            $reply_type = SupportTicketMsg::$replyTypeList[$v->reply_type];
        }
        echo "<b>  ".
            Dict::item("support_ticket_group", TicketUser::model()->getGroup($v->reply_user))
            ." </b>". $v->reply_user
            . " " .$v->create_time
            . "<span style='padding: 10px;color: red;'>" .$reply_type
            . "</span>";
     ?><br />
    <div style="padding: 5px;"><?php echo htmlentities($v->message,ENT_QUOTES,'UTF-8'); ?></div>
    </div>
</div>
<?php endforeach;?>
<?php 
$information_fee = $fee->information_fee;
$insurance_fee = $fee->insurance_fee;
$fine_fee = $fee->fine_fee;
$other_fee = $fee->other_fee;
?>
<div class="well span9">
<table  class=""  style="padding: 10px;">   
  <tr>
    <th>补偿类型</th>
    <th>&nbsp;</th>
    <th>补偿金额</th>
  </tr>
  <tr>
    <td>信息费</td>
    <td>&nbsp;</td>
    <td><input type="text" name="information_fee" id="information_fee" value=<?php echo $information_fee ?> disabled="disabled" /></td>
  </tr>
 <tr>
    <td>保险费</td>
    <td>&nbsp;</td>
    <td><input type="text" name="insurance_fee" id="insurance_fee"  value=<?php echo $insurance_fee ?> disabled="disabled" /></td>
  </tr>
 <tr>
    <td>罚金</td>
    <td>&nbsp;</td>
    <td><input type="text" name="fine_fee" id="fine_fee"  value=<?php echo $fine_fee ?> disabled="disabled" /></td>
  </tr>
 <tr>
    <td>其他</td>
    <td>&nbsp;</td>
    <td><input type="text" name="other_fee" id="other_fee"  value=<?php echo $other_fee ?> disabled="disabled" /></td>
  </tr>
</table>
</div>
<?php if($view != '1'){ ?>
<div class="span6">
    <a class="btn btn-primary" href="javascript:;" id="ope" > 确认处理 </a>&nbsp;
    <a class="btn btn-primary" href="javascript:;" id="reject"> 驳回 </a>&nbsp;
</div>
<?php } ?>
<script type="text/javascript">
$("#ope").click(function(){
	if(!confirm("确认处理此补偿？")){
            return false;
        }

	var information_fee = $("#information_fee").val()=='' ? 0 : $("#information_fee").val();
        var insurance_fee =  $("#insurance_fee").val()=='' ? 0 : $("#insurance_fee").val();
        var fine_fee = $("#fine_fee").val() =='' ? 0 : $("#fine_fee").val();
        var other_fee = $("#other_fee").val() =='' ? 0 : $("#other_fee").val();

	var st_id = <?php echo $model->id;?>;
	var url = '<?php echo Yii::app()->createUrl("/crm/compensate");?>';
	$.post(url,{
            st_id:st_id
        },function(data){
            alert(data.msg);
            window.location.reload();
        },'json');
});

$("#reject").click(function(){
        if(!confirm("确认驳回此补偿？")){
            return false;
        }
        var st_id = <?php echo $model->id;?>;
        var url = '<?php echo Yii::app()->createUrl("/crm/reject");?>';
        $.post(url,{
            st_id:st_id
        },function(data){
            alert(data.msg);
            window.location.reload();
        },'json');
});
</script>

<style type="text/css">
    .div_head{
        width: 950px;
        height: 100px;
        border: solid 1px;
        margin-left: 10px;
        padding: 10px;
    }
    .div_content{
        width: 950px;
        height: 150px;
        border: solid 1px;
        margin-left: 10px;
        margin-top: 10px;
        padding: 10px;
    }
    .head_tr_td{
        margin-left: 10px;
        margin-top: 10px;
        width: 100px;
        padding: 5px;
    }
</style>
