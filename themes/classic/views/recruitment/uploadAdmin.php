<?php
$fancybox_js_path = SP_URL_STO.'www/js/fancybox';

$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_STO.'www/js/swfobject.js');
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.uploadify.v2.1.0.min.js');

?>
<style type="text/css">
    div#wrap {
        width:auto;
        margin:0 auto;
    }
    td {
        line-height: 40px;
    }
</style>

<div class="row-fluid">
    <ul class="thumbnails">
        <!--
        <li class="span4">
            <div class="thumbnail">
                <div id="head" style="height : 193px;">
                    <a href="<?php echo $bath_list['head']['url'].'?ver='.time();?>" target="_blank">
                <?php
                    //if (file_exists($bath_list['head']['url'])) {
                        echo CHtml::image($bath_list['head']['url'].'?ver='.time(), '', array('id'=>'img_head', 'style'=>'width:156px; height:193px;'));
                    //}
                ?>
                    </a>
                </div>
                <div class="caption">
                    <h3>头 像</h3>
                    <p><input type="file"  img_type="head" class="uploadify" id="file_head"/></p>
                </div>
            </div>
        </li>
        -->
        <li class="span6">
            <div class="thumbnail">
                <div id="id_card" style="height : 193px;">
                    <a href="<?php echo $bath_list['id_card']['url'].'?ver='.time();?>" target="_blank">
                    <?php
                    //if (file_exists($bath_list['id_card']['url'])) {
                    echo CHtml::image($bath_list['id_card']['url'].'?ver='.time(), '', array('id'=>'img_id_card', 'style'=>'width:156px; height:193px;'));
                    //}
                    ?>
                    </a>
                </div>
                <div class="caption">
                    <h3>司机资料</h3>
                    <p><input type="file"  img_type="id_card" class="uploadify" id="file_id_card"/></p>
                </div>
            </div>
        </li>
        <li class="span6">
            <div class="thumbnail">
                <div id="driver_card" style="height : 193px;">
                    <a href="<?php echo $bath_list['driver_card']['url'].'?ver='.time();?>" target="_blank">
                    <?php
                    //if (file_exists($bath_list['head']['url'])) {
                    echo CHtml::image($bath_list['driver_card']['url'].'?ver='.time(), '', array('id'=>'img_driver_card', 'style'=>'width:156px; height:193px;'));
                    //}
                    ?>
                    </a>
                </div>
                <div class="caption">
                    <h3>担保资料</h3>
                    <p><input type="file" img_type="driver_card" class="uploadify" id="file_driver_card"/></p>
                </div>
            </div>
        </li>
    </ul>

<ul class="thumbnails">
        <li class="span6">
            <div class="thumbnail">
                <div id="pic1" style="height : 193px;">
                    <a href="<?php echo $bath_list['pic1']['url'].'?ver='.time();?>" target="_blank">
                    <?php
                    //if (file_exists($bath_list['id_card']['url'])) {
                    echo CHtml::image($bath_list['pic1']['url'].'?ver='.time(), '', array('id'=>'img_pic1', 'style'=>'width:156px; height:193px;'));
                    //}
                    ?>
                    </a>
                </div>
                <div class="caption">
                    <h3>驾驶证主页</h3>
                    <p><input type="file"  img_type="pic1" class="uploadify" id="file_pic1"/></p>
                </div>
            </div>
        </li>
        <li class="span6">
            <div class="thumbnail">
                <div id="pic2" style="height : 193px;">
                    <a href="<?php echo $bath_list['pic2']['url'].'?ver='.time();?>" target="_blank">
                    <?php
                    //if (file_exists($bath_list['head']['url'])) {
                    echo CHtml::image($bath_list['pic2']['url'].'?ver='.time(), '', array('id'=>'img_pic2', 'style'=>'width:156px; height:193px;'));
                    //}
                    ?>
                    </a>
                </div>
                <div class="caption">
                    <h3>驾驶证副页</h3>
                    <p><input type="file" img_type="pic2" class="uploadify" id="file_pic2"/></p>
                </div>
            </div>
        </li>
    </ul>


