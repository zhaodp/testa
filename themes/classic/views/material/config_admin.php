<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'define_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'物料管理',
        'autoOpen'=>false,
        'width'=>'600',
        'height'=>'580',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#define_dialog").dialog("close");}'))));
echo '<div id="define_dialog"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

Yii::app()->clientScript->registerScript('addMaterialMod', "
$('#materialModAddBtn').click(function(){
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

$this->renderPartial('tab',array('tab'=> 5));


?>



<div class="row span2">
    <a id="materialModAddBtn" class="btn btn-primary" href="<?php echo Yii::app()->createUrl("material/configCreate",array("id"=>0,"dialog"=>1,"grid_id"=>'material_config-grid'));?>">添加物料</a>
</div>
<?php
    $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'material_config-grid',
    //'ajaxUpdate' => false,
    'cssFile'=>SP_URL_CSS.'table.css',
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'dataProvider' => $model->search(),
    'columns' => array(
        'third_id',
        'name',
        array(
            'name'=>'type_id',
            'type' => 'raw',
            'value'=> 'Material::getTypeInfoName($data->type_id)'
        ),
        'price',
        'depreciation',
        'loss_cost',

        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => 'Material::getStatus($data->status)', //array($this,'getAdminStatus') //'($data->status==AdminUserNew::STATUS_NORMAL)?"正常":"禁用"'
        ),

        array(
            'class' => 'CButtonColumn',
            'template' => ' {delete}',
            'buttons'=>array(
//                'update'=>array(
//                    'label'=>'修改',
//                    'url'=>'$this->grid->controller->createUrl("configUpdate",array("id"=>$data->id,"dialog"=>1,"grid_id"=>$this->grid->id));',
//                    'click'=>$click_update,
//                    //'options' => array('data-loading-text'=>'Loading...','data-complete-text'=>'操作完成'),
//                    'visible'=>'Material::model()->haveViewPermission("material", "update",$data->id)'
//                ),
                'delete'=>array(
                    'label'=>'删除',
                    'url' =>'$this->grid->controller->createUrl("configDelete",array("id"=>$data->id));',
                    'options' => array('data-loading-text'=>'Loading...','data-complete-text'=>'操作完成'),
                    'visible'=>'Material::model()->haveViewPermission("material", "delete",$data->id)',
                ),
            ),
        ),
    ),
));
 ?>
