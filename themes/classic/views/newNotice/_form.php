<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'notice-create-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'focus' => array($model, 'NewNotice'),
));
?>
<div class="span12">
        <div class="row-fluid">
            <div class="span1">发布范围</div>
            <div class="span1">
                <label for="checked_all_citys">
                    <?php echo CHtml::CheckBox('city_id_all','',array('id'=>'checked_all_citys')).'&nbsp;&nbsp;全部';?>
                </label>
            </div>
        </div>
        <br/>
        <div class="row-fluid">
            <div class="span1">　</div>
            <div class="span10">
                <?php
                $city = explode(',', $model->city_ids);
                $citys = Dict::items('city');
                unset($citys[0]);
                foreach ($citys as $key=>$item){
                    if(mb_strlen($item,'utf-8')==2){
                        $item=$item.'　';
                    }
                    echo CHtml::checkBox("city[]",false,array("value"=>$key,'class'=>'city_id','id'=>$key)).'　'.$item.'　';
                    if($key%8==0) echo '<br/>';
                }
                ?>
            </div>
        </div>
        <br/>
        <div class="row-fluid">
            <div class="span1">发布形式</div>
            <div class="span3"><?php echo CHtml::dropDownList('type',!isset($model->type)?0:($model->type==0?0:1),NewNotice::$types,array('id'=>'type_id')); ?></div>
            
            <div class="span1">有效期至</div>
            <div class="span5">
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array (
                    'model'=>$model,
                    'name'=>'deadline',
                    'value'=>$model->deadline,
                    'attribute'=>'deadline',
                    'mode'=>'datetime',
                    'options'=>array (
                        'dateFormat'=>'yy-mm-dd'
                    ),
                    'language'=>'zh',
                    'htmlOptions'=>array(
                        'placeholder'=>"yy-mm-dd",
                    ),
                ));
                ?>　

                <span style="color:red">*默认有效期为一周</span>

            </div>
        </div>
        <div class="row-fluid punish_rules">
            <div class="span1">发布规则</div>
            <div class="span10"><strong>司机打开程序(任何状态下)即刻开始播报，且不可取消跳过，用于高级别的公告！适用于：规则变更、警示类、现场紧急调度或直接影响司机利益的公告内容。</strong></div>
        </div>
        <br/>
        <div class="row-fluid">
            <div class="span1">标题</div>
            <div class="span10">
                <?php echo CHtml::activeDropDownList($model,'category',NewNotice::$WebCategorys,array('class'=>'span2')); ?>
                <?php echo $form->textField($model,'title',array('class'=>'span8'))?>
            </div>
        </div>
        <br/>
        <div class="row-fluid">
        <div class="span1">优先级</div>
            <div class="span3"><?php echo CHtml::dropDownList('priority',$model->priority,NewNotice::$prioritys,array('id'=>'priority_id')); ?></div>
         </div>
        <br/>
        <div class="row-fluid">
            <div class="span1">内容</div>
            <div class="span10">
                <?php echo CHtml::textArea('content',$model->content,array('style'=>'width:100%;height:100px;','id'=>'content_text_id'));?>
            </div>
        </div>
        <br/>
        <div class="row-fluid long_is_display">
            <div class="span1">　</div>
            <div class="span10 long_is_display_title"></div><br/>
        </div>
        <div class="row-fluid add_other_long">
            <div class="span1">　</div>
            <div class="span10">
                <a data-toggle="modal" data-target="" id="get_long_post_id" class="btn btn-info btn-small"  url="<?php echo Yii::app()->createUrl('newNoticePost/addLong',array('post_id'=>$model->post_id?$model->post_id:0))?>" style="display:inline-block;cursor:pointer;" >附加长文章</a>　　
                <span>长文章可使用图片，支持各类文本编辑功能，如无必要尽量使用简单内容发布公告。</span>
            </div>
        </div>
        <?php if(!$model->isNewRecord && $model->source!='WEB' && $model->audio_url!='') { ?>
            <div class="row-fluid">
                <div class="span1">音频地址</div>
                <div class="span10"><a href="<?php echo $model->audio_url ?>" class="btn btn-small btn-info">下载</a></div>
            </div>
        <?php } ?>
        <input type="hidden" name="re" value="<?php echo isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:''?>"/>
        <input type="hidden" name="post_id" value="<?php echo $model->post_id?$model->post_id:0; ?>" id="post_id_hidden"/>
        <div class="row-fluid">
            <div class="span1">　</div>
            <div class="span10" style="text-align: center">
                <?php echo CHtml::submitButton($model->isNewRecord ? '发布' : '保存',array('class'=>'btn-large btn btn-info')); ?>　
                <?php echo CHtml::activeCheckBox($model,'booking_push_flag',array('id'=>'booking_push_flag','name'=>'booking_push_flag')).'&nbsp;&nbsp;预约发布';?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array (
                    'id'=>'booking_push_datetime',
                    'model'=>$model,
                    'name'=>'booking_push_datetime',
                    'value'=>$model->booking_push_datetime,
                    'attribute'=>'booking_push_datetime',
                    'mode'=>'datetime',
                    'options'=>array (
                        'dateFormat'=>'yy-mm-dd',
                    ),
                    'language'=>'zh',
                    'htmlOptions'=>array(
                        'placeholder'=>"yy-mm-dd",
                    ),
                ));
                ?>
                <span style="color:red">*预约发布时间不能小于当前系统时间,如小于则为失效</span>
            </div>
        </div>
