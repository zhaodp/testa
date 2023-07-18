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
$cs->registerCssFile(SP_URL_STO . 'www/css/cityselector.css');
$cs->registerScriptFile(SP_URL_STO . 'www/js/cityselector.js');
?>
<style>
body {
	padding-left:56px;
	padding-top: 50px;
}
</style>
</head>
<body>
<link href="<?php echo SP_URL_HOME ?>sto/classic/menu/fnav.css" type="text/css" rel="stylesheet" charset="utf-8">
<script src="<?php echo SP_URL_HOME ?>sto/classic/menu/fnav.js" type="text/javascript"></script>
<?php 
	if(!isset(Yii::app()->user->user_id)){
	    $this->redirect(array('site/login'));
	}
	$route = Yii::app()->getController()->getRoute();
	$params = Yii::app()->getController()->getActionParams();
	//$this->beginContent('//layouts/nav',array('route'=>$route,'params'=>$params));
	//$this->endContent();
?>
	<div class="container-fluid">
		<div class="row-fluid">
			<?php

            if(Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER){
                echo '<div class="span2">';
                $this->beginContent('//layouts/menu',array('route'=>$route,'params'=>$params));
                $this->endContent();
                echo '</div><div class="span10">';
            }else{
                echo '<div class="span12">';
            }

			?>
			<?php echo $content; ?>
			</div>
		</div>
		<div class="row-fluid">
		    <div class="span12" style="text-align:center">
		    	<hr class="divider"/>
		    	<p>Copyright@2011-<?php echo date('Y');?> edaijia.cn All Right Reserved <br/>24小时热线：4006-91-3939 <br/>
                    当前运行服务：<?php echo Common::getMyHostName();?></p>
		    </div>
		</div>
	</div>
</body>
</html>
