<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
    var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
    $(this).text(title);
	return false;
});
$('.search-form form').submit(function(){
	$('#bonus-library-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	$('#bonus-library-grid-count').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

?>


<?php echo CHtml::link('收起搜索', '#', array('class' => 'search-button btn')); ?>
<?php if ($is_manager == 0) { ?>
    &nbsp;
    <?php echo CHtml::link('全国城市查看', Yii::app()->createUrl('bonusLibrary/bonus_distri'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('全国渠道查看', Yii::app()->createUrl('bonusLibrary/channel_bonus'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('全国已分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distried'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('全国异常卡', Yii::app()->createUrl('bonusLibrary/bonus_distried&type=3'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('库存未分配', Yii::app()->createUrl('bonusLibrary/bonus_distring'), array('class' => 'btn btn-success', 'target' => '_self')); ?>

    <?php if ($city_id > 0) { ?>
        &nbsp;
        <?php echo CHtml::link('分公司详情', Yii::app()->createUrl('bonusLibrary/channel_bonus&city_id='.$city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
        &nbsp;
        <?php echo CHtml::link('分公司已分配', Yii::app()->createUrl('bonusLibrary/bonus_distried&type=1&city_id='.$city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
        &nbsp;
        <?php echo CHtml::link('分公司未分配', Yii::app()->createUrl('bonusLibrary/bonus_distring&type=1&city_id='.$city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    <?php } ?>
<?php } else { ?>
    &nbsp;
    <?php echo CHtml::link('按渠道查看', Yii::app()->createUrl('bonusLibrary/channel_bonus'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('已分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distried'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('未分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distring'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
<?php } ?>

<div class="search-form" style="display:block;">
    <?php
    $this->renderPartial('_search_city', array(
        'model' => $model,
        'is_manager' => $is_manager,
        'arr_dis' => $arr_dis,
        'dateStart' => $dateStart,
        'dateEnd' => $dateEnd
    ));
    ?>
</div>
<!-- search-form -->
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '实体卷使用',
        'autoOpen' => false,
        'width' => '900',
        'height' => '500',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#mydialog").dialog("close");} '
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>



<?php
if ($show_type == 0) {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid-count',
        'dataProvider' => $num,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => 'bonus_sn',
                'header' => '实体卷总数',
                'type' => 'raw',
                'value' => '$data["bonus_sn"]==""?0:$data["bonus_sn"]',
            ),
            array(
                'name' => 'channel',
                'header' => '已分配',
                'type' => 'raw',
                'value' => '$data["channel"]==""?0:$data["channel"]',
            ),
            array(

                'name' => 'number',
                'type' => 'raw',
                'header' => '已使用',
                'value' => '$data["number"]==""?0:$data["number"]',
            ),

            array(
                'name' => 'password',
                'type' => 'raw',
                'header' => '未使用',
                'value' => '$data["password"]==""?0:$data["password"]',
            ),
            array(
                'name' => 'money',
                'type' => 'raw',
                'header' => '库存',
                'value' => '$data["money"]==""?0:$data["money"]',
            ),
            array(
                'name' => 'bonus_id',
                'type' => 'raw',
                'header' => '坏卡',
                'value' => '$data["bonus_id"]==""?0:$data["bonus_id"]',
            ),
            array(
                'name' => 'owner',
                'header' => '异常卡',
                'type' => 'raw',
                'value' => '$data["owner"]==""?0:$data["owner"]',
            ),
            array(
                'name' => 'owner',
                'header' => '回收率',
                'type' => 'raw',
                'value' => '$data["bonus_sn"]==0?0:(round($data["number"]/$data["bonus_sn"], 2)*100)."%"',
            ),
            array(
                'name' => 'update_by',
                'header' => '渠道数量',
                'type' => 'raw',
                'value' => '$data["update_by"]==""?0:$data["update_by"]',
            ),
        ),
    ));
} else {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid-count',
        'dataProvider' => $num,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => 'bonus_sn',
                'header' => '实体卷总数',
                'type' => 'raw',
                'value' => '$data["bonus_sn"]==""?0:$data["bonus_sn"]',
            ),
            array(
                'name' => 'channel',
                'header' => '已分配',
                'type' => 'raw',
                'value' => '$data["channel"]==""?0:$data["channel"]',
            ),
            array(

                'name' => 'number',
                'type' => 'raw',
                'header' => '已使用',
                'value' => '$data["number"]==""?0:$data["number"]',
            ),

            array(
                'name' => 'password',
                'type' => 'raw',
                'header' => '未使用',
                'value' => '$data["password"]==""?0:$data["password"]',
            ),
            array(
                'name' => 'money',
                'type' => 'raw',
                'header' => '未分配',
                'value' => '$data["money"]==""?0:$data["money"]',
            ),
            array(
                'name' => 'owner',
                'header' => '回收率',
                'type' => 'raw',
                'value' => '$data["bonus_sn"]==0?0:(round($data["number"]/$data["bonus_sn"], 2)*100)."%"',
            ),
            array(
                'name' => 'update_by',
                'header' => '渠道数量',
                'type' => 'raw',
                'value' => '$data["update_by"]==""?0:$data["update_by"]',
            ),
        ),
    ));
}
?>


<?php

if ($is_manager == 0) {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid',
        'dataProvider' => $data,
        'itemsCssClass' => 'table table-striped',
        'enableSorting' => FALSE,
        'columns' => array(
            array(
                'name' => 'city_id',
                'header' => '城市',
                'type' => 'raw',
                'value' => 'Dict::item("city",$data["city_id"])',
            ),
            array(
                'name' => 'channel_name',
                'header' => '渠道名称',
                'type' => 'raw',
                'value' => '$data["channel_name"]',
            ),

            array(
                'name' => 'contact',
                'header' => '联系人',
                'type' => 'raw',
                'value' => '$data["contact"]',
            ),
            array(
                'name' => 'tel',
                'header' => '联系电话',
                'type' => 'raw',
                'value' => '$data["tel"]',
            ),
            array(
                'name' => 'distri_by',
                'header' => '分配人',
                'type' => 'raw',
                'value' => '$data["distri_by"]',
            ),

            array(
                'name' => 'dis_count',
                'header' => '被分配次数',
                'type' => 'raw',
                'value' => '$data["dis_count"]',

            ),
            array(
                'name' => 'number',
                'header' => '总分配数量',
                'type' => 'raw',
                'value' => '$data["number"]',
            ),
            array(
                'name' => 'password',
                'header' => '已使用',
                'type' => 'raw',
                'value' => '$data["password"]==0?0:CHtml::link($data["password"],array("bonusLibrary/channel_bonus_list","channel"=>$data["channel"],"dateStart"=>$data["dateStart"],"dateEnd"=>$data["dateEnd"]),array("channel"=>$data["channel"],"title" => "点击查看实体劵使用详情","onclick"=>"
                $(\'#cru-frame\').attr(\'src\',$(this).attr(\'href\'));
                 $(\'#cru-frame\').show();
                 $(\'#dialogdiv\').dialog(\'open\');
                 return false;
                 "))',
            ),
            array(
                'name' => 'noused',
                'header' => '未使用',
                'type' => 'raw',
                'value' => '$data["nuused"]',
            ),
            array(
                'name' => 'use',
                'header' => '回收率',
                'type' => 'raw',
                'value' => '$data["number"]==0?"0%":(round($data["password"]/$data["number"], 2)*100)."%"',
            ),
            array(
                'header' => '详情',
                'class' => 'CButtonColumn',
                'template' => '{select}',
                'buttons' => array(
                    'select' => array(
                        'label' => '详情',
                        //   'visible' => 'in_array($data->status, array(BonusCode::STATUS_APPROVED))',
                        'options' => array('target' => '_self'),
                    'url' => 'Yii::app()->createUrl("bonusLibrary/bonus_distried", array("channel"=>$data["channel"],"city_id"=>$data["city_id"],"dateStart"=>$data["dateStart"],"dateEnd"=>$data["dateEnd"]))',
                    ),
                )
            ),
        ),
    ));
} else {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid',
        'dataProvider' => $data,
        'itemsCssClass' => 'table table-striped',
        'enableSorting' => FALSE,
        'columns' => array(
            array(
                'name' => 'channel',
                'header' => '渠道名称',
                'type' => 'raw',
                'value' => '$data["channel_name"]',
            ),

            array(
                'name' => 'contact',
                'header' => '联系人',
                'type' => 'raw',
                'value' => '$data["contact"]',
            ),
            array(
                'name' => 'tel',
                'header' => '联系电话',
                'type' => 'raw',
                'value' => '$data["tel"]',
            ),
            array(
                'name' => 'distri_by',
                'header' => '分配人',
                'type' => 'raw',
                'value' => '$data["distri_by"]',
            ),

            array(
                'name' => 'channel',
                'header' => '被分配次数',
                'type' => 'raw',
                'value' => '$data["dis_count"]',

            ),
            array(
                'name' => 'number',
                'header' => '总分配数量',
                'type' => 'raw',
                'value' => '$data["number"]',
            ),
            array(
                'name' => 'password',
                'header' => '已使用',
                'type' => 'raw',
                'value' => '$data["password"]==0?0:CHtml::link($data["password"],array("bonusLibrary/channel_bonus_list","channel"=>$data["channel"],"dateStart"=>$data["dateStart"],"dateEnd"=>$data["dateEnd"]),array("channel"=>$data["channel"],"title" => "点击查看实体劵使用详情","onclick"=>"
                $(\'#cru-frame\').attr(\'src\',$(this).attr(\'href\'));
                 $(\'#cru-frame\').show();
                 $(\'#dialogdiv\').dialog(\'open\');
                 return false;
                 "))',
            ),
            array(
                'name' => 'noused',
                'header' => '未使用',
                'type' => 'raw',
                'value' => '$data["nuused"]',
            ),
            array(
                'name' => 'use',
                'header' => '回收率',
                'type' => 'raw',
                'value' => '$data["number"]==0?"0%":(round($data["password"]/$data["number"], 2)*100)."%"',
            ),
            array(
                'header' => '详情',
                'class' => 'CButtonColumn',
                'template' => '{select}',
                'buttons' => array(
                    'select' => array(
                        'label' => '详情',
                        //   'visible' => 'in_array($data->status, array(BonusCode::STATUS_APPROVED))',
                        'options' => array('target' => '_self'),
                        'url' => 'Yii::app()->createUrl("bonusLibrary/bonus_distried", array("channel"=>$data["channel"],"city_id"=>$data["city_id"],"dateStart"=>$data["dateStart"],"dateEnd"=>$data["dateEnd"]))',
                    ),
                )
            ),
        ),
    ));
}
?>


<script type="text/javascript">
    function openInit(channel) {
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();

        var src = "<?php echo Yii::app()->createUrl('/bonusLibrary/channel_bonus_list');?>" + "&channel=" + channel + "&dateStart=" + dateStart + "&dateEnd=" + dateEnd;
        $("#cru-frame").attr("src", src);
        $("#dialogdiv").dialog("open");
        return false;
    }

</script>
