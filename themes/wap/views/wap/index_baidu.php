<?php
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
<div class="baidu_sme_main">
    <div class="ad_background">e代驾，全国最大的代驾公司</div>
    <div class="app_daijia">代驾费 <span>39</span><b>元</b> 起</div>
    <div class="down_button">
        <a class="iphone_sme" href='/jump.html?go=<?php echo urlencode($params['appVersionIphone']['url']); ?>'
           onClick="_gaq.push(['_setCustomVar', 1, 'Download', 'iPhone',3]);_gaq.push(['_trackPageview', '<?php echo $trackview; ?>download/iPhone']);">iphone
            版<br/>免费下载</a>
        <a class="android_sme" href='/jump.html?go=<?php echo urlencode($params['appVersionAndroid']['semurl']); ?>'
           onClick="_gaq.push(['_setCustomVar', 1, 'Download', 'Android',3]);_gaq.push(['_trackPageview', '<?php echo $trackview; ?>download/android']);">android
            版<br/>免费下载</a>
    </div>
    <div class="clear_both">
        没有您的手机客户端？
    </div>
    <div class="clear_both hot_phone">
        代驾热线：<a href="tel:4006913939">4006-91-3939</a>
    </div>
    <div class="clear_both city_link">
        各城市价格:
        <a href="/?s=price&city_id=1">北京</a>
        <a href="/?s=price&city_id=3">上海</a>
        <a href="/?s=price&city_id=5">广州</a>
        <a href="/?s=price&city_id=6">深圳</a>
        <a href="/?s=price&city_id=4">杭州</a>
        <a href="/?s=price&city_id=7">重庆</a>
        <br/>
        <a href="/?s=price&city_id=2">成都</a>
        <a href="/?s=price&city_id=8">南京</a>
        <a href="/?s=price&city_id=11">西安</a>
        <a href="/?s=price&city_id=18">郑州</a>
        <a href="/?s=price&city_id=10">武汉</a>
        <a href="/?s=price&city_id=14">天津</a>
        <a href="/?s=price&city_id=15">济南</a>
        <br/>
        <a href="/?s=price&city_id=9">长沙</a>
        <a href="/?s=price&city_id=12">宁波</a>
        <a href="/?s=price&city_id=16">苏州</a>
        <a href="/?s=price&city_id=20">青岛</a>
    </div>
</div>

<div style="display: none">
    <script type="text/javascript">
        var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
        document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F7454e01c8dde2d8b0c96cd03b58b0c23' type='text/javascript'%3E%3C/script%3E"));
    </script>
</div>