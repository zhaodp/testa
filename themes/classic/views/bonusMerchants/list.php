<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('#search-form').submit(function(){
	$('#bonus-merchants-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'create_bonus_dialog',
    'options' => array(
        'title' => '新增优惠劵',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#create_bonus_dialog").dialog("close");} '
        ),
    ),
));
echo '<div id="create_bonus_dialog_div"></div>';
echo '<iframe id="cru-frame-create_bonus" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'create_dialog',
    'options' => array(
        'title' => '新增优惠劵商家',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#create_dialog").dialog("close");} '
        ),
    ),
));
echo '<div id="create_dialog_div"></div>';
echo '<iframe id="cru-frame-create" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'edit_dialog',
    'options' => array(
        'title' => '修改商家',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#edit_dialog").dialog("close");} '
        ),
    ),
));
echo '<div id="edit_dialog_div"></div>';
echo '<iframe id="cru-frame-edit" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h3>优惠劵商家管理</h3>
<div class="search-form" style="display:block">
    <?php $this->renderPartial('_search', array('name' => $name,'model'=>$model)); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bm-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name' => '商家名称',
            'type' => 'raw',
            'value' => 'CHtml::link("$data->name",
                Yii::app()->createUrl("bonusMerchants/edit",array("id"=>$data->primaryKey,)))',
        ),
        array(
            'name' => '商家类型',
            'value' => 'Dict::item(\'bonus_shop_type\',$data->shop_type)'
        ),
        array(
            'name' => '开通时间',
            'value' => '$data->create_time'
        ),
        array(
            'name' => '已关联优惠劵',
            'value' => array($this, 'getBonusNum')
        ),
        array(
            'name' => '总消费金额',
            'value' => array($this, 'getTotalAmount')
        ),
        array(
            'name' => '余额',
            'value' => array($this,'getBalance')
        ),
        array(
            'name' => '操作',
            'value' => array($this, 'showButton')
        ),
    ),
)); ?>

<script type="text/javascript">
    function createDialogdivInit(href) {
        $("#cru-frame-create").attr("src", href);
        $("#create_dialog").dialog("open");
        return false;
    }
    function editDialogdivInit(href) {
        $("#cru-frame-edit").attr("src", href);
        $("#edit_dialog").dialog("open");
        return false;
    }
    function addDialogdivInit(href) {
        $("#cru-frame-create_bonus").attr("src", href);
        $("#create_bonus_dialog").dialog("open");
        return false;
    }
</script>
