<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

$cs = Yii::app()->getClientScript();
$cs->registerScriptFile("http://api.map.baidu.com/library/MapWrapper/1.2/src/MapWrapper.min.js",CClientScript::POS_HEAD);
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '订单信息',
        'autoOpen' => false,
        'width' => '750',
        'height' => '450',
        'modal' => true,
        'buttons' => array(
            'Close' => 'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<div class="row-fluid">
    <div class="span6">
        <?php
        if (isset($driver) && !empty($driver)) {
            echo "<h1>" . $driver->name . "(" . $driver->user . "  当前状态：" . $status .")" . "</h1>";
            ?>

            <table class="table table-bordered table-striped">
                <tr>
                    <th width="23%">
                        电话
                    </th>
                    <td width="15%">
                        <?php echo $driver->phone; ?>
                    </td>

                    <th width="17%">
                        备用电话
                    </th>
                    <td width="15%">
                        <?php echo $driver->ext_phone; ?>
                    </td>
                    <th width="15%">
                        屏蔽状态
                    </th>
                    <td width="15%">
                        <?php echo ($driver->mark == Employee::MARK_DISNABLE) ? "已屏蔽" . (($driver->block_at != 0) ? "欠费屏蔽" : "") . (($driver->block_mt != 0) ? "手动屏蔽" : "") : (($driver->mark == Employee::MARK_LEAVE) ? "已解约" : "正常") ?>
                    </td>
                </tr>

                <tr>
                    <th width="23%">
                        星级
                    </th>
                    <td width="15%">
                        <?php echo empty($driver->level) ? 0 : $driver->level; ?>
                    </td>

                    <th width="17%">
                        代驾次数
                    </th>
                    <td width="15%">
                        <?php echo $driver_info['service_times']; ?>
                    </td>
                    <th width="15%">
                        上线天数
                    </th>
                    <td width="15%">
                        <?php echo $driver_info['normal_days']; ?>
                    </td>
                </tr>

                <tr>
                    <th>
                        给公司带来收入
                    </th>
                    <td>
                        <?php echo $driver_info['deductions']; ?>
                    </td>

                    <th>
                        奖励次数
                    </th>
                    <td>
                        <?php echo $driver_info['recommend']; ?>
                    </td>
                    <th>
                        处罚次数
                    </th>
                    <td>
                        <?php echo $driver_info['punish']; ?>
                    </td>
                </tr>
            </table>
            <form action="<?php echo Yii::app()->createUrl("/client/service",array('phone'=>$_GET['phone']));?>" method="post">
                <div class="row-fluid">
                    <input type="hidden" class="span8" id="c_status" value="0"/>
                    <span>请司机师傅详细描述需要反馈的信息。</span>
                    <h4>工单类型：</h4>
                    <table>
                        <tr>
                            <?php
                            $cates = Dict::items('ticket_category');
                            //unset($cates[4]);
                            //unset($cates[5]);
                            foreach($cates as $k => $v){
                                echo '<td style="width: 100px;"><input type="radio" name="type" onclick="change_type(this)" value="'.$k.'">' . ' ' . $v .'</td>';
                            }
                            ?>
                        </tr>
                    </table>
                    </p><br />
                    <div id="driver_complaint_div" class="hide">
                        <table>
                            <tr>
                                <td>投诉对象类型：</td>
                                <td>投诉对象：</td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo CHtml::dropDownList("complaint_type",0,SupportTicket::$driver_complaint_type)?>
                                </td>
                                <td>
                                    <input type="text" placeholder="手机号/工号" name="complaint_target" id="complaint_target" />
                                </td>
                            </tr>
                        </table>

                    </div>
                    <div class="hide" id="appeal_div">
                        <?php
                        $complains = array();
                        if($driver->user){
                            $complains = CustomerComplain::model()->getComplainListByDriver($driver->user,1,5,true);
                        }
                        if($complains){
                            ?>
                            <table  class="span12 table table-bordered">
                                <tr>
                                    <td style="width:45px">投诉ID</td>
                                    <td style="width: 80px">投诉时间</td>
                                    <td style="width: 60px">投诉分类</td>
                                    <td style="width: 338px">投诉内容</td>
                                    <td>操作</td>
                                </tr>
                                <?php
                                foreach($complains as $c){
                                    ?>
                                    <tr>
                                        <td><?php echo $c->id;?></td>
                                        <td><?php echo $c->date;?></td>
                                        <td><?php echo $c->complain_type;?></td>
                                        <td><?php echo $c->content;?></td>
                                        <td><a href="javascript:select_this_complain(<?php echo $c->id;?>)">选择</a></td>
                                    </tr>
                                <?php }?>
                            </table>
                            申诉此投诉：id:<input class="span3" id="complaint_id" name="complaint_id" value="" readonly="readonly"/>
                        <?php
                        }else{
                            echo "此司机暂无被投诉 或都已申诉<br />";
                        }
                        ?>
                    </div>
                    <h4>问题描述：</h4>
                    <textarea class="span12" rows="5" id="content" name='content'></textarea>
                    <input type="hidden" name="is_finish" id="is_finish"  value='0' />

                </div>
                <div class="row-fluid" style="padding-bottom: 15px;">
                    <button class="btn btn-primary pull-right" id="sub" >提交相应部门</button>
                    <a class="btn btn-primary pull-right" id="to_finish" href="javascript:;" style="margin-right: 15px;" >已解决</a>
                </div>
                <div id="div_finish">
                    <h4>解决方法：</h4>
                    <textarea class="span12" rows="5" id="finish_content" name='finish_content'></textarea>
                    <button class="btn btn-primary pull-right" id="finish"  style="margin-right: 15px;" > 完成 </button>
                </div>
                <input type="hidden" id="is_show" value='0'/>
            </form>
        <?php
        } else {
            echo "<h1>来电电话：" . $_GET['phone'] . "</h1>";
        }
        ?>

<!--        <div class="row-fluid">-->
<!--            <input type="hidden" class="span8" id="c_status" value="0"/>-->
<!--            <h4>咨询描述：</h4>-->
<!--            <textarea class="span12" rows="5" id="c_content"></textarea>-->
<!--        </div>-->
<!--        <div class="row-fluid" style="padding-bottom: 15px;">-->
<!--            <button class="btn btn-primary pull-right" id="created">确定</button>-->
<!--        </div>-->


        <?php
            echo $this->renderPartial('_service_driver_map', array('driver'=>$driver));
        ?>
        
        <?php
        if (!empty($knowledgeProblems) && (!empty($knowledgeProblems['title']) || !empty($knowledgeProblems['content']))) {
            echo "<div class='well'>";
            if (!empty($knowledgeProblems['content'])) {
                $knowledgeProblems = json_decode($knowledgeProblems['content'], true);
                echo "<p><b>咨询内容：</b></p>";
                foreach ($knowledgeProblems as $list) {
                    echo "<p style='padding-bottom:10px;'>&nbsp;&nbsp" .
                        nl2br($list['content']) . "<br />" .
                        "<span class ='pull-right'>操作人：" . $list['operator'] . "&nbsp;&nbsp;时间：" . $list['created'] . "</span>" .
                        "</p>";
                }
            }
            echo "</div>";
        }
        ?>


        <?php if (!empty($order_list)) { ?>
            <div class="row-fluid">
                <table class="table table-striped">
                    <tr>
                        <th class="span2">订单编号</th>
                        <th class="span3">客户电话</th>
                        <th class="span3">订单时间</th>
                        <th class="span2">收费</th>
                        <th class="span3">订单来源</th>
                        <th class="span2">状态</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    foreach ($order_list as $order) {
                        $c_phone='';
                        if(!empty($order['phone'])){
                            $c_phone.='呼叫:'.$order['phone'];
                        }
                        if(!empty($order['contact_phone'])){
                            if(!empty($c_phone) )
                                $c_phone.='<br/>';
                            $c_phone.='联系:'.$order['contact_phone'];
                        }

                        //获取再次推送按钮
                        $order_queue_map = OrderQueueMap::model()->getQueueIdByOrderId($order['order_id']);
                        $again_send_btn = CHtml::button("再次推送", array("onclick"=>"PushAgain(".$order_queue_map['map']['queue_id'] . " , '".$driver->user."')" ));

                        echo "<tr>
                        <td> <a target='_blank' href = '".Yii::app()->createUrl('order/view',array('id'=>$order['order_id']))."' onclick = '{//orderDialogdivInit(" . $order['order_id'] . ");}' >" . $order['order_id'] . "</a><br/>" . $order['order_number'] . "</td>
                        <td>" . $c_phone ."</td>
                        <td>" . date('m-d H:i', $order['call_time']) . "<br/>" . date('m-d H:i', $order['booking_time']) . "</td>
                        <td>" . $order['income'] . "</td>
                        <td>" . ($order['source'] == 0 ? "客户呼叫" : (($order['source'] == 1) ? "呼叫中心" : ($order['source'] == 2 ? "客户呼叫补单" : ($order['source'] == 3 ? "呼叫中心补单" : "")))) . "</td>
                        <td>" . ($order['status'] == 0 ? '未报单' : ($order['status'] == 1 ? '报单' : '销单')) . "</td>
                        <td>" .$again_send_btn . "</td>
                    </tr>";
                    }
                    ?>
                </table>
            </div>
        <?php } ?>
    </div>

    <div class="span6">
        <h1>知识库查询</h1>

        <div class="row-fluid">
            <?php $form = $this->beginWidget('CActiveForm', array(
                'action' => Yii::app()->createUrl($this->route),
                'method' => 'get',
                'htmlOptions' => array('class' => 'form-inline'),
            )); ?>
            <?php if (isset($_GET['kp_id'])) { ?>
                <input id="kp_id" type="hidden" name="kp_id" value="<?php echo $_GET['kp_id']; ?>">

            <?php } ?>
            <input id="Knowledge_phone" type="hidden" name="phone" value="<?php echo $_GET['phone']; ?>">
            <input id="Knowledge_title" type="text" name="title" maxlength="100" size="60" class="span7"
                   value="<?php echo empty($_GET['title']) ? '' : $_GET['title']; ?>">
            <?php echo CHtml::submitButton('搜 索', array('class' => 'btn span3')); ?>&nbsp;&nbsp;

            <?php $this->endWidget(); ?>
        </div>
    <?php
        $sms_list = SmsTemplate::model()->getListByType(SmsTemplate::CALLCENTER);
        // receive 1 全部 2 用户 3 司机 4 员工
        if(!empty($sms_list)) {
        echo '<div class="accordion" id="accordion_sms">';
        echo "<h5>短信模版:</h5>";
        ?>
            <div class="accordion-group" id="customized-sms">
                <div class="accordion-heading">
                  <a class="accordion-toggle" data-toggle="collapse"  href="#customized-sms-body">
                    自定义短信
                  </a>
                </div>
                <div class="accordion-body collapse" id="customized-sms-body">
                  <div class="accordion-inner">
                    <p style="word-wrap:break-word; word-break:normal;"><textarea style="width:90%" id="customized-message" placeholder="自定义短信内容：最大100字" maxlength="100"></textarea></p>
                    <a href="#" >发送</a>
                  </div>
                </div>
              </div>
        <?php
            foreach($sms_list as $i) {
                if($i->receive == 3){
                ?>
                  <div class="accordion-group">
                    <div class="accordion-heading">
                      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_sms" href="#<?php echo $i->subject ?>">
                        <?php echo $i->name ?>
                      </a>
                    </div>
                    <div id="<?php echo $i->subject ?>" class="accordion-body collapse">
                      <div class="accordion-inner">
                        <p style="word-wrap:break-word; word-break:normal;"><?php echo $i->content ?></p>
                        <a href="#" id="sendsms_<?php echo $i->subject; ?>" onclick="send_sms_template('<?php echo $_REQUEST['phone']; ?>','<?php echo $i->subject; ?>');">发送</a>
                      </div>
                    </div>
                  </div>
                <?php
                }
            }
        echo '</div>';
        }?>
        <div class="row-fluid">
            <?php if ((empty($_GET['title']) && !isset($_GET['catid'])) && !isset($_GET['id'])) { ?>
                <?php
                $i = 1;
                foreach ($knowledge_list as $k => $v) {
                    if (!empty($v['list'])) {
                        echo "<div class = 'box box_border'>";
                        echo "<h4><a href = '" . Yii::app()->createUrl('/client/service', array('phone' => $_GET['phone'], 'catid' => $k)) . "'>" . $v['name'] . "</a></h4>
                <table class='table'>";

                        foreach ($v['list'] as $list) {
                            echo "<tr><td><a href = '" . Yii::app()->createUrl("/client/service", array('phone' => $_GET['phone'], 'id' => $list['id'])) . "'>" . $list['title'] . "</a></td></tr>";
                        }

                        echo "</table></div>";
                    }
                    $i++;
                }
                ?>
            <?php
            } else {
                $this->widget('zii.widgets.CListView', array(
                    'dataProvider' => $dataProvider,
                    'htmlOptions' => array('class' => 'list-view span12', 'id' => 'left'),
                    'itemView' => '_view',
                ));
            } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function select_this_complain(complain_id)
    {
        $("#complaint_id").val(complain_id);
    }
    function change_type(radio_obj){
        if(radio_obj.value == 4){
            $("#driver_complaint_div").show();
            $("#appeal_div").hide();
            $("#complaint_id").val('');
        }else{
            $("#driver_complaint_div").hide();
            $("#events_time_start").val('');
            $("#events_time_end").val('');
            $("#complaint_target").val('');
            $("#complaint_type").val('1');
            if(radio_obj.value == 5){
                $("#appeal_div").show();
            }else{
                $("#appeal_div").hide();
                $("#complaint_id").val('');
            }
        }
    }
    function PushAgain(queue_id , driver_id) {
        if(confirm("您确定再次给司机推送订单详情么？")){
            $.ajax({
                'url':'<?php echo Yii::app()->createUrl('/order/PushAgain');?>',
                'data':'queue_id='+queue_id+'&driver_id='+driver_id,
                'type':'get',
                'success':function(data){
                    if(data == 1){
                        alert("推送成功");
                    }else{
                        alert("推送失败");
                    }
                },
                'cache':false
            });
        }
    }
    $(function () {
        $("#content").focus();
        $("input[name='type']").first().attr('checked','checked');
        //已解决 点击
        $("#finish").click(function () {
            var content = $("#content").val();
            var finish_content = $("#finish_content").val();

            if($("input[name='type']:checked").val() == 4){
                var events_time_start =  $("#events_time_start").val();
                var events_time_end = $("#events_time_end").val();
                var complaint_target = $("#complaint_target").val();
                if(events_time_start == '' || events_time_end == '' || complaint_target == ''){
                    alert('投诉信息不完整！请检查实践时间和投诉对象！');
                    return false;
                }
            }
            if($("input[name='type']:checked").val() == 5){
                if($("#complaint_id").val() == ''){
                    alert('请选择要申诉的投诉！');
                    return false;
                }
            }
            $("#is_finish").val(1);
            if (content == "") {
                alert("问题描述不能为空");
                return false;
            }
            if (finish_content == "") {
                alert("解决方法不能为空");
                return false;
            }

        });
        $("#sub").click(function () {
            $("#is_finish").val(0);
            if($("input[name='type']:checked").val() == 4){
                var events_time_start =  $("#events_time_start").val();
                var events_time_end = $("#events_time_end").val();
                var complaint_target = $("#complaint_target").val();
                if(complaint_target == ''){
                    alert('投诉信息不完整！请检查实践时间和投诉对象！');
                    return false;
                }
            }
            var content = $("#content").val();
            if (content == "") {
                alert("问题描述不能为空");
                return false;
            }
            return true;

        });
        $("#div_finish").hide();
        //点击 已解决
        $("#to_finish").click(function (){
            if($("#is_show").val()=='0'){
                $("#div_finish").show();
                $("#sub").attr('disabled',true);
                $("#is_show").val('1');
            }else{
                $("#div_finish").hide();
                $("#sub").attr('disabled',false);
                $("#is_show").val('0');
            }
        });

        //$("#c_content").focus();

//        $("#created").click(function () {
//            var c_content = $("#c_content").val();
//            if (c_content == "") {
//                alert("请输入咨询描述");
//            } else {
//                if (confirm("此问题是否解决")) {
//                    $("#c_status").val('1');
//                    submit();
//                } else {
//                    $("#c_status").val('0');
//                    submit();
//                }
//            }
//        });
   });


    function submit() {
        var c_title = $("#Knowledge_title").val();
        var c_content = $("#c_content").val();
        var phone = $("#Knowledge_phone").val();
        var status = $("#c_status").val();
        $.ajax({
            type: 'post',
            url: '<?php echo Yii::app()->createUrl('/KnowledgeProblems/AjaxUpdate');?>',
            data: 'phone=' + phone + '&title=' + c_title + '&content=' + c_content + '&status=' + status,
            success: function (e) {
                if (status == 1) { //解决问题时才提示
                    if (e == 1) {
                        alert("已解决");
                        location.href = "<?php echo Yii::app()->createUrl('/KnowledgeProblems/admin');?>";
                    } else {
                        alert("不能对同一问题重复解决");
                    }
                } else {
                    location.href = "<?php echo Yii::app()->createUrl('/KnowledgeProblems/admin');?>";
                }
            }
        });
    }

    function orderDialogdivInit(orderId) {
        $('#dialogdiv').html("<img src='http://www.edaijia.cc/sto/classic/i/loading.gif' />");
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/order/view')?>',
            'data': 'id=' + orderId,
            'type': 'get',
            'success': function (data) {
                $('#dialogdiv').html(data);
            },
            'cache': false
        });
        jQuery("#mydialog").dialog("open");
        return false;
    }
    function send_sms_template(phone, template){
        if (phone == ''){
            alert ('电话信息不正确，请重新派单。');
            return false;
        }

        if (template == ''){
            alert('短信模版不能为空');
            return false;
        }

        $('#sendsms_'+template).attr("onclick",'alert("短信已经在发送途中....")');

        $.post("index.php", {r :'client/sendSmsTemplate', phone : phone, template : template },
        function(data){
            if (data == phone){
                alert ('成功发送到手机' + phone);
            } else {
                alert ('发送不成功。');
                    }
            });
    }
    ;(function($){
        $("#customized-sms .accordion-inner a").click(function(){
            var message = $("#customized-message").val(),phone='<?php if(isset($_REQUEST["phone"]))echo $_REQUEST["phone"]; ?>',username='<?php echo Yii::app()->user->name;?>';
            if(message==''){
                alert("消息不能为空。");
                return;
            }
            $.ajax({url:'index.php?r=client%2FsendustomizeSms',data:{username:username,message:message,phone:phone},method:"get",success:function(res){
                res=$.parseJSON(res);
                if(res.status == 0){
                    alert('发送成功！');
                }else{
                    alert('发送不成功。')
                }
            },error:function(){
                alert('发送不成功。')
            }})
        });
    })(jQuery);
</script>


