<?php
/* @var $this QuestionController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    'Questions',
);

//$question_type = isset($_REQUEST['question_type']) ? $_REQUEST['question_type'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$track = isset($_REQUEST['track']) ? $_REQUEST['track'] : '';
$city_id = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : '';
$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
?>

<h1>题库管理</h1>
<!--<div class="btn-group">-->
<!--    --><?php //echo CHtml::link('答卷管理', array('customerExam/index'),array('class'=>'btn'));?>
<!--    --><?php //echo CHtml::link('题库管理', '#',array('class'=>"search-button btn-primary btn"));?>
<!--</div>-->


<div class="search-form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">


        <div class="span3">
            <?php echo CHtml::label('题目状态','status');  ?>
            <?php echo CHtml::dropDownList('status', $status, QuestionNew::getStatus(),array('empty'=>'全部'));?>
        </div>

        <div class="span3">
            <?php echo CHtml::label('题目类型','category');  ?>
            <?php echo CHtml::dropDownList('category', $track, QuestionNew::getCategory(),array('empty'=>'全部'));?>
        </div>

        <div class="span3">
            <?php echo CHtml::label('适用城市','city_id');  ?>
            <?php
            $city_list=Dict::items('city');
            $city_list[]='通用题';
            ?>
            <?php echo CHtml::dropDownList('city_id', $city_id , $city_list)?>
        </div>

    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('标题','title');  ?>
            <?php echo CHtml::textField('title', $title);?>
        </div>
    </div>
    <div class="row-fluid">

        <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success','style'=>'margin-right:60px')); ?>
        <?php echo CHtml::link('添加新的答题', array('questionNew/create'),array('class'=>'search-button btn-primary btn'));?>

    </div>
    <?php $this->endWidget(); ?>
</div>

</div><!-- search-form -->


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'customer-question-grid',
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        'id',
        'title',
        'call_times',
        'right_times',
        array(
            'name'=>'是否屏蔽',
            'type'=>'raw',
            'value' => '($data->status == 0) ? "正常" : CHtml::link("解除", "javascript:void(0);", array (
					"onclick"=>"{setcancel($data->id);}"));'
        ),


        array(
            'header'=>'操作',
            'class'=>'CButtonColumn',
            'template'=>'{update} {delete}',
            'buttons'=>array(
                'update'=>array(
                    'label'=>'更新',
                    'url'=>'Yii::app()->controller->createUrl("questionNew/update",array("id"=>$data->id))',
                ),
                'delete' => array(
                    'label'=> '删除',
                    'url'=>'Yii::app()->controller->createUrl("questionNew/delete",array("id"=>$data->id))',
                ),
            ),
        ),
    ),
)); ?>
<div class='btn-group'>
    <?php //echo CHtml::link('添加新的答题', array('question/create'),array('class'=>'search-button btn-primary btn'));?>
</div>
<script>
    function setcancel(id){
        $.ajax({
            type: "GET",
            url: "<?php echo Yii::app()->createUrl('/questionNew/cancel');?>",
            data: "id="+id,
            success: function(data){
                if(data==1){
                    alert("操作成功");
                    $.fn.yiiGridView.update('customer-question-grid');
                }else{
                    alert("操作失败，请刷新后重试");
                }
            }
        });
    }
</script>