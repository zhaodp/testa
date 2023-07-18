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
    <div class="container navbar-fixed-top">
      <div class="navbar">
          <a class="brand logo_sem" href="/?s=sem"><img src="<?php echo SP_URL_STO;?>img/logo_sme.png" width="200px" border="0"/></a>
      </div>
    </div>
    <div class="container">
    	<?php echo $content; ?>
    </div>
    <div class="navbar-fixed-bottom footer_baidu">
		<a href="/?s=about">关于e代驾</a>
        <a href="http://www.edaijia.cn/">电脑版</a>
		<span>京ICP证090216号</span>
	</div>
    <div style="display:none;">
        <script type="text/javascript">
            var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
            document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fc03310ce23cf3cde07f3d6d1764fd5d3' type='text/javascript'%3E%3C/script%3E"));
        </script>
    </div>
    <script type="text/javascript">
 		var hideUrlBar = function(){
 	 		if (window.pageYOffset <= 0){window.scrollTo(0,1);}
		};
		window.addEventListener('load',function(){window.setTimeout(hideUrlBar,0);},false);

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