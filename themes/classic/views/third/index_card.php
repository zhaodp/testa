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

$order_status = isset($_REQUEST['order_status'])? $_REQUEST['order_status'] : 2;
$export_times = isset($_REQUEST['export_times'])? $_REQUEST['export_times'] : -1;
$name = isset($_REQUEST['name'])? $_REQUEST['name'] : '';
$driver_id = isset($_REQUEST['driver_id'])? $_REQUEST['driver_id'] : '';
$order_number = isset($_REQUEST['order_number'])? $_REQUEST['order_number'] : '';
//默认昨天到今天
$yesterday = $today = date("Y-m-d",strtotime("-1 day"));
$order_start = isset($_REQUEST['order_start'])? $_REQUEST['order_start'] : $yesterday;
$order_end = isset($_REQUEST['order_end'])? $_REQUEST['order_end'] : $today;

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
        <td><h1>工卡管理 <?php echo CHtml::Button('导入已制卡订单',array('class'=>'btn btn-success','id'=>'import_card', 'func'=>'import_card','style'=>'margin-left:30px')); ?></h1>
    </td>
    </tr>
</table>
<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'driver-admin-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'errorMessageCssClass'=>'alert alert-error',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post'
)); ?>
<div class="row-fluid">
    <div class="span3">
        <?php
        $status = DriverOrder::$status_array;
        $status[-1]='全部';
        unset($status[DriverOrder::STATUS_UN_PAY]);
        unset($status[DriverOrder::STATUS_PAYED]);
        unset($status[DriverOrder::STATUS_ENTRY]);
        unset($status[DriverOrder::STATUS_DELIVER]);
        unset($status[DriverOrder::STATUS_SIGNED]);
        unset($status[DriverOrder::STATUS_OFFLINE]);
        unset($status[DriverOrder::STATUS_EXCEPTION]);

        ksort($status);

        echo CHtml::label('订单状态','order_status');
        echo CHtml::dropDownList('order_status',
            $order_status, //默认选中
            $status,
            array()
        );
        ?>
    </div>

    <div class="span3">
        <?php
        $times = array(
            '-1'=>'全部',
            '0'=>0,
            '1'=>'1',
            '2'=>'2',
            '3'=>'3',
            '4'=>'4',
            '5'=>'>=5',

        );
        echo CHtml::label('导出次数','export_times');
        echo CHtml::dropDownList('export_times',
            $export_times,
            $times,
            array()
        );
        ?>
    </div>


    <div class="span3">
        <label for="mobile">下单日期</label>
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker',array(
            'attribute'=>'visit_time',
            'language'=>'zh_cn',
            'name'=>'order_start',
            'id'=>'order_start',
            'value'=>$order_start,
            'options'=>array(
                'showAnim'=>'fold',
                'showOn'=>'both',
                //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                'buttonImageOnly'=>true,
                //'minDate'=>'new Date()',
                'dateFormat'=>'yy-mm-dd',
                'changeYear'=>true,
                'changeMonth'=> true,
            ),
            'htmlOptions'=>array(
                'style'=>'width:85px',
            ),
        ));
        ?>
        至
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker',array(
            'attribute'=>'visit_time',
            'language'=>'zh_cn',
            'name'=>'order_end',
            'id'=>'order_end',
            'value'=>$order_end,
            'options'=>array(
                'showAnim'=>'fold',
                'showOn'=>'both',
                //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                'buttonImageOnly'=>true,
                //'minDate'=>'new Date()',
                'dateFormat'=>'yy-mm-dd',
                'changeYear'=>true,
                'changeMonth'=> true,
            ),
            'htmlOptions'=>array(
                'style'=>'width:85px',
            ),
        ));
        ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span3">
        <label for="name">姓名</label>
        <input type="text" id="name" name="name" value="<?php echo $name;?>" />
    </div>
    <div class="span3">
        <label for="order_number">订单编号</label>
        <input type="text" id="order_number"  name="order_number" value="<?php echo $order_number;?>" />
    </div>
    <div class="span3">
        <label for="driver_id">工号</label>
        <input type="text" id="driver_id"  name="driver_id" value="<?php echo $driver_id;?>" />
    </div>

</div>

<div class="row-fluid">
    <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
    <span><?php echo CHtml::Button('导出',array('class'=>'btn btn-success','id'=>'inform_btn', 'act'=>'export_header','func'=>'export_header','style'=>'margin-left:30px')); ?>
    </span>
    <span><?php echo CHtml::Button('导出记录',array('class'=>'btn btn-success','id'=>'inform_btn','style'=>'margin-left:30px','onclick'=>'location.href=\''. Yii::app()->createUrl('third/ExportHistory'). '\';')); ?>
    </span>

