<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'order-form',
    'focus' => array($model, 'order_number'),
    'errorMessageCssClass' => 'alert alert-error',
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => false, // 这个是设置是否把提交按钮也做成客户端验证。
    ),
    'enableAjaxValidation' => false,
    'htmlOptions' => array("onSubmit" => "checkSubmit()")
));
?>
<?php echo $form->hiddenField($model, 'user_id'); ?>
<fieldset>
    <legend>订单基本信息</legend>
    <label>客户电话：<?php echo $model->phone; ?></label>
    <?php
    switch ($model->cost_type) {
        case 1:
            echo '<label>客户类型：VIP卡号' . $model->vipcard . '</label>';
            break;
        case 2:
            echo '<label>客户类型：优惠劵号码' . Common::parseBonus($model->bonus_code) . '</label>';
            break;
        case 4:
            echo '<label>客户类型：优惠劵号码' . Common::parseBonus($model->bonus_code) . '</label>';
            break;
        case 8:
            echo '<label>客户类型：预付费用户</label>';
            break;
    }
    ?>
    <label>订单来源：<?php echo $model->description; ?></label>
    <label>呼叫时间：<?php echo date('Y-m-d H:i', $model->call_time); ?></label>
    <label>预约时间：<?php echo date('Y-m-d H:i', $model->booking_time); ?></label>

    <div class="control-group">
        <?php echo $form->hiddenField($model, 'bonus_code', array('placeholder' => '仅限微信优惠码')); ?>
        <?php echo $form->hiddenField($model, 'vipcard', array('placeholder' => 'VIP客户卡号')); ?>
        <div class="controls">
            <label class='control-label'>单号：</label>
            <?php echo $form->textField($model, 'order_number', array('class' => 'require', 'placeholder' => '填入完整单号，包括带A的单号', 'autocomplete' => 'off')); ?>
            &nbsp;&nbsp;*必填
            <?php echo $form->error($model, 'order_number', array('style' => 'width:210px;')); ?>
        </div>

        <label class="control-label">客户姓名:</label>

        <div class="controls">
            <?php
            echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20, 'class' => 'require', 'autocomplete' => 'off'));
            ?>&nbsp;&nbsp;*必填
            <?php echo $form->error($model, 'name', array('style' => 'width:210px;')); ?>
        </div>

        <label class="control-label">车牌号:</label>

        <div class="controls">
            <input type="text" value="<?php echo $parameter['car_number']; ?>" id="Order_car_number"
                   name="Order[car_number]" maxlength="20" placeholder="车牌号码" autocomplete="off" class="require">&nbsp;&nbsp;*必填
            <?php echo $form->error($model, 'car_number', array('style' => 'width:210px;')); ?>
        </div>
        <label class="control-label">出发地点:</label>

        <div class="controls">
            <?php echo $form->textField($model, 'location_start', array('size' => 20, 'maxlength' => 20, 'class' => 'require', 'autocomplete' => 'off')); ?>
            &nbsp;&nbsp;*必填
            <?php echo $form->error($model, 'location_start', array('style' => 'width:210px;')); ?>
        </div>
        <label class="control-label">到达地点:</label>

        <div class="controls">
            <?php echo $form->textField($model, 'location_end', array('size' => 20, 'maxlength' => 20, 'class' => 'require', 'autocomplete' => 'off')); ?>
            &nbsp;&nbsp;*必填
            <?php echo $form->error($model, 'location_end', array('style' => 'width:210px;')); ?>
        </div>
        <label class="control-label">出发时间:</label>

        <div class="controls">
            <?php
            $days = (time() - $model->call_time) / 3600 / 24 + 1;
            $call_time = date('Y-m-d', $model->call_time);
            $order_date[$call_time] = $call_time;
            for ($i = $days; $i >= 0; $i--) {
                $curr_date = date('Y-m-d', $model->call_time + ($i - 1) * 3600 * 24);
                $order_date[$curr_date] = $curr_date;
            }
            echo CHtml::dropDownList('Order[start_time]', '', $order_date, array('style' => 'width:120px')) . "&nbsp;";
            echo CHtml::textField('Order[start_hour]', ($model->start_time) ? Date('H', $model->start_time) : '', array('size' => 2, 'maxlength' => 2, 'style' => 'width:18px', 'class' => 'require', 'autocomplete' => 'off')) . '时';
            echo CHtml::textField('Order[start_min]', ($model->start_time) ? Date('i', $model->start_time) : '', array('size' => 2, 'maxlength' => 2, 'style' => 'width:18px', 'class' => 'require', 'autocomplete' => 'off')) . '分 &nbsp;&nbsp;*必填';
            ?>
        </div>
        <label class="control-label">到达时间:</label>

        <div class="controls">
            <?php
            echo CHtml::dropDownList('Order[end_time]', '', $order_date, array('style' => 'width:120px')) . "&nbsp;";
            echo CHtml::textField('Order[end_hour]', ($model->end_time) ? Date('H', $model->end_time) : '', array('size' => 2, 'maxlength' => 2, 'style' => 'width:18px', 'class' => 'require', 'autocomplete' => 'off')) . '时';
            echo CHtml::textField('Order[end_min]', ($model->end_time) ? Date('i', $model->end_time) : '', array('size' => 2, 'maxlength' => 2, 'style' => 'width:18px', 'class' => 'require', 'autocomplete' => 'off')) . '分 &nbsp;&nbsp;*必填';
            echo $form->error($model, 'end_time', array('style' => 'width:210px;'));
            ?>
        </div>

        <label class="control-label">等候时间:</label>

        <div class="controls">
            <?php
            if (isset($modelExt)) {
                echo CHtml::textField('OrderExt[wait_time]', $parameter['wait_time'], array('style' => 'width:210px;', 'placeholder' => '等候时间', 'autocomplete' => 'off'));
            } else {
                echo CHtml::textField('OrderExt[wait_time]', $parameter['wait_time'], array('style' => 'width:210px;', 'placeholder' => '等候时间', 'autocomplete' => 'off'));
            }
            ?>
            分钟
            <?php echo $form->error($model, 'wait_time', array('style' => 'width:210px;')); ?>
        </div>
        <label class="control-label">里程:</label>

        <div class="controls">
            <?php echo $form->textField($model, 'distance', array('class' => 'require', 'placeholder' => '代驾里程，可以为0', 'autocomplete' => 'off')); ?>
            &nbsp;&nbsp;公里
            <?php echo $form->error($model, 'distance', array('style' => 'width:210px;')); ?>
        </div>

        <?php
        if ($model->cost_type > 0) {
            switch ($model->cost_type) {
                case 1:
                    echo '<label>';
                    if ($model->cost_type == 1 || $model->income == 0) {
                        echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                    } else {
                        echo $form->checkBox($model, 'cost_type');
                    }
                    echo '&nbsp;从客户账户中扣除代驾费</label>';
                    break;
                case 2:
                    echo '<label>';
                    if ($model->cost_type == 2 || $model->income == 0) {
                        echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                    } else {
                        echo $form->checkBox($model, 'cost_type');
                    }
                    echo '&nbsp;使用客户优惠券抵扣代驾费</label>';
                    break;
                case 4:
                    echo '<label>';
                    if ($model->cost_type == 2 || $model->income == 0) {
                        echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                    } else {
                        echo $form->checkBox($model, 'cost_type');
                    }
                    echo '&nbsp;使用客户优惠券抵扣代驾费</label>';
                    break;
                case 8:
                    echo '<label>';
                    if ($model->cost_type == 2 || $model->income == 0) {
                        echo $form->checkBox($model, 'cost_type', array('checked' => 'true'));
                    } else {
                        echo $form->checkBox($model, 'cost_type');
                    }
                    echo '&nbsp;预付费用户</label>';
                    break;
            }
        }
        ?>
        <div id="controls_income">

        </div>
        <label class="control-label">代驾服务费:</label>

        <div class="controls">
            <?php echo $form->textField($model, 'price', array('class' => 'require', 'placeholder' => '实际收取的代驾费用', 'autocomplete' => 'off'));

            echo $form->error($model, 'price', array('style' => 'width:210px;'));?>
        </div>
        <div class="controls">
            <b style="color:#ff0000">注意：此处费用为收到的现金，请如<br/>实填写，如没有收到现金，可不输入</b>
        </div>

        <label class="control-label">备注:</label>

        <div class="controls">
            <textarea rows="4" cols="35" id="Order_log" name="Order[log]"
                      placeholder="<?php if (empty($model->vipcard)) {
                          echo "客人额外支付的费用（包括小费），需描述清楚";
                      } else {
                          echo "客人额外支付的现金费用（包括小费），需描述清楚，此处填写的费用不会从VIP账户里面扣除";
                      } ?>"></textarea>
        </div>
        <input type="checkbox" name="isComplaint" id="isComplaint">
        是否投诉
        <div class="controls" id="complaintBox" style="display:none">
            <?php
            $dict = Dict::items('confirm_c_type');
            $dict[0] = '请选择投诉类型';
            rsort($dict);
            echo CHtml::dropDownList('status', 0, $dict)?>
            <br>
            <textarea rows="2" cols="35" id="complaint" name="complaint"></textarea>
        </div>
    </div>
