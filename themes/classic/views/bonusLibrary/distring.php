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
<?php

if ($type == 3) {
    echo CHtml::link('库房未分配', Yii::app()->createUrl('bonusLibrary/bonus_distring'), array('class' => 'btn btn-success', 'target' => '_self'));
} else {
    echo CHtml::link('库房坏卡', Yii::app()->createUrl('bonusLibrary/bonus_distring&type=3'), array('class' => 'btn btn-success', 'target' => '_self'));
}
?>
    <?php if ($city_id > 0) { ?>
        &nbsp;
        <?php echo CHtml::link('分公司详情', Yii::app()->createUrl('bonusLibrary/channel_bonus&city_id='.$city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
        &nbsp;
        <?php echo CHtml::link('分公司已分配', Yii::app()->createUrl('bonusLibrary/bonus_distried&type=1&city_id='.$city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
        &nbsp;
        <?php echo CHtml::link('分公司未分配', Yii::app()->createUrl('bonusLibrary/bonus_distring&type=1&city_id='.$city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    <?php } ?>
<?php }else { ?>
    &nbsp;
    <?php echo CHtml::link('按渠道查看', Yii::app()->createUrl('bonusLibrary/channel_bonus'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('已分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distried'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('未分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distring'), array('class' => 'btn btn-success', 'target' => '_self')); ?>

<?php } ?>
<div class="search-form" style="display:block;">
    <?php
    $this->renderPartial('_search_distring', array(
        'model' => $model,
        'is_manager'=>$is_manager
    ));
    ?>
</div>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'view_bonus_dialog',
    'options' => array(
        'title' => '分配渠道',
        'autoOpen' => false,
        'width' => '780',
        'height' => '540',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#view_bonus_dialog").dialog("close");}'))));
echo '<div id="view_bonus_dialog"></div>';
echo '<iframe id="view_bonus_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');


$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'view_bonus_dialog_error',
    'options' => array(
        'title' => '标记坏卡',
        'autoOpen' => false,
        'width' => '780',
        'height' => '540',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#view_bonus_dialog_error").dialog("close");}'))));
echo '<div id="view_bonus_dialog_error"></div>';
echo '<iframe id="view_bonus_frame_error" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>


<?php
if($show_type==0||$show_type==1) {
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
}else{
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

if ($type == 3 || $show_type==2) {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid',
        'dataProvider' => $repdp,
        'itemsCssClass' => 'table table-striped',
        'enableSorting' => FALSE,
//	'filter'=>$model,
        'columns' => array(
            array(
                'header' => '实体卷编号',
                'name' => 'bonus_id',
                'value' => '$data["bonus_sn"]',
            ),
            array(
                'header' => '实体卷名称',
                'name' => 'bonus_sn',
                'type' => 'raw',
                'value' => '$data["name"]',
            ),
            array(
                'header' => '创建时间',
                'name' => 'created',
                'type' => 'raw',
                'value' => 'substr($data["created"],0,16)',
            ),
            array(
                'name' => 'date',
                'header' => '日期限制',
                'type' => 'raw',
                'value' => '
                "生效日期：".substr($data["effective_date"],0,16)."<br>".
                "绑定截止：".substr($data["binding_deadline"],0,16)."<br>".
                "使用截止：".substr($data["end_date"],0,16)."<br>"
            ',
            ),
            array(
                'name' => 'channel_limited',
                'htmlOptions' => array(
                    'style' => 'width:18%;'
                ),
                'header' => '类型限制',
                'type' => 'raw',
                'value' => '
                "使用：".Dict::item(\'channel_limited\',$data["channel_limited"])
            ',
            ),
            array(
                'name' => 'money',
                'type' => 'raw',
                'header' => '金额',
                'value' => '$data["money"]',
            ),
        ),
    ));
} else {

    if($is_manager==0){
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'bonus-library-grid',
            'dataProvider' => $repdp,
            'itemsCssClass' => 'table table-striped',
            'enableSorting' => FALSE,
//	'filter'=>$model,
            'columns' => array(
//        'id',
                array(
                    'header' => '<input id="areaAll" type="checkbox" name="areaAll" value="-1">',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap',
                        'style' => 'width:45px;'
                    ),
                    'type' => 'raw',
                    'value' => 'CHtml::checkBox("area",false,array("value" => $data["bonus_sn"]));',
                    'footer' => '<button type="button" onclick="GetAllBox();" style="width:45px">全选</button>',
                ),
                array(
                    'header' => '实体卷编号',
                    'name' => 'bonus_id',
                    'value' => '$data["bonus_sn"]',
                    'footer' => '<button type="button" onclick="GetUnBox();" style="width:76px">取消全选</button>
                        <button type="button" id="reverse" style="width:50px">反选</button>',
                ),
                array(
                    'header' => '实体卷名称',
                    'name' => 'bonus_sn',
                    'type' => 'raw',
                    'value' => '$data["name"]',
                    'footer' => '<button type="button" onclick="openInit(\'\',\'0\')" style="width:45px">分配</button>
<button type="button" onclick="openInit(\'\',\'1\')" style="width:90px">分配分公司</button>
<button type="button" onclick="openErrorCard()" style="width:76px">标记坏卡</button>',
                ),
                array(
                    'header' => '创建时间',
                    'name' => 'created',
                    'type' => 'raw',
                    'value' => 'substr($data["created"],0,16)',
                ),
                array(
                    'name' => 'date',
                    'header' => '日期限制',
                    'type' => 'raw',
                    'value' => '
                "生效日期：".substr($data["effective_date"],0,16)."<br>".
                "绑定截止：".substr($data["binding_deadline"],0,16)."<br>".
                "使用截止：".substr($data["end_date"],0,16)."<br>"
            ',
                ),
                array(
                    'name' => 'channel_limited',
                    'htmlOptions' => array(
                        'style' => 'width:18%;'
                    ),
                    'header' => '类型限制',
                    'type' => 'raw',
                    'value' => '
                "使用：".Dict::item(\'channel_limited\',$data["channel_limited"])
            ',
                ),
                array(
                    'name' => 'money',
                    'type' => 'raw',
                    'header' => '金额',
                    'value' => '$data["money"]',
                ),
            ),
        ));
    }else{
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'bonus-library-grid',
            'dataProvider' => $repdp,
            'itemsCssClass' => 'table table-striped',
            'enableSorting' => FALSE,
//	'filter'=>$model,
            'columns' => array(
//        'id',
                array(
                    'header' => '<input id="areaAll" type="checkbox" name="areaAll" value="-1">',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap',
                        'style' => 'width:45px;'
                    ),
                    'type' => 'raw',
                    'value' => 'CHtml::checkBox("area",false,array("value" => $data["bonus_sn"]));',
                    'footer' => '<button type="button" onclick="GetAllBox();" style="width:45px">全选</button>',
                ),
                array(
                    'header' => '实体卷编号',
                    'name' => 'bonus_id',
                    'value' => '$data["bonus_sn"]',
                    'footer' => '<button type="button" onclick="GetUnBox();" style="width:76px">取消全选</button>
                        <button type="button" id="reverse" style="width:50px">反选</button>',
                ),
                array(
                    'header' => '实体卷名称',
                    'name' => 'bonus_sn',
                    'type' => 'raw',
                    'value' => '$data["name"]',
                    'footer' => '<button type="button" onclick="openInit(\'\',\'0\')" style="width:45px">分配</button>',
                ),
                array(
                    'header' => '创建时间',
                    'name' => 'created',
                    'type' => 'raw',
                    'value' => 'substr($data["created"],0,16)',
                ),
                array(
                    'name' => 'date',
                    'header' => '日期限制',
                    'type' => 'raw',
                    'value' => '
                "生效日期：".substr($data["effective_date"],0,16)."<br>".
                "绑定截止：".substr($data["binding_deadline"],0,16)."<br>".
                "使用截止：".substr($data["end_date"],0,16)."<br>"
            ',
                ),
                array(
                    'name' => 'channel_limited',
                    'htmlOptions' => array(
                        'style' => 'width:18%;'
                    ),
                    'header' => '类型限制',
                    'type' => 'raw',
                    'value' => '
                "使用：".Dict::item(\'channel_limited\',$data["channel_limited"])
            ',
                ),
                array(
                    'name' => 'money',
                    'type' => 'raw',
                    'header' => '金额',
                    'value' => '$data["money"]',
                ),
            ),
        ));
    }

}
?>

<script type="text/javascript">
    function openInit(bonus_sn, type) {
        var bonus_sn = "";
        var select = $('input[name=area]');
        $.each(select, function (index, data) {
            if ($(data).is(":checked")) {
                bonus_sn += "," + $(data).val();
            }
        });

        if (bonus_sn.length > 0) {
            bonus_sn.substring(1, bonus_sn.length);
        }

        var src = "<?php echo Yii::app()->createUrl('/bonusLibrary/bonus_distri_channel');?>" + "&bonus_sn=" + bonus_sn + "&dis_city=" + type;
        $("#view_bonus_frame").attr("src", src);
        $("#view_bonus_dialog").dialog("open");
        return false;
    }


    function GetAllBox() {
        $("[name = area]:checkbox").attr("checked", true);
    }

    function openErrorCard() {
        var select = $('input[name=area]');
        var bonus_sn = '';
        $.each(select, function (index, data) {
            if ($(data).is(":checked")) {
                bonus_sn += "," + $(data).val();
            }
        });

        if (bonus_sn.length > 0) {
            bonus_sn.substring(1, bonus_sn.length);
        }else{
            alert('请选择坏卡！');
            return false;
        }

        var src = "<?php echo Yii::app()->createUrl('/bonusLibrary/error_card');?>" + "&bonus_sn=" + bonus_sn;
        $("#view_bonus_frame_error").attr("src", src);
        $("#view_bonus_dialog_error").dialog("open");
        return false;
    }
    function GetUnBox() {
        $("[name = area]:checkbox").attr("checked", false);
    }

    $("document").ready(function () {
        $("#areaAll").live("change", function () {
            if ($("#areaAll").attr("checked")) {
                $("[name = area]:checkbox").attr("checked", true);
            } else {
                $("[name = area]:checkbox").attr("checked", false);
            }
        });

        $("#reverse").live("click", function () {
            $("[name = area]:checkbox").each(function () {
                $(this).attr("checked", !$(this).attr("checked"));
            });
        });


    })
</script>
