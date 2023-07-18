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
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'view_bonus_dialog',
    'options' => array(
        'title' => '优惠劵规则',
        'autoOpen' => false,
        'width' => '580',
        'height' => '440',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#view_bonus_dialog").dialog("close");}'))));
echo '<div id="view_bonus_dialog"></div>';
echo '<iframe id="view_bonus_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<h1>实体劵管理</h1>

<?php echo CHtml::link('实物卡使用统计', array('bonusLibrary/bonus_channel'), array('class' => 'btn')); ?>&nbsp;
<?php echo CHtml::link('实物卡渠道分配', array('bonusLibrary/assign'), array('class' => 'btn')); ?>

<div class="search-form">
    <?php
    $this->renderPartial('_search_assign', array(
        'model' => $model,
        'area' => $area
    ));
    ?>
</div>
<!-- search-form -->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-library-grid',
    'dataProvider' => $data,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'header' => '<input id="areaAll" type="checkbox" name="areaAll" value="-1">',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'style' => 'width:45px;'
            ),
            'type' => 'raw',
            'value' => '$data->owner == "" ? CHtml::checkBox("area",false,array("value" => $data->bonus_sn)) : ""',
            'footer' => '<button type="button" onclick="GetAllBox();" style="width:45px">全选</button>',
        ),
        array(
            'name' => 'number',
            'type' => 'raw',
            'value' => '$data->number',
        ),
        array(
            'name' => 'bonus_id',
            'value' => '$data->bonus_id."(".BonusLibrary::model()->getBonusName($data->bonus_id).")"',
            'footer' => '<button type="button" onclick="GetUnBox();" style="width:76px">取消全选</button>
                        <button type="button" id="reverse" style="width:50px">反选</button>',
        ),

        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => '$data->status == 0 ? "未绑定" : "已绑定"',
            'footer' => '<button type="button" onclick="openInit(\'\',\'\')" style="width:45px">分配</button>',
        ),
        'money',
        array(
            'name' => 'sn_type',
            'type' => 'raw',
            'value' => 'Dict::item(\'bonus_sn_type\',$data->sn_type)',
        ),
        'create_by',
        'created',
        array(
            'name' => 'owner',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'BonusLibrary::model()->ownerShow($data)'
        ),
    ),
));
?>

<script type="text/javascript">
    function openInit(bonus_sn, bonus_id) {
        var src = "<?php echo Yii::app()->createUrl('/bonusCode/owner');?>" + "&bonus_sn=" + bonus_sn + "&bonus_id=" + bonus_id;
        $("#view_bonus_frame").attr("src", src);
        $("#view_bonus_dialog").dialog("open");
        return false;
    }

    function GetAllBox(){
        $("[name = area]:checkbox").attr("checked", true);
    }

    function GetUnBox(){
        $("[name = area]:checkbox").attr("checked", false);
    }

    $("document").ready(function () {
        $("#areaAll").bind("change", function () {
            if ($("#areaAll").attr("checked")) {
                $("[name = area]:checkbox").attr("checked", true);
            } else {
                $("[name = area]:checkbox").attr("checked", false);
            }
        });

        $("#reverse").bind("click", function () {
            $("[name = area]:checkbox").each(function () {
                $(this).attr("checked", !$(this).attr("checked"));
            });
        });

        select_channel();
    })

    function select_channel() {
        var area_id = $("#BonusLibrary_city_id").val();
        if (area_id != 0) {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('/bonusCode/ajax_channel');?>',
                'data': 'area_id=' + area_id,
                'type': 'get',
                'success': function (data) {
                    $("#BonusLibrary_owner").empty().append(data);
                },
                'cache': false
            });
        }
        return false;
    }
</script>
