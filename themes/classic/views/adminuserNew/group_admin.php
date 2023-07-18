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

    if($dep_name){
    ?>

    <ul class="breadcrumb">
        <li><a href="<?php if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_ADMIN) echo  Yii::app()->createUrl('adminuserNew/depadmin'); else echo '#'; ?>">部门管理</a> <span class="divider"> > </span></li>
        <li class="active"><?php echo $dep_name;?></li>
    </ul>
<?php } ?>

<ul class="nav nav-tabs" id="myTab">
    <li class=""><a  href="<?php  echo Yii::app()->createUrl('adminuserNew/admin',array("dep_id"=>$dep_id)); ?>">用户管理</a></li>
    <li class=""><a href="<?php echo Yii::app()->createUrl('adminuserNew/roleadmin',array("dep_id"=>$dep_id)); ?>">角色组管理</a></li>
    <?php if(in_array(Yii::app()->user->admin_level,array(AdminUserNew::LEVEL_DEPARTMENT_ADMIN,AdminUserNew::LEVEL_ADMIN)) ){ ?>
        <li class="active"><a data-toggle="tab" href="<?php echo Yii::app()->createUrl('adminuserNew/groupadmin',array("dep_id"=>$dep_id)); ?>">小组管理</a></li>
    <?php }?>
</ul>
<div class="row">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>


    <div class="row span3">
        <label for="AdminDepartment_name">小组名称</label>
        <?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20));
        echo CHtml::hiddenField('dep_id',isset($_GET['dep_id']) ? $_GET['dep_id']: '')?>
    </div>

    <div class="row span2">
        <label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>
    <a id="depModAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("adminuserNew/groupcreate",array("dep_id"=>$dep_id,"dialog"=>1,"grid_id"=>'admin-dep-grid'));?>">创建小组</a>

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
                    'url'=>'$this->grid->controller->createUrl("groupupdate",array("id"=>$data->id,"dialog"=>1,"grid_id"=>$this->grid->id));',
                    'click'=>$click_update)
            )
        )
    )
));

?>

