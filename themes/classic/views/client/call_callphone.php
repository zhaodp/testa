<?php
	if(!isset(Yii::app()->user->agent)){
		echo ('没有绑定坐席');
		Yii::app()->end();
	}
	$this->pageTitle = '工号:'.Yii::app()->user->agent['agent_num'] . ' 电话:' .Yii::app()->user->agent['phone'] . ' - 话务中心';
	$cs=Yii::app()->clientScript;
	$cs->registerCoreScript('jquery-ui');
	$cs->registerScriptFile(SP_URL_JS.'jquery.cleverTabs.js',CClientScript::POS_END);
	$cs->registerScriptFile(SP_URL_JS.'jquery.contextMenu.js',CClientScript::POS_END);
	
	if(isset(Yii::app()->user->agent)){
		$this->beginContent('//layouts/phonebar', array('useCloud' => $useCloud));
		$this->endContent();
	}
?>
<div id="tabs"><ul></ul></div>
<div id='messages'>消息条</div>

<script type="text/javascript">
var tabs;

$(document).ready(function(){
	resize();

	tabs = $('#tabs').cleverTabs({
		setupContextMenu: false});

	tabs.add({
		url: 'index.php?r=driver/map',
		lock: true,
		label: '司机分布'
	});

	tabs.add({
		url: 'index.php?r=order/dispatch',
		lock: true,
		label: '派单队列'
	});
	
	$(window).bind('resize', function() {
		resize();
	});
});

function resize(){
	height = $(window).height();
	tab_height = height - 100;
	$("div#tabs").height(tab_height);
}

	
function addRing(id,title,url){
    //var tabs = $('#tabs').cleverTabs();
    tabs.add({
        //必须是在tabs内唯一的id
        id: id,
        //将要在iframe的src属性设置的值
        url: url,
        //显示在Tab头上的文字
        label: title.slice(-4),
        //关闭本Tab时需要刷新的Tab的url(默认: null)
        closeREfresh: 'tab url',
        //关闭本Tab时需要激活的Tab的url(默认: null)
        closeActivate: 'index.php?r=order/dispatch',
        //关闭本Tab时需要执行的回调函数
        callback: function () { /*do something*/ }
    });

    $("#cleverTabHeaderItem-" + id + " .ui-icon.ui-icon-close").click(function(){
        var $form = $("#cleverTabPanelItem-" + id + " iframe").contents().find("#order-queue-create-form");
        if ($form.length < 1) {
            return;
        }
        var phone = $form.find("#OrderQueue_phone").val();
        var ringTime = $form.find("#ringTime").val();
        var dispatchType = $form.find("#dispatchType").val();
        $.post("index.php", {
            "r" : "client/dispatch",
            "action" : "cancel",
            "phone" : phone,
            "ringTime" : ringTime,
            "dispatchType" : dispatchType
        });
    });
}

function test(){
	id = parseInt(100000000*Math.random());
	url = "index.php?r=client/dispatch&phone=18911883373&callid="+id+"&dialog=1";
	addRing('18911883373',id,url);
}

function addOrder(){
	title = '手工派单';
	id = parseInt(100000000000000*Math.random());
	url = "index.php?r=client/dispatch&callid="+id+"&dialog=1";
	addRing(id,title,url);
}

function OrderQueue(){
	tabs.add({
		url: 'index.php?r=order/dispatch&_dispatch=1',
		lock: true,
		label: '派单队列[派单人员]'
	});
}


$(window).bind('beforeunload',function(){
	return '确定离开此页面吗？';
});
</script>
