<?php
        header("Content-Type:text/html;charset=UTF-8");
	if(empty($params['agent_name']) || empty($params['password']) || empty($params['agent_num'])){
		echo ('没有设置天润坐席');
		Yii::app()->end();
	}
	$this->pageTitle = '工号:'.$params['agent_num']. ' 电话:'.' - 话务中心[天润]';
	$cs=Yii::app()->clientScript;
	$cs->registerCoreScript('jquery-ui');
	$cs->registerScriptFile(SP_URL_JS.'jquery.cleverTabs.js',CClientScript::POS_END);
	$cs->registerScriptFile(SP_URL_JS.'jquery.contextMenu.js',CClientScript::POS_END);
	
        $this->beginContent('//layouts/cicc_toolbar');
        $this->endContent();
?>

<input type="hidden" id='cno' value="<?php echo $params['agent_name'] ?>">
<input type="hidden" id='pwd' value="<?php echo $params['password'] ?>">

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
		url: 'index.php?r=order/dispatch&crm=cicc',
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
    var tab = tabs.add({
        //必须是在tabs内唯一的id
        id: id,
        //将要在iframe的src属性设置的值
        url: url,
        //显示在Tab头上的文字
        label: title.slice(-4),
        //关闭本Tab时需要刷新的Tab的url(默认: null)
        closeREfresh: 'tab url',
        //关闭本Tab时需要激活的Tab的url(默认: null)
        closeActivate: 'index.php?r=order/dispatch&crm=cicc',
        //关闭本Tab时需要执行的回调函数
        callback: function () { /*do something*/ }
    });
    $("#cleverTabPanelItem-" + id + " iframe").load(function() {
        if ($(this).find("input#client_service").length > 0) {
            var $title = tab.header.find("a:first-child");
            var title = "咨询";
            $title.text(title).attr("title", title);
        }
    });
}

function addOrder(){
	title = '手工派单';
        id = parseInt(100000000000000*Math.random());
	url = "index.php?r=client/dispatch&callid="+id+"&dialog=1";
	addRing(id,title,url);
}

function OrderQueue(){
	tabs.add({
		url: 'index.php?r=order/dispatch&crm=cicc',
		lock: true,
		label: '派单队列'
	});
}

</script>
