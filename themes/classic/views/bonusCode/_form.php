<?php
/* @var $this BonusCodeController */
/* @var $model BonusCode */
/* @var $form CActiveForm */
?>



<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'bonus-code-form',
    'enableAjaxValidation' => false,
)); ?>

<!--	<p class="note">Fields with <span class="required">*</span> are required.</p>-->
<?php echo $form->errorSummary($model); ?>
<div class="row-fluid">
<div class="span3">
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 60, 'tabIndex' => 1)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'money'); ?>
        <?php echo $form->textField($model, 'money', array('tabIndex' => 2, 'value' => '')); ?>
        <?php echo $form->error($model, 'money'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'channel'); ?>
        <?php $channelList = Dict::items('bonus_channel');
        $channelList = array('99' => '请选择') + $channelList;
        echo $form->dropDownList($model, 'channel', $channelList, array('tabIndex' => 3)); ?>
        <?php echo $form->error($model, 'channel'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'sn_type'); ?>
        <?php
        // 过滤掉区域固定码
        $snType = Dict::items('bonus_sn_type');
        foreach ($snType as $k => $v) {
            if ($snType[$k] == '区域固定码') {
                unset($snType[$k]);
            }
        }
        ?>
        <?php echo $form->radioButtonList($model, 'sn_type', $snType,
            array(
                'tabIndex' => 4,
                'template' => '{input}{label}',
                'separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:2px;')));?>
        <?php echo $form->error($model, 'sn_type'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'coupon_rules'); ?>
        <?php //echo $form->textField($model,'coupon_rules',array('size'=>60,'maxlength'=>1000)); ?>
        <?php $coupon_rules = array(10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18) ?>
        <?php echo $form->dropDownList($model, 'coupon_rules', $coupon_rules, array('tabIndex' => 5)); ?>
        <?php echo $form->error($model, 'coupon_rules'); ?>
    </div>

    <div class="row-fluid" id="parent_percentage_ispassword">
        <!--<div class="span3" id="parent_BonusCode_percentage">
                    <?php /*echo CHtml::label('猜中比率 (%)','BonusCode_percentage', array('style' => 'width:90px;'));*/?>
                    <?php /*echo CHtml::textField('BonusCode[percentage]', 2, array('class' => 'span','tabIndex'=>7, 'readonly' => 'readonly'));*/?>
                </div>-->
        <div class="span3" id="parent_BonusCode_issued">
            <?php echo $form->labelEx($model, 'issued'); ?>
            <?php echo $form->textField($model, 'issued', array('class' => 'span', 'tabIndex' => 9, 'value' => '')); ?>
            <?php echo $form->error($model, 'issued'); ?>
        </div>
        <div class="span3" id="parent_BonusCode_ispassword" style="width:120px;padding-left:20px;">
            <?php echo CHtml::label('是否设置密码', 'BonusCode_ispassword'); ?>
            <?php echo CHtml::checkBox('BonusCode[ispassword]', false, array('tabIndex' => 8)); ?>
            <?php echo CHtml::label('设置密码', 'BonusCode_ispassword', array('class' => 'checkbox inline', 'style' => 'padding-left:0px;')); ?>
        </div>
    </div>

    <!--<div class="row-fluid" id="ispassword_generate">
                <!--<div class="span6" id="parent_BonusCode_issued">
                    <?php /*echo $form->labelEx($model,'issued'); */?>
                    <?php /*echo $form->textField($model,'issued', array('class' => 'span', 'tabIndex' => 9, 'value' => '')); */?>
                    <?php /*echo $form->error($model,'issued'); */?>
                </div>-->
    <!--<div class="span6" id="parent_BonusCode_generate" style="width: 120px;padding-left: 20px;">
                    <?php /*echo CHtml::label('优惠码总数','BonusCode_generate', array('style' => 'width:120px;'));*/?>
                    <?php /*echo CHtml::textField('BonusCode[generate]',200000,array('readonly' => "readonly", 'class' => "span", 'style' => 'width:120px;'));*/?>
                </div>
            </div>-->
    <div class="row-fluid">
        <div id="iseven_pwdnum" style="display: none">
            <div class="span6">
                <?php echo CHtml::label('密码位数', 'BonusCode_PwdNum'); ?>
                <?php $password_num = array(6 => 6, 7 => 7, 8 => 8) ?>
                <?php echo CHtml::dropDownList('BonusCode[PwdNum]', '', $password_num, array('style' => 'width:75px;')); ?>
            </div>
            <div class="span6" id="parent_BonusCode_iseven">
                <?php echo $form->label($model, '&nbsp;'); ?>
                <?php echo CHtml::checkBox('BonusCode[iseven]', false) ?>
                <?php echo CHtml::label('连号', 'BonusCode_iseven', array('class' => 'checkbox inline', 'style' => 'padding-left:0px;')) ?>
            </div>
        </div>
        <div id="btn_check_bonusCode"></div>
    </div>
