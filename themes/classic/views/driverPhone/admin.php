<?php
$this->pageTitle = '司机信息';

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-phone-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array('id' => 'update_driver_phone_dialog', // additional javascript options for the dialog plugin
    'options' => array('title' => '修改司机电话信息', 'autoOpen' => false, 'width' => '580', 'height' => '480', 'modal' => true, 'buttons' => array('关闭' => 'js:function() {closedDialog("update_driver_phone_dialog")}'))));
echo '<div id="update_driver_phone_dialog"></div>';
echo '<iframe id="update_driver_phone_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_update = <<<EOD
function(){
	$("#update_driver_phone_frame").attr("src",$(this).attr("href"));
	$("#update_driver_phone_dialog").dialog("open");
	return false;
}
EOD;
?>

<h1><?php
    echo $this->pageTitle;
    ?></h1>

<div class="search-form">
    <?php
    $this->renderPartial('_search_phone', array('model' => $model));
    ?>
</div>
<!-- search-form -->


<?php
$this->widget('zii.widgets.grid.CGridView',
    array('id' => 'driver-phone-grid',
        'dataProvider' => $model->search(),
        'itemsCssClass' => 'table table-condensed',
        'pagerCssClass'=>'pagination text-center',
        'htmlOptions' =>
        array(
            'class' => 'row span11'),
        'columns' => array(
            array(
                'name' => 'driver_id',
                'headerHtmlOptions' =>
                array(
                    'width' => '50px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data->driver_id'),
//				'value' => 'CHtml::link($data->driver_id,"javascript:void(0)",array("id"=>"driver_$row","onClick" => "updateDriverID(\'driver_$row\',\'$data->imei\',\'$data->simcard\')"))' ),
            array(
                'name' => 'phone',
                'headerHtmlOptions' =>
                array(
                    'width' => '50px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data->phone'),
            array(
                'name' => 'imei',
                'headerHtmlOptions' =>
                array('width' => '50px', 'nowrap' => 'nowrap'), 'type' => 'raw', 'value' => '$data->imei'),
            array('name' => 'simcard', 'headerHtmlOptions' => array('width' => '50px', 'nowrap' => 'nowrap')),
            array('name' => 'device', 'headerHtmlOptions' => array('width' => '50px', 'nowrap' => 'nowrap')),
            array(
                'name' => 'is_bind',
                'headerHtmlOptions' =>
                array(
                    'width' => '50px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '($data->is_bind == 1) ? "已绑定" : "未绑定"'),
            array(
                'name' => '操作',
                'headerHtmlOptions' =>
                array(
                    'width' => '50px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => array($this, 'buttonShow')),

        )
    ));
?>


<script type="text/javascript">
    function closedDialog(id) {
        $("#" + id).dialog("close");
        $('.search-form form').submit();
    }
    function updateDriverID(demo, imei, simcard) {
        var demo_a = $("#" + demo)
        var driver_id = demo_a.html();
        var str_input = '<input type="text" id="driver_id" value="' + driver_id + '" onblur="DriverIDblur(\'' + demo + '\',\'' + imei + '\',\'' + simcard + '\')"/>';
        demo_a.hide();
        demo_a.after(str_input);
        $("#driver_id").focus();
    }
    function DriverIDblur(demo, imei, simcard) {
        var driver_old_id = $("#" + demo).html();
        var driver_id = $("#driver_id").val();
        if (driver_old_id != driver_id) {
            $.ajax({
                url: '<?php echo Yii::app ()->createUrl ( '/driverPhone/ajaxDriverPhone' );?>',
                data: {driver_old_id: driver_old_id, driver_id: driver_id, imei: imei, simcard: simcard},
                dataType: "html",
                success: function (data) {
                    if (data == 1) {
                        $("#driver_id").remove();
                        $("#" + demo).html(driver_id).show();
                        $("#" + demo).parent().parent().children("td:eq(3)").html("已绑定");
                        alert("修改成功！");
                    }
                    else {
                        if (data == 0)
                            alert("修改失败！");
                        if (data == 2)
                            alert("司机工号不存在或已屏蔽！");
                        if (data == 3)
                            alert("司机已经存在！");
                        $("#driver_id").remove();
                        $("#" + demo).show();
                    }
                }
            });
        } else {
            $("#driver_id").remove();
            $("#" + demo).show();
        }
    }

    jQuery(function ($) {
        jQuery('#update_driver_phone_dialog').dialog({'title': '修改司机电话信息', 'autoOpen': false, 'width': '580', 'height': '480', 'modal': true, 'buttons': {'关闭': function () {
            closedDialog("update_driver_phone_dialog")
        }}});
        jQuery(document).on('click', '#driver-phone-grid a.update', function () {
            $("#update_driver_phone_frame").attr("src", $(this).attr("href"));
            $("#update_driver_phone_dialog").dialog("open");
            return false;
        });
        jQuery(document).on('click', '#driver-phone-grid a.delete', function () {
            if (!confirm('确定要删除这条数据吗?')) return false;
            var th = this,
                afterDelete = function () {
                };
            jQuery('#driver-phone-grid').yiiGridView('update', {
                type: 'POST',
                url: jQuery(this).attr('href'),
                success: function (data) {
                    jQuery('#driver-phone-grid').yiiGridView('update');
                    afterDelete(th, true, data);
                },
                error: function (XHR) {
                    return afterDelete(th, false, XHR);
                }
            });
            return false;
        });
        jQuery('#driver-phone-grid').yiiGridView({'ajaxUpdate': ['driver-phone-grid'], 'ajaxVar': 'ajax', 'pagerClass': 'pager', 'loadingClass': 'grid-view-loading', 'filterClass': 'filters', 'tableClass': 'table table-condensed', 'selectableRows': 1, 'enableHistory': false, 'updateSelector': '{page}, {sort}', 'filterSelector': '{filter}', 'pageVar': 'DriverPhone_page'});
    });
</script>

