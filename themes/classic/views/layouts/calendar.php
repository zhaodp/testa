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

$host = $_SERVER['HTTP_HOST'];
if (strpos($host, "www.edaijia.cn") !== FALSE) {
    define('SRC_PRE', 'http://h5.edaijia.cn/ev2/');
} else if (strpos($host, "www.d.edaijia.cn") !== FALSE) {
    define('SRC_PRE', 'http://h5.d.edaijia.cn/ev2/');
} else {
    define('SRC_PRE', 'http://h5.d.edaijia.cn/ev2/');
}
$cs->registerScriptFile(SRC_PRE . 'js/moment.min.js?v=1.0');
$cs->registerScriptFile(SRC_PRE . 'js/jquery.md5.js?v=1.0');
$cs->registerScriptFile(SRC_PRE . 'js/fullcalendar.min.js?v=1.0');
$cs->registerScriptFile(SRC_PRE . 'js/lang-all.js?v=1.0');
$cs->registerScriptFile(SRC_PRE . 'js/jquery.fancybox.js?v=1.0');
$cs->registerScriptFile(SRC_PRE . 'js/common.js?v=1.0');
$cs->registerScriptFile(SRC_PRE . 'js/app.js?v=1.0');
$cs->registerCssFile(SRC_PRE . 'css/jquery.fancybox.css?v=1.0');
$cs->registerCssFile(SRC_PRE . 'css/fullcalendar.min.css?v=1.0');
$cs->registerCssFile(SRC_PRE . 'css/style.css?v=1.0');
?>
</head>
<body>
<?php 
	$route = Yii::app()->getController()->getRoute();
	$params = Yii::app()->getController()->getActionParams();
	$this->beginContent('//layouts/nav',array('route'=>$route,'params'=>$params));
	$this->endContent();
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

	<script type="text/javascript">
		var updater = {
		    poll: function(){
		        $.ajax({url: "index.php",
		        		data: {r:'notice/check'},
		                type: "GET",
		                dataType: "json",
		                success: updater.onSuccess,
		                error: updater.onError});
		    },
		    onSuccess: function(data, dataStatus){
		        try{
					
		        }
		        catch(e){
		            updater.onError();
		            return;
		        }
		        interval = window.setTimeout(updater.poll, 300000);
		    },
		    onError: function(){
		        console.log("Poll error;");
		    }
		};
		
		updater.poll();
	</script>
</body>
</html>
