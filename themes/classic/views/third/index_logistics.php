<?php
/* @var $this CardController */
/* @var $model DriverOrder */

//Yii::app()->clientScript->registerScript('search', "
//$('.search-button').click(function(){
//	$('.search-form').toggle();
//	return false;
//});
//$('.search-form form').submit(function(){
//	$.fn.yiiGridView.update('driver-zhaopin-grid', {
//		data: $(this).serialize()
//	});
//	return false;
//});
//");

$order_status = isset($_REQUEST['order_status']) ? $_REQUEST['order_status'] : -1;
$city_id = isset($_REQUEST['city_id']  ) ? $_REQUEST['city_id'] : 0;
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$driver_phone = isset($_REQUEST['driver_phone']) ? $_REQUEST['driver_phone'] : '';
$order_number = isset($_REQUEST['order_number']) ? $_REQUEST['order_number'] : '';
//默认昨天到今天
$yesterday = $today = date("Y-m-d", strtotime("-1 day"));
$order_start = isset($_REQUEST['order_start']) ? $_REQUEST['order_start'] : $yesterday;
$order_end = isset($_REQUEST['order_end']) ? $_REQUEST['order_end'] : $today;

?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'import_delivery_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'',
        'autoOpen'=>false,
        'width'=>'480',
        'height'=>'380',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#import_delivery_dialog").dialog("close");}'))));
echo '<div id="import_delivery_dialog"></div>';
echo '<iframe id="import_delivery_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<table>
    <tr>
        <td><h1>物流管理</h1></td>
        <td>
                    <span><?php echo CHtml::Button('导入已入库记录', array('class' => 'btn btn-success', 'id' => 'import_btn_one', 'act' => 'import_btn_one', 'func' => 'import_btn_one', 'style' => 'margin-left:30px')); ?>
</span>
        <span><?php echo CHtml::Button('导入已发货记录', array('class' => 'btn btn-success', 'id' => 'import_btn_two', 'act' => 'import_btn_two', 'func' => 'import_btn_two', 'style' => 'margin-left:30px')); ?>
</span>
        <span><?php echo CHtml::Button('导入签收记录', array('class' => 'btn btn-success', 'id' => 'import_btn_three', 'act' => 'import_btn_three', 'func' => 'import_btn_three', 'style' => 'margin-left:30px')); ?>
</span>
        <span><?php echo CHtml::Button('导入异常订单', array('class' => 'btn btn-success', 'id' => 'import_btn_four', 'act' => 'import_btn_four', 'func' => 'import_btn_four', 'style' => 'margin-left:30px')); ?>
</span>
        </td>
    </tr>
</table>
<div class="search-form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'driver-admin-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'errorMessageCssClass' => 'alert alert-error',
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'post'
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php
            $status = DriverOrder::$status_array;
            $status[-1] = '全部';
            ksort($status);
            unset($status[DriverOrder::STATUS_UN_PAY]);
            unset($status[DriverOrder::STATUS_TO_CARD]);
            unset($status[DriverOrder::STATUS_PAYED]);
            unset($status[DriverOrder::STATUS_OFFLINE]);
            echo CHtml::label('订单状态', 'order_status');
            echo CHtml::dropDownList('order_status',
                $order_status, //默认选中
                $status,
                array()
            );
            ?>
        </div>

        <div class="span3">

            <?php
            echo CHtml::label('城市（如需重新选择“全国”，请刷新页面）', 'city');
            $user_city_id = Yii::app()->user->city;

            if ($user_city_id != 0) {
                $city_list = array(
                    '城市' => array(
                        $user_city_id => Dict::item('city', $user_city_id)
                    )
                );
                $city_id = $user_city_id;
            } else {
                $city_list = CityTools::cityPinYinSort();
            }
            $this->widget("application.widgets.common.DropDownCity", array(
                'cityList' => $city_list,
                'name' => 'city_id',
                'value' => $city_id,
                'type' => 'modal',
                'htmlOptions' => array(
                    'style' => 'width: 134px; cursor: pointer;',
                    'readonly' => 'readonly',
                )
            ));
            ?>
        </div>


        <div class="span3">
            <label for="mobile">下单日期</label>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'attribute' => 'visit_time',
                'language' => 'zh_cn',
                'name' => 'order_start',
                'value' => $order_start,
                'options' => array(
                    'showAnim' => 'fold',
                    'showOn' => 'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly' => true,
                    //'minDate'=>'new Date()',
                    'dateFormat' => 'yy-mm-dd',
                    'changeYear' => true,
                    'changeMonth' => true,
                ),
                'htmlOptions' => array(
                    'style' => 'width:85px',
                ),
            ));
            ?>
            至
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'attribute' => 'visit_time',
                'language' => 'zh_cn',
                'name' => 'order_end',
                'value' => $order_end,
                'options' => array(
                    'showAnim' => 'fold',
                    'showOn' => 'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly' => true,
                    //'minDate'=>'new Date()',
                    'dateFormat' => 'yy-mm-dd',
                    'changeYear' => true,
                    'changeMonth' => true,
                ),
                'htmlOptions' => array(
                    'style' => 'width:85px',
                ),
            ));
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3">
            <label for="name">姓名</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>"/>
        </div>
        <div class="span3">
            <label for="order_number">订单编号</label>
            <input type="text" id="order_number" name="order_number" value="<?php echo $order_number; ?>"/>
        </div>
        <div class="span3">
            <label for="driver_phone">手机号</label>
            <input type="text" id="driver_phone" name="driver_phone" value="<?php echo $driver_phone; ?>"/>
        </div>

    </div>

    <div class="row-fluid">
        <?php echo CHtml::submitButton('搜索', array('class' => 'btn btn-success')); ?>
    <span><?php echo CHtml::Button('导出', array('class' => 'btn btn-success', 'id' => 'export_btn', 'act' => 'export_btn', 'func' => 'export_btn', 'style' => 'margin-left:30px')); ?>
