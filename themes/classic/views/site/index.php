<!DOCTYPE html>
<html xmlns:wb=“http://open.weibo.com/wb”>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <title>e代驾 -全国最大专业代驾公司，39元起，APP、微信就能找代驾</title>
    <meta name="description" content="e代驾万名代驾司机24小时为您服务  服务热线：4006-91-3939。e代驾已开通：北京上海广州深圳杭州重庆南京成都武汉西安郑州济南天津咸阳" />
    <meta name="keywords" content="e代驾,易代驾,北京代驾公司,酒后代驾公司,长途代驾,北京汽车代驾,代驾服务公司" />
    <?php
    $cs=Yii::app()->clientScript;
    $cs->coreScriptPosition=CClientScript::POS_HEAD;
    $cs->scriptMap=array();
    $cs->registerCoreScript('jquery');
    $cs->registerScriptFile(SP_URL_STO.'www/js/png.js');
    $cs->registerScriptFile(SP_URL_STO.'www/js/tab.js');
    $cs->registerScriptFile(SP_URL_STO.'www/js/box.js');
    $cs->registerCssFile(SP_URL_STO.'www/css/edaijia.css');
    ?>
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
<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".current").css("opacity", "1.0");
        setInterval("slideSwitch2()", 5000);
        show();
    });
    function slideSwitch2(){
        var $current = $("#slideshow22 li.current");
        var $next = $current.next().length ? $current.next() : $("#slideshow22 li:first");
        $current.fadeOut();

        $next.css({
            opacity: 0.0,display:'block'
        }).addClass("current").animate({
                opacity: 1.0
            }, 4000, function () {
                $current.removeClass("current prev");
            });
    }

//    function show(){
//        $(".ad").slideDown(1500);
//    }
//
//    function hide() {
//        $(".ad").slideUp(1500);
//    }
</script>
<style type="text/css">
    .down_img{
        width:310px;
        position:absolute;
        top:10px;
        right:0px;
        font-family:"华文黑体",Verdana, Arial;;
    }
    .down_img .down_list_img{
        float:left;
        margin-left:40px;
        _margin-left:30px;
    *
        }
    .down_img .down_list_img dd{
        color:#0a112d;
    }

</style>
<body>
<?php
if (!isset($s)) {
    echo '<p style = "display: none"><a class="ajax" href="http://www.edaijia.cn/v2/index.php?r=site/ad"></a></p>';
}
?>
<div id="header">
    <div class="block head">
        <a href="/" class="logo"><img src="<?php echo SP_URL_STO;?>www/images/logo.png" width="320" height="45" border="0" /></a>
        <ul class="nav">
            <li><a href="/" class="actives">首页</a></li>
            <li><a href="/vip/">VIP办理</a></li>
            <li><a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/entry/">司机在线报名</a></li>
            <li><a href="/about/">关于我们</a></li>
            <li><a href="/faq/">FAQ</a></li>
        </ul>
    </div>
</div>
<div class="blank0"></div>
<div class="block clearfix">
    <div class="banner2">
        <div class="banner2_img">
            <ul id="slideshow22">
                <li class="current">
                    <a href="/wedding/"><img src="<?php echo SP_URL_STO;?>www/images/edaijia_1.png" width="980" height="110" border="0" /></a>
                </li>
                <li>
                    <a href="http://shop102047517.taobao.com/" target="_blank"><img src="<?php echo SP_URL_STO;?>www/images/edaijia_2.png" width="980" height="110" border="0" /></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="blank0"></div>
    <div class="banner">
        <div class="banner_img">
            <ul id="slideshow2">
                <li class="current"><img src="<?php echo SP_URL_STO;?>www/images/banner-driver.png" width="460" height="350" border="0" /></li>
                <li><img src="<?php echo SP_URL_STO;?>www/images/banner-39.png" width="460" height="350" border="0" /></li>
                <li><img src="<?php echo SP_URL_STO;?>www/images/banner-3.png" width="460" height="350" border="0" /></li>
            </ul>
        </div>
    </div>
    <div class="blank0"></div>

    <div class="down">
        <ul class="down_btn">
            <li><a href="http://itunes.apple.com/cn/app/id468591734?mt=8" target="_blank" id="iOS_download"><img src="<?php echo SP_URL_STO;?>www/images/download-iPhone.png" width="191" height="71" border="0" /></a></li>
            <?php
            if (isset($s)) {
                echo '<li><a href = "'. $appInfo["url"] . '" target = "_blank" id="Android_download" title="' .$appInfo["title"]. '" >
	<img src = "' . SP_URL_STO . 'www/images/download-android.png" width = "191" height = "71" border = "0" /></a ></li>';
            }else{
                echo '<li><a href = "' . $appInfo["url"] . '" target = "_blank" id="Android_download" title="' .$appInfo["title"]. '">
	<img src = "' . SP_URL_STO . 'www/images/download-android.png" width = "191" height = "71" border = "0" /></a ></li>';
            }
            ?>
            <li><a href="<?php echo Yii::app()->params["appVersion"]["appVersionWinphone"]["url"]; ?>" target="_blank"><img src="<?php echo SP_URL_STO;?>www/images/download-winphone.png" width="191" height="71" border="0" /></a></li>
        </ul>
        <div class="down_img">
            <dl class="down_list_img">
                <dt><img src="<?php echo SP_URL_STO;?>www/images/app_edaijia.jpg" width="100" height="100" border="0" /></dt>
                <dd>扫描免费下载app</dd>
            </dl>
            <dl class="down_list_img">
                <dt><img src="<?php echo SP_URL_STO;?>www/images/weixin_edaijia.jpg" width="100" height="100" border="0" /></dt>
                <dd>扫描添e代驾微信号</dd>
            </dl>
        </div>
    </div>
