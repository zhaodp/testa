<?php
$this->breadcrumbs=array(
    '列表'=>array('ticketList'),
    $model->id,
);
$display = $model->status==SupportTicket::ST_STATUS_CLOSE?"none":"block";
?>

<div class="span9" style="margin-left: 30px;">
    <h2>工单详情</h2>
    <p>
        <a href="<?php echo Yii::app()->createUrl("/crm/ticketList");?>">返回工单列表</a>&nbsp;&nbsp;
        <?php
            if($model->type == TicketUser::TICKET_CATEGORY_COMPLAINT || $model->type == TicketUser::TICKET_CATEGORY_APPEAL){
                $url = Yii::app()->createUrl("/complain/list",array('id'=>$model->customer_complain_id));
                echo "<a href='" . $url . "' target='_blank'>投诉列表</a>";
            }
        ?>
    </p>

</div>


<div class="well span9">
    <table class="table table-bordered"  style="width: 850px;margin:3px;">
       <tr>
           <td class="head_tr_td">工单ID：<?php echo $model->id?></td>
           <td class="head_tr_td">工单状态：<?php echo SupportTicket::$statusList[$model->status]?></td>
           <td class="head_tr_td">所属部门：<?php echo Dict::item("support_ticket_group", $model->group);?></td>
       </tr>
       <tr>
           <td class="head_tr_td">跟单人：<?php echo $model->follow_user;?></td>
           <td class="head_tr_td">处理人：<?php echo $model->operation_user;?></td>
           <td> </td>
       </tr>
    </table>
</div>

<div class="well span9" style="border:0px">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'search-form',
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>
    <div class="controls controls-row">
     <span class='span2'>
            <?php echo '工单类型' ?>
            <?php
                $cates = Dict::items('ticket_category');
                echo $form->dropDownList($model,
                                         'type',
                                         $cates,
                                         array(
                                              'ajax'=>array(
                                              'url'=>Yii::app()->createUrl('crm/dynamicClass'),
                                              'data'=>array('type_id'=>'js:this.value','from'=>'detail'),
                                              'update'=>'#SupportTicket_class',
                                              ),
                                          'class'=>"span12"
                                         )
                                );
            ?>
        </span>
        <span class='span2'>
                <?php echo '工单分类' ?>
                <?php
                        echo $form->dropDownList($model,
                                                 'class',
                                                 SupportTicketClass::model()->getClasses($model->type,'detail'),
                                                 array('empty'=>array(''=>''),'class'=>"span12")
                                        );
                ?>
        </span>
	</div>
 <?php $this->endWidget(); ?>
</div>



<div class="well span9" style="height: auto;">
    <span style="padding: 10px;">
<!--        <b>-->
<!--        <span style="padding: 5px;">--><?php //echo Dict::item("ticket_category", $model->type);?><!--</span>-->
<!--        描述 ：</b>-->
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
    <div style='padding: 5px; width: 600px;<?php echo $v->reply_user_type==1?"text-align: left;margin-left:5px;":"text-align: right;margin-left:250px;";?>'>
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

<div class="well span9" style="display: <?php echo $display;?>;">
    <p>
        <b>您的回复：</b>
