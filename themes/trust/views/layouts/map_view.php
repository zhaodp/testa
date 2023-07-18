<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<?php 
$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/bootstrap.min.js',CClientScript::POS_HEAD);
$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/bootstrap-dropdown.js',CClientScript::POS_HEAD);
//$cs->registerScriptFile('http://api.map.baidu.com/api?v=1.3',CClientScript::POS_HEAD);
$cs->registerScriptFile('http://api.map.baidu.com/api?v=2.0&ak=ECfffb5d16a4f1b23c885c0527e91774',CClientScript::POS_HEAD);
$cs->registerScriptFile('http://api.map.baidu.com/library/GeoUtils/1.2/src/GeoUtils_min.js',CClientScript::POS_HEAD);
$cs->registerScriptFile(SP_URL_JS.'map.js',CClientScript::POS_HEAD);
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.css');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
?>

<style type="text/css">
body, html,#map_canvas {width: 100%;height: 100%;overflow: hidden;margin:0;}
.container-fluid {padding:0px;}
.shoppingcart{position:absolute;top:10px;width:203px;color:#000;height:28px;right:0px;font-weight:bold;font-size:15px;}
.cart_open {padding:0 5px;right:200px;margin-bottom:5px;}
.cart_open dl {margin:0;padding:0;height:30px;padding-left:8px;clear:both}
.cart_open dd {float:left;padding-top:5px;padding-right:0px;line-height:20px;}
.cart_open a {color:red;}
.cart_open dd.name {width:75%;height:20px;display:block;text-align:left;}
.cart_open span {clear:both;line-height:20px;font-size:12px;color:#F00;text-align:center;color:#000;display:block;color:#000}
.flyout-menu {display:none;position:absolute;visibility:hidden;z-index:999999;}
</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
		<?php echo $content; ?>
		</div>
	</div>
</body>
</html>
