<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1.0, maximum-scale=1.0"" />
<title><?php echo $this->pageTitle;?></title>
<?php 
$trackview = '';

$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerCoreScript('jquery');
$cs->registerCssFile(SP_URL_CSS.'bootstrap.min.css');
$cs->registerCssFile(SP_URL_CSS.'metro-bootstrap.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
$cs->registerCssFile(SP_URL_CSS.'bootstrap-responsive.min.css');

$cs->registerScriptFile(SP_URL_JS.'bootstrap.min.js',CClientScript::POS_END);
$cs->registerScriptFile(SP_URL_JS.'bootstrap-button.js',CClientScript::POS_END);
?>
</head>
<body>
 <?php 
 	$this->beginContent('//layouts/nav');
	$this->endContent();
 ?>
    <div class="container">
    	<?php echo $content; ?>
    </div>
    <section class="container">
    
    <div style="margin-top:15px; text-align:center;padding-bottom:30px;">
		<div class="foot_nav">
			<a href="/about/">关于</a> 
			<a href="/hezuo/">商务合作</a>
			<a href="/faq/">常见问题</a>
			<a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/">e代驾招募</a>
			<a href="http://www.edaijia.cn/v2/">司机专区</a>
		</div>    
		24小时服务热线:<a href="tel:4006913939">4006-91-3939</a>
	</div>
	</section>
 	<script>
 		var hideUrlBar = function(){
 	 		if (window.pageYOffset <= 0){window.scrollTo(0,1);}
		};
		window.addEventListener('load',function(){window.setTimeout(hideUrlBar,0);},false);
	</script>
	<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-33826171-1']);
		  _gaq.push(['_setDomainName', 'edaijia.cn']);
		  _gaq.push(['_trackPageview','<?php echo $trackview; ?>']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
	</script>	
</body>
</html>
