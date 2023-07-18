<?php 
	$route = Yii::app()->getController()->getRoute();
?>
<div class="navbar-fixed-top">
	<div id="header">
    <div class="block head">
        <a href="/" class="logo"><img src="http://www.edaijia.cn/v2/sto/classic/www/images/logo.png" width="320" height="45" border="0"></a>
        <ul class="nav">
        	<li><a href="http://www.<?php echo Common::getDomain(SP_HOST);?>/">首页</a></li>
        	<li><a href="/entry" <?php if($route=='zhaopin/entry') echo 'class="actives"'?>>合作招募</a></li>
        	<li><a href="/notice" <?php if($route=='zhaopin/notice') echo 'class="actives"'?>>培训资料</a></li>
			<li><a href="/signup" <?php if($route=='zhaopin/signup') echo 'class="actives"'?>>在线报名</a></li>
			<!-- <li><a href="/spec" <?php if($route=='zhaopin/spec') echo 'class="actives"'?>>服务规范</a></li>-->
			<li><a href="/exam" <?php if($route=='zhaopin/exam') echo 'class="actives"'?>>在线考试</a></li>
            <li><a href="/queue?act=interview" <?php if($route=='zhaopin/exam') echo 'class="actives"'?>>预约面试</a></li>
            <li><a href="/queue" <?php if($route=='zhaopin/queue') echo 'class="actives"'?>>报名查询</a></li>
        </ul>
    </div>
</div>
</div>