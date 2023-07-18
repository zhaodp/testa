<?php

$display = "block";
?>

<div class="span9" style="margin-left: 30px;">
    <?php if($type == CustomerSuggestion::TYPE_FEEDBACK) echo '<h2>客户意见反馈回复详情</h2>'?>
    <?php if($type == CustomerSuggestion::TYPE_COMPLAIN) echo '<h2>投诉回复详情</h2>'?>
</div>


<div class="head_tr_td" style="padding-left:20px">ID：<?php echo $cid ?> </div>

<?php foreach($msg_models as $k => $v):?>
<div class="well span9" style="height: auto;display: <?php echo count($msg_models)>0?"block":"none";?>;">
    <div style='padding: 5px; width: 600px;<?php echo $v['role']==0?"text-align: left;margin-left:5px;":"text-align: right;margin-left:250px;";?>'>
    <?php
        echo "<b>  ".
            Dict::item("support_ticket_group", TicketUser::model()->getGroup($v['user']))
            ." </b>". $v['user']
            . " " .$v['create_time'];
     ?><br />
    <div style="padding: 5px;"><?php echo htmlentities($v['content'],ENT_QUOTES,'UTF-8'); ?></div>
    </div>
</div>
<?php endforeach;?>

<div class="well span9" style="display: <?php echo $display;?>;">
    <p>
        <b>您的回复：</b>
<!--        <input type="checkbox" id="reply_type"  name="reply_type" value="--><?php //echo SupportTicketMsg::REPLY_TYPE_TO_DRIVER;?><!--" />-->
<!--        回复给司机-->
    </p>
    <textarea rows="4" name="content" id="msg"  style="width:800px;margin-left: 10px;"></textarea>
    <div class="span9">
        <a href="javascript:;" onclick="show_default_rc_div();">使用标准语回复</a>
    </div>
    <div class="hide span9" id="default_rc_div">
        <table class=""  style="padding: 10px;">
            <tr>
                <td>
                    <span id="default_reply_one">尊敬的师傅您好，感谢您的支持与反馈，你的问题我们已收到，将尽快为您处理，请您耐心等待。</span>
                    <a class="btn btn-info" href="javascript:;" onclick='use_default_content("default_reply_one")'>使用</a>
                </td>
            </tr>
            <tr>
                <td>
                    <span id="default_reply_two">尊敬的师傅您好，感谢您积极的反馈。您反馈的问题，已经转交相关部门处理，请您耐心等待。</span>
                    <a class="btn btn-info" href="javascript:;" onclick='use_default_content("default_reply_two")'>使用</a>
                </td>
            </tr>
            <tr>
                <td>
                    <span id="default_reply_three">尊敬的师傅您好，感谢您积极的反馈，我们会考虑您的建议，谢谢。</span>
                    <a class="btn btn-info" href="javascript:;" onclick='use_default_content("default_reply_three")'>使用</a>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="well span9">
<div class="span6" style="display: <?php echo $display;?>;">
    <div id="zhip_div" name="zhip_div" class="hide" style="margin-left: 22px;">
        部门 ：<?php
        $groups = Dict::items('support_ticket_group');
        unset($groups[7]);
        echo CHtml::dropDownList('group',0,$groups,array('style'=>"width:130px;"));
        ?>
        <span id="span_child" class="hide">处理人：<select id="child_select" style="width: 130px;"></select></span>
    </div>
    <br/>
    <a class="btn btn-primary" href="javascript:;" id="reply"> 回复用户 </a>&nbsp;
</div>
</div>

<script type="text/javascript">
    
    function show_default_rc_div()
    {
        if($("#default_rc_div").is(":hidden")){
            $("#default_rc_div").show();
        }else{
            $("#default_rc_div").hide();
        }
    }
    function use_default_content(default_content_id){
       var default_content = $("#"+default_content_id).html();
       $("#msg").val(default_content);
    }
   
    $("#reply").click(function(){
        var msg = $("#msg").val();
        var suggestion_id = <?php echo $suggestion_id;?>;
        var type = <?php echo $type;?>;
        var cid = <?php echo $cid;?>;

        if(msg == ''){
            alert("回复内容不能为空，请填写回复内容！");
            return false;
        }
    
        
        var url = '<?php echo Yii::app()->createUrl("/complain/reply");?>';
        $.post(url,{
            suggestion_id:suggestion_id,
            content:msg,
            type:type,
            cid:cid,
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
