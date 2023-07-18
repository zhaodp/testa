<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
    var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
    $(this).text(title);
	return false;
});
$('.search-form form').submit(function(){
	$('#envelope-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php echo CHtml::link('展开搜索', '#', array('class' => 'btn search-button')); ?>
    &nbsp;
<?php echo CHtml::link('创建红包', Yii::app()->createUrl('envelope/create'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
<?php echo CHtml::link('进行中的红包', Yii::app()->createUrl('envelope/admin'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
<?php echo CHtml::link('红包发放列表', Yii::app()->createUrl('envelope/extend'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
<?php echo CHtml::link('红包发放统计', Yii::app()->createUrl('envelope/city'), array('class' => 'btn btn-success', 'target' => '_self')); ?>


    <h1>进行中的红包</h1>
    <div class="search-form" style="display:none">
        <?php
        $this->renderPartial('_form_admin', array(
            'model' => $model,
            'model_map' => $model_map
        ));
        ?>
    </div><!-- search-form -->


<?php
$gridId = 'envelope-grid';
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => $gridId,
    'dataProvider' => $data,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'name' => '发放日期',
            'type' => 'raw',
            'value' => '$data["time"]',
        ),
        array(
            'name' => '红包发放方式',
            'type' => 'raw',
            'value' => '$data["envelope_type"]',
        ),
        array(
            'name' => '发放城市',
            'type' => 'raw',
            'value' => '$data["city"]',
        ),

        array(
            'name' => '金额',
            'type' => 'raw',
            'value' => '$data["envelope_role"]',
        ),
        array(
            'header' => '操作',
            'htmlOptions' => array(
                'style' => 'width:85px;'
            ),
            'class' => 'CButtonColumn',
            'template' => '{update1}{delete1}',
            'buttons' => array(
                'update1' => array(
                    'label' => '禁用',
                    'visible' => '$data["status"] == 1',
                    'url' => 'Yii::app()->createUrl("envelope/audit",array("id"=>$data["id"],"status"=>$data["status"]))',
                    'click' => 'function(){
                        $(\'#auditIframe\').attr(\'src\',$(this).attr(\'href\'));
                        $(\'#auditIframe\').show();
                        $("#auditDialog").dialog("open");
                        return false;
                    }'
                ),
                'delete1' => array(
                    'label' => '启用',
                    'visible' => '$data["status"] == 2',
                    'url' => 'Yii::app()->createUrl("envelope/audit",array("id"=>$data["id"],"status"=>$data["status"]))',
                    'click' => 'function(){
                        $(\'#auditIframe\').attr(\'src\',$(this).attr(\'href\'));
                        $(\'#auditIframe\').show();
                        $("#auditDialog").dialog("open");
                        return false;
                    }'
                ),
            )
        ),
    ),
));

?>



<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'auditDialog',
    'options' => array(
        'title' => '操作',
        'autoOpen' => false,
        'width' => '600',
        'height' => '450',
        'buttons' => array(
            '关闭' => 'js:function(){$("#auditDialog").dialog("close");}',
        )
    ),
));
?>
<iframe id="auditIframe" src="" style="width:550px;height:330px;border:0px;margin:0px;display:none;"></iframe>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>


