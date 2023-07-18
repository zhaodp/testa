<?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'complain-list-search',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>


<div class="row-fluid">
<?php echo CHtml::hiddenField('type',$type,array());?>
    <div class="span3">
<?php echo CHtml::label('投诉时间','create_time');?>
<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
    'name'=>'start_time',
    'value'=>$s_time,
    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
    'options'=>array (
        'dateFormat'=>'yy-mm-dd'
    ),
    'language'=>'zh',
    'htmlOptions'=>array(
        'placeholder'=>"开始",
    ),


));?>
<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
    'name'=>'end_time',
    'value'=>$e_time,
    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
    'options'=>array (
        'dateFormat'=>'yy-mm-dd'
    ),  // jquery plugin options
    'language'=>'zh',
    'htmlOptions'=>array(
        'placeholder'=>"结束",
    ),
));
?>
    </div>
    <div class="span3">
        <?php echo CHtml::label('投诉大类','reason');?>
        <?php  echo CHtml::dropDownList('category',
            $category,
            $cateList

        );?>
    </div>
    <div class="span3">
        <?php echo CHtml::label('投诉类型','reason');?>
        <?php  echo CHtml::dropDownList('complain_maintype',
            $top_type,
            $typeList,
            array(
                'ajax' => array(
                    'type'=>'POST', //request type
                    'url'=>Yii::app()->createUrl('complain/getsubtypeall'),
                    'update'=>'#sub_type', //selector to update
                    'data'=>array('complain_maintype'=>'js:$("#complain_maintype").val()')
                ))
        );?>
        <?php echo CHtml::dropDownList('sub_type',$second_type,$secondTypeList); ?>

    </div>
    <div class="span3">
        <?php echo CHtml::label('投诉任务组','reason');?>
        <?php echo CHtml::dropDownList('group_id',
                        $task_group,
                        $groupList,
                        array(
                            'ajax' => array(
                                'type'=>'POST', //request type
                                'url'=>Yii::app()->createUrl('complain/getgroupuser'),
                                'update'=>'#task_operator', //selector to update
                                'data'=>array('group_id'=>'js:$("#group_id").val()')
                            ))
                    );
        ?>
        <?php echo CHtml::dropDownList('task_operator',$task_operator,$operatorList); ?>
    </div>
</div>

    <div class="row-fluid">
        <div class="span10">
            <button class="btn btn-primary span2" type="submit" name="search">查询</button>&nbsp;&nbsp;

            <!-- Button to trigger modal -->
            <?php echo CHtml::Button('导出excel',array('class'=>'btn btn-success','id'=>'down_excel_btn')); ?>
        </div>

    </div>
<?php $this->endWidget(); ?>
<script>
    var type = '<?php echo $type; ?>';
    $(function(){
        $("#down_excel_btn").click(function(){
            //新页面打开开始下载
            url = '<?php echo Yii::app()->createUrl('/complain/monitoroperator')?>&type=download_'+type;
            // alert(url);
            window.open(url);
        });
    });
</script>