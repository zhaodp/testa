<h1>商家绑定优惠劵列表</h1>
<h1><?PHP echo $name ?></h1>
<?php echo CHtml::link('新增优惠劵', 'javaScript:void(0);', array('class' => 'btn btn-primary','onClick' => 'addDialogdivInit(\'' . Yii::app()->createUrl("bonusMerchants/addRelation", array("id" => $_GET['id'])) . '\')')); ?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'create_dialog',
    'options' => array(
        'title' => '新增优惠劵',
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
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bm-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'name' => 'name',
            'htmlOptions' => array(
                'style' => 'width:15%;'
            ),
	    'value' => '$data->name',
        ),
        array(
                'header' => '金额',
                'value' => '$data->money'
        ),
	array(
                'header' => '关联时间',
		'type' => 'raw',
                'value' => array($this, 'getRelatedTime')
	),
       array(
                'header' => '已绑定',
                'type' => 'raw',
                'value' => array($this,'getBindButton')
         ),
	array(
                'header' => '已消费',
                'type' => 'raw',
                'value' => array($this,'getUsedButton')
         ),
	array(
                'header' => '消费金额',
                'type' => 'raw',
                'value' => 'CustomerBonus::model()->getUsedMoneyByID($data)."元"'
         ),
        array(
            'header' => '操作',
            'htmlOptions' => array(
                'style' => 'width:85px;'
            ),
	    'value' => array($this,'getOperate')
        ),
    ),
));
?>
<script type="text/javascript">
function addDialogdivInit(href) {
        $("#cru-frame-create").attr("src", href);
        $("#create_dialog").dialog("open");
        return false;
}

jQuery(document).on('click','#bm-grid a.delete',function() {
	if(!confirm('确定要解除关联吗?')) return false;
	var th = this,
		afterDelete = function(){};
	jQuery('#bm-grid').yiiGridView('update', {
		type: 'POST',
		url: jQuery(this).attr('href'),
		success: function(data) {
			jQuery('#bm-grid').yiiGridView('update');
			afterDelete(th, true, data);
		},
		error: function(XHR) {
			return afterDelete(th, false, XHR);
		}
	});
	return false;
});
</script>     
