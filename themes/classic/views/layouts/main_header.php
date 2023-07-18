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
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.min.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.min.css');
?>
<style>
	body {padding-top:50px;}
</style>
</head>
<body>
<?php 
	$route = Yii::app()->getController()->getRoute();
	$params = Yii::app()->getController()->getActionParams();
?>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">      
        <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>        
		<div class="nav-collapse collapse">
            <ul class="nav">
				<li class="">
                <a href="/" style="padding:0px"><img src="<?php echo SP_URL_IMG;?>logo.gif" style="padding:0px;height:40px;margin: -2px 0px;"></a>
              </li>
            </ul>
          </div>
      </div>
    </div><!-- /navbar-inner -->
</div>


	<div class="container-fluid">
		<div class="row-fluid">
<!--		<div class="span2">
			<?php 
				//$this->beginContent('//layouts/menu',array('route'=>$route,'params'=>$params)); 
				//$this->endContent(); 
			?>
			</div>
-->			
			<div class="span12">
			<?php echo $content; ?>
			</div>
		</div>
		<div class="row-fluid">
		    <div class="span12" style="text-align:center">
		    	<hr class="divider"/>
		    	<p>Copyright@2011-2013 edaijia.cn All Right Reserved <br/>24小时热线：4006-91-3939</p>
		    </div>
		</div>
	</div>
</body>
</html>
