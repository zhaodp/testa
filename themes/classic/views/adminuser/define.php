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

<h1>权限配置</h1>
<div class="row">

<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

    <div class="row span3">
        <?php echo $form->label($model,'controller'); ?>
        <?php echo $form->textField($model,'controller',array('size'=>20,'maxlength'=>20)); ?>
    </div>

    <div class="row span3">
        <?php echo $form->label($model,'action'); ?>
        <?php echo $form->textField($model,'action',array('size'=>20,'maxlength'=>20)); ?>
    </div>

    <div class="row span3">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
    </div>

    <div class="row span2">
    	<label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn')); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
<hr class="divider" />

    <div class="row span2">
    <a id="rolesModAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("adminuser/rolesmodadd",array("id"=>0,"dialog"=>1,"grid_id"=>'admin-user-grid'));?>">添加功能项</a> 
    </div>
<?php
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'admin-user-grid', 
	'itemsCssClass'=>'table table-striped', 
	'dataProvider'=>$model->search(), 
	'columns'=>array (
		'controller', 
		'action', 
		'name', 
		array ( 
			'name'=>'roles', 
			'headerHtmlOptions'=>array (
				'width'=>'100px'), 
			'type'=>'raw', 
			'value'=>array($this,'showGroupName')), 
		array (
			'class'=>'CButtonColumn', 
			'template'=>'{update}', 
			'buttons'=>array (
				'update'=>array (
					'label'=>'修改', 
					'url'=>'$this->grid->controller->createUrl("defineupdate",array("id"=>$data->id,"dialog"=>1,"grid_id"=>$this->grid->id));', 
					'click'=>$click_update))))));

?>