</div>

</div>
<?php $this->endWidget(); ?>



<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'driver-card-grid',
    'cssFile' => SP_URL_CSS . 'table.css',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'selectableRows'=>2,
    'columns'=>array(

        array(
            'name'=>'姓名',
            'value'=>'$data->driver_name',
        ),
        array (
            'name' => '工号',
            'value' => '$data->driver_id',
        ),
        array(
            'name'=>'照片',
            'headerHtmlOptions'=>array(
                'height'=>'50px',
                'nowrap'=>'nowrap'),
            'type'=>'raw',
            'value'=>'CHtml::image(Driver::model()->getHeadUrl($data->driver_id),"司机头像",array("width"=>60, "height"=>60));'
        ),
        array(
            'name'=>'二维码',
            'headerHtmlOptions'=>array(
                'height'=>'50px',
                'nowrap'=>'nowrap'),
            'type'=>'raw',
            'value'=>'CHtml::image(Driver::model()->getCodeUrl($data->driver_id),"司机二维码",array("width"=>60, "height"=>60));'
        ),
        array(
            'name'=>'订单编号',
            'value'=>'$data->order_number',
        ),
        array(
            'name'=>'下单时间',
            'type'=>'raw',
            'value' =>'$data->order_time',
        ),
        array(
            'name'=>'订单状态',
            'type'=>'raw',
            'value'=>array($this,'getStatusName')
        ),
        array(
            'name'=>'导出次数',
            'type'=>'raw',
            'value' =>'$data->export_times',
        ),
    ),
));


?>
<script>

window.onload = function() {
    jQuery('.ui-datepicker-trigger').remove();
}

function getItemCountString(cityId){
    $.ajax({
        'url':'<?php echo Yii::app()->createUrl('/recruitment/getitemcount');?>',
        'data':'id='+cityId,
        'type':'get',
        'success':function(data){
            $('#item_count_string').html(data);
        },
        'cache':false
    });
    return false;
}

$(function(){

    //通知路考
    $("[func='send_msg']").click(function(){
        var id = jQuery(this).attr('act');
        if (id == 'inform_btn') {
            $(".ui-dialog-title").html("面试通知");
            var action = '<?php echo DriverRecruitment::SMS_TYPE_EXAM;?>';
        } else if (id == 'send_msg_btn') {
            $(".ui-dialog-title").html("发送短信");
            var action = '<?php echo DriverRecruitment::SMS_TYPE_COMMON;?>';
        }
        id_length = $("input[name='recruitment_id[]']:checked").length;
        if(id_length<=0){
            if (id == 'inform_btn') {
                alert("请选择需要通知路考的司机！");
            } else if (id == 'send_msg_btn') {
                alert("请选择需要发送短信的司机！");
            }
            return false;
        }
        id_str = '';
        for(i=0;i<id_length;i++)
        {
            id_str += $("input[name='recruitment_id[]']:checked").eq(i).val()+',';
        }
        url = '<?php echo Yii::app()->createUrl('/recruitment/informexam');?>&ids_str='+id_str+'&batch='+$("#batch").val()+'&action='+action;
        $("#view_informexam_frame").attr("src",url);
        $("#mydialog").dialog("open");
    });

    /**
     * 批量导入已制卡
     */
    $("[func='import_card']").click(function () {
        var href = "<?php echo Yii::app()->createUrl('/third/importLogRecord'); ?>"+'&type=3';
        $("#import_delivery_frame").attr("src",href);
        $("#import_delivery_dialog").dialog("open");
        return false;
    })

    //导出司机头像
    $("[func='export_header']").click(function(){
        var status = $('#order_status').val();
        var export_times = $('#export_times').val();
        var order_start = $('#order_start').val();
        var order_end = $('#order_end').val();
        var name = $('#name').val();
        var order_number = $('#order_number').val();
        var driver_id = $('#driver_id').val();

        if(status == -1 && export_times == -1 && !order_start && !order_end && !name && !order_number && !driver_id){
            alert('请选择查询条件');
            return false;
        }
        var url = '<?php echo Yii::app()->createUrl('third/Export');?>';
        var params = {
            "con[order_status]":status,
            "con[export_times]": export_times,
            "con[order_start]": order_start,
            "con[order_end]": order_end,
            "con[name]": name,
            "con[order_number]": order_number,
            "con[driver_id]": driver_id
        };
        //var p = $.param(params);
        //url += '&'+p;
        //alert(url);return false;

        $.ajax({
            url: url,
            type: "GET",
            data: params,
            success: function(data){
                alert(data.msg);
            },
            dataType: "json"
        });


    });
});
</script>