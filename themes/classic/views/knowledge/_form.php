<?php
/* @var $this KnowledgeController */
/* @var $model Knowledge */
/* @var $form CActiveForm */
?>
<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'knowledge-form',
        'enableAjaxValidation' => false,
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'title', array('class' => 'span1')); ?>
        <?php echo $form->textField($model, 'title', array('class' => 'span5')); ?>
        <?php echo $form->error($model, 'title'); ?>
        <?php echo $form->error($model, 'description'); ?><br>
        <?php echo $form->error($model, 'city_id'); ?><br>
    </div>

    <div class="row-fluid">
        <?php
        $type = Dict::items('knowledge_type');
        echo $form->labelEx($model, 'typeid', array('class' => 'span1')); ?>
        <?php echo $form->radioButtonList($model, 'typeid', $type, array('separator' => '', 'template' => '<span class="radio inline">{input} {label}</span> ')); ?>
        <?php echo $form->error($model, 'typeid'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'city_id', array('class' => 'span1')); ?>
        <span class="span8" style="margin-left: -2px;">
            <input type="checkbox" name="all" id="che_all" value="1">&nbsp;&nbsp;全选
                <br />
                <?php
                $city = explode(',', $model->city_id);
                $citys = Dict::items('city');
                unset($citys[0]);
                $count = 0;
                foreach ($citys as $key=>$item){
                    $checked = in_array($key,$city)?true:false;
                    echo CHtml::checkBox("city[]",$checked,array("value"=>$key,'class'=>'city_id'))."&nbsp;&nbsp;".$item.'&nbsp;&nbsp;&nbsp;&nbsp;';
                    $count++;
                    if($count%9 == 0)
                        echo "<br />";
                }
                ?>
            <br/><br/>
        </span>

    </div>
    <div class="row-fluid" style="display: none;">
        <div class="span3">
            <?php echo $form->label($model, 'status'); ?>
            <?php echo $form->dropDownList($model, 'status', array('1' => '待审核', '2' => '已审核')); ?>
        </div>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'keywords', array('class' => 'span1')); ?>
        <?php echo $form->textField($model, 'keywords', array('class' => 'span8')); ?>
        <?php echo $form->error($model, 'keywords'); ?>
    </div>
    <br>
    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'description', array('class' => 'span1')); ?>
        <p>图片上传支持格式"jpg","bmp","gif","png"</p>
        <span class="span2" style="margin-left:0px;">
      <?php $this->widget('application.extensions.ckeditor.CKEditor',array(
                'model' => $model_data,
                'attribute' => 'content',
                'language'=>'zh-cn',
                'editorTemplate'=>'public',
                'options' => array(
                    'height' => '300px',
                    'width' => '850px',
                    'filebrowserImageUploadUrl'=>'index.php?r=image/imgupload&type=img&base_path='.Knowledge::PIC_BASE_PATH,
                ),
            ));
            ?>
        </span>
        <?php echo $form->error($model_data,'content'); ?>
    </div>
    <br>
    <div class="row-fluid">
        <?php echo $form->labelEx($model_data, 'drviercontent', array('class' => 'span1')); ?>
        <p>图片上传支持格式"jpg","bmp","gif","png"</p>
        <span class="span2" style="margin-left:0px;">
      <?php $this->widget('application.extensions.ckeditor.CKEditor',array(
          'model' => $model_data,
          'attribute' => 'drviercontent',
          'language'=>'zh-cn',
          'editorTemplate'=>'public',
          'options' => array(
              'height' => '300px',
              'width' => '850px',
              'filebrowserImageUploadUrl'=>'index.php?r=image/imgupload&type=img&base_path='.Knowledge::PIC_BASE_PATH,
          ),
      ));
      ?>
        </span>
        <?php echo $form->error($model_data,'drviercontent'); ?>
    </div>
    <br>
    <div class="row-fluid">
        <?php echo $form->labelEx($model_data, 'customercontent', array('class' => 'span1')); ?>
        <p>图片上传支持格式"jpg","bmp","gif","png"</p>
        <span class="span2" style="margin-left:0px;">
      <?php $this->widget('application.extensions.ckeditor.CKEditor',array(
          'model' => $model_data,
          'attribute' => 'customercontent',
          'language'=>'zh-cn',
          'editorTemplate'=>'public',
          'options' => array(
              'height' => '300px',
              'width' => '850px',
              'filebrowserImageUploadUrl'=>'index.php?r=image/imgupload&type=img&base_path='.Knowledge::PIC_BASE_PATH,
          ),
      ));
      ?>
        </span>
        <?php echo $form->error($model_data,'customercontent'); ?>
    </div>

    <div class="row-fluid buttons">
        <?php echo $form->labelEx($model_data, '&nbsp;', array('class' => 'span1')); ?>
        <?php echo CHtml::submitButton($model->isNewRecord ? '添加知识' : '保存知识', array('class' => 'btn btn-success span2','id'=>'subId')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->


<script type="text/javascript">
    $('body').on('click', '#case_add', function () {
        var length = $("#case .case").length;
        var case_add = '<div class="case" id = "case_' + length + '">' +
            '<label class="span1" >&nbsp</label>' +
            '<input type="hidden" name="KnowledgeCase[' + length + '][id]">' +
            '<textarea class="span8" name="KnowledgeCase[' + length + '][content]" rows="4"></textarea>' +
            '（<a class="case_minus" title = "去除" href = "javascript:void(0);" onclick = "del_case(\'case_' + length + '\')" id = "case_minus_' + length + '" rows="4">—</a>）' +
            '</div>';
        $("#case").append(case_add);
    });

    function del_case(id) {
        var kc_id = $('#' + id).attr('date');

        if (kc_id != '') {
            $.ajax({
                type: 'get',
                url: '<?php echo Yii::app()->createUrl('/knowledge/ajaxDel');?>',
                data: 'id=' + kc_id,
                success: function (json) {

                }
            });
        }
        $("#" + id).remove();

    }

    function is_show_faq(t){
        if(t.value == 5){
            $("#faq_category").show();
        }else{
            $("#faq_category").hide();
            $("#Knowledge_category_pid").val('');
            $("#Knowledge_category_cid").val('');
        }
    }

    function updateCategoryLevel2(){
        var level1 = $("#Knowledge_category_pid").val();

        if(level1 != ''){
            $.post('<?php echo Yii::app()->createUrl('/knowledge/childCategory');?>',{
                pid:level1
            },function (data){
                $("#Knowledge_category_cid").html('');
                for(var key in data){
                    options='<option value="'+key+'">'+data[key]+'</option>';
                    $("#Knowledge_category_cid").append(options);
                }
            },'json');
        }else{
            $("#Knowledge_category_cid").html('');
            options='<option value="">请选择</option>';
            $("#Knowledge_category_cid").append(options);
        }
    }
    $('#che_all').click(function(){
        if($(this).attr("checked")){
            $("input[name='city[]']").each(function(){
                $(this).attr("checked","true")
            });
        }else{
            $("input[name='city[]']").each(function(){
                $(this).removeAttr("checked");
            });
        }
    })
</script>