</div>
<?php $this->endWidget(); ?>
<script type="text/javascript">
    $(function(){
        $('.long_is_display').css('display','none');
        var city = '<?php echo $model->city_ids;?>';
        var city_arr = city.split(',');
        //alert(city);
        if(city_arr.length>0&&city!=0){

            for(i=0;i<city_arr.length;i++){
                $('#'+city_arr[i]).attr("checked","true");
            }
            if($(".city_id:checked").size()==$('.city_id').length){
                $('#checked_all_citys').attr('checked',true);
            }
        }

        if($('#type_id').val()==0){
            $('.punish_rules').css('display','none');
            $('.add_other_long').css('display','block');
        }else{
            $('.add_other_long').css('display','none');
        }

        $('#checked_all_citys').click(function(){
            if($('#checked_all_citys').attr('checked')){
                $('.city_id').attr('checked',true);
            }else{
                $('.city_id').attr('checked',false);
            }
        });
        $('.city_id').click(function(){
            if(this.checked==false){
                $('#checked_all_citys').attr('checked',false);
            }else if($(".city_id:checked").size()==$('.city_id').length){
                $('#checked_all_citys').attr('checked',true);
            }
        });
        $('#type_id').change(function(){
            if($('#type_id').val()==1){
                $('.punish_rules').css('display','block');
                $('.add_other_long').css('display','none');
                $('#post_id_hidden').val(0);
                $('.long_is_display').css('display','none');
            }else if($('#type_id').val()==0){
                $('.punish_rules').css('display','none');
                $('.add_other_long').css('display','block');
            }
        });
        //验证提交
        $('input[type="submit"]').click(function(){
            <?php if($model->isNewRecord) {?>
                if($('#content_text_id').val()==''){
                    alert('内容在不能为空');
                    return false;
                }
            <?php }?>

            if($('#booking_push_flag').attr('checked')){
                if($('#booking_push_datetime').val()==''){
                    alert('选择预约发布请填写预约发布时间');
                    return false;
                }
                if($('#booking_push_datetime').val()>$('#deadline').val()){
                    alert('预约发布时间不能大于有效截止时间');
                    return false;
                }
            }
            if($('#content_text_id').val().length>150){
                alert('内容在150字内');
                return false;
            }
            if($(".city_id:checked").size()==0){
                alert('请选择城市');
                return false;
            }
            if($('#NewNotice_title').val()==''||$('#NewNotice_title').val().length>255){
                alert('标题不能为空且在255位内');
                return false;
            }
            $('#notice-create-form').submit();
            $('input[type="submit"]').attr('disabled',true);
        });
    });
</script>
<?php $this->renderPartial('public'); ?>