</div>
<div class="blank"></div>
<div id="main" class="block clearfix">
    <div class="main_box clearfix">
        <div class="AearL">
            <dl class="AearL_dl_l">
                <dt><img src="<?php echo SP_URL_STO;?>www/images/body-sh.png" width="220" height="276" border="0" /></dt>
                <dd>
                    <h2>实惠</h2>
                    <p>
                        代驾？ 不再是贵族享有特权，【e代驾】打破<br />
                        市场坚冰，让e代驾成为百姓必备软件，<br />
                        记住呦：谁说开车不喝酒，e代驾只需39。<br />
                        最in的价格就在e代驾
                    </p>
                </dd>
            </dl>
            <div class="blank0"></div>
            <dl class="AearL_dl_r">
                <dt><img src="<?php echo SP_URL_STO;?>www/images/body-aq.png" width="220" height="276" border="0" /></dt>
                <dd>
                    <h2>安全</h2>
                    <p>
                        全国唯一拥有代驾责任险的公司，五年以上驾龄<br />
                        的代驾司机为您护航，上岗前e代驾定期对每一<br />
                        位代驾司机进行严格考核，定期进行安全驾驶<br />
                        培训，旨在为您安享最值得信赖代驾服务
                    </p>
                </dd>
            </dl>
            <div class="blank0"></div>
            <dl class="AearL_dl_l">
                <dt><img src="<?php echo SP_URL_STO;?>www/images/body-kj.png" width="220" height="276" border="0" /></dt>
                <dd class="border_none">
                    <h2>快速</h2>
                    <p>
                        海量代驾司机，近才够快！<br />
                        e代驾平均到达客户时间15分钟，使用e代驾<br />
                        客户端直接召唤离您最近的5名司机，<br />
                        代驾，无需苦苦等待
                    </p>
                </dd>
            </dl>
        </div>
        <div class="AearR">
            <div class="e_title"><span id="city" onclick="city()">北京</span>开通城市</div>
            <ul class="ddl" style="display:none;">
                <?php
                $count = 1;
                foreach($city_fee['citys'] as $fee_id => $city_arr){
                    foreach($city_arr as $city_id=>$city_name){
                        echo '<li><a href="javascript:;" id="city_list_'.$city_id.'" onclick="city_sel('.$count.','.$city_id.')">'.$city_name.'</a></li>';
                    }
                    $count ++;

                }
                ?>
            </ul>
            <div class="date_region">
                <?php
                $c = 1;
                foreach($city_fee['fees'] as $k => $v){

                    //////---------
                    //$fee = $fee_arr['fees'][$k];
                    //echo $k;echo '-------';
                    $str = '';
                    $str.= '<dl id="time'.$c.'"'.($c != 1 ? 'style="display:none;"':'').'>';
                    switch($c){
                        case 1:
                            $str.='<dt><span style="width:120px;">时间段</span><span style="width:84px;">代驾费</span></dt>';
                            break;
                        case 2:
                            $str.='<dt><span style="width:120px;">时间段</span><span style="width:84px;">代驾费</span></dt>';
                            break;
                        case 3:
                            $str.='<dd><span style="width:90px;">时间段</span><span style="width:114px;">起步价(10公里以内)</span></dd>';
                            break;
                        case 4:
                            $str.='<dd><span>时间段</span><span>起步价(5公里以内)</span></dd>';
                    }

                    if (!empty($v['minFee'])) {
                        if (!empty($v['firstFee'])) {
                            $fee_first = $v['minFeeHour'] . '—' . $v['firstFeeHour'];
                        } else {
                            $fee_first = '全天';
                        }
                        $str .=  '<dd><span style="width:120px;">'.$fee_first  . '</span><span style="width:84px;">' . $v['minFee'].'元';
                    }

                    if (!empty($v['firstFee'])) {
                        if (!empty($v['secondFee'])) {
                            $fee_second = $v['firstFeeHour'] . '—' . $v['secondFeeHour'];
                        } else {
                            $fee_second = $v['firstFeeHour'] . '—' . $v['minFeeHour'];
                        }
                        $str .= '<dd><span style="width:120px;">'.$fee_second .'</span><span style="width:84px;">' . $v['firstFee'].'元';
                    }

                    if (!empty($v['secondFee'])) {
                        if (!empty($v['thirdFeeHour'])) {
                            $fee_second = $v['secondFeeHour'] . '—' . $v['thirdFeeHour'];
                        } else {
                            $fee_second = $v['secondFeeHour'] . '—' . $v['minFeeHour'];
                        }
                        $str .= '<dd><span style="width:120px;">'.$fee_second .'</span><span style="width:84px;">' .  $v['secondFee'].'元';
                    }

                    if (!empty($v['thirdFeeHour'])) {
                        $fee_second = $v['thirdFeeHour'] . '—' . $v['minFeeHour'];

                        $str .=  '<dd><span style="width:120px;">'.$fee_second .'</span><span style="width:84px;">' .  $v['thirdFee'].'元';
                    }


                    $str.='</dl>';
                    echo $str;

                    $c ++;
                }
                ?>

                <div class="blank0"></div>
                <p>&nbsp;</p>
                <pre id="beizhu1"><b>注：</b>
