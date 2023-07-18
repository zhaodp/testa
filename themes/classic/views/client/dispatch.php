<?php
$this->pageTitle = '手工派单';
?>
<style type="text/css">
    .container-fluid {
        padding: 0px;
    }

    /*.row-fluid input {width:150px;}*/
</style>

<div class="row-fluid">
    <div class="span8">
        <?php echo $this->renderPartial('_dispatch_customer_info',
            array('vip' => $vip, 'vipPhone' => $vipPhone, 'driver' => $driver, 'customerInfo' => $customerInfo, 'appOrderNum' => $appOrderNum, 'a400OrderNum' => $a400OrderNum, 'otherOrderNum' => $otherOrderNum,'appBonusCount' => $appBonusCount,'firstOrderTime'=>$firstOrderTime,'commonBonusCount'=>$commonBonusCount)); ?>
    </div>
    <div class="span4">
        <?php echo $this->renderPartial('_dispatch_functions',
            array('model' => $model,
                'phone' => isset($_REQUEST['phone']) ? $_REQUEST['phone'] : null,
                'driver' => $driver)); ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span8 well" style="min-width:540px;">
        <?php echo $this->renderPartial('_dispatch_order',
            array('model' => $model, 'ringTime' => $ringTime, 'dispatchType' => $dispatchType)); ?>
    </div>
    <div class="span4">
        <input type="hidden" id="flag-bind-code">

        <?php
        if (isset($_REQUEST['phone'])) {
            $sms_list = SmsTemplate::model()->getListByType(SmsTemplate::CALLCENTER);
            // receive 1 全部 2 用户 3 司机 4 员工
            echo "<h5>优惠券绑定:</h5>";
            foreach ($bonusCodeList as $code) {
                ?>
                <div class="accordion-group" id="customized-sms-<?php echo $code['id'] ?>">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse"
                           href="#customized-sms-body-<?php echo $code['id'] ?>">
                            <?php echo $code['bonus_name'] ?>

                        </a>
                    </div>
                    <div class="accordion-body collapse" id="customized-sms-body-<?php echo $code['id'] ?>">
                        <div class="accordion-inner">
                            <p style="word-wrap:break-word; word-break:normal;">
                                <span>备注：</span><?php echo $code['remark'];
                                $url = $this->createUrl("bonusCode/user_bind_code", array('phone' => $_REQUEST['phone'], 'bonus_code_id' => $code['bonus_code_id']));
                                ?>
                            </p>
                            <a href="javascript:void(0)" class="btn btn-primary code-bind"
                               url="<?php echo $url ?>">绑定</a>
                        </div>
                    </div>
                </div>
            <?php
            }


            if (!empty($sms_list)) {
                echo '<div class="accordion" id="accordion_sms">';
                echo "<h5>短信模版:</h5>";
                ?>
                <div class="accordion-group" id="customized-sms">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" href="#customized-sms-body">
                            自定义短信
                        </a>
                    </div>
                    <div class="accordion-body collapse" id="customized-sms-body">
                        <div class="accordion-inner">
                            <p style="word-wrap:break-word; word-break:normal;"><textarea style="width:90%"
                                                                                          id="customized-message"
                                                                                          placeholder="自定义短信内容：最大100字"
                                                                                          maxlength="100"></textarea>
                            </p>
                            <a href="#">发送</a>
                        </div>
                    </div>
                </div>
                <?php
                foreach ($sms_list as $i) {
                    if ($i->receive == 2) {
                        ?>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_sms"
                                   href="#<?php echo $i->subject ?>">
                                    <?php echo $i->name ?>
                                </a>
                            </div>
                            <div id="<?php echo $i->subject ?>" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <p style="word-wrap:break-word; word-break:normal;"><?php echo $i->content ?></p>
                                    <a href="#" id="sendsms_<?php echo $i->subject; ?>"
                                       onclick="send_sms_template('<?php echo $_REQUEST['phone']; ?>','<?php echo $i->subject; ?>');">发送</a>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                }
                echo '</div>';
            }
        }?>
        <?php if (!empty($driver)) { ?>
            最近派单记录：
            <?php
            $start_time = strtotime(date('Y-m-d 07:00:00', time() - 7 * 60 * 60));
            $criteria = new CDbCriteria();
            $criteria->select = 'name, phone, contact_phone , vipcard, driver_id,call_time, booking_time, location_start, status';

            $criteria->with = array('order_queue_map' => array('select' => 'queue_id'));
            $criteria->condition = 't.driver_id=:driver_id and status=0 and created >=:created';
            $criteria->order = 'call_time desc';
            $criteria->params = array(':driver_id' => $driver->user, ':created' => $start_time);
            //$criteria->limit = 10;
            $dataProvider = new CActiveDataProvider('Order', array(
                'pagination' => array(
                    'pageSize' => 100),
                'criteria' => $criteria));

            echo $this->renderPartial('_dispatch_driver_orders', array('dataProvider' => $dataProvider));
            ?>
            <?php
            echo $this->renderPartial('_dispatch_driver_info', array('driver' => $driver));
            ?>
        <?php } else { ?>

            <?php if (isset($_REQUEST['phone'])) {
                if ($_REQUEST['phone'] == '02160951566') {  //增加雪弗兰客户电话标识 BY AndyCong 2014-01-22
                    ?>
                    <h3>雪弗兰合作客户</h3>
                <?php
                }
                ?>

                <div class="alert alert-info">
                    <a href="#" name="sendpricebtn" id="sendpricebtn"
                       onclick="sendprice('<?php echo $_REQUEST['phone']; ?>');">发送价格表</a>
                </div>
            <?php } ?>
            <?php echo $this->renderPartial('_client_queue', array('model' => $queue)); ?>
        <?php } ?>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="modalBody" name="modalBody">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->


<script type="text/javascript">
var city;
//城市下拉框改为弹出框后该change方法已经废弃
$('#OrderQueue_city_id').change(function () {
    autocomplete();
    local_search();
});


$(document).ready(function () {

    $("a[data-toggle=modal]").click(function () {
        var target = $(this).attr('data-target');
        var url = $(this).attr('url');
        var mewidth = $(this).attr('mewidth');
        if (mewidth == null) mewidth = '850px';
        if (url != null) {
            $('#myModal').modal('toggle').css({
                'width': mewidth, 'margin-left': function () {
                    return -($(this).width() / 2);
                }
            });
            $('#myModal').modal('show');
            $('#modalBody').load(url);
        }
        return true;
    });
    var hasEnter = 0;//阻止enter和blur事件的互相冲突
    var hasBlur = 0;
    $('input#OrderQueue_address').keydown(function (e) {
        if (e.keyCode == 13) {
            if (hasBlur > 0) {
                hasBlur = 0;
                return;
            }
            ++hasEnter;
            var _cityName = $("input[name='city_list']").val() + "市";//将搜索城市优先定位在所选的城市
            local.setLocation(_cityName);
            map.clearOverlays();
            local.search($(this).val());
        }
    });

    $('input#OrderQueue_address').blur(function (e) {
        if (hasEnter > 0) {
            hasEnter = 0;
            return false;
        }
        ++hasBlur;
        var _cityName = $("input[name='city_list']").val() + "市";//将搜索城市优先定位在所选的城市
        local.setLocation(_cityName);
        map.clearOverlays();
        local.search($(this).val());

    });

    $('body').delegate("input[name='sex']", "click", function (event) {
        var selectedSexName = $(event.target).val();
        var customerName = $("input#OrderQueue_name").val();
        // 如果客户名称已经包含了选择的尊称，就不必追加了
        if (customerName.match(selectedSexName + "$")) {
            return;
        }

        var newCustomerName;
        var unselectedSexName = $("input[name='sex']:not(:checked)").val();

        // 如果客户名称包含了相反性别的尊称，需要修改
        if (customerName.match(unselectedSexName + "$")) {
            newCustomerName = customerName.substring(0, customerName.length - 2) + selectedSexName;
        } else {
            newCustomerName = customerName + selectedSexName;
        }
        $("input#OrderQueue_name").val(newCustomerName);
    });

    //$('input#OrderQueue_address').focus();
    var cityName = $("input[name='city_list']").val();//初始化的时候得到该电话号码的城市名加载地图
    autocomplete(cityName);
    local_search(cityName);
})

function local_search(city) {
    /*城市下拉框改为弹出框后城市名由弹出框控件传过来
     city = $("#OrderQueue_city_id").find("option:selected").text();*/
    map.centerAndZoom(city, 15);
}

function autocomplete(location) {
    /*城市下拉框改为弹出框后城市名由弹出框控件传过来
     var location = $("#OrderQueue_city_id").find("option:selected").text();
     Autocomplete是结果提示、自动完成类。 new Autocomplete([options:AutocompleteOptions])
     */
    var ac = new BMap.Autocomplete(
        {
            "input": "OrderQueue_address",
            "location": location
        });

    /* 键盘或者鼠标移动，某条记录高亮之后，触发*/
    ac.addEventListener("onhighlight", function (e) {
        var str = "";
        if (e.fromitem.value) {
            var _value = e.fromitem.value;
        }
        var value = "";
        if (e.fromitem.index > -1) {
            value = _value.business;
        }

        value = "";
        if (e.toitem.index > -1) {
            _value = e.toitem.value;
            value = _value.business;
        }
        //$('input#OrderQueue_address').val(_value.business);
    });

    var myValue;
    //鼠标点击下拉列表后的事件
    ac.addEventListener("onconfirm", function (e) {
        var _value = e.item.value;
        //  _value.province +  _value.city +  _value.district +  _value.street +
        myValue = _value.business;
        $('input#OrderQueue_address').val(_value.business);
    });
}

function sendprice(phone) {
    if (phone == '') {
        alert('电话信息不正确，请重新派单。');
        return false;
    }

    $('#sendpricebtn').attr("onclick", 'alert("价格表已经在发送途中....")');

    if ($("#OrderQueue_city_id").val() == 0) {
        alert('未知城市！请先确定城市。');
        return false;
    }

    $.get("index.php", {r: 'client/sendprice', phone: phone, city_id: $("#OrderQueue_city_id").val()},
        function (data) {
            if (data == phone) {
                alert('价格表成功发送到手机' + phone);
            } else {
                alert('价格表发送不成功。');
                $('#sendpricebtn').attr("disabled", false);
            }
        });
}

function send_sms_template(phone, template) {
    if (phone == '') {
        alert('电话信息不正确，请重新派单。');
        return false;
    }

    if (template == '') {
        alert('短信模版不能为空');
        return false;
    }

    $('#sendsms_' + template).attr("onclick", 'alert("短信已经在发送途中....")');

    $.post("index.php", {r: 'client/sendSmsTemplate', phone: phone, template: template},
        function (data) {
            if (data == phone) {
                alert('成功发送到手机' + phone);
            } else {
                alert('发送不成功。');
            }
        });
}

function str2date(c_date) {
    if (!c_date)
        return "";
    var tempArray = c_date.split("-");
    if (tempArray.length != 3) {
        alert("你输入的日期格式不正确,正确的格式:2000-05-01 02:54:12");
        return 0;
    }
    var dateArr = c_date.split(" ");
    var date = null;
    if (dateArr.length == 2) {
        var yymmdd = dateArr[0].split("-");
        var hhmmss = dateArr[1].split(":");
        date = new Date(yymmdd[0], yymmdd[1] - 1, yymmdd[2], hhmmss[0], hhmmss[1], hhmmss[2]);
    } else {
        date = new Date(tempArray[0], tempArray[1] - 1, tempArray[2], 00, 00, 01);
    }
    return date;
}
;


function recordCallTimes(queue_id) {
    $.getJSON("index.php", {r: 'client/RecordCallTimes', qid: queue_id},
        function (data) {
            alert(data.msg);
        });
}
(function ($) {
    $("#customized-sms .accordion-inner a").click(function () {
        var message = $("#customized-message").val(), phone = '<?php if(isset($_REQUEST["phone"]))echo $_REQUEST["phone"]; ?>', username = '<?php echo Yii::app()->user->name;?>';
        if (message == '') {
            alert("消息不能为空。");
            return;
        }
        $.ajax({
            url: 'index.php?r=client%2FsendustomizeSms',
            data: {username: username, message: message, phone: phone},
            method: "get",
            success: function (res) {
                res = $.parseJSON(res);
                if (res.status == 0) {
                    alert('发送成功！');
                } else {
                    alert('发送不成功。')
                }
            },
            error: function () {
                alert('发送不成功。')
            }
        })
    });
})(jQuery);


$(function () {
    var TimeFn = null;
    $(".code-bind").click(function () {
        var current  = $(this);
        clearTimeout(TimeFn);
        TimeFn = setTimeout(function () {
            if(current.attr("class").indexOf("disabled")>0){
                return false;
            }
            current.addClass("disabled");
            var url = current.attr("url");
            if ($("#flag-bind-code").val() == 1) {
                alert("你刚刚已经绑定过优惠券了，不允许同一次来电再次绑定");
                return false;
            }
            $.getJSON(url, function (data) {
                if (data.code == 1) {
                    $("#flag-bind-code").val(1);
                    alert(data.result);
                    current.html("已绑定");
                }else if(data.code == 0){
                    alert(data.result);
//                    current.html("绑定").removeClass("disabled");
                }
                else {
//                    current.html("绑定").removeClass("disabled");
                    alert(data.result);

                }
            });
        }, 300);
    });
})

</script>
