<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});

$('#admin-user-new-grid a.resetpwd').click(function(){
	var ajax_url = jQuery(this).attr('href');
	var btn = $(this);
    btn.button('loading');
    jQuery.get(
		ajax_url,
		function(d) {
		    alert(d.msg);
		    if(d.succ=='1'){
		        jQuery(this).attr('data-complete-text','操作成功');
            }
		    if(d.succ=='0'){
		       jQuery(this).attr('data-complete-text','操作失败');
		    }
            btn.button('reset');
		},
		'json'
	);

	return false;

});
");
?>
<?php

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '权限信息',
        'autoOpen' => false,
        'width' => '800',
        'height' => '600',
        'modal' => true,
        'buttons' => array(
            'Close' => 'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_info_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

if($dep_info){
?>

<ul class="breadcrumb">
    <li><a href="<?php if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_ADMIN) echo  Yii::app()->createUrl('adminuserNew/depadmin'); else echo '#'; ?>">部门管理</a> <span class="divider"> > </span></li>
    <?php if($group_info){
        if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_GROUP_ADMIN){
            echo '<li class="active">'.$dep_info->name.'<span class="divider"> > </span></li>';
        }else{
            echo '<li><a href="'.Yii::app()->createUrl('adminuserNew/admin',array('dep_id'=>$dep_info->id)).'">'.$dep_info->name.'</a> <span class="divider"> > </span></li>';
        }
        echo '<li class="active">'.$group_info->name.'</li>';
    } else {
        echo '<li class="active">'.$dep_info->name.'</li>';
    }?>

</ul>
<?php } ?>
<ul class="nav nav-tabs" id="myTab">
    <?php if(!isset($_GET['dep_id'])){?>
        <li class=""><a href="<?php echo Yii::app()->createUrl('adminuserNew/depadmin'); ?>">部门管理</a></li>
    <?php } ?>
    <li class="active"><a data-toggle="tab" href="<?php  echo Yii::app()->createUrl('adminuserNew/admin',array("dep_id"=>$dep_id)); ?>">用户管理</a></li>
    <?php if(isset($_GET['dep_id']) && $_GET['dep_id']){?>
    <li class=""><a href="<?php if($group_info) { echo Yii::app()->createUrl('adminuserNew/roleadmin',array("dep_id"=>$group_info->id,'parent_id'=>$dep_info->id));}
        else {echo Yii::app()->createUrl('adminuserNew/roleadmin',array("dep_id"=>$dep_id));} ?>">角色组管理</a></li>
    <?php if(in_array(Yii::app()->user->admin_level,array(AdminUserNew::LEVEL_DEPARTMENT_ADMIN,AdminUserNew::LEVEL_ADMIN)) && !$group_info){ ?>
        <li class=""><a href="<?php echo Yii::app()->createUrl('adminuserNew/groupadmin',array("dep_id"=>$dep_id)); ?>">小组管理</a></li>
    <?php }
    }?>
</ul>
<div class="well search-form">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
        'dep_id'=>$dep_id,
        'dep_info'=>$dep_info,
        'group_info'=>$group_info,
    )); ?>
</div><!-- search-form -->



