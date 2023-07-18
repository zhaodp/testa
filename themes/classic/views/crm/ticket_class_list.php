<?php
/* @var $this CrmController */
/* @var $model SupportTicket */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('#search-form').submit(function(){
	$('#support-ticket-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'tcdialog',
    'options' => array(
        'title' => '工单分类信息',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#tcdialog").dialog("close");} '
        ),
    ),
));
echo '<div id="tcdialogdiv"></div>';
echo '<iframe id="cru-frame-tc" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>


<h3>工单分类设置</h3>

<div class="search-form" style="display:block">
	<?php $this->renderPartial('_add',array('model'=>$model,)); ?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'tc-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '工单类别',
                'value' => 'Dict::item("ticket_category",$data->type_id)'
            ),
            array(
                'name' => '工单分类',
                'value' =>'$data->name'
            ),
	    array(
                'name' => '状态',
                'value' =>'$data->status==0?"启用":"屏蔽"'
            ),
		
	    array(
            	'name' => '操作',
            	'value' => array($this, 'showButton')
            ),
        ),
    )); ?>

<script type="text/javascript">
    function tcDialogdivInit(href) {
        $("#cru-frame-tc").attr("src", href);
        $("#tcdialog").dialog("open");
        return false;
    }

   function checkClass(){
	var tclass=$("#class").val();
	if(tclass==''){
		alert('工单分类不能为空');
		return;
	}
	$('#yw0').submit();
   } 

</script>
