<?php
/**
 * Created by PhpStorm.
 * User: guanzhisong
 * Date: 2015/4/8
 * Time: 17:02
 */

/* @var $this CityConfigController */
/* @var $model CityConfig */
$this->setPageTitle('问卷活动管理');
?>
<div class="search-form">
    <?php $this->renderPartial('InvestSearchForm', array('model' => $model,)); ?>
    <a href="<?php echo Yii::app()->createUrl('/invest/investInfo'); ?>&cmd=new" target="_blank">新增问卷</a>
</div>

<?php
$this->beginWidget('zii.widgets.grid.CGridView', array(
    'id' => 'invest-list-grid',
    'cssFile' => SP_URL_CSS . 'table.css',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
//    'filter'=>$model,
    'columns' => array(
        'id',
        'title',
        'des',
        array('name' => 'status', 'value' => '$data->status==0?"停止":"激活"'),
        'coupon',
        'coupon_code',
        'send_num',
        'reply_num',
        'start_time',
        'end_time',
        'operator',
        array(
            'name' => '操作',
            'headerHtmlOptions' => array(
                //'width'=>'80px',
                'nowrap' => 'nowrap'),
            'type' => 'raw',
            'value' => array($this, 'operatorMenu'),
        ),
    ),
));
$this->endWidget();

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'investDialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '问卷',
        'autoOpen' => false,
        'width' => '700',
        'height' => '500',
        'modal' => true,
        'buttons' => array(
            'Close' => 'js:function(){$("#investDialog").dialog("close");}'
        ),
    ),
));

echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_invest_info_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget("zii.widgets.jui.CJuiDialog");
?>

<script type="text/javascript">
    function updateInvestStatus(investId, status) {
        var postUrl = '<?php  echo Yii::app()->createUrl('/invest/investStatusChange');?>';
        $.ajax({
            type: 'POST',
            url: postUrl,
            data: {
                'investId': investId,
                'status': status
            },
            success: function (data) {
                if (data == 0) {
                    history.go(0);
                }
            }
        })
    }

    function copyInvest(investId) {
        var postUrl = '<?php  echo Yii::app()->createUrl('/invest/investCopy');?>';
        $.ajax({
            type: 'POST',
            url: postUrl,
            data: {
                'investId': investId
            },
            success: function (data) {
                if (data == 0) {
                    history.go(0);
                }
            }
        })
    }

    function getInvest($investId) {
        var postUrl = '<?php  echo Yii::app()->createUrl('/invest/getInvest');?>';

        $.ajax({
            type: 'POST',
            url: postUrl,
            data: {
                'investId': investId
            },
            success: function (data) {
                alert(data);
            }
        })
    }

</script>
