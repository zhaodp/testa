var INNER_TEL_PREFIX = "0106414";
var currentStatus;
var durationTimerID;

setServerUrl("172.16.11.11");

$(document).ready(function() {// 页面加载完毕
    if (buttType) {
        if (typeof window.external.readyLoad != 'undefined') {
            window.external.readyLoad("ready");
        }
    } else {
        if (typeof window.parent.readyLoad != 'undefined') {
            window.parent.readyLoad("ready");
        }
    }
    initDialog();
    login();
});

$(window).bind('beforeunload', function() {
    var message = "确定离开此页面吗？";
    if (currentStatus !== "offline") {
        message += "如果离开，电话将会自动解绑。";
    }
    return message;
});

$(window).bind("unload", function() {
    logout();
});

function initDialog() {
    $("#consultDialog").dialog({
        title: "咨询目标",
        width: 380, 
        modal: true, 
        autoOpen: false,
        resizable: false,
        buttons: [
            { text: "确定", click: consult, "class" : "confirm-button", disabled: "disabled" },
            { text: "关闭", click: closeConsultDialog }
        ]
    });
    
    $("#transferDialog").dialog({
        title: "转移目标",
        width: 380, 
        modal: true, 
        autoOpen: false,
        resizable: false,
        buttons: [
            { text: "确定", click: transfer, "class" : "confirm-button", disabled: "disabled" },
            { text: "关闭", click: closeTransferDialog }
        ]
    });
    
    $("#consultDialog, #transferDialog").delegate("input[type=text]", "keyup", function(){
        var hasNumber = $(this).val().length > 0;
        $(this).closest(".ui-dialog").find(".confirm-button").prop("disabled", !hasNumber);
    });
}

function openConsultDialog() {
    executeAction('doHold');
    $("#consultDialog").dialog("open");
}

function closeConsultDialog() {
    executeAction('doUnhold');
    $("#consultDialog").dialog("close");
}

function openTransferDialog() {
    executeAction('doHold');
    $("#transferDialog").dialog("open");
}

function closeTransferDialog() {
    executeAction('doUnhold');
    $("#transferDialog").dialog("close");
}

function consult() {
    var params = {};
    params.objectType = $("input[name=consultType]:checked").val();
    params.consultObject = $.trim($("input[name=consultNumber]").val());
    closeConsultDialog();
    executeAction('doConsult', params);
}

function transfer() {
    var params = {};
    params.objectType = $("input[name=transferType]:checked").val();
    params.transferObject = $.trim($("input[name=transferNumber]").val());
    closeTransferDialog();
    executeAction('doTransfer', params);
}

function cbLogin(token) {// 登陆
    if (token.code == "0") {
        $("input[id=orderQueue]").prop("disabled", false);
        $("input[id=dispatchOrder]").prop("disabled", false);
        $("input[id=login_btn]").prop("disabled", true);
        $("input[id=logout_btn]").prop("disabled", false);
    } else {
        alert("登录失败！" + token.msg);
    }
}

function cbLogout(token) {// 退出
    if (buttType) {
        token = JSON.parse(token);
    }
    if (token.code == "0") {
        typeButton.buttonDisabled();
        $("#statusImg").css({
            "backgroundPosition" : "0px 0px"
        });
        $('#status').text("离线");
        $("input[id=orderQueue]").prop("disabled", true);
        $("input[id=dispatchOrder]").prop("disabled", true);
        $("input[id=login_btn]").prop("disabled", false);
        $("input[id=logout_btn]").prop("disabled", true);
        disableDurationTimer();
        currentStatus = "offline";
        alert("已退出");
    }
}

function cbBreakLine(json) {//断线
    if(json.msg != 'open'){
        if(json.attempts == '3'){
            alert("由于网络原因,无法连接天润系统,请确保网络正常后,重新登录");
        }
    }
}

function disableDurationTimer() {
    if (durationTimerID) {
        clearInterval(durationTimerID);
    }
    $("#durationCell").hide();
}

function cbWelcome(token) {// 连接成功

}

