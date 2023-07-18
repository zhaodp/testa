<?php
/* @var $this ZhaopinController */
$this->pageTitle = 'e代驾';

$source = isset($_GET['s']) ? $_GET['s'] : 0;
switch ($source) {
    case 1:
        $trackview = '/sms/';
        break;
    default:
        $trackview = '/';
        break;
}
?>
<head>
<script type="text/javascript"> 
  var _adwq = _adwq || []; 
  _adwq.push(['_setAccount', 'v81er']); 
  _adwq.push(['_setDomainName', '.edaijia.cn']); 
  _adwq.push(['_trackPageview']); 
</script> 

<script type="text/javascript" src="http://d.emarbox.com/js/adw.js?adwa=v81er"></script> 

<script type="text/javascript"> 
$("#iOS_download").bind("click",function(){
var _adwq=window._adwq||[];
_adwq.push([ 
'_setAction','7qb0jy', 
'userid' 
]);  
}); 
</script>

<script type="text/javascript"> 
$("#Android_download").bind("click",function(){
var _adwq=window._adwq||[];
_adwq.push([
'_setAction','7qb47b',
'userid'
]); 
});
</script>

<script type='text/javascript'> 
_adwq.push([  
'_setAction','7qbcd0', 
'userid'
]); 
</script> 

</head>
<div class="modal" id="maskBox" style="display:none;position: fixed;top:0;left:0;right:0;bottom:0;z-index:990;background:url('<?php echo SP_URL_STO; ?>img/help.png') right top no-repeat;
        background-size:300px;"></div>
<div style="text-align:center;">
    <div>
        <img src="<?php echo SP_URL_STO; ?>img/logo_b.png" width="180px" border="0"/><br/>
        <h4>e代驾，就是快！</h4>
        <a class='btn btn-primary btn-large'
           style="margin-top:20px;width:250px; background:#0074CC url('<?php echo SP_URL_STO; ?>img/iphone.png') no-repeat 16px 40px;"
           href='<?php echo $params['appVersionIphone']['url'];?>' id="iOS_download"
           onClick="_gaq.push(['_setCustomVar', 1, 'Download', 'iPhone',3]);_gaq.push(['_trackPageview', '<?php echo $trackview; ?>download/iPhone']);">
            AppStore（苹果商店）<br/>
            <span style="color:#FF9900;font-size:12px;">最新版本：<?php echo $params['appVersionIphone']['latest']; ?></span>
            <br/><span style="font-size:12px;">更新日期：<?php echo $params['appVersionIphone']['updatetime']; ?></span></a>
        <a class='btn btn-primary btn-large'
           style="margin-top:20px;width:250px; background:#0074CC url('<?php echo SP_URL_STO; ?>img/android.png') no-repeat 16px 40px;"
           href='<?php echo $appInfo['url']; ?>' title='<?php echo $appInfo['title']; ?>' id='Android_download' >
            Android（安卓版）<br/>
            <span
                style="color:#FF9900;font-size:12px;">最新版本：<?php echo $params['appVersionAndroid']['latest']; ?></span>
            <br/><span style="font-size:12px;">更新日期：<?php echo $params['appVersionAndroid']['updatetime']; ?></span></a>
	<?php
	if(!isset($_GET['type']) || (isset($_GET['type']) && $_GET['type'] != '18')){
	?>
        <a class='btn btn-primary btn-large'
           style="margin-top:20px;width:250px; background:#0074CC url('<?php echo SP_URL_STO; ?>img/windowsphone.png') no-repeat 16px 40px;"
           href='<?php echo $params['appVersionWinphone']['url']; ?>'
           onClick="_gaq.push(['_setCustomVar', 1, 'Download', 'Android',3]);_gaq.push(['_trackPageview', '<?php echo $trackview; ?>download/windowsphone']);">
            Windows Phone<br/>
            <span
                style="color:#FF9900;font-size:12px;">最新版本：<?php echo $params['appVersionWinphone']['latest']; ?></span>
            <br/><span style="font-size:12px;">更新日期：<?php echo $params['appVersionWinphone']['updatetime']; ?></span></a>
	<?php } ?>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <a href='<?php echo $params['appVersionDriver']['url']; ?>'>e代驾司机端 (最新版本)</a>
    </div>
</div>
<div style="display: none;">
    <script type="text/javascript">
        var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
        document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F42c139c22d246f0433abfb7db19ab4e0' type='text/javascript'%3E%3C/script%3E"));
    </script>
</div>

<img src="<?php echo $params['baidu_code']; ?>" width="0" height="0"/>
<script type="text/javascript">
    var is_ios = !!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
    function is_weixin(){
        var ua = navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i)=="micromessenger") {
            return true;
        } else {
            return false;
        }
    }
    if(is_weixin()&&!is_ios){
        $("#maskBox").show().click( function () {
            $("#maskBox").hide();
        });
    }

</script>
