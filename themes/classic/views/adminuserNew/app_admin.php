<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('admin-user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'define_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'权限管理',
        'autoOpen'=>false,
        'width'=>'600',
        'height'=>'580',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#define_dialog").dialog("close");}'))));
echo '<div id="define_dialog"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

Yii::app()->clientScript->registerScript('addRolesMod', "
$('#rolesModAddBtn').click(function(){
	$('#cru-frame').attr('src',$(this).attr('href'));
	$('#define_dialog').dialog('open');return false;
});
");

$click_update = <<<EOD
function(){
	$("#cru-frame").attr("src",$(this).attr("href"));
	$("#define_dialog").dialog("open");
	return false;
}
EOD;
?>

<ul class="nav nav-tabs" id="myTab">
    <li class=""><a href="<?php echo Yii::app()->createUrl('adminuserNew/actionadmin'); ?>">权限配置</a></li>
    <li class="active"><a data-toggle="tab" href="<?php echo Yii::app()->createUrl('adminuserNew/appadmin'); ?>">应用系统管理</a></li>
</ul>

<div class="row span2">
    <a id="rolesModAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("adminuserNew/appcreate",array("id"=>0,"dialog"=>1,"grid_id"=>'admin-app-grid'));?>">添加应用系统</a>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'admin-app-grid',
    'itemsCssClass'=>'table table-striped',
    'dataProvider'=>$model->search(),
    'columns'=>array (
        'id',
        'name',
        'desc',
        'url',
        'key',
        array(
            'name'=>'状态',
            'value'=>'AdminApp::getStatus($data->status)',
        ),
        array (
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array (
                'update'=>array (
                    'label'=>'修改',
                    'url'=>'$this->grid->controller->createUrl("appupdate",array("id"=>$data->id,"dialog"=>1,"grid_id"=>$this->grid->id));',
                    'click'=>$click_update))))));

?>

