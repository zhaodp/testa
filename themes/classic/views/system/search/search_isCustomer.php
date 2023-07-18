<h1>客户信息</h1>

<div id="div_customerInfo">
    <iframe brand_link="#div_customerInfo" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="customerInfo" name="customerInfo" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'customerInfo')); ?>"></iframe>
</div>

<div id="div_order">
    <iframe brand_link="#div_order" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="order" name="order" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'customerOrder')); ?>"></iframe>
</div>

<div id="div_complain">
    <iframe brand_link="#div_complain" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="complain" name="complain" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'customerComplainInfo')); ?>"></iframe>
</div>

<div id="div_commentSms">
    <iframe brand_link="#div_commentSms" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="commentSms" name="commentSms" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'customerCommentSms')); ?>"></iframe>
</div>

<div id="div_bonusCode">
    <iframe brand_link="#div_bonusCode" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="bonusCode" name="bonusCode" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'customerBonusCode')); ?>"></iframe>
</div>


<script>

    var iframe_list = new Array(
        'basic', 'order', 'mark_log',
        'recruitment_log', 'recommand',
        'punish', 'complain','commentSms',
        'bonusCode', 'customerInfo'
    );

    jQuery(document).ready(function(){
        jQuery('strong').css('color', '#316AAF');
    });

    function reinitIframe(){
        for(var i=0; i<iframe_list.length; i++) {

        var iframe = document.getElementById(iframe_list[i]);
        try{
//            var bHeight = iframe.contentWindow.document.body.scrollHeight;
            var bHeight = 30;
            var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
            var height = Math.max(bHeight, dHeight);
            iframe.height = height;
            $(window.frames[iframe_list[i]].document).find(".brand").attr('href','<?php echo Yii::app()->request->url; ?>'+$('#'+iframe_list[i]).attr('brand_link'));
        }catch (ex){}
        }
    }

    window.setInterval("reinitIframe()", 200);
</script>