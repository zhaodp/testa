<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zty
 * Date: 13-8-6
 * Time: 下午2:56
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '司机档案';
?>

<style>
    body {
        margin: 0px;
        padding: 0px;
    }
</style>


<h1>司机档案</h1>

<div id="div_basic">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'basic')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="basic" name="basic" >
    </iframe>
</div>

<div id="div_complain">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'complain')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="complain" name="complain" >
    </iframe>
</div>

<div id="div_score">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'score')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="score" name="score" >
    </iframe>
</div>

<div id="div_refuseorder">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'refuseorder')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="refuseorder" name="refuseorder" >
    </iframe>
</div>

<div id="div_comment">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'comment')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="comment" name="comment" >
    </iframe>
</div>

<div id="div_wealth">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'wealth')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="wealth" name="wealth" >
    </iframe>
</div>

<div id="div_recruitment_log">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'recruitment_log')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="recruitment_log" name="recruitment_log" >
    </iframe>
</div>

<div id="div_mark_log">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=> 'mark_log'));?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="mark_log" name="mark_log" >
    </iframe>
</div>

<div id="div_recommand">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'recommand')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="recommand" name="recommand" >
    </iframe>
</div>


<div id="div_punish">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'punish')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="punish" name="punish" >
    </iframe>
</div>


<div id="div_order">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'order')); ?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="order" name="order" >
    </iframe>
</div>

<div id="div_online">
    <iframe width="100%" src ="<?php echo Yii::app()->createUrl('/driver/archives', array('id'=>$driver_id, 'act'=>'online'));?>" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" id="online" name="online" >
    </iframe>
</div>

<script>

    var iframe_list = new Array(
        'basic', 'complain','refuseorder' ,'comment', 'order', 'mark_log',
        'recruitment_log', 'recommand','wealth','score',
        'punish', 'online'
    );

    jQuery(document).ready(function(){
        jQuery('strong').css('color', '#316AAF');
    });

    function reinitIframe(){
        for(var i=0; i<iframe_list.length; i++) {

        var iframe = document.getElementById(iframe_list[i]);
        try{
            var bHeight = iframe.contentWindow.document.body.scrollHeight;
            var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
            var height = Math.max(bHeight, dHeight);
            iframe.height = height;
        }catch (ex){}
        }
    }

    window.setInterval("reinitIframe()", 200);
</script>