</div>


<div class="span3">
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'user_limited'); ?>
        <?php echo $form->dropDownList($model, 'user_limited', Dict::items('user_limited'), array('tabIndex' => 10)); ?>
        <?php echo $form->error($model, 'user_limited'); ?>
    </div>


    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'repeat_limited'); ?>
        <?php echo $form->dropDownList($model, 'repeat_limited', Dict::items('repeat_limited'), array('tabIndex' => 11)); ?>
        <?php echo $form->error($model, 'repeat_limited'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'channel_limited'); ?>
        <?php echo $form->dropDownList($model, 'channel_limited', Dict::items('channel_limited'), array('tabIndex' => 12)); ?>
        <?php echo $form->error($model, 'channel_limited'); ?>
    </div>

    <div class="row-fluid">
        <?php /*echo $form->labelEx($model_city,'city_id'); */?><!--
                <?php /*echo $form->textField($model_city, 'city_id', array('value' => '不限城市' ,'readonly' => 'readonly', 'tabIndex' => 13));*/?>
                --><?php /*echo $form->error($model_city,'city_id'); */ ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'effective_date'); ?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $dayBegin = mktime(0, 0, 0, $month, $day, $year); //当天开始时间戳
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
                'tabIndex' => 14,
            ),
            'value' => date("Y-m-d H:i", $dayBegin),
            'language' => 'zh',
        ));
        ?>
        <?php echo $form->error($model, 'effective_date'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'binding_deadline'); ?>
        <?php
        $year = date("Y", strtotime('+1 years'));
        $month = date("m", strtotime('+1 years'));
        $day = date("d", strtotime('+1 years'));
        $dayEnd = mktime(23, 59, 59, $month, $day, $year);
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
                'tabIndex' => 15,
            ),
            'value' => date("Y-m-d H:i", $dayEnd),
            'language' => 'zh',
        ));
        ?>
        <?php echo $form->error($model, 'binding_deadline'); ?>
    </div>

    <div class="row-fluid">
        <?php /*echo $form->checkBox($model,'isconsumer')*/?><!--
                --><?php /*echo CHtml::label('是否可多次消费', 'BonusCode_isconsumer', array('class'=>'checkbox inline'))*/ ?>
        <?php echo $form->checkBox($model, 'back_type') ?>
        <?php echo CHtml::label('按消费金额返还', 'BonusCode_back_type', array('class' => 'checkbox inline', 'style' => 'padding-left:0px;')) ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->checkBox($model, 'ismerchants') ?>
        <?php echo $form->labelEx($model, 'ismerchants', array('class' => 'checkbox inline', 'style' => 'padding-left:0px;')); ?>
    </div>

</div>


<div class="span3">
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'end_date'); ?>
        <?php echo CHtml::radioButton('BonusCode[deadline_type]', 'deadline_type', array('value' => 0, 'id' => 'BonusCode_deadline_type_0')) . '<label for="BonusCode_deadline_type_0" class="radio inline" style="padding-left:2px;">绑 定 后</label>&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
        <?php echo $form->textField($model, 'end_day', array('value' => '', 'style' => 'margin-top:15px;width:100px;', 'tabIndex' => 16,)) . '&nbsp;&nbsp;天<br />'; ?>
    </div>
    <div class="row-fluid">
        <?php echo CHtml::radioButton('BonusCode[deadline_type]', 'deadline_type', array('value' => 1, 'id' => 'BonusCode_deadline_type_1')) . '<label for="BonusCode_deadline_type_1" class="radio inline" style="padding-left:2px;">固定日期</label>&nbsp;&nbsp;&nbsp;'; ?>
        <?php
        $year = date("Y", strtotime('+13 months'));
        $month = date("m", strtotime('+13 months'));
        $day = date("d", strtotime('+13 months'));
        $dayEnd = mktime(23, 59, 59, $month, $day, $year);
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
                'tabIndex' => 17,
            ),
            'value' => date("Y-m-d H:i", $dayEnd),
            'language' => 'zh',
        ));
        ?>
        <?php echo $form->error($model, 'end_date'); ?>
    </div>
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'remark'); ?>
        <?php echo $form->textArea($model, 'remark', array('size' => 60, 'maxlength' => 200, 'style' => 'width: 205px; height: 90px;', 'tabIndex' => 18)); ?>
        <?php echo $form->error($model, 'remark'); ?>
    </div>
