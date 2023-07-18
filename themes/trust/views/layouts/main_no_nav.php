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
$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/bootstrap.min.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/bootstrap-dropdown.js',CClientScript::POS_END);
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.css');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
?>
</head>
<body>
	<?php echo $content; ?>
</body>
</html>
