<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
    <title>e代驾 -全国最大代驾公司，代驾费只需39元起，免费下载e代驾手机应用，已开通城市北京上海杭州广州深圳重庆，服务热线4006-91-3939</title>
    <meta name="description" content="e代驾，提供酒后代驾、商务代驾，服务城市开通北京上海杭州广州深圳，正规服务，专业代驾，费用最低39起步。e代驾，易代驾！电话4006-91-3939"/>
    <meta name="keywords" content="e代驾,北京代驾公司,酒后代驾公司,长途代驾,北京汽车代驾,代驾服务公司"/>
    <?php
    $cs = Yii::app()->clientScript;
    $cs->coreScriptPosition = CClientScript::POS_HEAD;
    $cs->scriptMap = array();
    $cs->registerCoreScript('jquery');
    $cs->registerScriptFile(SP_URL_STO . 'www/js/png.js');
    $cs->registerScriptFile(SP_URL_STO . 'www/js/tab.js');
    $cs->registerScriptFile(SP_URL_STO . 'www/js/box.js');
    $cs->registerCssFile(SP_URL_STO . 'www/css/edaijia.css');
    ?>
    <script>

        //        $(document).ready(function () {
        //            $(".ajax").colorbox({iframe: true, innerWidth: 985, innerHeight: 505});
        //            //Example of preserving a JavaScript event for inline calls.
        //            $("#click").click(function () {
        //                $('#click').css({"background-color": "#f00", "color": "#fff", "cursor": "inherit"}).text("Open this window again and this message will still be here.");
        //                return false;
        //            });
        //        });
        //
        $(window).load(function () {
            show();
        });


        function show() {
            $(".ad").slideDown(1500);
        }

        function hide() {
            $(".ad").slideUp(1500);
        }
    </script>
</head>
<body>
<div id="header">
    <div class="block head">
        <a href="/" class="logo"><img src="<?php echo SP_URL_STO; ?>www/images/logo.png" width="320" height="45"
                                      border="0"/></a>
        <ul class="nav">
            <li><a href="/" class="actives">首页</a></li>
            <li><a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/entry/">司机在线报名</a></li>
            <li><a href="/about/">关于我们</a></li>
            <li><a href="/faq/">FAQ</a></li>
        </ul>
    </div>
</div>
<div class="blank0"></div>
<div class="block clearfix">
    <div class="banner">
        <div class="banner_img">
            <ul id="slideshow2">
                <li class="current"><img src="<?php echo SP_URL_STO; ?>www/images/banner-driver.png" width="460"
                                         height="350" border="0"/></li>
                <li><img src="<?php echo SP_URL_STO; ?>www/images/banner-39.png" width="460" height="350" border="0"/>
                </li>
                <li><img src="<?php echo SP_URL_STO; ?>www/images/banner-3.png" width="460" height="350" border="0"/>
                </li>
            </ul>
        </div>
    </div>
    <div class="blank0"></div>

    <div class="down">
        <ul class="down_btn">
            <li><a id="iphone" href="/jump.html?go=<?php echo urlencode($params['appVersionIphone']['url']); ?>"
                   target="_blank"><img
                        src="<?php echo SP_URL_STO; ?>www/images/download-iPhone.png" width="191" height="71"
                        border="0"/></a></li>
            <li><a id = "android" href='/jump.html?go=<?php echo urlencode($params["appVersionAndroid"]["semurl"]); ?>'
                   target="_blank"><img src="<?php echo SP_URL_STO; ?>www/images/download-android.png" width="191"
                                        height="71" border="0"/></a></li>

        </ul>
    </div>