<!--        <input type="checkbox" id="reply_type"  name="reply_type" value="--><?php //echo SupportTicketMsg::REPLY_TYPE_TO_DRIVER;?><!--" />-->
<!--        回复给司机-->
    </p>
    <textarea rows="4" name="content" id="content"  style="width:800px;margin-left: 10px;"></textarea>
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
<?php
$information_fee_text;
$insurance_fee_text;
$fine_fee_text;
$other_fee_text;
if(isset($fee)){
	$information_fee_text = 'value='.$fee->information_fee;
	$insurance_fee_text = 'value='.$fee->insurance_fee;
	$fine_fee_text = 'value='.$fee->fine_fee;
	$other_fee_text = 'value='.$fee->other_fee;
}else{  
	$information_fee_text = 'disabled="disabled"';
        $insurance_fee_text = 'disabled="disabled"';
        $fine_fee_text = 'disabled="disabled"';
        $other_fee_text = 'disabled="disabled"';
}
?>
<div class="well span9" style="display: <?php echo $display;?>;">
<table  class=""  style="padding: 10px;">   
  <tr>
    <th>补偿类型</th>
    <th>&nbsp;</th>
    <th>补偿金额</th>
  </tr>
  <tr>
    <td>信息费</td>
    <td>&nbsp;</td>
    <td><input type="text" name="information_fee" id="information_fee" <?php echo  $information_fee_text ?> /></td>
  </tr>
 <tr>
    <td>保险费</td>
    <td>&nbsp;</td>
    <td><input type="text" name="insurance_fee" id="insurance_fee" <?php echo  $insurance_fee_text ?> /></td>
  </tr>
 <tr>
    <td>罚金</td>
    <td>&nbsp;</td>
    <td><input type="text" name="fine_fee" id="fine_fee" <?php echo  $fine_fee_text ?> /></td>
  </tr>
 <tr>
    <td>其他</td>
    <td>&nbsp;</td>
    <td><input type="text" name="other_fee" id="other_fee" <?php echo  $other_fee_text ?> /></td>
  </tr>
 <tr>
    <?php if(!isset($fee)){?>
    <td><input type="radio" checked="checked" name="ope" id="ope_no" value="0" />不补偿</td>
    <td>&nbsp;</td>
    <td><input type="radio" name="ope" id="ope_yes" value="1" />补偿</td>
    <?php }else{ ?>
    <td><input type="radio" name="ope" id="ope_no" value="0" />不补偿</td>
    <td>&nbsp;</td>
    <td><input type="radio" checked="checked" name="ope" id="ope_yes" value="1" />补偿</td>
    <?php } ?>
  </tr>
</table>
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
    <a class="btn btn-primary" href="javascript:;" id="close" > 结束工单 </a>&nbsp;
    <a class="btn btn-primary" href="javascript:;" id="reply"> 回复司机 </a>&nbsp;
    <a class="btn btn-primary" href="javascript:;" id="call_on"> 指派 </a>&nbsp;
    <a class="btn btn-primary hide" href="javascript:;" id="reply_driver_assign" onclick="assign(1)"> 通知司机并指派 </a>&nbsp;
    <a class="btn btn-primary hide" href="javascript:;" id="reply_assign" onclick="assign(0)" > 内部沟通并指派 </a>&nbsp;
    <a class="btn hide" id="assign_close" href="javascript:;" onclick="assign_close()"> 返回 </a>&nbsp;
</div>
</div>
<div class="span9" style="padding: 10px;display: <?php echo $display=="none"?"block":"none";?>;">
    <span style="color: red;">
       <?php echo $model->close_time;?>
       已由 <?php echo $model->operation_user;?> 结束工单
    </span>
</div>

