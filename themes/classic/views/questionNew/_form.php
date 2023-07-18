<?php
/* @var $this QuestionController */
/* @var $model Question */
/* @var $form CActiveForm */

$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_STO.'www/js/swfobject.js');
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.uploadify.v2.1.0.min.js');
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.validate.js');

?>
<!--<link rel="stylesheet"  type="text/css" href="--><?php //echo SP_URL_STO;?><!--/www/css/uploadify.css">-->
<!--<script type="text/javascript" src="--><?php //echo SP_URL_STO;?><!--/www/js/jquery.uploadify.v3.2.1.min.js"></script>-->
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'question-form',
    'enableAjaxValidation'=>false,
)); ?>
<div class='grid-view'>
<?php echo $form->errorSummary($model); ?>

    <div id='hiden_div'>
        <?php echo $form->labelEx($model, 'category');
        if (Yii::app()->user->city == 0) {
            echo $form->dropDownList($model, 'category', QuestionNew::getCategory());
        } else {
            $category = array('13'=>'地域题');
            echo $form->dropDownList($model, 'category', $category);
        }
         echo $form->error($model,'category')?>
    </div>

    <?php echo $form->labelEx($model,'title'); ?>
    <?php echo $form->textArea($model, 'title',array('style'=>'width:400px;height:40px','maxlength'=>255));?>
    <?php echo $form->error($model,'title'); ?>

    <span>
        <input type="file"  func="uploadify" file_target="title_img" id="title_imga"/>
        <input required="true" isPic="true" id="title_img" type="hidden" name="QuestionNew[title_img]" value="<?php if($model->title_img) { echo $model->title_img;} ?>" /> 上传标题图片
        <div id="title_img_container">
            <?php if($model->title_img) { echo '<img src="'.$model->title_img.'" style="height:100px;">';} ?>
        </div>
    </span>



<!--    <div id="fileQueue" style="width: 400px;height: 300px; border: 2px solid green;"></div>-->
    <div>
        <label for="CustomerQuestion_contents"><h4>选项列表</h4> <!-- <a href="javascript:;" onclick="add_list();">添加选项</a> --></label>
        <div id="item_list">
            <div id="list_A" class="customer_list">
                <label for="CustomerQuestion_contents">A选项</label>
                <input type="checkbox" name="QuestionNew[answer][]" value="A" />&nbsp;&nbsp;
                <?php echo $form->textField($model, 'option_a', array('style'=>'width:480px;','size'=>80)); ?>
                <?php
                echo $form->hiddenField($model,'img_a',array('value'=>$model->img_a,'id'=>'option_a'))?>
                <span>
                <input type="file"  func="uploadify" file_target="option_a" id="option_aa"/>
                上传A选项图片
                <div id="option_a_container">
                    <?php if($model->img_a) { echo '<img src="'.$model->img_a.'" style="height:100px;" id="option_a_show">';} ?>
                </div>
                </span>

            </div>
            <div id="list_B" class="customer_list">
                <label for="CustomerQuestion_contents">B选项</label>
                <input type="checkbox" name="QuestionNew[answer][]" value="B" />&nbsp;&nbsp;
                <?php echo $form->textField($model, 'option_b', array('style'=>'width:480px;','size'=>80));
                echo $form->hiddenField($model,'img_b',array('value'=>$model->img_b,'id'=>'option_b'));?>
                <span>
                <input type="file"  func="uploadify" file_target="option_b" id="option_ba"/>
                 上传B选项图片
                <div id="option_b_container">
                    <?php if($model->img_b) { echo '<img src="'.$model->img_b.'" style="height:100px;"  id="option_b_show">';}?>
                </div>
                </span>

            </div>
            <div id="list_C" class="customer_list">
                <label for="CustomerQuestion_contents">C选项</label>
                <input type="checkbox" name="QuestionNew[answer][]" value="C" />&nbsp;&nbsp;
                <?php echo $form->textField($model, 'option_c', array('style'=>'width:480px;','size'=>80));
                echo $form->hiddenField($model,'img_c',array('value'=>$model->img_c,'id'=>'option_c'))?>
                <span>
                <input type="file"  func="uploadify" file_target="option_c" id="option_ca"/>
                上传C选项图片
                <div id="option_c_container">
                    <?php if($model->img_c) { echo '<img src="'.$model->img_c.'" style="height:100px;"  id="option_c_show">';}?>
                </div>
                </span>

            </div>
            <div id="list_D" class="customer_list">
                <label for="CustomerQuestion_contents">D选项</label>
                <input type="checkbox" name="QuestionNew[answer][]" value="D" />&nbsp;&nbsp;
                <?php echo $form->textField($model, 'option_d', array('style'=>'width:480px;','size'=>80));
                echo $form->hiddenField($model,'img_d',array('value'=>$model->img_d,'id'=>'option_d'))?>
                <span>
                <input type="file"  func="uploadify" file_target="option_d" id="option_da"/>
                 上传D选项图片
                <div id="option_d_container">
                    <?php if($model->img_d) { echo '<img src="'.$model->img_d.'" style="height:100px;">';}?>
                </div>
                </span>
            </div>

            <?php echo $form->hiddenField($model, 'type');?>
        </div>
    </div>
    <div><?php echo $form->labelEx($model, 'interpretation')?>
        <?php echo $form->textArea($model, 'interpretation',array('style'=>'width:400px;height:200px'));?>
    </div>
    <div id="city_container" style="padding: 10px;" >
        <label for="Question_city_id">适用城市：</label>
        <?php
        $citys = Dict::items('city');
        //        $disabled = Yii::app()->user->city == 0 ? '' : "return false";
        foreach ($citys as $key => $item){
            //分公司只有分公司所在地被选中，总部全部选中
            $checked = '';
            $id = '';
            if ($this->getAction()->getId() == 'create') {
                if (Yii::app()->user->city == $key || Yii::app()->user->city == 0) {
                    $checked = 'checked="checked"';
                }
            } else if ($this->getAction()->getId() == 'update') {
                if (in_array($key, $city_ids)) {
                    $checked = 'checked="checked"';
                }
            }
            if ($key == 0) {
                $id = 'id="Question_city_id"';
            }
            if (Yii::app()->user->city != 0) {
                echo '<input type="checkbox" name="QuestionNew[city_id][]" onclick="return false;" value="'.$key.'" '.$checked.' '.$id.'/>'.$item."&nbsp;";
            } else {
                echo '<input type="checkbox" name="QuestionNew[city_id][]" value="'.$key.'" '.$checked.' '.$id.'/>'.$item."&nbsp;";
            }
        }
        ?>
    </div>

        <div class="buttons">
            <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '更新'); ?>
            <?php echo CHtml::button('取消',array('onclick'=>'window.open("'.Yii::app()->createUrl('question/index').'","_self","param")'));?>
        </div>
    </div><?php $this->endWidget(); ?>

