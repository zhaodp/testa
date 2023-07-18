<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<?php 
$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerCoreScript('jquery');
$cs->registerCssFile(SP_URL_CSS.'bootstrap.css');
$cs->registerCssFile(SP_URL_CSS.'docs.css');
$cs->registerCssFile(SP_URL_CSS.'bootstrap-responsive.css');
$cs->registerScriptFile(SP_URL_JS.'bootstrap.min.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-tooltip.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-affix.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-alert.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-button.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-carousel.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-collapse.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-dropdown.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-modal.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-popover.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-scrollspy.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-tab.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-transition.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-typeahead.js',CClientScript::POS_END);
?>
</head>
<body data-spy="scroll" data-target=".bs-docs-sidebar">
<?php 
	$this->beginContent('//layouts/nav');
	$this->endContent();
?>
	<div class="block">
		<div class="row" style="margin:0px;">
			<?php echo $content; ?>
		</div>
	</div>
    <div class="block">
    <hr class="divider"/>
    <div id="footer" class="block">
		<div class="foot_nav"><a href="http://www.edaijia.cn/about/">关于e代驾</a><a href="http://www.edaijia.cn/zhaopin/">e代驾招募</a><a href="http://www.edaijia.cn/hezuo/">服务与合作</a><a href="http://www.edaijia.cn/v2/">司机专区</a></div>
	    <div class="copyright">Copyright © 2011-2013 edaijia.cn All Right Reserved 版权所有 京ICP备13048976号-1</div>
	</div>
    </div>
</body>
</html>
