<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});

$('#admin-user-grid a.resetpwd').click(function(){
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
?>
<h1>用户管理</h1>

<div class="well search-form">
    <?php $this->renderPartial('_search', array(
        'model' => $model,
    )); ?>
</div><!-- search-form -->


<hr class="divider"/>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'admin-user-grid',
    'ajaxUpdate' => false,
    'cssFile'=>SP_URL_CSS.'table.css',
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'dataProvider' => $model->search(),
    'columns' => array(
        array(
            'name' => '姓名',
            'type' => 'raw',
            'value' => '$data->name'
        ),
        array(
            'name' => '手机',
            'type' => 'raw',
            'value' => '$data->phone'
        ),
        array(
            'name' => '邮箱',
            'type' => 'raw',
            'value' => '$data->email'
        ),
        array(
            'name' => '查看权限',
            'type' => 'raw',
            'value' => array($this, 'getUserRolesToStr')
        ),
        array(
            'name' => '城市',
            'type' => 'raw',
            'value' => 'Dict::item("city",$data->city)'
        ),
        array(
            'name' => '部门',
            'type' => 'raw',
            'value' => '$data->department'
        ),
        array(
            'name' => '级别',
            'type' => 'raw',
            'value' => '($data->admin_level==1)?"组管理员":( ($data->admin_level==2) ? "超级管理员" : "普通用户" )'
        ),

        array(
            'name' => '状态',
            'type' => 'raw',
            'value' => '($data->status==1)?"正常":"禁用"'
        ),
        array(
            'header'=>'重置密码',
            'class' => 'CButtonColumn',
            'template' => '{reset_init} | {reset} | {reset_e}',
            'buttons'=>array(
                'reset_init' => array(
                    'label'=>'默认密码',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuser/resetpwd",array("id"=>$data->user_id,"method"=>"init"))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminRoles::model()->havingPermissions("adminuser", "resetpwd")'),
                'reset' => array(
                    'label'=>'短信',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuser/resetpwd",array("id"=>$data->user_id,"method"=>1))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'AdminRoles::model()->havingPermissions("adminuser", "resetpwd")'),
                'reset_e' => array(
                    'label'=>'邮箱',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("adminuser/resetpwd",array("id"=>$data->user_id,"method"=>2))',
                    'options' => array('class'=>'resetpwd','data-loading-text'=>'Loading...','data-complete-text'=>'操作完成' ),
                    'visible'=>'(AdminRoles::model()->havingPermissions("adminuser", "resetpwd") && $data->email)'),
            ),
            'visible'=>AdminRoles::model()->havingPermissions("adminuser", "resetpwd"),
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{modify}',
            'buttons'=>array(
                'modify'=>array(
                    'label'=>'编辑',
                    'url'=>'$this->grid->controller->createUrl("adminuser/update",array("id"=>$data->user_id));',
                    'options' => array('class'=>'btn', 'target'=>'_blank'),
                    'visible'=>'AdminRoles::model()->havingPermissions("adminuser", "update")'
                ),
            ),
        ),
    ),
)); ?>
<script>
    function showRoles(id) {
        url = '<?php echo Yii::app()->createUrl('/adminuser/groupmods');?>&gid=' + id;
        $("#view_info_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }
</script>