<style type="text/css">
    .row-fluid * {
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        -o-box-sizing: border-box;
    }

    .text-right {
        text-align: right;
    }

    .row-fluid table {
        width: 100%;
    }

    .row-fluid table tr {
    }

    .row-fluid table {
        width: 100%;
    }

    .row-fluid table tr {
        height: 50px;
    }

    .row-fluid table {
        width: 100%;
    }

    .row-fluid table tr {
        height: 50px;
    }

</style>
<div class="form span11">
    <div class="row-fluid">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <label>输入问卷标题<span class="required">*</span>：</label>
                </td>
                <td>
                    <textarea id="txtInvestTitle" name="investTitle" rows="1"></textarea>
                </td>
                <td>
                    <label class="text-right">输入问卷描述<span class="required">*</span>：</label>
                </td>
                <td>
                    <textarea id="txtInvestDesc" name="investDesc" rows="1"></textarea>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="radio" name="presentCoupon" value="0" checked="checked"/>
                    <span>不赠送优惠券</span>
                </td>
                <td colspan="2">
                    <input type="radio" name="presentCoupon" value="1"/>
                    <span>赠送优惠券</span>
                </td>
            </tr>

            <tr>
                <td>
                    <label>优惠券金额<span class="required">*</span>:</label></td>
                <td>
                    <input type="text" name="coupon"/><span>元</span>
                </td>
                <td>
                    <label class="text-right">优惠券码<span class="required">*</span>:</label>
                </td>
                <td>
                    <input type="text" name="coupon_code"/>
                </td>
            </tr>

            <tr>
                <td colspan="4">发送策略:</td>
            </tr>

            <tr>
                <td colspan="4">
                    <span>是否发送给VIP用户:</span> <span> <input type="checkbox" name="cbVip"></span>
                </td>
            </tr>

            <tr>
                <td>
                    <label>单次发送分数:</label>
                </td>
                <td>
                    <input type="text" name="txtSendLimit"><span>份</span>
                </td>
                <td>
                    <label class="text-right">回收上限:</label>
                </td>
                <td>
                    <input type="text" name="txtReplyLimit"><span>份</span>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <?php echo CHtml::label('起始时间', 'create_time');
                    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                    $this->beginWidget('CJuiDateTimePicker', array(
                        'name' => 'start_time',
                        'value' => '',
                        'mode' => 'datetime',  //use "time","date" or "datetime" (default)
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        ),
                        'language' => 'zh',
                        'htmlOptions' => array(
                            'placeholder' => "起始时间",
                        ),
                    ));
                    $this->endWidget();
                    ?>
                </td>
                <td colspan="2">
                    <?php echo CHtml::label('结束时间', 'create_time');
                    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                    $this->beginWidget('CJuiDateTimePicker', array(
                        'name' => 'end_time',
                        'value' => '',
                        'mode' => 'datetime',  //use "time","date" or "datetime" (default)
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        ),
                        'language' => 'zh',
                        'htmlOptions' => array(
                            'placeholder' => "结束时间",
                        ),
                    ));
                    $this->endWidget();
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <!--问题列表-->
    <div id="question">

    </div>
    <input type="button" value="增加问题" class="btn btn-success" id="addQuestion">


    <div id="hiddenMainOption" style="display: none"></div>
    <div id="hiddenSubOption" style="display: none"></div>
    <br>

    <div>
        <span>选择城市:</span>
        <input type="checkbox" name="cbAllCity" value="0"/> <span>全部城市</span>
    </div>
    <br>

    <div class="row-fluid">
        <div class="span1">　</div>
        <div class="span10">
            <?php
            $city = explode(',', $model->city_ids);
            $citys = Dict::items('city');
            unset($citys[0]);
            foreach ($citys as $key => $item) {
                if (mb_strlen($item, 'utf-8') == 2) {
                    $item = $item . '　';
                }
                echo CHtml::checkBox("city[]", false, array("value" => $key, 'class' => 'city_id', 'id' => $key)) . '　' . $item . '　';

                if ($key % 10 == 0) {
                    echo '<br/>';
                }
            }
            ?>
        </div>
    </div>
    <br>
    <input type="submit" value="保存" class="btn btn-success" id="submit_btn">
