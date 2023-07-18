<div class="form span11">
<div class="row-fluid">
    <table>
        <tr>
            <td>
                <label>输入问卷标题<span class="required">*</span>：</label>
            </td>
            <td>
                <input type="hidden" name="investId" value="<?php echo $invest->id; ?>">
                <textarea id="txtInvestTitle" name="investTitle"><?php echo $invest->title; ?> </textarea>
            </td>
            <td>
                <label>输入问卷描述<span class="required">*</span>：</label>
            </td>
            <td>
                <textarea id="txtInvestDesc" name="investDesc"><?php echo $invest->des; ?></textarea>
            </td>
        </tr>

        <tr>
            <td>
                <input type="radio" name="presentCoupon" value="0"
                    <?php echo $invest->coupon > 0 ? '' : 'checked="checked"'; ?>/>
                <span>不赠送优惠券</span>
            </td>
            <td>
                <input type="radio" value="1"
                       name="presentCoupon" <?php echo $invest->coupon > 0 ? 'checked="checked"' : ''; ?>/>
                <span>赠送优惠券</span>
            </td>
        </tr>

        <tr>
            <td>
                <label>优惠券金额<span class="required">*</span>:</label>
                <input type="text" name="coupon" value="<?php echo $invest->coupon; ?>"/><span>元</span>
            </td>
            <td>
                <label>优惠券码<span class="required">*</span>:</label>
                <input type="text" name="coupon_code" value="<?php echo $invest->coupon_code; ?>"/>
            </td>
        </tr>

        <tr>
            <td>发送策略:</td>
        </tr>

        <tr>
            <td>
                <input type="checkbox"
                       name="cbVip"  <?php echo $investRules->send_vip > 0 ? 'checked="checked"' : ''; ?>/> <span>是否发送给VIP用户:</span>
            </td>
        </tr>

        <tr>
            <td>
                <label>单次发送份数:</label>
            </td>
            <td>
                <input type="text" name="txtSendLimit"
                       value="<?php echo $investRules->send_per_time; ?>"><span>份</span>
            </td>
            <td>
                <label>回收上限:</label>
            </td>
            <td>
                <input type="text" name="txtReplyLimit"
                       value="<?php echo $investRules->reply_limit; ?>"><span>份</span>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo CHtml::label('起始时间', 'create_time');
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->beginWidget('CJuiDateTimePicker', array(
                    'name' => 'start_time',
                    'value' => $invest->start_time,
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
            <td>
                <?php echo CHtml::label('结束时间', 'create_time');
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->beginWidget('CJuiDateTimePicker', array(
                    'name' => 'end_time',
                    'value' => $invest->end_time,
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
    <?php
    $questionIndex = 1;

    $criteria = new CDbCriteria();
    $criteria->compare("status", 1);
    $criteria->select = 'id,name';
    $criteria->order = 'id asc';

    $criteria->compare('parent_id', 0);
    $mainTypeList = CustomerComplainType::model()->findAll($criteria);

    foreach ($mainTypeList as $mainTypeEach) {
        $mainTypeArr[] = array("id" => $mainTypeEach->id, "name" => $mainTypeEach->name,);
    }
    // 问题列表
    if (is_array($questionList) && count($questionList) > 0) {
        foreach ($questionList as $question) {
            echo '<table><tr><td>';
            echo '<hr style="border:1px dotted #036"/></td><td><hr style="border:1px dotted #036"/>';
            echo '</td></tr>';
            echo '<tr name="title"><td>(Q' . $questionIndex . ')输入问题:</td><td><input type="hidden" value="' . $question->id . '"/> ';
            echo '<input type="button" value="删除问题" class="delQuestion btn"></td></tr>';
            $mainType = $question->complain_main_type;
            $subType = $question->complain_sub_type;
            $criteria = new CDbCriteria();
            $criteria->compare("status", 1);
            $criteria->select = 'id,name';
            $criteria->order = 'id asc';
            // 查询子分类
            $criteria->compare('parent_id', $mainType);
            $subTypeList = CustomerComplainType::model()->findAll($criteria);

            foreach ($subTypeList as $subTypeEach) {
                $subTypeArr[] = array("id" => $subTypeEach->id, "name" => $subTypeEach->name,);
            }
            echo '<tr name = "questionType" > ';
            echo '<td> 问题类型:';
            echo CHtml::dropDownList('complain_main_type', $mainType, CHtml::listData($mainTypeArr, 'id', 'name'), array('style' => 'width:120px;',));

            echo CHtml::dropDownList('complain_sub_type', $subType, CHtml::listData($subTypeArr, 'id', 'name'), array('style' => 'width:120px;',));
            echo '</td></tr>';
            echo '<tr name = "questionTr" > ';
            echo '<td><input type = "text" value = "' . $question->title . '" /></td> ';
            echo '<td><input type = "button"   class="addOption btn" value = "添加选项" ></td ></tr> ';

            // 选项列表
            $optionList = InvestOption::model()->findAll("question_id=:questionId", array(":questionId" => $question->id,));
            if ($optionList && count($optionList) > 0) {
                echo '<tr name = "optionTr" > ';
                $optionIndex = 1;
                foreach ($optionList as $option) {
                    echo '<td > 选项' . $optionIndex . ':';
                    echo '<input type="hidden" value="' . $option->id . '" />';
                    echo '<input type="text" value ="' . $option->title . '"/>';
                    echo '<input type="button"   class="delOption btn" value ="删除选项"/></td > ';
                    $optionIndex++;
                }
                echo '</tr>';
            } else {
                echo '<tr name="optionTr"></tr>';
            }
            echo '</table>';
            $questionIndex++;
        }
    }
    ?>
</div>
<input type="button" value="增加问题" class="btn btn-success" id="addQuestion">

<br>

<div>
    <span>选择城市:</span>
    <?php
    if (is_array($cityIds)) {
        echo CHtml::checkBox("cbAllCity", false, array());
    } else {
        echo CHtml::checkBox("cbAllCity", true, array());
    }
    ?>
    <span>全部城市</span>
</div>
<div class="row-fluid">
    <div class="span10">
        <?php
        $city = explode(',', $model->city_ids);
        $citys = Dict::items('city');
        $checkCityIds = $cityIds;
        unset($citys[0]);
        if (is_array($checkCityIds)) {
            foreach ($citys as $key => $item) {
                if (mb_strlen($item, 'utf-8') == 2) {
                    $item = $item . '';
                }
                if (in_array($key, $checkCityIds)) {
                    echo CHtml::checkBox("city[]", true, array("value" => $key, 'class' => 'city_id', 'id' => $key)) . '　' . $item . '　';
                } else {
                    echo CHtml::checkBox("city[]", false, array("value" => $key, 'class' => 'city_id', 'id' => $key)) . '　' . $item . '　';
                }

                if ($key % 10 == 0) echo ' <br/>';
            }
        } else {
            foreach ($citys as $key => $item) {
                if (mb_strlen($item, 'utf-8') == 2) {
                    $item = $item . '　';
                }
                echo CHtml::checkBox("city[]", true, array("value" => $key, 'checked' => 'checked', 'class' => 'city_id', 'id' => $key)) . '　' . $item . '　';
                if ($key % 10 == 0) echo '<br/>';
            }
        }
        ?>
    </div>
</div>

<input type="submit" value="保存" class="btn btn-success" id="submit_btn">
</div>
<!--分类列表-->
<div id="hiddenMainOption" style="display: none"></div>
<div id="hiddenSubOption" style="display: none"></div>
<script type="text/javascript">
$(function () {
    <?php
        if($invest->coupon == 0){
            echo '$(\'input[name="coupon"]\').parent().parent().hide();';
            echo '$(\'input[name="coupon_code"]\').parent().parent().hide();';
        }
    ?>
    loadMainType();
    // 问题编号
    var questionNo = 1;
    // 删除问题数组
    var questionIdDelArr = new Array();
    // 删除选项数组
    var optionIdDelArr = new Array();

    $("#addQuestion").live('click', (function () {
        var questionLen = $("#question>table").length + 1;
        var mainOptionTemplate = $("#hiddenMainOption").val();
        var templateDiv = '' +
            '<table><tr name="hr"><td><hr style = "border:1px dotted #036" /> <td> <hr style = "border:1px dotted #036" / > </td></tr>' +
            '<tr name = "title" > <td> (Q' + questionLen + ')输入问题 </td>' +
            '<td><input type = "hidden" name = "questionId" value = "0" / ><input type = "button" class="delQuestion btn" value = "删除问题"/><td>' +
            '</tr> ' +
            '<tr name = "questionType" > ' +
            '<td> 问题类型:<select name = "complain_main_type" style = "width: 120px;" > ' + mainOptionTemplate + '</select>' +
            '<select name="complain_sub_type" style="width: 120px;"></select></td>' +
            '</tr>' +
            '<tr name = "questionTr" > ' +
            '<td><input type = "text" placeholder = "问题描述" /> </td>' +
            '<td> <input type = "button" class="addOption btn" value = "添加选项"/></td> </tr>' +
            '<tr name = "optionTr" > ' +
            '</tr></table>';
        $("#question").append(templateDiv);
        questionNo++;
    }));

    $(".delQuestion").live('click', function () {
        var questionID = $(this).siblings().val();
        if (questionID != 0) {
            questionIdDelArr.push(questionID);
        }
        //$(this).parent().parent().parent().sibling('hr').remove();
        $(this).parent().parent().parent().parent().remove();
    });

    $(".addOption").live('click', function () {
        var optionLen = $(this).parent().parent().siblings("tr[name='optionTr']").find('td').length;
        optionLen += 1;
        var templateDiv = '<td >' +
            '选项' + optionLen + ':' +
            '<input type="hidden" name="optionIndex" value="0">' +
            '<input type="text" name="option"/>' +
            '<input type="button" class="delOption btn" value="删除选项"/>' +
            '</td >';
        $(this).parent().parent().siblings("tr[name='optionTr']").append(templateDiv);
    });

    $(".delOption").live('click', function () {
        var optionId = ($(this)).parent().find('input[type="hidden"]').val();
        if (optionId != 0) {
            optionIdDelArr.push(optionId);
        }
        $(this).parent().remove();
    });


    $('select[name="complain_main_type"]').live('change', function () {
        var postUrl = '<?php echo Yii::app()->createUrl('/invest/investComplainSub'); ?>';
        var main_type = $(this).val();
        var thisObj = $(this);
        thisObj.attr('value', main_type);
        var subObj = $(this).siblings();
        var template = '';
        $.ajax({
            url: postUrl,
            data: {'main_type': main_type},
            type: 'post',
            success: function (msg) {
                var jsonData = eval(msg);
                subObj.empty();
                $.each(jsonData, function (index, val) {
                    template += '<option value = "' + val.id + '">' + val.name + ' </option>';
                });
                subObj.append(template);
            }
        });
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

    $("#submit_btn").click(function () {
        var investId = $('input[name="investId"]').val();
        var investTitle = $.trim($('#txtInvestTitle').val());

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
            var questionId = $('input[type=hidden]', $(this).find('tr[name="title"]')).val();
            var mainType = $('select[name="complain_main_type"]', $(this).find('tr[name="questionType"]')).val();
            var subType = $('select[name="complain_sub_type"]', $(this).find('tr[name="questionType"]')).val();
            var questionDesc = $('input[type=text]', $(this).find('tr[name="questionTr"]')).val();
            question.title = questionDesc;
            question.index = questionId;
            question.mainType = mainType;
            question.subType = subType;
            var optionArr = new Array();

            var optionLen = $(this).find('tr[name="optionTr"]>td').length;
            $(this).find('tr[name="optionTr"]>td').each(function () {
                var optionIndex = $('input[type="hidden"]', $(this)).val();
                var optionDesc = $('input[type="text"]', $(this)).val();
                var optionObject = new Object();
                optionObject.index = optionIndex;
                optionObject.title = optionDesc;
                optionArr.push(optionObject);
            });
            // 选项数组
            question.option = optionArr;
            questionArr.push(question);
        });
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

        var postUrl = '<?php echo Yii::app()->createUrl('/invest/investAdd'); ?>';
        $.ajax({
            type: "POST",
            url: postUrl,
            data: {
                'investId': investId,
                'title': investTitle,
                'desc': investDesc,
                'presentCoupon': presentCoupon,
                'cbVip': cbVip,
                'sendLimit': sendLimit,
                'replyLimit': replyLimit,
                'startTime': start_time,
                'endTime': end_time,
                'cityId': cityIds,
                'coupon': coupon,
                'couponCode': couponCode,
                'questionArr': questionArr,
                'questionIdDelArr': questionIdDelArr,
                'optionIdDelArr': optionIdDelArr
            },
            success: function (msg) {
                if (msg == 0) {
                    alert('修改成功');
                    history.go(0);
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