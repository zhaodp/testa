<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'cru-dialog',
    'options'=>array(
        'title'=>'坐席分配',
        'autoOpen'=>false,
        'modal'=>true,
        'width'=>750,
        'height'=>450,
        'buttons'=>array(
            '关闭'=>'js:function(){$("#cru-dialog").dialog("close");}'
        )
    ),
));
?>
<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>
<?php
$this->endWidget();


$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'admin-agnet-grid',
    'itemsCssClass'=>'table table-striped',
    'dataProvider'=>$model->search(),
    'columns'=>array (
        'agent_num',
        array (
            'name'=>'user_id',
            'type'=>'raw',
            'value'=>'AdminUserNew::model()->getName($data->user_id)'),
        array (
            'name'=>'phone',
            'type'=>'raw'),
        array (
            'name'=>'is_lock',
            'type'=>'raw',
            'value'=>'($data->is_lock==1)?"固定坐席":(($data->is_lock==2)?"天润坐席":"")'),
        array (
            'header'=>'操作',
            'class'=>'CButtonColumn',
            'template'=>'{operate}',
            'buttons'=>array (
                'operate'=>array (
                    'label'=>'分配',
                    'url'=>'$this->grid->controller->createUrl("agentallot", array("agent_num"=>$data->agent_num,"user_id"=>$data->user_id,"asDialog"=>1,"gridId"=>$this->grid->id))',
                    'click'=>'function(){$("#cru-frame").attr("src",$(this).attr("href")); $("#cru-dialog").dialog("open");  return false;}',
                    'visible'=>'AdminActions::model()->havepermission("adminuserNew", "agentallot")'))))));
?>
