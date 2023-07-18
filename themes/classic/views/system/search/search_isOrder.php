
<div id="div_order">
    <iframe brand_link="#div_order" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="order" name="order" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'OrderInfo')); ?>"></iframe>
</div>

<!--<div id="div_position">
    <iframe brand_link="#div_position" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="position" name="position" src="<?php // echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'OrderPosition')); ?>"></iframe>
</div>-->

<div id="div_complain">
    <iframe brand_link="#div_complain" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="complain" name="complain" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'OrderComplainInfo')); ?>"></iframe>
</div>

<div id="div_commentSms">
    <iframe brand_link="#div_commentSms" width="100%" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto"  id="commentSms" name="commentSms" src="<?php echo Yii::app()->createUrl('system/searchItem', array('q'=>$q, 'item'=>'OrderCommentSms')); ?>"></iframe>
</div>


<script>

    var iframe_list = new Array(
        'basic', 'order', 'mark_log',
        'recruitment_log', 'recommand',
        'punish', 'complain','commentSms',
        'position'
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