</span>
        <!--        <span>--><?php //echo CHtml::Button('历史记录', array('class' => 'btn btn-success', 'id' => 'his_btn', 'act' => 'his_btn', 'func' => 'his_btn', 'style' => 'margin-left:30px')); ?>
        <!--</span>-->
    </div>

</div>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'driver-card-grid',
    'cssFile' => SP_URL_CSS . 'table.css',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
    'selectableRows' => 2,
    'columns' => array(
        array(
            'name' => '订单编号',
            'value' => '$data->order_number',
        ),
        array(
            'name' => '姓名',
            'value' => '$data->driver_name',
        ),
        array(
            'name' => '手机号',
            'value' => '$data->driver_phone',
        ),

        array(
            'name' => '城市',
            'value' => 'Dict::item("city",$data->city_id)',
        ),

        array(
            'name' => '下单时间',
            'type' => 'raw',
            'value' => '$data->order_time',
        ),
        array(
            'name' => '订单状态',
            'type' => 'raw',
            'value' => 'DriverOrder::$status_dict[$data->order_status]',
        ),
        array(
            'name' => '物流单号',
            'type' => 'raw',
            'value' => '$data->logistics_number',
        ),
        array(
            'name' => '操作',
            'type' => 'raw',
            'value'=>array($this,'opt')
        ),
    ),
));


?>
<script>

    window.onload = function () {
        jQuery('.ui-datepicker-trigger').remove();
    }

    $(function () {
        //导出
        $("[func='export_btn']").click(function () {
            //获取搜索参数
            var order_status = $('#order_status').val();
            var city_id = $('#city_id').val();
            var order_start = $('#order_start').val();
            var order_end = $('#order_end').val();
            var name = $('#name').val();
            var order_number = $('#order_number').val();
            var driver_phone = $('#driver_phone').val();

            if(order_status == -1 && city_id == 0 && !order_start && !order_end && !name && !order_number && !driver_phone){
                alert('请选择查询条件');
                return false;
            }
            var url = '<?php echo Yii::app()->createUrl('third/exportLogistics');?>'+'&order_status='+order_status
                +'&order_status='+order_status
                +'&city_id='+city_id
                +'&order_start='+order_start
                +'&order_end='+order_end
                +'&order_number='+order_number
                +'&driver_phone='+driver_phone
                +'&name='+name;
            window.open(url);
        });

        //导出历史记录页面
        $("[func='his_btn']").click(function () {
            var url = '<?php echo Yii::app()->createUrl('third/logisticsRecord');?>';
            window.open(url);
        });

        $("[func='import_btn_one']").click(function () {
            var href = "<?php echo Yii::app()->createUrl('/third/importLogRecord'); ?>"+'&type=4';
            $("#import_delivery_frame").attr("src",href);
            $("#import_delivery_dialog").dialog("open");
            return false;
        })

        $("[func='import_btn_two']").click(function () {
            var href = "<?php echo Yii::app()->createUrl('/third/importLogRecord'); ?>"+'&type=5';
            $("#import_delivery_frame").attr("src",href);
            $("#import_delivery_dialog").dialog("open");
            return false;
        })

        $("[func='import_btn_three']").click(function () {
            var href = "<?php echo Yii::app()->createUrl('/third/importLogRecord'); ?>"+'&type=6';
            $("#import_delivery_frame").attr("src",href);
            $("#import_delivery_dialog").dialog("open");
            return false;
        })

        $("[func='import_btn_four']").click(function () {
            var href = "<?php echo Yii::app()->createUrl('/third/importLogRecord'); ?>"+'&type=8';
            $("#import_delivery_frame").attr("src",href);
            $("#import_delivery_dialog").dialog("open");
            return false;
        })
    });
</script>