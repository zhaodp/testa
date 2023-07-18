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
$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/bootstrap-button.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_IMG.'bootstrap/js/twitter-bootstrap-hover-dropdown.min.js',CClientScript::POS_END);
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.min.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.min.css');
?>
</head>
<body>
<?php 
	if(!isset(Yii::app()->user->user_id)){
	    $this->redirect(array('site/login'));
	}
	$route = Yii::app()->getController()->getRoute();
	$params = Yii::app()->getController()->getActionParams();
	$this->beginContent('//layouts/nav_top',array('route'=>$route,'params'=>$params));
	$this->endContent();
?>
</body>
</html>
