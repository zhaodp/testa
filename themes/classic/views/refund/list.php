
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="well span12">
        客户手机号码：<input type="input" name="phone" value="<?php echo $phone ?>">
        <input class="btn btn-primary" type="submit" name="yt0" value="查询">
    </div>
    <div class="well span12">
            账户余额:&nbsp;&nbsp;&nbsp;&nbsp;<input type="input" name="balance" value="<?php echo $isVip ? $vip_balance : $cust_amount ?>" id="balance" readonly="true">&nbsp;&nbsp;&nbsp;&nbsp;
            <input class="btn btn-primary" id="sumrefund" type="button" name="yt0" value="全额退款">
            <p style="color:#ff0000"><?php echo $isVip ? '*VIP账户*' : '';?></p>
    </div>
    <?php $this->endWidget(); ?>
    <?php if($isVip){ ?>
        <div class="row-fluid">
            <?php $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'customer-trans-grid',
                'dataProvider' => $dataProvider,
                'itemsCssClass' => 'table table-striped',
                'columns' => array(
                    array(
                        'name' => '时间',
                        'value' => 'date("Y-m-d H:i:s",$data->created)'
                    ),
                    array(
                        'name' => '交易类型',
                        'value' => 'VipTrade::$trans_type[$data->type]'
                    ),
                    array(
                        'name' => '交易金额',
                        'value' => '$data->amount'
                    ),

                    array(
                        'name' => '当前余额',
                        'value' => '$data->balance'
                    ),
                    array(
                        'name' => 'VIP卡号',
                        'value' => '$data->vipcard'
                    ),
                    array(
                        'name' => '备注',
                        'value' => '$data->comment'
                    ),
                ),
            )); ?>
        </div>

    <?php }else{?>
        <div class="row-fluid">
            <?php $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'customer-trans-grid',
                'dataProvider' => $dataProvider,
                'itemsCssClass' => 'table table-striped',
                'columns' => array(
                    array(
                        'name' => '时间',
                        'value' => '$data->create_time'
                    ),
                    array(
                        'name' => '交易类型',
                        'value' => 'CarCustomerTrans::$trans_type[$data->trans_type]'
                    ),
                    array(
                        'name' => '交易金额',
                        'value' => '$data->amount'
                    ),
                    array(
                        'name' => '备注',
                        'value' => '$data->remark'
                    ),
                ),
            )); ?>
        </div>
    <?php } ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#sumrefund").click(function(){
                var isVIP = '<?php echo $isVip ?>';
                if(isVIP){//
                    //说明为VIP客户弹出错误提示：「VIP 账户暂不能退款。」
                    alert("VIP 账户暂不能退款");
                    return false;
                }else{
                    //如果非VIP客户即清空该客户账户余额为零，,并给出弹窗提示「客户账户已清零，退款完成。」
                    var aid = '<?php echo $accountId ?>'//普通用户id既t_customer_main的id
                    var cid = '<?php echo $custMainId ?>'//该客户的账户余额id既t_customer_account的id
                    var amount = $("#balance").val();
                    var sumrefund_url = '<?php echo Yii::app()->createUrl('refund/sumRefund');?>';
                    if(!aid){
                        alert('当前不能进行该操作！');
                        return false;
                    }
                    if(amount == '0.00' || amount <= 0){
                        alert('余额为0或负数不能退款！');
                        return false;
                    }
                    $.get(
                        sumrefund_url,
                        {'cid':cid,'amount':amount,'aid':aid},
                        function(data){
                            if(data == 1){
                                alert("客户账户已清零，退款完成!");
                                $("#balance").val('0.00');
                            }else{
                                alert("系统故障退款失败稍后再试!");
                            }
                        }
                    );
                }
            });
        });
    </script>