<ul class="thumbnails">
        <li class="span6">
            <div class="thumbnail">
                <div id="pic3" style="height : 193px;">
                    <a href="<?php echo $bath_list['pic3']['url'].'?ver='.time();?>" target="_blank">
                    <?php
                    //if (file_exists($bath_list['id_card']['url'])) {
                    echo CHtml::image($bath_list['pic3']['url'].'?ver='.time(), '', array('id'=>'img_pic3', 'style'=>'width:156px; height:193px;'));
                    //}
                    ?>
                    </a>
                </div>
                <div class="caption">
                    <h3>身份证正面</h3>
                    <p><input type="file"  img_type="pic3" class="uploadify" id="file_pic3"/></p>
                </div>
            </div>
        </li>
        <li class="span6">
            <div class="thumbnail">
                <div id="pic4" style="height : 193px;">
                    <a href="<?php echo $bath_list['pic4']['url'].'?ver='.time();?>" target="_blank">
                    <?php
                    //if (file_exists($bath_list['head']['url'])) {
                    echo CHtml::image($bath_list['pic4']['url'].'?ver='.time(), '', array('id'=>'img_pic4', 'style'=>'width:156px; height:193px;'));
                    //}
                    ?>
                    </a>
                </div>
                <div class="caption">
                    <h3>身份证反面</h3>
                    <p><input type="file" img_type="pic4" class="uploadify" id="file_pic4"/></p>
                </div>
            </div>
        </li>
    </ul>
</div>

<script>
    jQuery(document).ready(function(){
        /*
         *  上传插件配置参数
         */
        var uploadify_config = {
            'uploader'       : '<?php echo SP_URL_STO.'www/js/uploadify.swf'; ?>',
            'script'         : '<?php echo Yii::app()->createUrl('image/upload');?>',
            'cancelImg'      : '<?php echo SP_URL_STO.'www/images/cancel.png'; ?>',
            'folder'         : '0', //上传又拍的文件夹
            'queueID'        : 'head',
            'buttonText'     : 'upload',
            'auto'           : true,
            'multi'          : true,
            'displayData'    : 'speed',
            //'fileDesc'       : 'jpg文件或jpeg文件',
            //'fileExt'        : '*.jpg;*.png',
            'scriptData'     : {bucketname:'edriver'},
            onComplete : function(evt, queueID, fileObj, response, data){
                eval("var theJsonValue = "+response);
                var img_url = theJsonValue.data;
                //img_url += '_middle';
                var file_target_id = jQuery(evt.target).attr('img_type');
                var file_target = jQuery('#'+file_target_id);
                if (file_target.find('#img_'+file_target_id).length > 0){
                    file_target.find('#img_'+file_target_id).remove();
                }
                file_target.append('<img id="img_'+file_target_id+'" style="height:187px;" src="'+img_url+'?ver='+RndNum(10)+createCode()+'" height="100" />');
            },
            onError : function(a, b, c, d){
                if (d.status == 404)
                    alert('Could not find upload script. Use a path relative to: '+'<?= getcwd() ?>');
                else if (d.type === "HTTP")
                    alert('error '+d.type+": "+d.status);
                else if (d.type ==="File Size")
                    alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
                else
                    alert('error '+d.type+": "+d.text);
            }
        }

        /*
         * 绑定上传事件
         */
        var bath = <?php echo json_encode($bath_list);?>;
        jQuery('.uploadify').each(function(){
            var file_target = jQuery(this).attr('img_type');
            var my_config = uploadify_config;
            var bath_list = bath[file_target];
            my_config.queueID = file_target;
            my_config.folder = bath_list['up_dir'];
            var scriptData = my_config.scriptData;
            scriptData['img_name'] = bath_list['name'];
            my_config.scriptData = scriptData;
            jQuery(this).uploadify(my_config);
        });



    });


    function createCode()
    {
        code="";
        var codeLength=8;
        var selectChar=new Array(0,1,2,3,4,5,6,7,8,9,"A","B","C","D","E","F","G","H","I","G","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        for(var i=0;i<codeLength;i++)
        {
            var charIndex=Math.floor(Math.random()*32);
            code+=selectChar[charIndex];
        }
        if(code.length!=codeLength)
        {
            createCode();
        }
        return code;
    }


    function RndNum(n){
        var rnd="";
        for(var i=0;i<n;i++)
            rnd+=Math.floor(Math.random()*10);
        return rnd;
    }
</script>
