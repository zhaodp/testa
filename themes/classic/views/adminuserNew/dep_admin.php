<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('admin-dep-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'dep_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'部门管理',
        'autoOpen'=>false,
        'width'=>'600',
        'height'=>'580',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#dep_dialog").dialog("close");}'))));
echo '<div id="dep_dialog"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

Yii::app()->clientScript->registerScript('addRolesMod', "
$('#depModAddBtn').click(function(){
	$('#cru-frame').attr('src',$(this).attr('href'));
	$('#dep_dialog').dialog('open');return false;
});
");

$click_update = <<<EOD
function(){
	$("#cru-frame").attr("src",$(this).attr("href"));
	$("#dep_dialog").dialog("open");
	return false;
}
EOD;
?>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a  data-toggle="tab" href="<?php echo Yii::app()->createUrl('adminuserNew/depadmin'); ?>">部门管理</a></li>
    <li class=""><a  href="<?php echo Yii::app()->createUrl('adminuserNew/admin'); ?>">用户管理</a></li>
</ul>
<div class="row">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>


    <div class="row span3">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
    </div>

    <div class="row span2">
        <label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>
    <a id="depModAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("adminuserNew/depcreate",array("id"=>0,"dialog"=>1,"grid_id"=>'admin-dep-grid'));?>">创建部门</a>

</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'admin-dep-grid',
    'itemsCssClass'=>'table table-striped',
    'dataProvider'=>$model->search(),
    'columns'=>array (
        'id',

        array(
            'name'=>'name',
            'type'=>'raw',
            'value' => array($this,'getRoleAdminUrl'),
        ),
        'desc',
        array(
            'name'=>'人数',
            'value'=>array($this,'getDepUserNum'),
        ),

        array(
            'name'=>'状态',
            'value'=>'AdminDepartment::getStatus($data->status)',
        ),
        'update_time',
        'create_time',

        array (
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array (
                'update'=>array (
                    'label'=>'修改',
                    'url'=>'$this->grid->controller->createUrl("depupdate",array("id"=>$data->id,"dialog"=>1,"grid_id"=>$this->grid->id));',
                    'click'=>$click_update)
            )
        )
    )
));

?>

