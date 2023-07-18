<?php
/**
 * 优惠劵创建修改重构
 * User: mtx
 * Date: 13-11-18
 * Time: 下午1:39
 * auther mengtianxue
 */
?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'bonus-code-form',
    'enableAjaxValidation' => false,
));
$ext = CJSON::decode($model->coupon_rules);
?>

<?php echo $form->errorSummary($model); ?>
<?php
    //如果优惠码已经通过审核，那么部分属性就不能修改readonly
    $approved=in_array($model->status, array(BonusCode::STATUS_APPROVED));
?>
<div class="row-fluid">
<div class="span3">
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 60)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'rename'); ?>
        <?php echo $form->textField($model, 'rename', array('size' => 60, 'maxlength' => 60)); ?>
        <?php echo $form->error($model, 'rename'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'money'); ?>
        <?php echo $form->textField($model, 'money',array('readonly' => $approved)); ?>
        <?php echo $form->error($model, 'money'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'channel'); ?>
        <?php $channelList = Dict::items('bonus_channel');
        $channelList = array('99' => '请选择') + $channelList;
        echo $form->dropDownList($model, 'channel', $channelList,array('disabled' => $approved)); ?>
        <?php echo $form->error($model, 'channel'); ?>
    </div>

    <div class="row-fluid" style='margin-bottom:10px;'>
        <?php echo $form->labelEx($model, 'sn_type'); ?>
        <?php
        $snType = Dict::items('bonus_sn_type');
        ?>
        <?php echo $form->radioButtonList($model, 'sn_type', $snType,
            array(
                'template' => '&nbsp;&nbsp;&nbsp;{input} {label}',
                'separator' => '&nbsp;&nbsp;',
                'disabled' => $approved ,
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;')));?>
        <?php echo $form->error($model, 'sn_type'); ?>
    </div>

    <div class="row-fluid">
        <div class="span5">
            <?php echo $form->labelEx($model, 'coupon_rules'); ?>
            <?php
            $coupon_rules = array();
            for ($i = 10; $i <= 18; $i++) {
                $coupon_rules[$i] = $i;
            }
            ?>
            <?php
            $code_num_value = (!empty($ext) && isset($ext['code_num'])) ? $ext['code_num'] : '';
            echo CHtml::dropDownList('ext[code_num]', $code_num_value, $coupon_rules, array('class' => 'span9', 'disabled' => $approved, 'value' => $code_num_value)); ?>
            <?php echo $form->error($model, 'coupon_rules'); ?>
        </div>


        <div class="span6">
            <?php echo $form->labelEx($model, 'issued', array("id" => "issued_id")); ?>
            <?php echo $form->textField($model, 'issued', array('class' => 'span9' , 'readonly' => $approved)); ?>
            <?php echo $form->error($model, 'issued'); ?>
        </div>
    </div>


    <div class="row-fluid" id="area_code">
        <div class="span5">
            <?php echo CHtml::label('编号位数', ''); ?>
            <?php
            $num = array();
            for ($i = 6; $i <= 20; $i++) {
                $num[$i] = $i;
            }
            $num_value = (!empty($ext) && isset($ext['num'])) ? $ext['num'] : '';
            echo CHtml::dropDownList('ext[num]', $num_value, $num, array('class' => 'span9' ,'disabled' => $approved)); ?>
        </div>
        <div class="span6">
            <?php echo CHtml::label('编号前缀', ''); ?>
            <?php
            $num_prdfix_value = (!empty($ext) && isset($ext['num_prdfix'])) ? $ext['num_prdfix'] : '';
            echo CHtml::textField('ext[num_prdfix]', $num_prdfix_value, array('class' => 'span9' , 'readonly' => $approved)) ?>
        </div>
    </div>

    <div class="row-fluid" id="fixed_code" style="display: none">
        <div class="span3"><input type="button" value="生成" name="yt0" class="btn btn-primary" id="generate_coupon">
        </div>
        <div class="span3"><input type="button" value="校验" name="yt1" class="btn" id="coupon_check"></div>
    </div>
</div>


<div class="span3">
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'user_limited'); ?>
        <?php echo $form->dropDownList($model, 'user_limited', Dict::items('user_limited')); ?>
        <?php echo $form->error($model, 'user_limited'); ?>
    </div>


    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'repeat_limited'); ?>
        <?php echo $form->dropDownList($model, 'repeat_limited', Dict::items('repeat_limited')); ?>
        <?php echo $form->error($model, 'repeat_limited'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'channel_limited'); ?>
        <?php echo $form->dropDownList($model, 'channel_limited', Dict::items('channel_limited')); ?>
        <?php echo $form->error($model, 'channel_limited'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'bonus_use_limit'); ?>
        <?php echo $form->dropDownList($model, 'bonus_use_limit', Dict::items('bonus_use_limit')); ?>
        <?php echo $form->error($model, 'bonus_use_limit'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'is_bonus_sn'); ?>
        <?php echo $form->dropDownList($model, 'is_bonus_sn', Dict::items('is_bonus_sn')); ?>
        <?php echo $form->error($model, 'is_bonus_sn'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'effective_date'); ?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array(
            'name' => 'BonusCode[effective_date]',
            // additional javascript options for the date picker plugin
            'mode' => 'datetime',
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => 'yy-mm-dd',
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;',
            ),
            'value' =>  isset($model->effective_date)?$model['effective_date']:date("Y-m-d H:i"),
            'language' => 'zh',
        ));
        ?>
        <?php echo $form->error($model, 'effective_date'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'binding_deadline'); ?>
        <?php
        $this->widget('CJuiDateTimePicker', array(
            'name' => 'BonusCode[binding_deadline]',
            'mode' => 'datetime',
            // additional javascript options for the date picker plugin
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => 'yy-mm-dd',
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;',
            ),
            'value' => isset($model->binding_deadline)?$model['binding_deadline']:date("Y-m-d 23:59", strtotime("12 month")),
            'language' => 'zh',
        ));
        ?>
        <?php echo $form->error($model, 'binding_deadline'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->checkBox($model, 'back_type' ,array( 'disabled' => $approved)) ?>
        <?php echo CHtml::label('按消费金额返还', 'BonusCode_back_type', array('class' => 'checkbox inline', 'style' => 'padding-left:0px;')) ?>
    </div>

    <div class="row-fluid" style="margin-top:5px;">
        <?php echo $form->checkBox($model, 'ismerchants' ,array( 'disabled' => $approved)) ?>
        <?php echo $form->labelEx($model, 'ismerchants', array('class' => 'checkbox inline', 'style' => 'padding-left:0px;')); ?>
    </div>

</div>


<div class="span3">
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'end_date'); ?>
        <?php echo CHtml::radioButton('ext[deadline_type]', 'deadline_type', array('value' => 0)) .
            '<label for="BonusCode_deadline_type_0" class="radio inline" style="padding-left:2px;">绑 定 后</label>&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
        <?php echo $form->textField($model, 'end_day', array('style' => 'margin-top:15px;width:100px;')) . '&nbsp;&nbsp;天<br />'; ?>
    </div>
    <div class="row-fluid">
        <?php echo CHtml::radioButton('ext[deadline_type]', 'deadline_type', array('value' => 1)) .
            '<label for="BonusCode_deadline_type_1" class="radio inline" style="padding-left:2px;">固定日期</label>&nbsp;&nbsp;&nbsp;'; ?>
        <?php
        $this->widget('CJuiDateTimePicker', array(
            'name' => 'BonusCode[end_date]',
            'mode' => 'datetime',
            // additional javascript options for the date picker plugin
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => 'yy-mm-dd',
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;width:125px;margin-top:15px;',
            ),
            'value' =>(isset($model->end_date)&& strtotime($model->end_date) > 0 )?$model['end_date']:date("Y-m-d 23:59", strtotime("13 month")),
            'language' => 'zh',
        ));
        ?>
        <?php echo $form->error($model, 'end_date'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'Introducte'); ?>
        <?php echo $form->textArea($model, 'Introducte', array('size' => 60, 'maxlength' => 200, 'style' => 'width: 205px; height: 60px;')); ?>
        <?php echo $form->error($model, 'Introducte'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'remark'); ?>
        <?php echo $form->textArea($model, 'remark', array('size' => 60, 'maxlength' => 200, 'style' => 'width: 205px; height: 90px;')); ?>
        <?php echo $form->error($model, 'remark'); ?>
    </div>
