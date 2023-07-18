<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
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
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
?>
<style>
	body {
		padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
	}
</style>
<link href="<?php echo SP_URL_IMG;?>bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
</head>
<body>
	<?php
		$route = Yii::app()->getController()->getRoute();
		$params = Yii::app()->getController()->getActionParams();
		if(isset(Yii::app()->user->id)){
			//if(in_array(AdminGroup::model()->getID('drivers'), Yii::app()->user->roles)){
            if(Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER){
				$this->renderPartial('//layouts/menu_drivers',array('route'=>$route,'params'=>$params)); 
			}else{
				$this->renderPartial('//layouts/menu_admin',array('route'=>$route,'params'=>$params));
			}
		}
	?>
    <div class="container">
    	<?php echo $content;?>
	</div>
	<div id="foot">
		<div class="span12" style="text-align:center"><a href="<?php echo Yii::app()->createUrl('/site/logout');?>">退出登录</a></div>
    	<div class="span12" style="text-align:center">Copyright @ 2011 edaijia.cn<br/>All Right Reserved <br/>24小时热线：4006-91-3939</div>
	</div>
</body>
</html>
