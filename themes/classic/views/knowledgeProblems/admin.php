<?php
/* @var $this ProblemsCollectController */
/* @var $model ProblemsCollect */

$this->breadcrumbs=array(
	'Problems Collects'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#problems-collect-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>问题收集管理</h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button btn')); ?> &nbsp;&nbsp;
<?php echo CHtml::link('添加问题', array("knowledgeProblems/create"), array('class' => 'btn', 'id' => 'created')); ?>
<div class="search-form" style="display:block">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'problems-collect-grid',
	'dataProvider'=>$model->search(),
    'itemsCssClass' => 'table table-striped',
	'columns'=>array(
        array(
            'name' => 'created',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '100px'
            ),
            'type' => 'raw',
            'value' => 'date("m-d H:i", strtotime($data->created))'
        ),
        array(
            'name' => 'driver_id',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '100px'
            ),
            'type' => 'raw',
            'value' => '$data->driver_id'
        ),
        array(
            'name' => 'name',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '100px'
            ),
            'type' => 'raw',
            'value' => '$data->name'
        ),
        array(
            'name' => 'phone',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '100px'
            ),
            'type' => 'raw',
            'value' => '$data->phone'
        ),
        array(
            'name' => 'content',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this,'contentList')
        ),
        array(
            'name' => 'status',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '100px'
            ),
            'type' => 'raw',
            'value' => '$data->status == 0 ? "未解决" : "已解决"'
        ),
        array(
            'name' => 'operator',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '80px'
            ),
            'type' => 'raw',
            'value' => '$data->operator'
        ),
        array(
            'name' => 'solve',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '80px'
            ),
            'type' => 'raw',
            'value' => '$data->solve'
        ),
        /*
		'updated',
		'created',
		*/
        array(
            'class' => 'CButtonColumn',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '80px'
            ),
            'header' => '操作',
            'template' => '{update_status}',
            'buttons' => array(
                'update_status' => array(
                    'label' => '回拨',
                    'visible' => '$data->status != "1"',
                    'options' => array('class' => 'update_status'),
                    'url' => 'Yii::app()->createUrl("/client/service",array("phone" => $data->phone, "kp_id"=> $data->id))',
                ),
            ),
        ),
	),
)); ?>


<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">添加知识</h3>
    </div>

    <div class="modal-body" id="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" id="submit_btn" data-dismiss="modal" aria-hidden="true">确定</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->


<script type="text/javascript">
    $(function () {
        $("#created").click(function () {
            var url = $(this).attr('href');
            var mewidth = $(this).attr('mewidth');
            if (mewidth == null) mewidth = '600px';
            if (url != null) {
                $('#myModal').modal('toggle').css({'width': mewidth, 'margin-left': function () {
                    return -($(this).width() / 2);
                }});
                $('#myModal').modal('show');
                $('#modal-body').load(url);
            }
            return false;
        });

        $("#submit_btn").click(function(){
            $('#problems-collect-form').submit();
        });

    });



</script>

