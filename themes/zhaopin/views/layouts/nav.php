<?php 
	$route = Yii::app()->getController()->getRoute();
?>
<div class="navbar-fixed-top">
	<div id="header">
    <div class="block head">
        <a href="/" class="logo"><img src="http://www.edaijia.cn/v2/sto/classic/www/images/logo.png" width="320" height="45" border="0"></a>
        <ul class="nav">
        	<li><a href="http://www.edaijia.cn/">首页</a></li>
        	<li><a href="/entry" <?php if($route=='zhaopin/entry') echo 'class="actives"'?>>合作模式介绍</a></li>
			<li><a href="/process" <?php if($route=='zhaopin/process') echo 'class="actives"'?>>招聘流程</a></li>
			<li><a href="/signup" <?php if($route=='zhaopin/signup') echo 'class="actives"'?>>在线报名</a></li>
			<li><a href="/queue" <?php if($route=='zhaopin/queue') echo 'class="actives"'?>>报名一览</a></li>
			<!--<li><a href="/spec" <?php if($route=='zhaopin/spec') echo 'class="actives"'?>>服务规范</a></li>-->
			<li><a href="/exam" <?php if($route=='zhaopin/exam') echo 'class="actives"'?>>在线考试</a></li>
        </ul>
    </div>
</div>
</div>