</div>

<div class="span3">
    <?php echo $form->labelEx($model, 'sms'); ?>
    <?php echo $form->textArea($model, 'sms', array('size' => 60, 'maxlength' => 200, 'style' => 'height: 120px;', 'class' => 'span11', 'tabIndex' => 19)); ?>
    <?php echo $form->error($model, 'sms'); ?>
    <?php echo $form->label($model, '系统短信') ?>
    <p id="sms_content" class="span11" style="margin-left: 0px;">已为您绑定<span
            class="coupon_money_class">0</span>元优惠券，<span
            id="user_limit">该券限新用户使用，</span><span id="channel_limit">限APP使用，</span><span
            id="area_limit"></span><span
            id="time_limit">使用有效期至<?php echo date("Y-m-d", strtotime("+13 months")); ?></span>。使用时，主动减免<span
            class="coupon_money_class">0</span>元。APP下载地址：http://t.cn/zjyUSmt </p>
</div>
<?php echo CHtml::hiddenField('BonusCode[hidden_city]', '0') ?>
<?php echo CHtml::hiddenField('BonusCode[hidden_sms]') ?>
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
$(function () {
    $("#parent_BonusCode_issued > label").first().addClass('required').append('<span class="required">*</span>');

    $('#BonusCode_ispassword').live('click', function () {
        if ($(this).attr('checked') == 'checked') {
            $('#iseven_pwdnum').show();
        } else {
            $('#iseven_pwdnum').hide();
        }
    });
    $('#BonusCode_end_day,#BonusCode_end_date').change(function () {
        if ($('#BonusCode_deadline_type_0').attr('checked') == 'checked') {
            var end_day = $('#BonusCode_end_day').val();
            if (end_day == '') {
                end_day = 0;
            }
            $('#time_limit').text('使用有效期至绑定后' + end_day + "天");
        }
        if ($('#BonusCode_deadline_type_1').attr('checked') == 'checked') {
            $('#time_limit').text('使用有效期至' + $('#BonusCode_end_date').val());
        }
    });
    $('#BonusCode_deadline_type_0').click(function () {
        var end_day = $('#BonusCode_end_day').val();
        if (end_day == '') {
            end_day = 0;
        }
        $('#time_limit').text('使用有效期至绑定后' + end_day + "天");
    });
    $('#BonusCode_deadline_type_1').click(function () {
        var end_date = $('#BonusCode_end_date').val();
        $('#time_limit').text('使用有效期至' + end_date);
    });
    $('#bonus-code-form').submit(function () {
        if ($('#BonusCode_name').val() == '') {
            alert('优惠券名称不能为空');
            return false;
        }
        if ($('#BonusCode_money').val() == '') {
            alert('优惠金额不能为空');
            return false;
        }
        var channel_val = $("#BonusCode_channel option:selected").val();
        if (channel_val == 99) {
            alert('请选择发放渠道');
            return false;
        }
        if ($("#BonusCode_sn_type_1").attr('checked') == 'checked') {
            var couponNum = $("#BonusCode_coupon_num").val();
            if (couponNum == '') {
                alert('优惠码不能为空');
                return false;
            } else {
                var reg = new RegExp("^[0-9]*$");
                if (!reg.test(couponNum)) {
                    alert('优惠码必须填写数字');
                    return false;
                } else {
                    var coupon_rules = $('#BonusCode_coupon_rules option:selected').text();
                    if (couponNum.length != coupon_rules) {
                        alert('优惠码位数与规则不符');
                        return false;
                    }
                }
            }
        }
        var BonusCodeIssued = $('#BonusCode_issued').val();     //生成数量
//            var BonusCodeGenerate = $('#BonusCode_generate').val(); //优惠码总数
        var BonusCodeGenerate = 200000; //优惠码总数
        if (BonusCodeIssued == '') {
            alert('优惠券生成数量不能为空')
            $('#BonusCode_issued').focus();
            return false;
        }
        if (BonusCodeIssued > BonusCodeGenerate) {
            alert('生成数量不能多于200000');
            $('#BonusCode_issued').val(parseInt(BonusCodeGenerate));
            return false;
        }
        if ($('#BonusCode_sms').val() == '') {
            $('#BonusCode_hidden_sms').val($('#sms_content').text());
        }
        var effectiveDate = $('#BonusCode_effective_date').val();
        var bindingDeadline = $('#BonusCode_binding_deadline').val();
        if (effectiveDate.replace(/-/g, '') >= bindingDeadline.replace(/-/g, '')) {
            alert('优惠券生效时间不能大于绑定截止时间');
            return false;
        }
        if ($('#BonusCode_deadline_type_1').attr('checked') == 'checked') {
            var endDate = $('#BonusCode_end_date').val();
            if (effectiveDate.replace(/-/g, '') >= endDate.replace(/-/g, '')) {
                alert('优惠券生效时间不能大于使用截止日期');
                return false;
            }
            if (bindingDeadline.replace(/-/g, '') >= endDate.replace(/-/g, '')) {
                alert('优惠券绑定截止时间不能大于使用截止日期');
                return false;
            }
        }
        if ($('#BonusCode_deadline_type_0').attr('checked') == 'checked') {
            var end_day = $('#BonusCode_end_day').val();
            if (end_day == '') {
                alert('请填写优惠券绑定后的使用截止日期');
                $('#BonusCode_end_day').focus()
                return false;
            }
        }
    });

    $('#BonusCode_money').change(function () {
        $('.coupon_money_class').text($(this).val());
    });

    $('#BonusCode_user_limited, #BonusCode_channel_limited').change(function () {
        var userLimitValue = $('#BonusCode_user_limited option:selected').val();
        var channelLimitValue = $('#BonusCode_channel_limited option:selected').val()
        if (userLimitValue == 1) {
            $('#user_limit').text('该券限老用户使用，');
        } else if (userLimitValue == 2) {
            $('#user_limit').text('该券限新用户使用，');
        } else {
            $('#user_limit').text('');
        }
        if (channelLimitValue == 1) {
            if (userLimitValue == 0) {
                $('#channel_limit').text('该券限APP使用，');
            } else {
                $('#channel_limit').text('限APP使用，');
            }
        } else {
            $('#channel_limit').text('');
        }
    });

    //根据猜算比率计算优惠码生成总数
    /*$("#BonusCode_coupon_rules").live('change', function(){
     var generate = $('#BonusCode_percentage').val();
     if(generate != ''){
     var rules = $('#BonusCode_coupon_rules option:selected').text() - 3;
     var issued = 0;
     switch (rules){
     case 7:
     issued = 10000000;
     break;
     case 8:
     issued = 100000000;
     break;
     case 9:
     issued = 1000000000;
     break;
     case 10:
     issued = 10000000000;
     break;
     case 11:
     issued = 100000000000;
     break;
     case 12:
     issued = 1000000000000;
     break;
     case 13:
     issued = 10000000000000;
     break;
     case 14:
     issued = 100000000000000;
     break;
     case 15:
     issued = 1000000000000000;
     break;

     }
     if(generate > 100){
     generate = 100;
     $('#BonusCode_percentage').val(generate);
     }
     var  coupon_total = (generate / 100) * issued;
     $('#BonusCode_generate').val(parseInt(coupon_total));
     }else{
     $('#BonusCode_generate').val(0);
     }
     });*/

    //选择优惠码类型区域码触发事件
    $('#BonusCode_sn_type_0').click(function () {

        if ($(this).attr('checked') == 'checked') {
            //$('#iseven_pwdnum').show();
            $('#btn_check_bonusCode').hide();
            $('#BonusCode_coupon_rules').html('<option value="10">10</option>' +
                '<option value="11">11</option>' +
                '<option value="12">12</option>' +
                '<option value="13">13</option>' +
                '<option value="14">14</option>' +
                '<option value="15">15</option>' +
                '<option value="16">16</option>' +
                '<option value="17">17</option>' +
                '<option value="18">18</option>'
            );
            $('#parent_percentage_ispassword').html(
                /*'<div id="parent_BonusCode_percentage" class="span3">'+
                 '<label for="BonusCode_percentage" style="width:90px;">猜中比率 (%)</label>'+
                 '<input type="text" id="BonusCode_percentage" name="BonusCode[percentage]" value="2" readonly = "readonly" class="span" tabIndex = "7">'+
                 '</div>'+*/
                '<div id="parent_BonusCode_issued" class="span3">' +
                    '<label for="BonusCode_issued">生成数量</label>' +
                    '<input type="text"  id="BonusCode_issued" name="BonusCode[issued]" style="" class="span" tabIndex = "9" value="">' +
                    '</div>' +
                    '<div style="width:120px;padding-left:20px;" id="parent_BonusCode_ispassword" class="span3">' +
                    '<label for="BonusCode_ispassword" style="">是否设置密码</label>' +
                    '<input type="checkbox" id="BonusCode_ispassword" name="BonusCode[ispassword]" value="1" tabIndex = "8">' +
                    '<label for="BonusCode_ispassword" style="padding-left:0px;" class="checkbox inline">设置密码</label>' +
                    '</div>').show();

            /*$('#ispassword_generate').html(
             '<div id="parent_BonusCode_issued" class="span3">'+
             '<label for="BonusCode_issued">生成数量</label>'+
             '<input type="text"  id="BonusCode_issued" name="BonusCode[issued]" style="" class="span" tabIndex = "9" value="">'+
             '</div>'+
             '<div id="parent_BonusCode_generate" class="span3" style="width: 120px;padding-left: 20px;">'+
             '<label style="width:120px;" for="BonusCode_generate">优惠码总数</label>'+
             '<input readonly="readonly" class="span" style="width:120px;" type="text" value="200000" name="BonusCode[generate]" id="BonusCode_generate">'+
             '</div>'
             );*/
        }
    });
    //优惠码类型选择固定码的时候触发事件
    $('#BonusCode_sn_type_1').click(function () {
        if ($(this).attr('checked') == 'checked') {
            $('#BonusCode_coupon_rules').html('<option value="4">4</option>' +
                '<option value="5">5</option>' +
                '<option value="10">10</option>' +
                '<option value="11">11</option>' +
                '<option value="12">12</option>' +
                '<option value="13">13</option>' +
                '<option value="14">14</option>' +
                '<option value="15">15</option>' +
                '<option value="16">16</option>' +
                '<option value="17">17</option>' +
                '<option value="18">18</option>'
            );
            $('#parent_percentage_ispassword').html(
                '<label for="BonusCode_coupon_num">优惠码</label>' +
                    '<input type="text" id="BonusCode_coupon_num" name="BonusCode[coupon_num]" value=""  tabIndex="6">'
            );
            $('#iseven_pwdnum').hide();
            $('#btn_check_bonusCode').show();
            $('#btn_check_bonusCode').html(
                '<div class="span3">' +
                    '<input type="button" value="生成" name="yt0" class="btn btn-primary" id="generate_coupon"></div> &nbsp;&nbsp;&nbsp;&nbsp;' +
                    '<div class="span3"><input type="button" value="校验" name="yt1" class="btn" id="coupon_check"></div>'
            );
        }
    });

    //自动生成固定优惠码触发点击事件
    $('#generate_coupon').live('click', function () {
        var snType = $('#BonusCode_sn_type_1').val();
        var couponLen = $('#BonusCode_coupon_rules option:selected').text();
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/bonusCode/ajax_check_code');?>',
            'data': {'sn_type': snType, 'coupon_rules': couponLen},
            'type': 'get',
            'dataType': 'json',
            'cache': false,
            'beforeSend': function () {
                $(this).attr('disabled', true);
            },
            'success': function (data) {
                $('#BonusCode_coupon_num').val(data.coupon_code);
            },
            'error': function (data) {
                alert(data.msg);
            },
            'complete': function () {
                $(this).attr('disabled', false);
            }
        });
    });
    //校验优惠码触发点击事件
    $('#coupon_check').live('click', function () {
        var percentage = $('#BonusCode_coupon_num').val();
        var coupon_rules = $('#BonusCode_coupon_rules option:selected').text();
        if (percentage == '') {
            return false;
        }
        if (percentage.length != coupon_rules) {
            return false;
        }
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/bonusCode/ajax_check_code');?>',
            'data': {'percentage': percentage},
            'type': 'get',
            'dataType': 'json',
            'cache': false,
            'beforeSend': function () {
                $(this).attr('disabled', true);
            },
            'success': function (data) {
                alert(data.msg);
            },
            'error': function (data) {
                alert(data.msg);
            },
            'complete': function () {
                $(this).attr('disabled', false);
            }
        });
    });
    //弹窗
    $('#BonusCodeCity_city_id').focus(function () {
        $('#myModal').modal('show');
        if ($('#BonusCodeCity_city_0').attr('checked') == 'checked') {
            $('#BonusCodeCity_city_0').attr('ischecked', 'yes');
        }
    });

    //选择城市触发点击事件
    $("input[name='BonusCodeCity_city[]']").live('click', function () {
        //声明城市值列表空数组
        var city_list = new Array();
        //声明拼接数字串空字符
        var city_join = '';
        //声明城市名称空数组
        var city_name = new Array();
        //声明拼接城市名称空字符串
        var name_join = '';
        if ($(this).attr('id') == 'BonusCodeCity_city_0') {
            if ($(this).attr('ischecked') == 'yes') {

                $("input[name='BonusCodeCity_city[]']").removeAttr('checked');

                $('#BonusCode_hidden_city').val("");
                $("#BonusCodeCity_city_id").val("选择城市");

                $(this).attr('ischecked', 'no');

            } else {

                $("input[name='BonusCodeCity_city[]']").attr('checked', true);
                /* $("input[name='BonusCodeCity_city[]']:checked").each(function(){
                 city_list.push($(this).val());
                 });
                 city_join = city_list.join(',');*/

                $('#BonusCode_hidden_city').val(0);
                $("#BonusCodeCity_city_id").val('不限城市');
                $('#area_limit').text('');
                $(this).attr('ischecked', 'yes');
            }
        } else {
            var city_val = $('#BonusCode_hidden_city').val();
            var city_num = $("input[name='BonusCodeCity_city[]']").length;
            if ($(this).attr('checked') == 'checked') {
                if (city_val == '') {
                    $('#BonusCode_hidden_city').val($(this).val());
                    $("#BonusCodeCity_city_id").val($(this).next().text());
                    $("#area_limit").text('限在' + $(this).next().text() + '使用，');
                } else {
                    $("input[name='BonusCodeCity_city[]']:checked").each(function () {
                        city_list.push($(this).val());
                        city_name.push($(this).next().text());
                    });

                    if (city_list.length == city_num - 1) {
                        $('#BonusCodeCity_city_0').attr('checked', true);
                        city_list.push($('#BonusCodeCity_city_0').val());
                        $('#BonusCodeCity_city_0').attr('ischecked', 'yes');
                        $("#BonusCodeCity_city_id").val('不限城市');
                        $('#area_limit').text('');
                        $('#BonusCode_hidden_city').val(0);
                    } else {
                        city_list.sort(sortNumber);
                        city_join = city_list.join(',');
                        name_join = city_name.join(',');
                        $("#BonusCodeCity_city_id").val(name_join);
                        $('#BonusCode_hidden_city').val(city_join);
                        $('#area_limit').text('限在' + name_join + '使用，');
                    }
                }
            } else {
                if (city_val.length == 1 && $(this).val() == city_val) {
                    $('#BonusCode_hidden_city').val("");
                    $("#BonusCodeCity_city_id").val("选择城市");
                }
                $("input[name='BonusCodeCity_city[]']:checked").each(function () {
                    if ($(this).val() == 0) {
                        $(this).removeAttr('checked');
                        $(this).attr('ischecked', 'no');
                    } else {
                        city_list.push($(this).val());
                        city_name.push($(this).next().text());
                    }
                });
                city_join = city_list.join(',');
                name_join = city_name.join(',');
                $('#BonusCode_hidden_city').val(city_join);
                $("#BonusCodeCity_city_id").val(name_join);
                $('#area_limit').text('限在' + name_join + '使用，');
            }
        }
    });
})
//排序
function sortNumber(a, b) {
    return a - b
}
</script>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">选择城市</h3>
    </div>
    <div class="modal-body">
        <p>
            <?php
            $citys = Dict::items('city');
            $keyItems = array_keys($citys);

            echo CHtml::checkBoxList('BonusCodeCity_city', $keyItems, $citys, array('labelOptions' => array('class ' => 'checkbox inline', 'style' => 'padding-left:0px;'), 'separator' => '&nbsp;'))
            ?>
        </p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        <button id="save_city" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">确认</button>
    </div>
</div>