<script type="text/javascript">
    function assign_close(){
        $("#reply_driver_assign").hide();
        $("#reply_assign").hide();
        $("#assign_close").hide();
        $("#close").show();
        $("#reply").show();
        $("#call_on").show();
        $("#zhip_div").hide();

    }
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
       $("#content").val(default_content);
    }
    $(function(){
        var is_follow_user = '<?php echo Yii::app()->user->name==$model->follow_user;?>';
        var is_admin = '<?php echo TicketUser::model()->checkUserAdmin(Yii::app()->user->name);?>';

        if(is_follow_user == '1' || is_admin == '1'){
            $("#close").show();
        }else{
            $("#close").hide();
        }

    });
    $("#reply").click(function(){
	var type_id = $('#SupportTicket_type').val(); 
	var ticket_class = $('#SupportTicket_class').val();
        if(ticket_class == null || ticket_class == ''){
            alert('工单分类不能为空');
            return false;
        }
        var reply_type = 1;
	var content = $("#content").val();
        var model_id = <?php echo $model->id;?>;
        if(content == ''){
            alert("回复内容不能为空，请填写回复内容！");
            return false;
        }
	
	//补偿费用
        /**var information_fee = 0;
        var insurance_fee = 0;
        var fine_fee = 0;
        var other_fee = 0;
        var ope = 'no';
        if($("#ope_yes").attr("checked") == "checked"){
                ope = 'yes';
                information_fee = $("#information_fee").val()=='' ? 0 : $("#information_fee").val();
                insurance_fee = $("#insurance_fee").val()=='' ? 0 : $("#insurance_fee").val();
                fine_fee = $("#fine_fee").val() =='' ? 0 : $("#fine_fee").val();
                other_fee = $("#other_fee").val() =='' ? 0 : $("#other_fee").val();
        }

	if(information_fee>1000){
                alert('信息费不能多于1000');return false;
        }
        if(insurance_fee>1000){
                alert('保险费不能多于1000');return false;
        }
        if(fine_fee>1000){
                alert('罚金不能多于1000');return false;
        }
        if(other_fee>1000){
                alert('其他不能多于1000');return false;
        }**/
	
        $("#close").hide();
        $("#reply").hide();
        $("#call_on").hide();
        var url = '<?php echo Yii::app()->createUrl("/crm/ticketReply");?>';
        $.post(url,{
            model_id:model_id,
            content:content,
            reply_type:reply_type,
	    type_id:type_id,
	    ticket_class:ticket_class 
	    /**ope:ope,
            information_fee:information_fee,
            insurance_fee:insurance_fee,
            fine_fee:fine_fee,
            other_fee:other_fee**/
        },function(data){
            alert(data.msg);
            window.location.reload();
        },'json');
    });

    $("#close").click(function(){
	var type_id = $('#SupportTicket_type').val(); 
        var ticket_class = $('#SupportTicket_class').val();
        if(ticket_class == null || ticket_class == ''){
            alert('工单分类不能为空');
            return false;
        }
        var content = $("#content").val();
        if(content == ''){
            alert("回复内容不能为空，请您填写回复内容！");
            return false;
        }
	//补偿费用
	var information_fee = 0;
	var insurance_fee = 0;
 	var fine_fee = 0;
 	var other_fee = 0;
	var ope = 'no';
        if($("#ope_yes").attr("checked") == "checked"){
		ope = 'yes';
		information_fee = $("#information_fee").val()=='' ? 0 : $("#information_fee").val();
		insurance_fee = $("#insurance_fee").val()=='' ? 0 : $("#insurance_fee").val();
		fine_fee = $("#fine_fee").val() =='' ? 0 : $("#fine_fee").val();
		other_fee = $("#other_fee").val() =='' ? 0 : $("#other_fee").val();

		var reg= /^[+-]?[0-9]+(.[0-9]{1,2})?$/;
		if(!reg.test(information_fee)){
			alert('"信息费"必须为最多有两位的小数或整数');
			return false;
		}
		if(information_fee>1000){
                	alert('信息费不能多于1000');return false;
        	}
		if(!reg.test(insurance_fee)){
                	alert('"保险费"必须为最多有两位的小数或整数');
                	return false;
        	}
        	if(insurance_fee>1000){
                	alert('保险费不能多于1000');return false;
        	}
		if(!reg.test(fine_fee)){
                	alert('"罚金"必须为最多有两位的小数或整数');
                	return false;
        	}
        	if(fine_fee>1000){
                	alert('罚金不能多于1000');return false;
        	}
		if(!reg.test(other_fee)){
                	alert('"其他"必须为最多有两位的小数或整数');
                	return false;
        	}
        	if(other_fee>1000){
                	alert('其他不能多于1000');return false;
        	}
		var totalnum = Number(information_fee) +Number(insurance_fee)+Number(fine_fee)+Number(other_fee);
		if(totalnum>1000){
			alert('补偿总金额不能大于1000元');
			return false;
		}
     	 }
       	 if(!confirm("确认结束此订单？")){
              return false;
         }

        $("#close").hide();
        $("#reply").hide();
        $("#call_on").hide();
        var st_id = <?php echo $model->id;?>;
        var url = '<?php echo Yii::app()->createUrl("/crm/closeTicket");?>';
        $.post(url,{
            st_id:st_id,
            content:content,
	    type_id:type_id,
            ticket_class:ticket_class,
	    ope:ope,
	    information_fee:information_fee,
            insurance_fee:insurance_fee,
	    fine_fee:fine_fee,
	    other_fee:other_fee
        },function(data){
            alert(data.msg);
            window.location.reload();
        },'json');
    });

    $("#group").change(function (){
        group_val = $("#group").val();
        $("#child_select").html('');
        var url = '<?php echo Yii::app()->createUrl("/crm/childOptions");?>';
        $.post(url,{
            group_val:group_val
        },function(data){
            if(data.code == 1){
                var opts = '';
                for(var i=0; i<data.data.length; i++)
                {
                    opts += '<option value="'+data.data[i]+'">'+data.data[i]+'</option>';
                }
                $("#span_child").show();
                $("#child_select").html(opts);
            }else{
                $("#span_child").hide();
            }
        },'json');
    });
    function assign(reply_type){
		var type_id = $('#SupportTicket_type').val(); 
        	var ticket_class = $('#SupportTicket_class').val();
        	if(ticket_class == null || ticket_class == ''){
            	    alert('工单分类不能为空');
            	    return false;
        	}
                var content = $("#content").val();
                if(content == ''){
                    alert("回复内容不能为空，请您填写回复内容！");
                    return false;
                }
		
		//补偿费用
        	/**var information_fee = 0;
        	var insurance_fee = 0;
        	var fine_fee = 0;
        	var other_fee = 0;
        	var ope = 'no';
        	if($("#ope_yes").attr("checked") == "checked"){
                	ope = 'yes';
                	information_fee = $("#information_fee").val()=='' ? 0 : $("#information_fee").val();
                	insurance_fee = $("#insurance_fee").val()=='' ? 0 : $("#insurance_fee").val();
                	fine_fee = $("#fine_fee").val() =='' ? 0 : $("#fine_fee").val();
                	other_fee = $("#other_fee").val() =='' ? 0 : $("#other_fee").val();
        	}
		
		if(information_fee>1000){
                	alert('信息费不能多于1000');return false;
        	}
        	if(insurance_fee>1000){
                	alert('保险费不能多于1000');return false;
        	}
        	if(fine_fee>1000){
                	alert('罚金不能多于1000');return false;
        	}
        	if(other_fee>1000){
                	alert('其他不能多于1000');return false;
        	}
		**/
                $("#reply_driver_assign").hide();
                $("#reply_assign").hide();
                var child_sel = $("#child_select").val();
                var group_sel = $("#group").val();
                var ticket_id = '<?php echo $model->id?>';
                var city_id = '<?php echo $model->city_id;?>';
                var url = '<?php echo Yii::app()->createUrl("/crm/assign");?>';
                $.post(url,{
                    child_sel:child_sel,
                    group_sel:group_sel,
                    ticket_id:ticket_id,
                    city_id:city_id,
                    content:content,
                    reply_type:reply_type,
		    type_id:type_id,
            	    ticket_class:ticket_class
		   /** ope:ope,
            	    information_fee:information_fee,
            	    insurance_fee:insurance_fee,
            	    fine_fee:fine_fee,
            	    other_fee:other_fee**/
                },function (data){
                    alert(data.msg);
                    window.close();
                },'json');
    }

    $("#call_on").click(function (){
        $("#reply_driver_assign").show();
        $("#reply_assign").show();
        $("#assign_close").show();
        $("#close").hide();
        $("#reply").hide();
        $("#call_on").hide();
        $("#zhip_div").show();
        $("#child_select").html('');
        var user_group = '<?php echo TicketUser::model()->getGroup(Yii::app()->user->name);?>';
        if(user_group == '1'){
            var url = '<?php echo Yii::app()->createUrl("/crm/childOptions");?>';
            $.post(url,{
                group_val:user_group
            },function(data){
                if(data.code == 1){
                    var opts = '';
                    for(var i=0; i<data.data.length; i++)
                    {
                        opts += '<option value="'+data.data[i]+'">'+data.data[i]+'</option>';
                    }
                    $("#span_child").show();
                    $("#child_select").html(opts);
                }else{
                    $("#span_child").hide();
                }
            },'json');
        }

    });

    $("#ope_yes").click(function(){
        $("#information_fee").removeAttr("disabled");
	$("#insurance_fee").removeAttr("disabled");
	$("#fine_fee").removeAttr("disabled");
	$("#other_fee").removeAttr("disabled");
    });
   $("#ope_no").click(function(){
	$("#information_fee").val("");
        $("#insurance_fee").val("");
        $("#fine_fee").val("");
        $("#other_fee").val("");
        $("#information_fee").attr("disabled","disabled");
        $("#insurance_fee").attr("disabled","disabled");
        $("#fine_fee").attr("disabled","disabled");
        $("#other_fee").attr("disabled","disabled");
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