</div>

<div class="span3">
    <?php echo $form->labelEx($model, '短信内容（最多<font color="red">69</font>个字）'); ?>
    <?php echo $form->textArea($model, 'sms', array('size' => 60, 'maxlength' => 69, 'style' => 'height: 120px;', 'class' => 'span11')); ?>
    <?php echo $form->error($model, 'sms'); ?>
    <?php echo $form->label($model, '参考信息内容') ?>
    <p id="sms_content" class="span11">已为您绑定
        <span class="coupon_money_class">0</span>元优惠券，
        <span id="user_limit">该券限新用户使用，</span>
        <span id="channel_limit">限APP使用，</span>
        <span id="time_limit">使用有效期至<?php echo date("Y-m-d", strtotime("+13 months")); ?></span>。使用时，主动减免
        <span class="coupon_money_class">0</span>元。APP下载地址：http://t.cn/zjyUSmt
    </p>
</div>

<div class="row-fluid" style="margin-top:20px;">
    <div class="span12">
        <div class="span4"></div>
        <div class="span8">
            <?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存', array('class' => 'btn btn-primary span3', 'id' => 'BonusCodeSbt')); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo CHtml::resetButton('取消', array('class' => 'btn btn-danger span3')) ?>
        </div>
    </div>
</div>
<!-- form -->