var typeButton = {
    phoneCallout : function() {// 外呼
        $("#phoneCallout").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#phoneCallout").unbind("mouseover mouseout click");
        $("#phoneCallout").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                var params = {};
                params.tel = $('#phoneCallText').val();
                params.callType = '3'; // 3点击外呼
                executeAction('doPreviewOutCall', params);
            }
        });
    },
    phoneCallText : function() {
        $("#phoneCallText").attr("disabled", false);
    },
    phoneCallCancel : function() {// 外呼取消
        $("#phoneCallCancel").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#phoneCallCancel").unbind("mouseover mouseout click");
        $("#phoneCallCancel").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doPreviewOutcallCancel');
            }
        });
    },
    refused : function() {// 拒接
        $("#refused").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#refused").unbind("mouseover mouseout click");
        $("#refused").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doRefuse');
            }
        });
    },
    unLink : function() {// 挂断

        $("#unLink").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#unLink").unbind("mouseover mouseout click");
        $("#unLink").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doUnLink');
            }
        });
    },
    hold : function() {// 保持
        $("#hold").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#hold").unbind("mouseover mouseout click");
        $("#hold").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doHold');
            }
        });
    },
    unHold : function() {// 保持接回

        $("#unHold").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#unHold").unbind("mouseover mouseout click");
        $("#unHold").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doUnhold');
            }
        });
    },
    investigation : function() {// 满意度调查
        $("#investigation").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#investigation").unbind("mouseover mouseout click");
        $("#investigation").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doInvestigation');
            }
        });
    },
    online : function() {// 空闲

        $("#online").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#online").unbind("mouseover mouseout click");
        $("#online").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doUnpause');
            }
        });
    },
    pause : function() {// 置忙

        $("#pause").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#pause").unbind("mouseover mouseout click");
        $("#pause").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                var param = {};
                param.description = "置忙";
                executeAction('doPause', param);

            }
        });
    },
    buttonDisabled : function() {// 按钮恢复状态
        $("#consultBack,#consultTransfer,#consultThreeway").hide();
        $("#toolbarButton input").css({
            "backgroundPosition" : "0px 0px"
        });
        $("#toolbarButton input").attr("disabled", true);
        $("#toolbarButton input").unbind("mouseover mouseout click");

    },
    consult : function() {// 咨询
        $("#consult").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#consult").unbind("mouseover mouseout click");
        $("#consult").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                openConsultDialog();
            }
        });
    },
    consultBack : function() {// 咨询接回
        $("#consultBack").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#consultBack").unbind("mouseover mouseout click");
        $("#consultBack").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doUnconsult');
            }
        });
    },
    consultTransfer : function() {// 咨询转接
        $("#consultTransfer").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#consultTransfer").unbind("mouseover mouseout click");
        $("#consultTransfer").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doConsultTransfer');
            }
        });
    },
    consultThreeway : function() {// 咨询三方
        $("#consultThreeway").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#consultThreeway").unbind("mouseover mouseout click");
        $("#consultThreeway").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                executeAction('doConsultThreeway');

            }
        });
    },
    transfer : function() {// 转移
        $("#transfer").show().attr("disabled", false).css({
            "backgroundPosition" : "0px -33px"
        });
        $("#transfer").unbind("mouseover mouseout click");
        $("#transfer").bind({
            mouseover : function() {
                $(this).css({
                    "backgroundPosition" : "0px -66px"
                });
            },
            mouseout : function() {
                $(this).css({
                    "backgroundPosition" : "0px -33px"
                });
            },
            click : function() {
                openTransferDialog();
            }
        });
    },
    answer : function() {// 接听 软电话功能
        if (userBasic.getBindType() == '3') {
            $("#answer").show().attr("disabled", false).css({
                "backgroundPosition" : "0px -33px"
            });
            $("#answer").unbind("mouseover mouseout click");
            $("#answer").bind({
                mouseover : function() {
                    $(this).css({
                        "backgroundPosition" : "0px -66px"
                    });
                },
                mouseout : function() {
                    $(this).css({
                        "backgroundPosition" : "0px -33px"
                    });
                },
                click : function() {
                    executeAction('doLink');
                }
            });
        }

    }
}
function cbThisStatus(token) {
    if (buttType) {
        token = JSON.parse(token);
    }
    switch (token.eventName) {
    case 'outRinging': // 外呼座席响铃
        if (token.name == "ringing") {
            typeButton.buttonDisabled();
            typeButton.answer(token);
            // $("#phoneCallout").hide();
            typeButton.phoneCallCancel();
            // 第三方代码
            top.CTI_ID = token.uniqueId;// 获取录音编号
        }
        break;
    case 'comeRinging': // 呼入座席响铃
        if (token.name == "ringing") {
            typeButton.buttonDisabled();
            typeButton.answer(token);
            typeButton.refused();
        }
        break;
    case 'normalBusy': // 呼入座席接听
        // 咨询失败后，会再进入正常通话状态，在此提示
        if (token.name === "consultError") {
            alert("咨询失败，回到正常通话状态");
        }
        
        typeButton.buttonDisabled();
        typeButton.unLink();
        typeButton.hold();
        typeButton.investigation();

        typeButton.consult();
        // typeButton.consultThreeway();
        // typeButton.consultTransfer();
        typeButton.transfer();
        break;
    case 'outBusy': // 外呼客户接听 客户和座席通话
        typeButton.buttonDisabled();
        typeButton.unLink();
        typeButton.hold();
        typeButton.investigation();

        typeButton.consult();
        // typeButton.consultThreeway();
        // typeButton.consultTransfer();
        // typeButton.transfer();
        break;
    case 'online': // 置闲
        typeButton.buttonDisabled();
        typeButton.phoneCallout();
        typeButton.phoneCallText();
        typeButton.pause();
        // $("#phoneCallCancel").hide();
        break;
    case 'pause': // 置忙
        typeButton.buttonDisabled();
        typeButton.phoneCallout();
        typeButton.phoneCallText();
        typeButton.online();
        // $("#phoneCallCancel").hide();
        break;
    case 'waitLink': // 外呼座席接听等待客户接听

        break;
    case 'neatenStart': // 整理开始（座席挂断）
        typeButton.buttonDisabled();
        typeButton.online();
        typeButton.pause();
        break;
    case 'neatenEnd': // 整理结束
        typeButton.buttonDisabled();
        typeButton.phoneCallout();
        typeButton.phoneCallText();
        var s = deviceStatus.deviceStatusLoginStatus(token.deviceStatus
                + token.loginStatus, token.pauseDescription,
                token.busyDescription);
        if (s == '空闲') {
            typeButton.pause();
        } else {
            typeButton.online();
        }
        break;
    case 'hold': // 保持开始
        typeButton.buttonDisabled();
        // $("#hold").hide();
        typeButton.unLink();
        typeButton.unHold();
        typeButton.investigation();
        break;
    case 'unHold': // 保持结束
        typeButton.buttonDisabled();
        // $("#unHold").hide();
        typeButton.unLink();
        typeButton.hold();
        typeButton.investigation();

        typeButton.consult();
        // typeButton.consultThreeway();
        // typeButton.consultTransfer();
        typeButton.transfer();

        break;
    case 'onlineUnlink': // 挂断后置闲
        typeButton.buttonDisabled();
        typeButton.phoneCallout();
        typeButton.phoneCallText();
        typeButton.pause();
        break;
    case 'pauseUnlink': // 挂断后置忙
        typeButton.buttonDisabled();
        typeButton.phoneCallout();
        typeButton.phoneCallText();
        typeButton.online();
        break;
    case 'consultLink': // 咨询成功
        $("#consult").hide();
        typeButton.consultBack();
        typeButton.consultThreeway();
        typeButton.consultTransfer();
        typeButton.transfer();
        break;
    case 'consulterOrTransferBusy': // 被咨询转接或转移的通话
        typeButton.buttonDisabled();
        typeButton.unLink();
        typeButton.hold();
        break;
    }
    var str = deviceStatus.deviceStatusLoginStatus(token.deviceStatus
            + token.loginStatus, token.pauseDescription, token.busyDescription);
    // alert(JSON.stringify(token)+"===================="+str);
    if (str != "") {
        $("#status").text(str);
    }
    if (str == '空闲') {
        $("#statusImg").css({
            "backgroundPosition" : "0px -25px"
        });
    } else if (str == '离线') {
        $("#statusImg").css({
            "backgroundPosition" : "0px 0px"
        });
    } else {
        $("#statusImg").css({
            "backgroundPosition" : "0px -50px"
        });
    }

    // ============================================================
    // 第三方代码写在下面
    if (token.eventName == "outRinging") {// 外呼座席响铃
        // top.CTI_ID = token.uniqueId;//获取录音编号
    } else if (token.eventName == "comeRinging") {// 呼入座席响铃
        url = '';
        customerNumber = token.customerNumber;
        $('#phoneCallText').val(customerNumber);
        hotline = token.hotline;
        title = customerNumber;
        id = parseInt(100000000000000 * Math.random());
        switch (hotline) {
        case '64149599':
        case '64149596':
        case '58103539':
            url = "index.php?r=client/dispatch&phone=" + customerNumber
                    + "&callid=" + id + "&dialog=1";
            break;
        // case '64149599':
        case '58103537':
            url = "index.php?r=client/service&phone=" + customerNumber
                    + "&callid=" + id + "&dialog=1";
            title = '咨询';
            break;
        }
        addRing(id, title, url);
    } else if (token.eventName == "normalBusy") {// 呼入座席接听

    } else if (token.eventName == "outBusy") {// 外呼客户接听
        // if(top.CTI_ID == "") {
        // top.CTI_ID = token.uniqueId;
        // }
    } else if (token.eventName == "online") {// 置闲

    } else if (token.eventName == "pause") {// 置忙

    } else if (token.eventName == "waitLink") {// 座席接听 等待客户接听

    } else if (token.eventName == "neatenStart") {// 整理开始（座席挂断）

    } else if (token.eventName == "neatenEnd") {// 整理结束

    } else if (token.eventName == "hold") {// 保持开始

    } else if (token.eventName == "unHold") {// 保持结束

    } else if (token.eventName == "consultLink") {// 咨询成功

    } else if (token.eventName == "consulterOrTransferBusy") {// 被咨询转接或转移的通话

    }
    
    restartDurationTimer();
    // ============================================================
}

