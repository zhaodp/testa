<?php
/* @var $this KnowledgeController */
/* @var $model Knowledge */

$this->breadcrumbs=array(
	'Knowledges'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#knowledge-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1>知识库管理</h1>

<div class="btn-group">
    <?php echo CHtml::link("待审核内容 ( $not_audit_num ) ", array("knowledge/admin", "Knowledge" => array("status" => "1")), array('class' => "btn")); ?>
    <?php echo CHtml::link('新建知识', array("knowledge/create"), array('class' => "btn", 'id' => 'created', 'target'=>'_blank')); ?>
</div>


<div class="search-form" style="display:block">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'knowledge-grid',
	'dataProvider'=>$model->search(),
    'itemsCssClass' => 'table table-striped',
//	'filter'=>$model,
	'columns'=>array(
//		'title',
        array(
            'name' => 'title',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this,'processTitle')
        ),
        array(
            'name' => 'typeid',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->typeid == 0 ? "全部" : Dict::item("knowledge_type", $data->typeid)'
        ),
		'keywords',
        array(
            'name' => 'status',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->status == 1 ? "未审核" : ($data->status == 2 ? "已审核" : "已过期")'
        ),
        'operator',
		/*
		'is_case',
		'status',
		'praise_num',
		'listorder',
		'updated',
		'created',
		*/

        array(
            'class' => 'CButtonColumn',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'width' => '120px'
            ),
            'header' => '操作',
            'template' => '{update} {update_status} {delete}',
            'buttons' => array(

                'update' => array(
                    'label' => '修改',
                    'visible' => 'AdminActions::model()->havepermission("knowledge", "update")',
                    'imageUrl' => '',
                ),

                'delete' => array(
                    'label' => '删除',
                    'imageUrl' => '',
                    'visible' => '$data->status != "3" && AdminActions::model()->havepermission("knowledge", "updateStatus")',
                    'url' => 'Yii::app()->createUrl("knowledge/updateStatus", array("id"=>$data->id,"status" => "3"))',
                ),

                'update_status' => array(
                    'label' => '审核',
                    'visible'=> '$data->status != "2" && AdminActions::model()->havepermission("knowledge", "updateStatus")',
                    'options' => array('class' => 'update_status'),
                    'url' => 'Yii::app()->createUrl("knowledge/updateStatus", array("id"=>$data->id,"status" => "2"))',
                ),
            ),
        ),
	),
)); ?>


<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">知识条目详情</h3>
    </div>

    <div class="modal-body" id="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->


<script type="text/javascript">
    $(function(){
        $("a[data-toggle=modal]").live('click',function(){
            var target = $(this).attr('data-target');
            var url = $(this).attr('url');
            var mewidth = $(this).attr('mewidth');
            if(mewidth==null) mewidth='850px';
            if(url!=null){
                $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
                $('#myModal').modal('show');
                $('#modal-body').load(url);
            }
            return true;
        });
    });


    jQuery(document).on('click', '#knowledge-grid a.update_status', function () {
        if (!confirm('确定要审核这条数据吗?')) return false;
        var th = this,
            afterDelete = function () {
            };
        jQuery('#knowledge-grid').yiiGridView('update', {
            type: 'POST',
            url: jQuery(this).attr('href'),
            success: function (data) {
                jQuery('#knowledge-grid').yiiGridView('update');
                afterDelete(th, true, data);
            },
            error: function (XHR) {
                return afterDelete(th, false, XHR);
            }
        });
        return false;
    });

</script>