<?php $this->endWidget(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        <?php
            $code_num_value = (!empty($ext) && isset($ext['deadline_type'])) ? $ext['deadline_type'] : 1;
            $sn_type = !empty($model) ? $model->sn_type : 1;
        ?>
        var deadline_type = "<?php echo $code_num_value; ?>";
        $("input[name='ext[deadline_type]']").get(deadline_type).checked = true;

        $sn_type = "<?php echo $sn_type; ?>";
        sn_type($sn_type, 1);

        $("#BonusCodeSbt").click(function () {
            //金额
            var BonusCode_money = $("#BonusCode_money").val();
            if (BonusCode_money == '' || BonusCode_money == 0) {
                alert("金额不能为 0！");
                return false;
            }

            //张数
            var sn_type = $("input[name='BonusCode[sn_type]']:checked").val();
            var BonusCode_issued = $("#BonusCode_issued").val();
            if (sn_type != 1 && BonusCode_issued == 0) {
                alert("生成数量不能为 0！");
                return false;
            }

            //短信内容
            var BonusCode_sms = $("#BonusCode_sms").val();
            if (BonusCode_sms == '') {
                alert("短信内容不能为空");
                return false;
            }
        });

        //金额控制
        $("#BonusCode_money").keyup(function () {
            $(".coupon_money_class").html($("#BonusCode_money").val());
        });
        $("#BonusCodeSbt").click(function(e){
            var deadline_type = $("input[name='ext[deadline_type]']:checked").val();
            if (deadline_type == 0) {
                if( $("#BonusCode_end_day").val()<=0) {
                    alert("使用截止时间处，请填写大于0的正整数");
                    $("#BonusCode_end_day").val('') ;
                    e.preventDefault();
                }
            }
        });

        //使用截止时间
        $("input[name='ext[deadline_type]']").change(function () {
            var deadline_type = $("input[name='ext[deadline_type]']:checked").val();
            if (deadline_type == 0) {
                $("#BonusCode_end_day").focus();
                $("#time_limit").html("绑定后" + $("#BonusCode_end_day").val() + "天内有效");
            } else {
                $("#BonusCode_end_date").focus();
                $("#time_limit").html("使用有效期至<b id = 'end_date'>" + $("#BonusCode_end_date").val() + "</b>");
            }
        });

        $("#BonusCode_end_day").keyup(function () {
            $("input[name='ext[deadline_type]']").get(0).checked = true;
            $("#BonusCode_end_date").val("");
            $("#time_limit").html("绑定后" + $("#BonusCode_end_day").val() + "内有效");
        });

        $("#BonusCode_end_date").focus(function () {
            $("input[name='ext[deadline_type]']").get(1).checked = true;
            $("#BonusCode_end_day").val("");
            $("#time_limit").html("使用有效期至<b id = 'end_date'>" + $("#BonusCode_end_date").val() + "</b>");
        });

        $("#BonusCode_channel_limited").change(function () {
            var channel_limited = $("#BonusCode_channel_limited").val();
            if (channel_limited == 1) {
                $("#channel_limit").html($("#BonusCode_channel_limited").find("option:selected").text() + '，');
            } else {
                $("#channel_limit").html("");
            }

        });
        $("#BonusCode_user_limited").change(function () {
            var channel_limited = $("#BonusCode_user_limited").val();
            if (channel_limited != 0) {
                $("#user_limit").html($("#BonusCode_user_limited").find("option:selected").text() + '，');
            } else {
                $("#user_limit").html("");
            }

        });

        $("input[name='BonusCode[sn_type]']").change(function () {
            var type = $("input[name='BonusCode[sn_type]']:checked").val();
            sn_type(type);
        });

        //自动生成固定优惠码触发点击事件
        $('#generate_coupon').live('click', function () {
            var snType = $('#BonusCode_sn_type_1').val();
            var couponLen = $("#ext_code_num").val();
            check_code(snType, couponLen);

        });

        //校验验证码
        $('#coupon_check').live('click', function () {

            var snType = $('#BonusCode_sn_type_1').val();
            var couponLen = $("#ext_code_num").val();
            var bonus_sn = $("#BonusCode_issued").val();
            if (bonus_sn == '') {
                alert("优惠码不能为空");
                return false;
            }
            if (bonus_sn.length != couponLen) {
                alert("选择的优惠码长度不符");
                return false;
            }
            check_code(snType, couponLen, bonus_sn);

        });
    });

    function sn_type(type, status) {
        if (type == 0 || type == 2) {
            $("#issued_id").html("生成数量");
            $("#ext_code_num option[value='4']").remove();
            $("#ext_code_num option[value='5']").remove();
            if (status == 0) {
                $("#BonusCode_issued").val("");
            }

            $("#area_code").show();
            $("#fixed_code").hide();
        } else {
            $("#issued_id").html("优惠码");
            var str = "<option value='4'>4</option> <option value='5'>5</option>";
            $("#ext_code_num").prepend(str).val(4);
            $("#area_code").hide();
            $("#fixed_code").show();
        }
    }

    //优惠码生成和校验
    function check_code(snType, couponLen, bonus_sn) {
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/bonusCode/ajax_check_code');?>',
            'data': {'sn_type': snType, 'coupon_rules': couponLen, 'bonus_sn': bonus_sn},
            'type': 'get',
            'dataType': 'json',
            'cache': false,
            'beforeSend': function () {
                $(this).attr('disabled', true);
            },
            'success': function (data) {
                $('#BonusCode_issued').val(data.coupon_code);
                alert(data.msg);
            },
            'error': function (data) {
                alert(data.msg);
            },
            'complete': function () {
                $(this).attr('disabled', false);
            }
        });
    }


</script>