</div>

<script type="text/javascript">
    $(function () {
        loadMainType();
        $('input[name="coupon"]').parent().parent().hide();
        // 问题编号
        var questionNo = 1;

        $("#addQuestion").click(function () {
            var questionLen = $('#question>table').length + 1;
            var mainOptionTemplate = $("#hiddenMainOption").val();
            var subOptionTemplate = $("#hiddenSubOption").val();
            var templateDiv = '' +
                '<table><tr name="hr"><td><hr style="border:1px dotted #036" /></td><td><hr style="border:1px dotted #036" /></td></tr><tr>' +
                '<td>(Q' + questionLen + ')输入问题</td>' +
                '<td><input type="button" class="delQuestion btn"  value="删除问题"/><td>' + '</tr>' +
                '<tr name="questionType">' +
                '<td>问题类型:<select class="complain_main_type" name="complain_main_type" style="width: 120px;">' + mainOptionTemplate + '</select>' +
                '<select class="complain_sub_type" name="complain_sub_type" style="width: 120px;">'+subOptionTemplate+'</select></td>' +
                '</tr>' +
                '<tr name="questionTr">' +
                '<td><span>问题描述:</span><input type="text" placeholder="问题描述"/></td>' +
                '<td><input type="button" class="addOption btn"  value="添加选项"/></td></tr>' +
                '<tr name="optionTr">' +
                '</tr></table>';
            $("#question").append(templateDiv);
            questionNo++;
            var select = $
        });

        $(".delQuestion").live('click', function () {
            $(this).parent().parent().parent().parent().remove();
        });

        $(".addOption").live('click', function () {
            var optionLen = $(this).parent().parent().siblings("tr[name='optionTr']").find('td').length;
            optionLen += 1;
            var templateDiv =
                '<td> ' +
                '选项' + optionLen + ':' +
                '<input type="text" name="option"/>' +
                '<input type="button" class="optionDel btn" value="删除选项"/>' +
                '</td>';
            $(this).parent().parent().siblings("tr[name='optionTr']").append(templateDiv);
        });

        $(".optionDel").live('click', function () {
            $(this).parent().remove();
        });

        $('input[name="cbAllCity"]').click(function () {
            if ($(this).is(':checked')) {
                $(".city_id").attr("checked", "checked");
            } else {
                $(".city_id").removeAttr("checked");
            }
        });

        $('.city_id').click(function () {
            $('input[name="cbAllCity"]').removeAttr("checked");
        });

        $(".complain_main_type").live('change', function () {
            var postUrl = '<?php echo Yii::app()->createUrl('/invest/investComplainSub');?>';
            var main_type = $(this).val();
            var thisObj = $(this);
            thisObj.attr('value', main_type);
            var subObj = $(this).siblings();
            $.ajax({
                url: postUrl,
                data: {'main_type': main_type},
                type: 'post',
                success: function (msg) {
                    var jsonData = eval(msg);
                    subObj.empty();
                    var template = '';
                    $.each(jsonData, function (index, val) {
                        template += '<option value="' + val.id + '">' + val.name + '</option>';
                    });
                    subObj.append(template);
                }
            });
        });


        $("#submit_btn").click(function () {
            var investTitle = $.trim($('#txtInvestTitle').val());
            if (investTitle == '') {
                alert("请填写问卷标题");
                return;
            }

            var investDesc = $.trim($('#txtInvestDesc').val());
            var presentCoupon = $('input[name= "presentCoupon"]:checked').val();
            var coupon = $('input[name="coupon"]').val();
            var couponCode = $('input[name="coupon_code"]').val();

            var cbVip = $('input[name="cbVip"]').attr('checked');
            if (cbVip == 'checked') {
                cbVip = 1;
            } else {
                cbVip = 0;
            }

            var sendLimit = $.trim($('input[name="txtSendLimit"]').val());
            var replyLimit = $.trim($('input[name="txtReplyLimit"]').val());

            sendLimit = sendLimit == '' ? 0 : sendLimit;
            replyLimit = replyLimit == '' ? 0 : replyLimit;

            var start_time = $.trim($('#start_time').val());
            var end_time = $.trim($('#end_time').val());
            // 问题数组
            var questionArr = new Array();

            $("#question > table").each(function () {
                // get questtion title
                var question = new Object();
                var complainMainType = $('.complain_main_type', $(this).find('tr[name="questionType"]')).val();
                var complainSubType = $('.complain_sub_type', $(this).find('tr[name="questionType"]')).val();
                var questionDesc = $('input[type=text]', $(this).find('tr[name="questionTr"]')).val();

                question.question = questionDesc;
                question.mainType = complainMainType;
                question.subType = complainSubType;
                var optionArr = new Array();

                var optionLen = $(this).find('tr[name="optionTr"]>td').length;
                $(this).find('tr[name="optionTr"]>td').each(function () {
                    var optionDesc = $('input[type="text"]', $(this)).val();
                    var optionObject = new Object();
                    optionObject.desc = optionDesc;
                    optionArr.push(optionObject);
                });
                // 选项数组
                question.option = optionArr;
                questionArr.push(question);
            });
            // 城市id
            var cityIds = "";
            if ($('input[name="cbAllCity"]').is(':checked')) {
                cityIds = "0";
            } else {
                $('.city_id:checked').each(function () {
                    cityIds += $(this).val() + ",";
                });
                var length = cityIds.length;
                cityIds = cityIds.substring(0, length - 1);
            }

            var postUrl = '<?php echo Yii::app()->createUrl('/invest/investAdd');?>';
            $.ajax({
                type: "POST",
                url: postUrl,
                data: {
                    'title': investTitle,
                    'desc': investDesc,
                    'cbVip': cbVip,
                    'sendLimit': sendLimit,
                    'replyLimit': replyLimit,
                    'startTime': start_time,
                    'endTime': end_time,
                    'cityId': cityIds,
                    'coupon': coupon,
                    'couponCode': couponCode,
                    'presentCoupon': presentCoupon,
                    'questionArr': questionArr
                },
                success: function (msg) {
                    if (msg == 0) {
                        alert('保存成功');
                        var locationUrl = '<?php echo Yii::app()->createUrl('/invest/investList');?>';
                        window.location.href = locationUrl;
                    }
                }
            });
        });

        $(":radio").click(function () {
            var status = $(this).val();
            if (status == 0) {
                $('input[name="coupon"]').parent().parent().hide();
                $('input[name="coupon_code"]').parent().parent().hide();
            } else {
                $('input[name="coupon"]').parent().parent().show();
                $('input[name="coupon_code"]').parent().parent().show();
            }
        });
    });

    function loadMainType() {
        var postUrl = '<?php echo Yii::app()->createUrl('/invest/investComplainMain');?>';
        var mainTemplate = '';
        var subTemplate = '';
        $.ajax({
            url: postUrl,
            data: {'main_type': 0},
            type: 'post',
            success: function (msg) {
                var jsonData = eval('('+msg+')');
                $.each(jsonData.main, function (index, val) {
                    mainTemplate += '<option value="' + val.id + '">' + val.name + '</option>';
                });

                $.each(jsonData.sub, function (index, val) {
                    subTemplate += '<option value="' + val.id + '">' + val.name + '</option>';
                });
                $("#hiddenMainOption").val(mainTemplate);
                $("#hiddenSubOption").val(subTemplate);
            }
        });
    }
</script>