1、不同时段的代驾起步费以实际出发
时间为准。
2、代驾距离超过10公里后，每10公里
加收20元，不足10公里按10公里计算。
3、等候时间每满30分钟收费20元，不
满30分钟不收费。
                </pre>

                <pre id="beizhu2" style="display:none;"><b>注：</b>
1、不同时段的代驾起步费以实际出发
时间为准。
2、代驾距离超过10公里后，每5公里
加收20元，不足5公里按5公里计算。
3、约定时间前到达客户指定位置，从
约定时间开始，每满30分钟收20元等
候费，不满30分钟不收费；约定时间
之后到达客户指定位置，从司机到达
时间后，每满30分钟收20元等候费，
不满30分钟不收费。
                </pre>
                <pre id="beizhu3" style="display:none;"><b>注：</b>
1、不同时段的代驾起步费以实际出发
时间为准。
2、代驾距离超过10公里后，每5公里
加收20元，不足5公里按5公里计算。
3、等候时间每满30分钟收费20元，不
满30分钟不收费。
                </pre>
                <pre id="beizhu4" style="display:none;"><b>注：</b>
1、不同时段的代驾起步费以实际出发
时间为准。
2、代驾距离超过5公里后，每5公里
加收20元，不足5公里按5公里计算。
3、等候时间每满30分钟收费20元，不
满30分钟不收费。
                </pre>
            </div>
            <div class="blank"></div>
            <div class="link">
		<wb:share-button appkey="265pvC" addition="simple" type="button" default_text="该喝酒时就喝酒，代驾只要39！全国最大的专业网络代驾，APP下单无需苦苦等待。猛击下载：http://t.cn/RvZQBQD" ralateUid="2336563625"></wb:share-button>
                <iframe width="205" height="501" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&width=205&height=502&fansRow=1&ptype=1&speed=100&skin=1&isTitle=1&noborder=1&isWeibo=1&isFans=1&uid=2336563625&verifier=c46ba8aa&dpc=1"></iframe>
            </div>
        </div>
        <div class="blank0"></div>
    </div>
</div>
<div class="blank"></div>
<div id="footer" class="block">
    <div class="foot_nav"><a href="/about/">关于e代驾</a><a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/">e代驾招募</a><a href="/hezuo/">服务与合作</a><a href="http://www.edaijia.cn/v2/">司机专区</a></div>
    <div class="copyright">Copyright &copy; 2011-2013 edaijia.cn All Right Reserved 版权所有 京ICP备13048976号-1 版权所有：北京亿心宜行汽车技术开发服务有限公司</div>
</div>
<!--弹屏广告-->
<!--<div class="ad">-->
<!--      <span onclick="hide()">X</span>-->
<!--    <img src="--><?php //echo SP_URL_STO; ?><!--www/images/screen.png" width="256" height="188" border="0"/>-->
<!--</div>-->
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fc03310ce23cf3cde07f3d6d1764fd5d3' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>