<?php if(isset($_GET['dep_id']) && $_GET['dep_id']) $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'admin-user-new-grid',
    'ajaxUpdate' => false,
    'cssFile'=>SP_URL_CSS.'table.css',
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'dataProvider' => $model->search(),
    'columns' => array(
        'id',
        'name',
        'phone',
        'email',
        array(
            'name' => '查看权限',
            'type' => 'raw',
            'value' => array($this, 'getUserRolesToStr')
        ),
        array(
            'name'=>'电话权限',
            'type' => 'raw',
            'value'=>array($this,'getSpecialBtnStr')
        ),
        array(
            'name' => 'city_id',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("city",$data->city_id)'
        ),
        array(
            'name' => 'department_id',
            'type' => 'raw',
            'value' => 'AdminDepartment::model()->getDepName($data->department_id)'
        ),
        array(
            'name' => 'group_id',
            'type' => 'raw',
            'value' => '$data->group_id ? AdminDepartment::model()->getDepName($data->group_id) : "无"'
        ),
        array(
            'name' => '角色组',
            'type' => 'raw',
            'value' => array($this,'showUserRole')
        ),
        array(
            'name'=>'type',
            'type' => 'raw',
            'value' =>'AdminUserNew::getUserType($data->type)'
        ),
        array(
            'name' => 'level',
            'type' => 'raw',
            'value' => 'AdminUserNew::getUserLevel($data->level)',//'($data->level==AdminUserNew::LEVEL_DEPARTMENT_ADMIN) ? "组管理员":( ($data->level==AdminUserNew::LEVEL_ADMIN) ? "超级管理员" : "普通用户" )'
        ),

        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => 'AdminUserNew::getUserStatus($data->status)', //array($this,'getAdminStatus') //'($data->status==AdminUserNew::STATUS_NORMAL)?"正常":"禁用"'
        ),
        array(
            'header'=>'重置密码',
            'class' => 'CButtonColumn',
            'template' => '{reset_init} | {reset} | {reset_e}',
            'buttons'=>array(
                'reset_init' => array(
                    'label'=>'默认密码',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuserNew/resetpwd",array("id"=>$data->id,"method"=>"init"))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$data->id)'),
                'reset' => array(
                    'label'=>'短信',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuserNew/resetpwd",array("id"=>$data->id,"method"=>1))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$data->id)'),
                'reset_e' => array(
                    'label'=>'邮箱',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuserNew/resetpwd",array("id"=>$data->id,"method"=>2))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$data->id) && $data->email '),
            ),
            //'visible'=>AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$model->id),
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{modify}',
            'buttons'=>array(
                'modify'=>array(
                    'label'=>'编辑',
                    'url'=>'$this->grid->controller->createUrl("adminuserNew/update",array("id"=>$data->id,"back_url"=>Yii::app()->request->getUrl()));',
                    'options' => array('class'=>'btn', 'target'=>'_blank'),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "update",$data->id)'
                ),
            ),
        ),
    ),
));
else $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'admin-user-new-grid',
    'ajaxUpdate' => false,
    'cssFile'=>SP_URL_CSS.'table.css',
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'dataProvider' => $model->search(),
    'columns' => array(
        'id',
        'name',
        'phone',
        'email',
        array(
            'name' => '查看权限',
            'type' => 'raw',
            'value' => array($this, 'getUserRolesToStr')
        ),
        array(
            'name'=>'电话权限',
            'type' => 'raw',
            'value'=>array($this,'getSpecialBtnStr')
        ),
        array(
            'name' => 'city_id',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("city",$data->city_id)'
        ),
        array(
            'name' => 'department_id',
            'type' => 'raw',
            'value' => 'AdminDepartment::model()->getDepName($data->department_id)'
        ),
        array(
            'name' => 'group_id',
            'type' => 'raw',
            'value' => '$data->group_id ? AdminDepartment::model()->getDepName($data->group_id) : "无"'
        ),
        array(
            'name'=>'type',
            'type' => 'raw',
            'value' =>'AdminUserNew::getUserType($data->type)'
        ),
        array(
            'name' => 'level',
            'type' => 'raw',
            'value' => 'AdminUserNew::getUserLevel($data->level)',//'($data->level==AdminUserNew::LEVEL_DEPARTMENT_ADMIN) ? "组管理员":( ($data->level==AdminUserNew::LEVEL_ADMIN) ? "超级管理员" : "普通用户" )'
        ),

        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => 'AdminUserNew::getUserStatus($data->status)', //array($this,'getAdminStatus') //'($data->status==AdminUserNew::STATUS_NORMAL)?"正常":"禁用"'
        ),
        array(
            'header'=>'重置密码',
            'class' => 'CButtonColumn',
            'template' => '{reset_init} | {reset} | {reset_e}',
            'buttons'=>array(
                'reset_init' => array(
                    'label'=>'默认密码',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuserNew/resetpwd",array("id"=>$data->id,"method"=>"init"))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$data->id)'),
                'reset' => array(
                    'label'=>'短信',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuserNew/resetpwd",array("id"=>$data->id,"method"=>1))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$data->id)'),
                'reset_e' => array(
                    'label'=>'邮箱',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuserNew/resetpwd",array("id"=>$data->id,"method"=>2))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$data->id) && $data->email '),
            ),
            //'visible'=>AdminUserNew::model()->haveViewPermission("adminuserNew", "resetpwd",$model->id),
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{modify}',
            'buttons'=>array(
                'modify'=>array(
                    'label'=>'编辑',
                    'url'=>'$this->grid->controller->createUrl("adminuserNew/update",array("id"=>$data->id,"back_url"=>Yii::app()->request->getUrl()));',
                    'options' => array('class'=>'btn', 'target'=>'_blank'),
                    'visible'=>'AdminUserNew::model()->haveViewPermission("adminuserNew", "update",$data->id)'
                ),
            ),
        ),
    ),
)); ?>

<script>
    function showRoles(id) {
        url = '<?php echo Yii::app()->createUrl('/adminuserNew/getActionByUserid');?>&user_id=' + id;
        $("#view_info_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }

    function showSpecial(user_id) {
        url = '<?php echo Yii::app()->createUrl('/adminuserNew/specialAuth');?>&dialog=1&user_id=' + user_id;
        $("#view_info_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }
    function changeGroup(){}
    $(document).ready(function(){
        $('#AdminUserNew_department_id').change(function(){
            //alert($('#AdminUserNew_department_id').val());
            var dep_id = $('#AdminUserNew_department_id').val();
            //alert(dep_id == '');
            if(dep_id != '' ){
                <?php if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_ADMIN){ ?>
                //var group_id = $('#AdminUserNew_group_id').val();
                //alert(dep_id);
                $('#group_box').css('display','block');
                $.get('<?php echo Yii::app()->createUrl('adminuserNew/GetGroupHtml');?>&dep_id='+dep_id,function(result){
                    //alert(result);
                    $('#group_pool').html(result);
                    $('#AdminUserNew_group_id').css('width','100px');
                });
                <?php }?>
            }else{
                $('#group_pool>select option').each(function(){
                    $(this).remove();
                });
                $("<option value=''>小组</option>").appendTo($("#group_pool>select"));
            }
        });
    });


</script>