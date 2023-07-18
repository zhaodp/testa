<iframe id="js_adminEvent" src="" style="width: 100%;height: auto;border: 0px;margin: 0px;padding: 0px;" scrolling="auto"  /></iframe>
<script type="text/javascript">
    $().ready(function(){
        $('#js_adminEvent').attr('src','<?php echo Yii::app()->params['h5_url'], '/calendar/index.html?_=1001'; ?>');
    });
    function reinitIframe() {
        var hh=document.documentElement.clientHeight;
        var varh = hh-150;      //菜单高度 + 底部高度 = 150px
        $("#js_adminEvent").css("height",varh);
    }
    window.setInterval('reinitIframe()',200);
</script>