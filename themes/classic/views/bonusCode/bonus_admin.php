<?php
$this->pageTitle = '优惠券绑定列表';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
");

?>

<h1><?php echo $this->pageTitle; ?></h1>

<?php echo CHtml::link('高级搜索', '#', array('class' => 'search-button btn')); ?>
<div class="search-form" style="display:block">
    <div class="well span12">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
            'id' => 'CustomerBonusSearch'
        ));

        if (isset($_GET['CustomerBonus'])) {
            $param = $_GET['CustomerBonus'];
        }

        $created = isset($param['created']) ? $param['created'] : '';
        $used = isset($param['used']) ? $param['used'] : '';

        $bind_type = 0;
        if (isset($_GET['bind_type']) && $_GET['bind_type'] > 0)
            $bind_type = $_GET['bind_type'];

        ?>

        <div class="row-fluid">
            <div class="span3">
                <?php echo $form->label($model, 'bonus_type_id'); ?>
                <?php
                $bonusTypeList = CustomerBonus::model()->getBonusCodeList();
                $bonusTypeList[0] = '全部';
                ksort($bonusTypeList);
                echo $form->dropDownList($model, 'bonus_type_id', $bonusTypeList);
                ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'bonus_sn'); ?>
                <?php echo $form->textField($model, 'bonus_sn', array('size' => 50, 'maxlength' => 50)); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'order_id'); ?>
                <?php echo $form->textField($model, 'order_id', array('size' => 50, 'maxlength' => 50)); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'customer_phone'); ?>
                <?php echo $form->textField($model, 'customer_phone', array('size' => 50, 'maxlength' => 50)); ?>
            </div>

        </div>
        <div class="row-fluid">

            <div class="span3">
                <?php echo CHtml::label('绑定类型', 'bind_type'); ?>
                <?php
                $bindType[0] = '全部';
                $bindType[1] = '已使用';
                $bindType[2] = '未使用';
                echo CHtml::dropDownList('bind_type', $bind_type, $bindType);
                ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'created'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'CustomerBonus[created]',
//					'model'=>$model,  //Model object
                    'value' => $created,
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                ));
                ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'used'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'CustomerBonus[used]',
//					'model'=>$model,  //Model object
                    'value' => $used,
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                ));
                ?>
            </div>

        </div>

        <div class="row-fluid">
            <div class="span4">
                <?php echo CHtml::submitButton('搜索', array('class' => "btn span4")); ?>
            </div>
        </div>

        <?php $this->endWidget(); ?>

    </div>
    <!-- search-form -->

</div><!-- search-form -->
<div class="row-fluid">
    <?php
    echo $bonus_type_info;
    ?>
</div>

<?php

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '订单信息',
        'autoOpen' => false,
        'width' => '750',
        'height' => '450',
        'modal' => true,
        'buttons' => array(
            'Close' => 'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-type-bind-list-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-condensed',
    'columns' => array(
        array(
            'name' => '订单流水号',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '($data->order_id == 0) ? "未使用" : CHtml::link($data->order_id, array("order/view","id"=>$data->order_id), array("onClick" =>"//orderDialogdivInit(\'$data->order_id\')","target"=>"_blank"))'
        ),

        array(
            'name' => '优惠券名称',
            'headerHtmlOptions' => array(
                'width' => '120px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'CustomerBonus::model()->getBonusCodeList($data->bonus_type_id)'
        ),

        array(
            'name' => '客户电话',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'CHtml::link(Common::parseCustomerPhone($data->customer_phone), array("bonusCode/bonus_admin", "CustomerBonus[customer_phone]"=>$data->customer_phone))'
        ),

        array(
            'name' => '优惠码',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            //'value' => '$data->bonus_sn',
            //'value' => 'substr_replace($data->bonus_sn, "**", -2, 2)',
            'value' => 'Common::parseBonus($data->bonus_sn)'
        ),

        array(
            'name' => '用户限制',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("user_limited", BonusCode::model()->getFieldValue("$data->bonus_type_id", "user_limited"))'
        ),

        array(
            'name' => '使用限制',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("channel_limited", BonusCode::model()->getFieldValue("$data->bonus_type_id", "channel_limited"))'
        ),

        array(
            'name' => '金额',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->balance'
        ),


        array(
            'name' => '绑定时间',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data->created ? date("Y-m-d H:i:s", $data->created) : ""'
        ),
        array(
            'name' => '消费时间',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data->used ? date("Y-m-d H:i:s", $data->used) : ""'
        ),
	array(
            'name' => '使用截止时间',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data->end_date'
        ),


    ),
)); ?>

<script>
    function orderDialogdivInit(orderId) {
        $('#dialogdiv').html("<img src='<?php echo SP_URL_IMG;?>loading.gif' />");
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/order/view');?>',
            'data': 'id=' + orderId,
            'type': 'get',
            'success': function (data) {
                $('#dialogdiv').html(data);
            },
            'cache': false
        });
        jQuery("#mydialog").dialog("open");
        return false;
    }
</script>
