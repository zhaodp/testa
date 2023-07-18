<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1.0, maximum-scale=1.0" />
<title>e代驾 - faq</title>
<?php 
$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerCoreScript('jquery');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.min.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.min.css');
?>
</head>
<body>
<div class="navbar">
	<div class="navbar-inner">
	<div class="container">
	  <div class="navbar">
	   	  <a class="brand" href="/"><img src="<?php echo SP_URL_IMG;?>logo.png" width="75px" border="0"/></a>
	  </div>
	</div>
	</div>
</div>

<div class="container">
<h1>司机客户端FAQ</h1>
	<p>
		<b>1.哪些手机可以使用司机客户端？</b><br/>
	答：Android系统版本2.2以上, 未Root，屏幕尺寸3.5寸以上, 分辨率480*800以上, 必须带GPS定位功能, 最好为三星、华为、HTC品牌手机。小米、红米、联想、魅族等部分国产机型，目前暂不支持。
	</p>
	<p>
		<b>2.在家里/室内定位不准, 有偏差怎么处理?</b><br/>
	答：在室内定位不准确,可以开启手机WIFI/WLAN, 进行辅助定位,可以大幅提高定位精确度。但是记得出门时关闭手机WIFI/WLAN。
	</p>
	<p>
		<b>3.我怎么在地图上看不到我的位置?</b><br/>
		1) 请确认手机时间是否和标准时间一致, 手机时间不能慢, 也不能太快。<br/>
			2) 在客户端查看周边司机, 如果周边的司机比较多, 系统会随机找出5名空闲的司机师傅, 这个时候, 如果您身边的司机较多, 在某些时刻, 您看不到自己, 但是客人在这个时刻有可能看到。
	</p>
	<p>
		<b>4.系统提示我”司机资料/手机信息变更, 请联系司机管理部” 怎么处理?</b><br/>
	答：请联系当地司机管理部, 反映问题, 并如实说明是否有更换手机/SIM卡的情况。
	</p>
	<p>
		<b>5.为啥我的手机没有笑脸,没有笑脸该怎么办?</b><br/>
	答：出现这种问题, 一般是由于手机不兼容导致的, 如果手机长时间不出笑脸, 请前往分公司进行如下处理:<br />
		1) 把司机客户端卸载 <br />
		2) 删除sdcard/libs 目录 <br />
		3) 再安装司机客户端 <br />
		如果问题依然未能解决，请记录司机的手机型号和使用的SIM卡的类型(移动/联通/电信). 技术部门会做进一步的处理。
	</p>
	
	<p>
		<b>6.没有笑脸会影响我接单吗?</b><br/>
	答：目前400派单全部使用自动派单系统, 没有笑脸就不能接受自动派单。
	</p>
	<p>
		<b>7.状态切换页面最下方的刷新位置, 需要一直点吗?</b><br/>
	答：不需要, 这个是在您觉得您的位置不准确的时候, 您可以手动刷新位置, 正常情况下不需要。
	</p>
	<p>
		<b>8.评价和星级是怎么对应的?</b><br/>
	答：星级和签约天数、订单量、服务着装及服务态度、客人好评数量都是相关的，而且客人投诉或差评会对星级有非常大的影响，请各位师傅一定要优质服务、赢得好评。
	</p>	
	<p>
		<b>9.为什么"清除数据"后, 还需要重新输入用户名和密码登录?</b><br/>
	答：如果手机出现定位不准确, 有位置偏移的问题, 请不要进行 "清除数据" 的操作, "清除数据"是不能解决这些问题的. 反复"清理数据" 反而会对司机端软件产生不良影响.<br />
	正确的处理方式是：尝试开启手机WiFi/WLAN, 这样会提高定位精度。但是在室外时, 请一定不要开启手机WiFi/WLAN。连接到一些公共的热点, 比如ChinaNet, 这些公共的热点需要登录, 如果不登陆, 那么就导致网络连接异常, 会被踢下班.
	</p> 
</div>
</body>
</html>