</div><!-- form -->
<style>
    .customer_list{ padding-bottom: 20px;}
</style>
<script type="text/javascript">

    $(document).ready(function(){



        jQuery('#question-form').submit(function(){
                var answer = jQuery('input[name="QuestionNew[answer][]"]:checked').length;
                if (answer > 1) {
                    jQuery('#QuestionNew_type').attr('value', 1);
                } else if (answer == 1) {
                    jQuery('#QuestionNew_type').attr('value', 0);
                } else {
                    alert('请设置正确答案');
                    return false;
                }

            if (jQuery('#QuestionNew_title').val() == '') {
                alert('请输入标题');
                return false;
            }
        });


        var citys_id = new Array();

        model_citys_id = '<?php if(isset($model->city_id)){ echo $model->city_id;}?>';
        if(model_citys_id!=''&&model_citys_id!=0){
            citys_id = model_citys_id.split(',');
            for(i=0;i<7;i++){
                if(citys_id[0]==0){
                    $(".city_id").attr('checked','checked');
                }
                if(citys_id[i]!=''&&citys_id[i]!='undefine')
                    $(".city_id").eq(citys_id[i]).attr('checked','checked');
            }
        }
        <?php if (Yii::app()->user->city == 0 ) {?>
        $("#Question_city_id").click(function(){
            $("input[name='QuestionNew[city_id][]']").attr('checked', this.checked);
        });
        <?php } ?>
        //正确答案选中
        var true_correct = '<?php if(isset($model->answer)){ $tmp = str_split($model->answer); echo implode(',',$tmp);} else echo '';?>';
        if(true_correct!=''){
            true_correct = true_correct.split(',');
            for(i=0;i<true_correct.length;i++){
                $("input[value='"+true_correct[i]+"']").attr('checked', 'checked');
            }
        }

        /*
         *  上传插件配置参数
         */
        var uploadify_config = {
            'uploader'       : '<?php echo SP_URL_STO.'www/js/uploadify.swf'; ?>',
            'script'         : '<?php echo Yii::app()->createUrl('image/upload');?>',
            'cancelImg'      : '<?php echo SP_URL_STO.'www/images/cancel.png'; ?>',
            'folder'         : 'examNew',
            'queueID'        : 'fileQueue',
            'buttonText'     : 'upload',
            'auto'           : true,
            'multi'          : true,
            'displayData'    : 'speed',
            'fileSizeLimit'  : '1024KB',
            //'fileDesc'       : 'jpg文件或jpeg文件',
            //'fileExt'        : '*.jpg;*.png',
            'scriptData'     : {bucketname:'edaijia'},

            onComplete : function(evt, queueID, fileObj, response, data){
                eval("var theJsonValue = "+response);
                var img_url = theJsonValue.data;
                var file_target_id = jQuery(evt.target).attr('file_target');
                var file_target = jQuery('#'+file_target_id);
                //var file_show = $('#'+file_target_id + '_container');
                file_target.val(img_url);
                $('#'+file_target_id + '_container').empty();
                $('#'+file_target_id + '_container').html('<img style="height:100px;" src="'+img_url+'" height="100" />');
                //file_target.parent().append('<img style="height:100px;" src="'+img_url+'" height="100" />');
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
        jQuery('[func="uploadify"]').each(function(){
            var file_target = jQuery(this).attr('file_target');
            var queueID = file_target.replace('_', '-');
            jQuery('#'+file_target).parent().attr('id', queueID);
            uploadify_config.queueID = queueID;
            jQuery(this).uploadify(uploadify_config);
            //alert('aaa');
            jQuery(this).removeAttr('func');
        });

        //END
//        containerShow();
//        jQuery('#Question_track').click(function(){
//            containerShow();
//        });
//
//        function containerShow() {
//            var exam_type = jQuery('#Question_track').val();
//            if (exam_type == 3 || exam_type == 4) {
//                jQuery('#city_container').show();
//            } else {
//                jQuery('#city_container').hide();
//            }
//        }
    });
    Array.prototype.indexOf = function(val) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == val) return i;
        }
        return -1;
    };
</script>