<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs = array(
    'Vips' => array('index'),
    'Manage',
);


$this->pageTitle = 'VIP卡 用户一览';
?>

<h1>VIP卡 用户管理</h1>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('vip-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'post',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('选择城市', 'city_id'); ?>
            <?php echo $form->dropDownList($model, 'city_id', Common::getOpenCity()); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('选择状态', 'status'); ?>
            <?php

            $status = Dict::items('vip_status');
            $status[0] = '全部';
            ksort($status);
            echo $form->dropDownList($model, 'status', $status); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('选择类型', 'type'); ?>
            <?php

            $type = Dict::items('vip_type');
            $type['-1'] = '全部';
            ksort($type);
            echo $form->dropDownList($model, 'type', $type); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'commercial_invoice'); ?>
            <?php echo $form->textField($model, 'commercial_invoice'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, 'id'); ?>
            <?php echo $form->textField($model, 'id'); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'phone'); ?>
            <?php echo $form->textField($model, 'phone'); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'name'); ?>
            <?php echo $form->textField($model, 'name'); ?>
        </div>
        <div class="span3">
            <label for="Vip_balance">余额小于</label>
            <?php echo $form->textField($model, 'balance'); ?>
        </div>
    </div>
    <div class='row-fluid'>
        <div class='span3'>
            <?php echo $form->label($model, 'company') ?>
            <?php echo $form->textField($model, 'company') ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <?php
            echo CHtml::submitButton('搜索', array('class' => 'btn btn-success'));
            if (AdminActions::model()->havepermission('vip', 'create')) {
                echo CHtml::link('新开VIP', 'javaScript:void(0);', array('onClick' => 'vipDialogdivInit(\'' . Yii::app()->createUrl("vip/create") . '\')', 'class' => 'btn', 'style' => 'margin-left:10px;'));
            }

            if (AdminActions::model()->havepermission('vip', 'admin_email')) {
                echo CHtml::link('账单邮件', array("vip/admin_email"), array('class' => 'btn', 'style' => 'margin-left:10px;'));
            }

            echo CHtml::link('月消费趋势', array('vip/vipCostMonthReport'), array('class' => 'btn btn-info', 'style' => 'margin-left:10px;', 'target' => '_blank'));
            echo CHtml::link('消费跟进管理', array('vip/costRecordAdmin'), array('class' => 'btn btn-info', 'style' => 'margin-left:10px;', 'target' => '_blank'));

            ?>

        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
<div class="row-fluid" id="item_count_string" name="item_count_string"><h3><?php echo $vipInfo; ?></h3></div>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => 'VIP充值',
        'autoOpen' => false,
        'width' => '600',
        'height' => '500',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#mydialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');


$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'vipdialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => 'VIP信息',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#vipdialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="vipdialogdiv"></div>';
echo '<iframe id="cru-frame-vip" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');


?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'chargedialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => 'VIP充值',
        'autoOpen' => false,
        'width' => '800',
        'height' => '600',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#chargedialog").dialog("close"); $(".search-form form").submit(); }'
        ),
    ),
));
echo '<div id="chargedialogdiv"></div>';
echo '<iframe id="charge-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'vip-grid',
    'dataProvider' => $model->search(),
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table  table-condensed',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'rowCssClassExpression' => array($this, 'amountStatus'),
    'columns' => array(
        array(
            'name' => 'id',
            'type' => 'raw',
            'value' => 'CHtml::link($data->id, array("vip/view", "id"=>$data->id))'
        ),
        'name',
        'company',
        //'phone',
        array(
            'name' => '手机号',
            'value' => array($this, 'showPhoneNumber'),
        ),
        array(
            'name' => '类型',
            'value' => 'Dict::item("vip_type",$data->type)'
        ),
        array(
            'name' => '城市',
            'value' => 'Yii::app()->controller->getVipCity($data->city_id)'
        ),
        array(
            'name' => '开卡时间',
            'value' => 'date("Y-m-d", $data->created)'
        ),
        'balance',
        'credit',
        array(
            'name' => 'status',
            'value' => 'Yii::app()->controller->getStatus($data->status)'
        ),
        array(
            'name' => '操作',
            'value' => array($this, 'showButton')
        ),
    ),
));
?>

<script type="text/javascript">
    function getItemCountString(cityId) {
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/vip/getvipinfo');?>',
            'data': 'id=' + cityId,
            'type': 'get',
            'success': function (data) {
                $('#item_count_string').html(data);
            },
            'cache': false
        });
        return false;
    }

    function DialogdivInit(href) {
        $("#cru-frame").attr("src", href);
        $("#mydialog").dialog("open");
        return false;
    }


    function vipDialogdivInit(href) {
        $("#cru-frame-vip").attr("src", href);
        $("#vipdialog").dialog("open");
        return false;
    }

    jQuery('#vip-grid a.delete').live('click', function () {

        if (!confirm('确定要禁用该VIP卡吗?')) return false;
        var th = this;
        var afterDelete = function (th, bool, data) {
        }//if(bool==false){alert("出错啦");}if(bool==true){alert("禁用成功");}};
        $.fn.yiiGridView.update('vip-grid', {
            type: 'POST',
            url: $(this).attr('href'),
            success: function (data) {
                if (data > 0) {
                    $(".search-form form").submit();
                }
            },
            error: function (XHR) {
                return afterDelete(th, false, XHR);
            }
        });
        return false;
    });


    jQuery('#vip-grid a.charge').live('click', function () {
        var url = $(this).attr('href');
        $("#charge-frame").attr("src", url);
        jQuery("#chargedialog").dialog("open");
        return false;
    });

</script>

