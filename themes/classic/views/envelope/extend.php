<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
    var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
    $(this).text(title);
	return false;
});
$('.search-form form').submit(function(){
	$('#envelope-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php echo CHtml::link('展开搜索', '#', array('class' => 'btn search-button')); ?>
    &nbsp;
<?php echo CHtml::link('创建红包', Yii::app()->createUrl('envelope/create'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
<?php echo CHtml::link('进行中的红包', Yii::app()->createUrl('envelope/admin'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
<?php echo CHtml::link('红包发放列表', Yii::app()->createUrl('envelope/extend'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
<?php echo CHtml::link('红包发放统计', Yii::app()->createUrl('envelope/city'), array('class' => 'btn btn-success', 'target' => '_self')); ?>


    <div class="search-form" style="display:none">
        <?php
        $this->renderPartial('_form_extend', array(
            'model' => $model,
            'arr_amount' =>$arr_amount
        ));
        ?>
    </div><!-- search-form -->

    <h1><?php echo $msg; ?></h1>

<?php
$gridId = 'envelope-grid';
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => $gridId,
    'dataProvider' => $data,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'name' => '城市',
            'type' => 'raw',
            'value' => 'Dict::item(\'city\',$data->city_id)',
        ),
        array(
            'name' => '司机',
            'type' => 'raw',
            'value' => '$data->drive_id',
        ),
        array(
            'name' => '金额',
            'type' => 'raw',
            'value' => '$data->amount',
        ),
        array(
            'name' => '时间',
            'type' => 'raw',
            'value' => '$data->create_date',
        ),
        array(
            'name' => '状态',
            'type' => 'raw',
            'value' => '$data->is_use==1?"已领取":("未领取".CHtml::link("重新发放",array("envelope/rePush","id"=>$data->id),array("title" => "重新发放红包","onclick"=>"
                 $(\'#cru-frame\').attr(\'src\',$(this).attr(\'href\'));
                 $(\'#cru-frame\').show();
                 $(\'#mydialog\').dialog(\'open\');
                 return false;
            ")))',
        ),
    ),
));

?>


<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    'options' => array(
        'title' => '重新发放',
        'autoOpen' => false,
        'width' => '600',
        'height' => '450',
        'buttons' => array('关闭' => 'js:function(){$("#mydialog").dialog("close");}')
    ),
));
?>
    <iframe id="cru-frame" src="" style="width:550px;height:330px;border:0px;margin:0px;display:none;"></iframe>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>