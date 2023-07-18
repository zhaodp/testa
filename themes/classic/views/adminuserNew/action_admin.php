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
    <li class="active"><a  data-toggle="tab" href="<?php echo Yii::app()->createUrl('adminuserNew/actionadmin'); ?>">权限配置</a></li>
    <li class=""><a  href="<?php echo Yii::app()->createUrl('adminuserNew/appadmin'); ?>">应用系统管理</a></li>
</ul>
<div class="row">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>

    <div class="row span2">
        <?php echo $form->label($model, 'app_id');
              echo $form->dropDownList($model, 'app_id', AdminApp::model()->getAll(1),array('style'=>'width:150px'));
	?>
    </div>

    <div class="row span2">
        <?php echo $form->label($model,'controller'); ?>
        <?php echo $form->textField($model,'controller',array('style'=>'width:150px','maxlength'=>20)); ?>
    </div>

    <div class="row span2">
        <?php echo $form->label($model,'action'); ?>
        <?php echo $form->textField($model,'action',array('style'=>'width:150px','maxlength'=>20)); ?>
    </div>

    <div class="row span2">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('style'=>'width:150px','maxlength'=>20)); ?>
    </div>

    <div class="row span2">
        <?php echo $form->label($model,'audit_status'); ?>
        <?php echo $form->dropDownList($model,'audit_status',AdminActions::$audit_status,array('style'=>"width:150px")); ?>
    </div>

    <div class="row span2">
        <label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>
<hr class="divider" />

<div class="row span2">
    <a id="rolesModAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("adminuserNew/actioncreate",array("id"=>0,"dialog"=>1,"grid_id"=>'admin-user-grid'));?>">添加功能项</a>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'admin-user-grid',
    'itemsCssClass'=>'table table-striped',
    'dataProvider'=>$model->search(),
    'columns'=>array (
        'id',
        array(
            'name' => 'app_id',
            'type' => 'raw',
            'value' => 'AdminApp::model()->getAppName($data->app_id)'
        ),
        'name',
        'desc',
        'controller',
        'action',
        'action_url',

        array(
            'name'=>'状态',
            'value'=>'AdminActions::getActionStatus($data->status)',
        ),
        array (
            'name'=>'拥有该权限部门',
            'headerHtmlOptions'=>array (
                'width'=>'100px'),
            'type'=>'raw',
            'value'=>array($this,'showGroupName')),
        array(
            'name'=>'审核配置',
            'headerHtmlOptions'=>array(
                'width'=>'70px',
                'nowrap'=>'nowrap',
            ),
            'type'=>'raw',
            'value' =>'CHtml::link("操作", array("adminuserNew/actioneditaudit","action_id"=>$data->id),array("onclick"=>"openDialog()","class"=>"update"))',
        ),
        array (
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array (
                'update'=>array (
                    'label'=>'修改',
                    'url'=>'$this->grid->controller->createUrl("actionupdate",array("id"=>$data->id,"dialog"=>1,"grid_id"=>$this->grid->id));',
                    'click'=>$click_update))))));

?>

