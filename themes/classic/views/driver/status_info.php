<?php
    $this->pageTitle = '司机状态信息 - ' . $this->pageTitle;
?>

<h1>司机状态信息</h1>

<?php
$items = array();
if (!empty($data)) {
    foreach ($data as $driver) {
        $driver_status = $driver['status'];
        $driver_location = $driver['location'];
        $driver_position = $driver_location->position;
        $driver_info = $driver_location->info;
        $item['driver_id'] = $driver['driver_id'];
        $item['city_id'] = $driver_location->city_id ? trim($driver_location->city_id) : '';
        $item['status'] = $driver_status['success'] ? $driver_status['msg']['redis']['status'] : $driver_status['msg'];
        $item['street'] = isset($driver_position['street']) ? $driver_position['street'] : '';
        $item['phone'] = $driver_location->phone ? trim($driver_location->phone) : '';
        $item['ext_phone'] = isset($driver_info['ext_phone']) ? $driver_info['ext_phone'] : '';
        $item['name'] = isset($driver_info['name']) ? $driver_info['name'] : '';
        $items[] = $item;
    }
}
$dataProvider = new CArrayDataProvider($items, array(
    'keyField' => 'driver_id',
    'sort' => array(
        'attributes' => array(
            'driver_id', 'status'
        ),
    ),
    'pagination' => FALSE,
));

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'vip-grid',
    'dataProvider' => $dataProvider,
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table  table-striped',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'columns' => array(
        array(
            'header' => '头像',
            'type' => 'raw',
            'value' => 'CHtml::image(Driver::getPictureUrl($data["driver_id"],$data["city_id"], Driver::PICTURE_SMALL, true), $data["name"], array("width"=>120, "height"=>144, "driver_img"=>$data["driver_id"]));',
        ),
        array(
            'name' => 'driver_id',
            'header' => '司机工号',
            'type' => 'raw',
            'value' => 'CHtml::link("$data[driver_id]", array("driver/archives", "id"=>$data["driver_id"]), array("target"=>"_blank")) . "<br>" . $data["name"]',
        ),
        array(
            'name' => 'status',
            'header' => '在线状态',
            'value' => '$data["status"]',
        ),
        array(
            'name' => 'phone',
            'header' => '司机手机号',
            'type' => 'raw',
            'value' => '"工作：" . Common::parseDriverPhone($data["phone"]) . "<br>备用：" . Common::parseDriverPhone($data["ext_phone"])',
        ),
        array(
            'name' => 'street',
            'header' => '当前位置',
            'type' => 'raw',
            'value' => '
                CHtml::encode($data["street"]) . "<br>" .
                CHtml::link("最近两小时轨迹",
                    array(
                        "driver/orderposition",
                        "driver_id"=>$data["driver_id"],
                        "startDate"=>date("Y-m-d H:i:s", time()-7200),
                        "endDate"=>date("Y-m-d H:i:s", time())
                    ),
                    array("target"=>"_blank", "class"=>"btn")
                )',
        ),
//        array(
//            'name' => 'phone',
//            'header' => 'phone',
//            'value' => 'CHtml::encode($data["phone"])',
//        ),
        array(
            'header' => '操作',
            'type' => 'raw',
            'value' => 'CHtml::link("发送短信", "javascript:;", array("class"=>"btn","onclick"=>"sms_to(\"$data[phone]\", \"$data[driver_id]\")"))',
        ),
    ),
));

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'sms_to_driver',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '发送短信',
        'autoOpen' => false,
        'width' => '800',
        'height' => '500',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#sms_to_driver").dialog("close"); $(".search-form form").submit(); }'
        ),
    ),
));

$this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'post',
    'htmlOptions' => array('class' => 'form span5'),
));
?>
<div class="row" style="width:100%;height:auto;">
    <div class="span3" >
        <label><?php echo CHtml::label('司机工号', 'driver'); ?></label>
        <?php echo CHtml::textField('driver', '', array('size' => 20, 'maxlength' => 20, 'disabled' => 'disabled')); ?>
    </div>
    <div class="span3">
        <label><?php echo CHtml::label('司机手机号', 'phone'); ?></label>
        <?php echo CHtml::textField('phone', '', array('size' => 20, 'maxlength' => 20, 'readonly' => 'readonly')); ?>
    </div>
    <div class="span5">
        <label><?php echo CHtml::label('短信内容', 'sms_content'); ?></label>
        <?php echo CHtml::textArea('sms_content', '', array('class' => 'span5', 'style' => 'height:155px;')); ?>
    </div>
    <div class="span3">
        <?php
        echo CHtml::ajaxSubmitButton(
            '发送',
            Yii::app()->createUrl($this->route),
            array('dataType' => 'json', 'success' => 'function(data){alert(data.msg);if(!data.error){$("#sms_to_driver").dialog("close");}}'),
            array('class' => 'btn', 'id' => 'sms_form', 'onclick' => '$(this).hide();setTimeout(function(){$("#sms_form").show()},3000);if($("#phone").val().length != 11){alert("手机号码格式不正确");return false;}')
        );
        ?>
    </div>
</div>
<?php
$this->endWidget();
$this->endWidget();
?>

<script>
    //发送短信
    function sms_to(phone, driver) {
        var $phone = phone.replace(' ', '');
        $("#sms_to_driver").dialog("open");
        $('#phone').val($phone);
        $('#driver').val(driver);
    }
</script>