</fieldset>

<?php
echo CHtml::submitButton($model->isNewRecord ? 'Create' : '保存', array(
    'class' => 'btn-large', 'id' => 'ceshi'
));

$this->endWidget();
?>
<script>
    window.onload = function () {
        Order_cost();
    }


    $("fieldset").find("input[type='text']").each(function (i) {
        if ($(this).attr('value') == '0' || $(this).attr('value') == '00') {
            $(this).val("");
        }
    });

    $('body').on('keyup', '#Order_distance', function () {
        var order_start_hour = $("#Order_start_hour").val();
        var order_start_min = $("#Order_start_min").val();
        if (order_start_hour != '' || order_start_min != '') {
            Order_cost();
        } else {
            alert("请先填写开始时间");
        }
    });

    $('body').on('keyup', '#Order_start_hour', function () {
        var order_distance = $("#Order_distance").val();
        if (order_distance != '') {
            Order_cost();
        }
    });

    $('body').on('keyup', '#Order_start_min', function () {
        var order_distance = $("#Order_distance").val();
        if (order_distance != '') {
            Order_cost();
        }
    });

    function Order_cost() {
        var city_id = '<?php echo Yii::app()->user->city;?>';
        var distance = $('#Order_distance').val();
        var booking_time = '<?php echo $model->call_time; ?>';
        var vipcard = '<?php echo $model->vipcard; ?>';
        var money = '<?php echo $money;?>';
        var cost_type = '<?php echo $model->cost_type;?>';
        var wait_time = $('#OrderExt_wait_time').val();
        var order_start_hour = $("#Order_start_hour").val();
        var order_start_min = $("#Order_start_min").val();
        var order_start_time = $("#Order_start_time").val();

        if (wait_time == NaN) {
            wait_time = 0;
        }
        var pars = 'cost_type=' + cost_type + '&city_id=' + city_id + '&distance=' + distance + '&booking_time=' + booking_time +
            '&wait_time=' + wait_time + '&vipcard=' + vipcard + '&money=' + money +
            '&order_start_hour=' + order_start_hour + '&order_start_min=' + order_start_min + '&order_start_time=' + order_start_time;
        $.ajax({
            type: 'get',
            url: '<?php echo Yii::app()->createUrl('/order/income');?>',
            data: pars,
            dataType: 'json',
            success: function (data) {
                str = '';
                if (data['income'] > 0) {
                    str += "<label>应付代驾费用:" + data['income'] + "元</label>";
                }
                switch(cost_type){
                    case '1':
                        if ($("#Order_cost_type").attr("checked") == 'checked'){
                            str += "<label class='show_label'> vip可用抵扣金额:" + data['vip'] + "元</label>";
                        }
                        else{
                            str += "<label class='show_label' style='display:none;'> vip可用抵扣金额:" + data['vip'] + "元</label>";
                        }
                        break;
                    default:
                        if ($("#Order_cost_type").attr("checked") == 'checked'){
                            str += "<label class='show_label'>帐户可用抵扣金额:" + data['bonus'] + "元</label>";
                        }
                        else{
                            str += "<label class='show_label' style='display:none;'>帐户可用抵扣金额:" + data['bonus'] + "元</label>";
                        }
                        break;
                }
                $("#controls_income").html(str);

            }});
    }


    $('body').on('change', '#Order_cost_type', function () {
        $(".show_label").toggle();
    })


    $('body').on('keyup', '#OrderExt_wait_time', function () {
        var distance = $('#OrderExt_wait_time').val();
        if (distance != '') {
            $("#Order_distance").keyup();
        }
    });


    $('body').on('blur', '#Order_vipcard', function () {
        var vipcard = $('#Order_vipcard').val();
        var phone = '<?php echo $model->phone; ?>';
        if (vipcard) {
            var pars = 'vipcard=' + vipcard + '&phone=' + phone;
            $.ajax({
                type: 'get',
                url: '<?php echo Yii::app()->createUrl('/client/checkvip');?>',
                data: pars,
                dataType: 'json',
                success: function (json) {
                    if (json == '0') {
                        alert('此VIP卡号与电话号码不匹配，请核对。');
                        $('#Order_vipcard').focus();
                    }
                    ;
                }});
        }
    });

    $('body').on('blur', '#Order_bonus_code', function () {
        var bonus_code = $('#Order_bonus_code').val();
        if (bonus_code) {
            var pars = 'bonus_code=' + bonus_code;
            $.ajax({
                type: 'get',
                url: '<?php echo Yii::app()->createUrl('/bonustype/validcode');?>',
                data: pars,
                dataType: 'json',
                success: function (json) {
                    if (json.code == '-1') {
                        alert(json.message);
                        $('#Order_bonus_code').focus();
                    } else {
                        $('span#bonus_name').html(json.name);
                    }
                    ;
                }});
        }
    });

    function InputCheckNum() {
        var str = $("#Order_distance").val();
        if (isNaN(str)) {
            alert("必须添加数字");
        }
    }


    function show(data) {
        alert(data);
    }

    $(function () {
        $("#isComplaint").change(function () {
            if ($("#isComplaint").attr("checked") == "checked") {
                $("#complaintBox").slideDown(200);
            } else {
                $("#complaintBox").slideUp(200);
            }
        });
        $(".btn-large").click(function () {

            if ($("#isComplaint").attr("checked") == "checked") {
                if ($("#complaint").val() == '') {
                    alert("请输入投诉内容");
                    return false;
                }
            }
        });
    });

    function checkSubmit() {
        $("#ceshi").attr("disabled", true);
    }
</script>
<!-- form -->