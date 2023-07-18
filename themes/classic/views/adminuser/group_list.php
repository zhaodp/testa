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

$selParentid = isset($selParentid) ? $selParentid :''; 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'group_user_add_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'添加/编辑用户组', 
		'autoOpen'=>false, 
		'width'=>'700', 
		'height'=>'550', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#group_user_add_dialog").dialog("close");}'))));
echo '<div id="group_user_add_dialog"></div>';
echo '<iframe id="guadd-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

Yii::app()->clientScript->registerScript('addGroup', "
$('#groupAddBtn').click(function(){
	$('#guadd-frame').attr('src',$(this).attr('href'));
	$('.ui-dialog-title').html('添加/编辑用户组');
	$('#group_user_add_dialog').dialog('open');return false;
});
");

$click_update = <<<EOD
function(){
	$("#guadd-frame").attr("src",$(this).attr("href"));
	$("#group_user_add_dialog").dialog("open");
	return false;
}
EOD;

?>

<h1>用户组（角色）管理</h1>
<div class="row">

<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

    <div class="row span3">
        <div class="row span3">
        <label for="AdminGroup_name">父类</label>
			<?php 
			
			echo CHtml::dropDownList('AdminGroup[parentid]',$selParentid,$parents); 
			?>
		</div>
    </div>
    <div class="row span3">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
    </div>

    <div class="row span2">
    	<label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn')); ?>
    </div>
    
    <div class="row span3">
    <label>&nbsp;</label>
    <a id="groupAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("adminuser/groupadd",array("id"=>0,"dialog"=>1,"grid_id"=>'admin-user-group-grid'));?>">添加用户组</a> 
    </div>

<?php $this->endWidget(); ?>


</div>
<hr class="divider" />
<?php
$this->widget('zii.widgets.grid.CGridView', 
	array (
		'id'=>'admin-user-group-grid', 
		'itemsCssClass'=>'table table-striped', 
		'dataProvider'=>$model->search(), 
		'columns'=>array (
			'id', 
			
			array(
				'name'=>'所属父类',
				'value'=>array($this,'getParentName')
			),
			'code', 
			'name', 
			'position', 
			'created',
				array(
						'name'=>'查看功能',
						'type'=>'raw',
						'value' => 'CHtml::link("查看功能", "javascript:void(0);", array (
						"onclick"=>"{showRoles($data->id);}"));'
				),
			array (
				'class'=>'CButtonColumn', 
				'template'=>'{update}', 
				'buttons'=>array (
					'update'=>array (
						'label'=>'修改', 
						'url'=>'$this->grid->controller->createUrl("groupedit",array("id"=>$data->id,"dialog"=>1,"grid_id"=>$this->grid->id));', 
						'click'=>$click_update
					),
				),
			),
		),
	)
);

?>

<script>
function showRoles(id){
	$(".ui-dialog-title").html("查看功能");
	url = '<?php echo Yii::app()->createUrl('/adminuser/groupmods');?>&gid='+id;
	$("#guadd-frame").attr("src",url);
	$("#group_user_add_dialog").dialog("open");
}
</script>