</div>
<div class="blank"></div>
<div id="main" class="block clearfix">
    <div class="main_box clearfix">
        <div class="AearL">
            <dl class="AearL_dl_l">
                <dt><img src="<?php echo SP_URL_STO; ?>www/images/body-sh.png" width="220" height="276" border="0"/>
                </dt>
                <dd>
                    <h2>实惠</h2>

                    <p>
                        代驾？ 不再是贵族享有特权，【e代驾】打破<br/>
                        市场坚冰，让e代驾成为百姓必备软件，<br/>
                        记住呦：谁说开车不喝酒，e代驾只需39。<br/>
                        最in的价格就在e代驾
                    </p>
                </dd>
            </dl>
            <div class="blank0"></div>
            <dl class="AearL_dl_r">
                <dt><img src="<?php echo SP_URL_STO; ?>www/images/body-aq.png" width="220" height="276" border="0"/>
                </dt>
                <dd>
                    <h2>安全</h2>

                    <p>
                        全国唯一拥有代驾责任险的公司，五年以上驾龄<br/>
                        的代驾司机为您护航，上岗前e代驾定期对每一<br/>
                        位代驾司机进行严格考核，定期进行安全驾驶<br/>
                        培训，旨在为您安享最值得信赖代驾服务
                    </p>
                </dd>
            </dl>
            <div class="blank0"></div>
            <dl class="AearL_dl_l">
                <dt><img src="<?php echo SP_URL_STO; ?>www/images/body-kj.png" width="220" height="276" border="0"/>
                </dt>
                <dd class="border_none">
                    <h2>快速</h2>

                    <p>
                        海量代驾司机，近才够快！<br/>
                        e代驾平均到达客户时间15分钟，使用e代驾<br/>
                        客户端直接召唤离您最近的5名司机，<br/>
                        代驾，无需苦苦等待
                    </p>
                </dd>
            </dl>
        </div>
        <div class="AearR">
            <div class="e_title"><span id="city" onclick="city()">北京</span>开通城市</div>
            <ul class="ddl" style="display:none;">
                <li><a href="javascript:;" id="city_list_1" onclick="city_sel(1,1)">北京</a></li>
                <li><a href="javascript:;" id="city_list_3" onclick="city_sel(1,3)">上海</a></li>
                <li><a href="javascript:;" id="city_list_5" onclick="city_sel(1,5)">广州</a></li>
                <li><a href="javascript:;" id="city_list_6" onclick="city_sel(1,6)">深圳</a></li>
                <li><a href="javascript:;" id="city_list_14" onclick="city_sel(3,14)">天津</a></li>
                <li><a href="javascript:;" id="city_list_4" onclick="city_sel(3,4)">杭州</a></li>
                <li><a href="javascript:;" id="city_list_7" onclick="city_sel(4,7)">重庆</a></li>
                <li><a href="javascript:;" id="city_list_2" onclick="city_sel(2,2)">成都</a></li>
                <li><a href="javascript:;" id="city_list_8" onclick="city_sel(3,8)">南京</a></li>
                <li><a href="javascript:;" id="city_list_11" onclick="city_sel(3,11)">西安</a></li>
                <li><a href="javascript:;" id="city_list_18" onclick="city_sel(3,18)">郑州</a></li>
                <li><a href="javascript:;" id="city_list_10" onclick="city_sel(3,10)">武汉</a></li>
                <li><a href="javascript:;" id="city_list_15" onclick="city_sel(3,15)">济南</a></li>
                <li><a href="javascript:;" id="city_list_9" onclick="city_sel(3,9)">长沙</a></li>
                <li><a href="javascript:;" id="city_list_20" onclick="city_sel(3,20)">青岛</a></li>
                <li><a href="javascript:;" id="city_list_12" onclick="city_sel(3,12)">宁波</a></li>
                <li><a href="javascript:;" id="city_list_16" onclick="city_sel(3,16)">苏州</a></li>
		<li><a href="javascript:;" id="city_list_24" onclick="city_sel(3,24)">哈尔滨</a></li>
                <li><a href="javascript:;" id="city_list_33" onclick="city_sel(3,33)">贵阳</a></li>
            </ul>
            <div class="date_region">
                <dl id="time1">
                    <dt><span style='width:120px;'>时间段</span><span style='width:84px;'>代驾费</span></dt>
                    <dd><span style='width:120px;'>07:00—21:59</span><span style='width:84px;'>39元</span></dd>
                    <dd><span style='width:120px;'>22:00—22:59</span><span style='width:84px;'>59元</span></dd>
                    <dd><span style='width:120px;'>23:00—23:59</span><span style='width:84px;'>79元</span></dd>
                    <dd><span style='width:120px;'>00:00—06:59</span><span style='width:84px;'>99元</span></dd>
                </dl>
                <dl id="time2" style="display:none;">
                    <dd><span>时间段</span><span>起步价(5公里以内)</span></dd>
                    <dd><span>全天</span><span>39元</span></dd>
                </dl>
                <dl id="time3" style="display:none;">
                    <dd><span style='width:120px;'>时间段</span><span style='width:84px;'>代驾费</span></dd>
                    <dd><span style='width:120px;'>07:00—21:59</span><span style='width:84px;'>39元</span></dd>
                    <dd><span style='width:120px;'>22:00—06:59</span><span style='width:84px;'>59元</span></dd>
                </dl>
                <dl id="time4" style="display:none;">
                    <dd><span style='width:90px;'>时间段</span><span style='width:114px;'>起步价(10公里以内)</span></dd>
                    <dd><span>全天</span><span>39元</span></dd>
                </dl>
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
2、代驾距离超过5公里后，每5公里
加收20元，不足5公里按5公里计算。
3、等候时间每满30分钟收费20元，不
满30分钟不收费。
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
2、代驾距离超过10公里后，每5公里
加收20元，不足5公里按5公里计算。
3、等候时间每满30分钟收费20元，不
满30分钟不收费。
                </pre>
            </div>
            <div class="blank"></div>
            <div class="link">
                <iframe width="205" height="390" class="share_self" frameborder="0" scrolling="no"
                        src="http://widget.weibo.com/weiboshow/index.php?language=&width=205&height=391&fansRow=1&ptype=1&speed=100&skin=1&isTitle=1&noborder=1&isWeibo=1&isFans=1&uid=2336563625&verifier=c46ba8aa&dpc=1"></iframe>
            </div>
        </div>
        <div class="blank0"></div>
    </div>
</div>
<div class="blank"></div>
<div id="footer" class="block">
    <div class="foot_nav"><a href="/about/">关于e代驾</a><a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/">e代驾招募</a><a href="/hezuo/">服务与合作</a><a
            href="http://www.edaijia.cn/v2/">司机专区</a></div>
    <div class="copyright">Copyright &copy; 2011-2013 edaijia.cn All Right Reserved 版权所有 京ICP备13048976号-1
        版权所有：北京亿心宜行汽车技术开发服务有限公司
    </div>
</div>

<!--弹屏广告-->
<div class="ad">
    <span onclick="hide()">X</span>
    <img src="<?php echo SP_URL_STO; ?>www/images/screen.png" width="256" height="188" border="0"/>
</div>
<div style="display: none">
    <script type="text/javascript">
        var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
        document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F7454e01c8dde2d8b0c96cd03b58b0c23' type='text/javascript'%3E%3C/script%3E"));
    </script>
</div>
</body>
</html>
