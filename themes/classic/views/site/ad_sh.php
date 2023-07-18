<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mtx
 * Date: 13-6-3
 * Time: 下午3:09
 * To change this template use File | Settings | File Templates.
 */
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <title>e代驾 -全国最大代驾公司，代驾费只需39元起，免费下载e代驾手机应用，已开通城市北京上海杭州广州深圳重庆，服务热线4006-91-3939</title>
    <?php
    $cs=Yii::app()->clientScript;
    $cs->coreScriptPosition=CClientScript::POS_HEAD;
    $cs->registerCssFile(SP_URL_STO.'www/css/edaijia.css');
    ?>
</head>
<body>
    <div class = "ad_box">
          <div class="ad_background">
               <a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/signup" class="baoming_btn"  target="_parent"></a>
          </div>
    </div>

</body>
</html>
