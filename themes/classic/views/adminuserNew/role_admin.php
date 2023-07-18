<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('admin-usernew-role-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'role_add_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'添加/编辑用户组',
        'autoOpen'=>false,
        'width'=>'700',
        'height'=>'550',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#role_add_dialog").dialog("close");}'))));
echo '<div id="role_add_dialog"></div>';
echo '<iframe id="roleadd_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

Yii::app()->clientScript->registerScript('addRole', "
$('#roleAddBtn').click(function(){
	$('#roleadd_frame').attr('src',$(this).attr('href'));
	$('.ui-dialog-title').html('添加/编辑用户组');
	$('#role_add_dialog').dialog('open');return false;
});
");

$click_update = <<<EOD
function(){
	$("#roleadd_frame").attr("src",$(this).attr("href"));
	$("#role_add_dialog").dialog("open");
	return false;
}
EOD;

if($dep_name){
    ?>

    <ul class="breadcrumb">
        <li><a href="<?php if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_ADMIN) echo  Yii::app()->createUrl('adminuserNew/depadmin'); else echo '#'; ?>">部门管理</a> <span class="divider"> > </span></li>
        <?php
            if($parent_info){
                if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_GROUP_ADMIN){
                    echo '<li class="active">'.$parent_info['name'].'<span class="divider"> > </span></li>';
                }else{
                    echo '<li><a href="'.Yii::app()->createUrl('adminuserNew/roleadmin',array('dep_id'=>$parent_info['id'])).'">'.$parent_info['name'].'</a><span class="divider"> > </span></li>';
                }

            }
        ?>
        <li class="active"><?php echo $dep_name;?></li>
    </ul>
<?php } ?>

<ul class="nav nav-tabs" id="myTab">
    <li class=""><a  href="<?php if($parent_info){ echo Yii::app()->createUrl('adminuserNew/admin',array("parent_id"=>$parent_info['id'],'dep_id'=>$dep_id));}
        else {echo Yii::app()->createUrl('adminuserNew/admin',array("dep_id"=>$dep_id));} ?>">用户管理</a></li>
    <li class="active"><a  data-toggle="tab" href="<?php echo Yii::app()->createUrl('adminuserNew/roleadmin',array("dep_id"=>$dep_id)); ?>">角色组管理</a></li>
    <?php if(in_array(Yii::app()->user->admin_level,array(AdminUserNew::LEVEL_DEPARTMENT_ADMIN,AdminUserNew::LEVEL_ADMIN)) && !$parent_info){ ?>
        <li class=""><a href="<?php echo Yii::app()->createUrl('adminuserNew/groupadmin',array("dep_id"=>$dep_id)); ?>">小组管理</a></li>
    <?php }?>
</ul>
<div class="row">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>
    <div class="row span3">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20));
        echo (isset($_GET['parent_id']) && !empty($_GET['parent_id'])) ? CHtml::hiddenField('parent_id',$_GET['parent_id']) : '';
        ?>
        <input type="hidden" name="dep_id" value="<?php echo $dep_id;?>">
    </div>

    <div class="row span2">
        <label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'search-form form')); ?>
    </div>

    <div class="row span3">
        <label>&nbsp;</label>
        <?php if($show_create_button){?>
            <a id="roleAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("adminuserNew/rolecreate",array("dep_id"=>$dep_id,"dialog"=>1,"grid_id"=>'admin-usernew-role-grid'));?>">添加角色</a>
        <?php }?>
    </div>

    <?php $this->endWidget(); ?>


</div>
<hr class="divider" />

<?php
$this->widget('zii.widgets.grid.CGridView',
    array (
        'id'=>'admin-usernew-role-grid',
        'itemsCssClass'=>'table table-striped',
        'dataProvider'=>$model->search(),
        'columns'=>array (
            'id',
            'name',
            'desc',
            array(
                'name'=>'管理员默认角色',
                'type'=>'raw',
                'value'=>'AdminRole::getRoleTypeList($data->type)',
            ),
            array(
                'name'=>'状态',
                'type'=>'raw',
                'value'=>'AdminRole::getRoleStatusList($data->status)'
            ),
            array(
                'name'=>'人数',
                'type' => 'raw',
                'value'=>array($this,'getRoleUserNum')
            ),
            'create_time',
            array(
                'name'=>'查看功能',
                'type'=>'raw',
                'value' => 'CHtml::link("查看功能", "javascript:void(0);", array (
						"onclick"=>"{showRoles($data->id);}"));'
            ),

            array(
                'name'=>'复制角色',
                'type'=>'raw',
                'value' => 'CHtml::link("复制角色", "javascript:void(0);", array (
						"onclick"=>"{copyRole($data->id,$data->department_id);}"));'
            ),
            array (

                'class'=>'CButtonColumn',
                'template'=>'{update}',
                'buttons'=>array (
                    'update'=>array (
                        'label'=>'修改',
                        'url'=>'$this->grid->controller->createUrl("roleupdate",array("id"=>$data->id,"dep_id"=>$_GET["dep_id"],"dialog"=>1,"grid_id"=>$this->grid->id));',
                        'click'=>$click_update,
                        'visible'=>'AdminRole::model()->haveEditPermission($data->type)',

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
        url = '<?php echo Yii::app()->createUrl('/adminuserNew/getActionByRoleid');?>&id='+id;
        $("#roleadd_frame").attr("src",url);
        $("#role_add_dialog").dialog("open");
    }

    function copyRole(id,dep_id){
        $(".ui-dialog-title").html("复制角色");
        url = '<?php echo Yii::app()->createUrl('/adminuserNew/rolecopy');?>&dialog=1&grid_id=admin-usernew-role-grid&id='+id+'&dep_id='+dep_id;
        $("#roleadd_frame").attr("src",url);
        $("#role_add_dialog").dialog("open");
    }
</script>

