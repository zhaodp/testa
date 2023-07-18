<?php
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
        if (isset($driver)) {
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
        <?php
        } else {
            echo "<h1>来电电话：" . $_GET['phone'] . "</h1>";
        }
        ?>
        <form action="" method="post">
        <div class="row-fluid">
            <input type="hidden" class="span8" id="c_status" value="0"/>
            <span>请司机师傅详细描述需要反馈的信息。</span>
            <h4>工单类型：</h4>
            <table>
                <tr>
                    <?php
                    $cates = Dict::items('ticket_category');
                    unset($cates[4]);
                    unset($cates[5]);
                        foreach($cates as $k => $v){
                            echo '<td style="width: 100px;"><input type="radio" name="type" value="'.$k.'">' . ' ' . $v .'</td>';
                        }
                    ?>
                </tr>
            </table>
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


                        echo "<tr>
                        <td> <a href = 'javascript:void(0);' onclick = '{orderDialogdivInit(" . $order['order_id'] . ");}' >" . $order['order_id'] . "</a><br/>" . $order['order_number'] . "</td>
                        <td>" . $c_phone ."</td>
                        <td>" . date('m-d H:i', $order['call_time']) . "<br/>" . date('m-d H:i', $order['booking_time']) . "</td>
                        <td>" . $order['income'] . "</td>
                        <td>" . ($order['source'] == 0 ? "客户呼叫" : (($order['source'] == 1) ? "呼叫中心" : ($order['source'] == 2 ? "客户呼叫补单" : ($order['source'] == 3 ? "呼叫中心补单" : "")))) . "</td>
                        <td>" . ($order['status'] == 0 ? '未报单' : ($order['status'] == 1 ? '报单' : '销单')) . "</td>
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
    $(function () {
        $("#content").focus();
        $("input[name='type']").first().attr('checked','checked');
        //已解决 点击
        $("#finish").click(function () {
            var content = $("#content").val();
            var finish_content = $("#finish_content").val();

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
            var content = $("#content").val();
            if (content == "") {
                alert("问题描述不能为空");
                return false;
            } else {
                return true;
            }
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

    });


</script>