function restartDurationTimer() {
    if (durationTimerID) {
        clearInterval(durationTimerID);
    }
    $("#duration").text("00:00:00");
    $("#durationCell").show();
    durationTimerID = setInterval("durationTiming()", 1000);
}

function durationTiming(){
    var $duration = $("#duration");
    var durationTime = $duration.text().split(':');
    var second = parseInt(durationTime[2],10);
    var minute = parseInt(durationTime[1],10);
    var hour = parseInt(durationTime[0],10);
    second++;
    if(second >= 60){
        minute = minute + 1;
        second = 0;
    }
    if(minute >= 60){
        hour = hour+1;
        minute = 0;
    }
    if(second.toString().length <2) second = "0" + second;
    if(minute.toString().length <2) minute = "0" + minute;
    if(hour.toString().length <2) hour = "0" + hour;
    $duration.text(hour + ":" + minute + ":" + second);
}

function login() {
    var bindTel = showBindingTelPopover();
    if (!bindTel) {
        return;
    }

    var params = {};
    params.hotLine = '64149599';
    params.cno = $("#cno").val();
    params.pwd = $("#pwd").val();
    params.bindTel = INNER_TEL_PREFIX + bindTel;
    //params.initStatus = 'online';
    params.initStatus = 'pause';
    params.bindType = 1;
    executeAction('doLogin', params);
}

function showBindingTelPopover() {
    var bindTel;
    while (true) {
        bindTel = prompt("请先输入绑定电话号码四位：");
        // if clicking cancel button
        if (bindTel == null) {
            return null;
        }

        bindTel = $.trim(bindTel);
        if (bindTel) {
            return bindTel;
        }
    }
}

function logout() {// 登出
    var params = {};
    params.type = 1;
    params.removeBinding = '1';
    executeAction('doLogout', JSON.stringify(params));
}

function call_driver(phone_number) { // 选司机页面 呼叫司机
    $('#phoneCallText').val(phone_number);
    var params = {};
    params.tel = phone_number;
    params.callType = '3'; // 3点击外呼
    executeAction('doPreviewOutCall', params);
}
