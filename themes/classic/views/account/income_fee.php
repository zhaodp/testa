<h2 class=" offset2">信息费充值
    <small>（充值，并激活）</small>
</h2>

<div class="span6">
    &nbsp;
</div>

<div class="well span6 form offset2">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="form-horizontal">
        <div class="control-group">
            <label class="control-label" for="inputEmail">司机工号</label>

            <div class="controls">
                <?php echo $form->textField($model, 'user', array('value' => isset($_GET['Driver']['user']) ? $_GET['Driver']['user'] : '', 'placeholder' => "司机工号")); ?>
                <?php echo CHtml::submitButton('Search', array('class' => 'btn span3')); ?>
            </div>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div>
<div class="span6">
    &nbsp;
</div>

<div class="form-horizontal span6  offset2">
    <div class="control-group">
        <label class="control-label">司机姓名</label>

        <div class="controls">
            <?php echo isset($user['name']) ? $user['name'] : '输入工号，检查司机是否存在'; ?>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">司机余额</label>

        <div class="controls">
            <?php echo isset($user['balance']) ? $user['balance'] : 0; ?>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">司机工号</label>

        <div class="controls">
            <?php echo $form->hiddenField($employeeAccount, 'city_id', array('value' => isset($user['city_id']) ? $user['city_id'] : 1)); ?>
            <?php echo $form->textField($employeeAccount, 'driver_id', array('value' => isset($user['user']) ? $user['user'] : '', 'placeholder' => "司机工号")); ?>
        </div>
    </div>


    <div class="control-group">
        <label class="control-label">充值金额</label>

        <div class="controls">
            <?php echo $form->textField($employeeAccount, 'cast', array('placeholder' => "要充值的金额")); ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">充值备注</label>

        <div class="controls">
            <?php
            $prepaid = array('0' => '--请选择---');
            $cast_channel = Dict::items('cast_channel');
            foreach ($cast_channel as $k => $v) {
                if ($k <= 10 || $k == 99) {
                    unset($cast_channel[$k]);
                }
            }

            echo $form->dropDownList($employeeAccount, 'channel', $cast_channel); ?>
        </div>
    </div>
    <div class="control-group" id="comment" style="display:none;">
        <label class="control-label" for="inputPassword">其他备注</label>

        <div class="controls">
            <?php echo $form->textArea($employeeAccount, 'comment'); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="button" class="btn" id="income" data-loading-text="Loading...">充值</button>

        </div>
    </div>

    <div class="span6">
        &nbsp;
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('#EmployeeAccount_channel').change(function () {
            var comment = $(this).val();
            if (comment == 24) {
                $("#comment").show();
            } else {
                $("#comment").hide();
                $("#EmployeeAccount_comment").val("");
            }

        })
    })

    $("#income").click(function () {
        var btn = $("#income");
        btn.button('loading');
        var user = $("#EmployeeAccount_driver_id").val();
        var cast = $("#EmployeeAccount_cast").val();
        var channel = $("#EmployeeAccount_channel").val();
        var comment = $("#EmployeeAccount_comment").val();
        var city_id = $("#EmployeeAccount_city_id").val();

        if (cast == '') {
            btn.button("reset");
            alert("信息费不能为空");
            return false;
        }

        if (channel == 0) {
            btn.button("reset");
            alert("选择充值备注");
            return false;
        } else {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('/account/income'); ?>',
                'data': 'cast=' + cast + '&user=' + user + '&comment=' + comment + '&channel=' + channel + '&city_id=' + city_id,
                'type': 'get',
                'success': function (data) {
                    if (data == 1) {
                        btn.button("reset");
                        alert("充值成功");
                        window.location.href = window.location.href;
                    } else {
                        btn.button("reset");
                        alert("充值失败");
                    }
                },
                'cache': false
            });
        }
    });


</script>
