
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
$driver_id = isset($_REQUEST['driver_id']  ) ? $_REQUEST['driver_id'] : '';
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$driver_phone = isset($_REQUEST['driver_phone']) ? $_REQUEST['driver_phone'] : '';
$order_number = isset($_REQUEST['order_number']) ? $_REQUEST['order_number'] : '';
//默认昨天到今天
$yesterday = $today = date("Y-m-d", strtotime("-1 day"));
$order_start = isset($_REQUEST['order_start']) ? $_REQUEST['order_start'] : $yesterday;
$order_end = isset($_REQUEST['order_end']) ? $_REQUEST['order_end'] : $today;

?>
<table>
    <tr>
        <td><h1>订单管理</h1></td>
    </tr>
</table>
<div class="wide form search-form">

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
            <?php echo CHtml::label('开始日期','start_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'order_start',
                'value'=>$order_start,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'style'=>'width:85px;'
                ),
            ));?>

            至
            <?php
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'order_end',
                'value'=>$order_end,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'style'=>'width:85px;'
                ),
            ));?>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span3">
            <label for="name">姓名</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>"/>
        </div>
        <div class="span3">
            <label for="order_number">电话</label>
            <input type="text" id="driver_phone" name="driver_phone" value="<?php echo $driver_phone; ?>"/>
        </div>
        <div class="span3">
            <label for="driver_phone">工号</label>
            <input type="text" id="driver_id" name="driver_id" value="<?php echo $driver_id; ?>"/>
        </div>

    </div>

    <div class="row-fluid">
        <div class="span3">
            <label for="order_number">订单编号</label>
            <input type="text" id="order_number" name="order_number" value="<?php echo $order_number; ?>"/>
        </div>
        <div class="span3">

            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('搜索',array('class'=>'btn search-button')); ?>
            <?php echo CHtml::Button('导出',array('class'=>'btn btn-success','id'=>'export_btn', 'act'=>'export_btn','func'=>'export_btn','style'=>'margin-left:30px')); ?>

        </div>

    </div>

    <?php $this->endWidget(); ?>
</div>

<div>
<?php
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'driver-order-grid',
    'itemsCssClass'=>'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'dataProvider'=>$dataProvider,
    'columns'=>array (
        array(
            'name'=>'城市',
            'value'=>'RCityList::model()->getCityByID($data->city_id,"city_name")',
        ),
        'driver_name',
        'driver_phone',
        'order_number',
        'order_time',

        array(
            'name'=>'状态',
            'value' => 'DriverOrder::$status_dict[$data->order_status]',
        ),

        array(
            'name'=>'复制订单',
            'type'=>'raw',
            'value' => 'CHtml::link("复制订单", "javascript:void(0);", array (
						    "onclick"=>"{copy($data->id);}"));',
            'visible'=>'AdminActions::model()->havepermission("driverOrder", "copy")',
        ),

        array(
            'header'=>'订单信息',
            'class' => 'CButtonColumn',
            'template' => '{detail} ',
            'buttons'=>array(
                'detail' => array(
                    'label'=>'查看详细信息',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("driverOrder/detail",array("id"=>$data->id))',
                    'options' => array('target'=>'_blank' ),
                    'visible'=>'AdminActions::model()->havepermission("driverOrder", "detail")'),

            ),
        ),
        array(
            'name'=>'催付款',
            'type'=>'raw',
            'value' => 'CHtml::link("催付款", "javascript:void(0);", array (
						    "onclick"=>"{sms_pay($data->id);}"));',
            'visible'=>'AdminActions::model()->havepermission("driverOrder", "sms_pay")',
        ),
        array(
            'name'=>'签收',
            'type'=>'raw',
            'value' => '$data->order_status == 6 ? "已签收" : (CHtml::link("签收", "javascript:void(0);", array (
						    "onclick"=>"{sign_for($data->id);}")));',
            'visible'=>'AdminActions::model()->havepermission("driverOrder", "copy")',
        ),
    )));

?>
</div>
<?php

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '',
        'autoOpen' => false,
        'width' => '600',
        'height' => '500',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="copy_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<script>
    function copy(id) {
        url = '<?php echo Yii::app()->createUrl('/driverOrder/copy');?>&id=' + id;
        $("#copy_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }

    function sign_for(id) {
        url = '<?php echo Yii::app()->createUrl('/driverOrder/signFor');?>&id=' + id;
        $("#copy_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }


    function sms_pay(id) {
        url = '<?php echo Yii::app()->createUrl('/driverOrder/smsPay');?>&id=' + id;
        $("#copy_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }

    //导出
    $('#export_btn').click(function () {
        //获取搜索参数
        var order_status = $('#order_status').val();
        var city_id = $('#city_id').val();
        var order_start = $('#order_start').val();
        var order_end = $('#order_end').val();
        var name = $('#name').val();
        var order_number = $('#order_number').val();
        var driver_phone = $('#driver_phone').val();
        var driver_id = $('#driver_id').val();


        if(order_status == -1 && city_id == 0 && !order_start && !order_end
            && !name && !order_number && !driver_phone && !driver_id){
            alert('请选择查询条件');
            return false;
        }
        var url = '<?php echo Yii::app()->createUrl('driverOrder/export');?>'+'&order_status='+order_status
            +'&order_status='+order_status
            +'&city_id='+city_id
            +'&order_start='+order_start
            +'&order_end='+order_end
            +'&order_number='+order_number
            +'&driver_phone='+driver_phone
            +'&driver_id='+driver_id
            +'&name='+name;
        window.open(url);
    });

</script>

