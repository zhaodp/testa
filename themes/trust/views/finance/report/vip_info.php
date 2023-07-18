<?php
/**
 * 订单详情
 * User: mtx
 * Date: 13-12-4
 * Time: 下午5:37
 * auther mengtianxue
 */
?>

<h1>VIP汇总表</h1>
<ul class="nav nav-tabs">
    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/vip_month_list',
            array('date_time' => 2013
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2013年汇总</a></li>
    </li>

    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/vip_month_list',
            array('date_time' => 2014
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2014年汇总</a></li>
    </li>
    <li>
        <?php
        $month_url = Yii::app()->createUrl('/finance/vip_daily_list',
            array('date_time' => date('Y-m', strtotime($params['daily_date'])),
            ));
        ?>
        <a href="<?php echo $month_url; ?>"><?php echo date('m', strtotime($params['daily_date'])); ?>月汇总</a></li>
    <li>
        <?php
        $url = Yii::app()->createUrl('/finance/daily_vip',
            array('ReportFsVipRp[city_id]' => $params['city_id'],
                'ReportFsVipRp[account_date]' => $params['daily_date'],
            ));
        ?>
        <a href="<?php echo $url; ?>">Vip日汇总</a>
    </li>

    <li class="active"><a href="javascript:void(0);">信息费详情</a></li>
</ul>
<!-- 搜索条件 -->
<div class="row-fluid">
    <div class="well span12">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>
        <div class="row span12">
            <?php echo $form->hiddenField($model, 'channel', array('value' => $params['channel'])); ?>

            <div class="span3">
                <?php echo $form->label($model, 'city_id'); ?>
                <?php echo $form->dropDownList($model, 'city_id', Dict::items('city'), array('value' => $params['city_id'])); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'daily_date'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'ReportFsVipTradeInfo[daily_date]',
                    'model' => $model, //Model object
                    'value' => $params['daily_date'],
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                ));
                ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, 'operator'); ?>
                <?php echo $form->textField($model, 'operator', array('value' => $params['operator'])); ?>
            </div>
            <div class="row span2">
                <?php echo $form->label($model, '&nbsp'); ?>
                <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>

<!-- 当前总额统计 -->
<div class="row-fluid" style="padding-bottom: 10px;">
    <h4>当前总额为：<?php echo number_format($accountCount, 2); ?>元</h4>
</div>


<div class="tabbable tabs-left" style="border-top:1px solid rgb(221, 221, 221); padding-top:10px;">
    <!-- Only required for left tabs -->
    <ul class="nav nav-tabs" style="margin-bottom: 0px;">
        <?php
        $channel = Dict::items('vip_channel');
        unset($channel['-1']);
        unset($channel['99']);
        foreach ($channel as $k => $v) {
            $url = Yii::app()->createUrl('/finance/vip_info',
                array('ReportFsVipTradeInfo[channel]' => $k,
                    'ReportFsVipTradeInfo[city_id]' => $params['city_id'],
                    'ReportFsVipTradeInfo[daily_date]' => $params['daily_date'],
                    'ReportFsVipTradeInfo[operator]' => $params['operator'],
                ));
            if ($k == $params['channel']) {
                echo "<li class='active'><a href='" . $url . "'>$v</a></li>";
            } else {
                echo "<li><a href='" . $url . "'>$v</a></li>";
            }
        }
        ?>
    </ul>

    <div class="tab-content">
        <?php
        switch ($params['channel']) {
            case 0:
            case 1:
            case 2:
            case 3:
            case 4:
                $this->widget('zii.widgets.grid.CGridView', array(
                    'id' => 'knowledge-grid',
                    'dataProvider' => $data,
                    'itemsCssClass' => 'table table-striped',
                    'columns' => array(
                        array(
                            'name' => 'daily_date',
                            'headerHtmlOptions' => array(
                                'style' => 'width:240px',
                                'nowrap' => 'nowrap'
                            ),
                            'type' => 'raw',
                            'value' => '$data->daily_date'
                        ),
                        'vipcard',
                        array(
                            'name' => '充值金额',
                            'headerHtmlOptions' => array(
                                'style' => 'width:240px',
                                'nowrap' => 'nowrap'
                            ),
                            'type' => 'raw',
                            'value' => '$data->amount'
                        )
                    ),
                ));
                break;
            case 5:
                $this->widget('zii.widgets.grid.CGridView', array(
                    'id' => 'knowledge-grid',
                    'dataProvider' => $data,
                    'itemsCssClass' => 'table table-striped',
                    'columns' => array(
                        array(
                            'name' => 'daily_date',
                            'headerHtmlOptions' => array(
                                'style' => 'width:240px',
                                'nowrap' => 'nowrap'
                            ),
                            'type' => 'raw',
                            'value' => '$data->daily_date'
                        ),
                        'order_id',
                        'vipcard',
                        'phone',
                        'cast'
                    ),
                ));
                break;
            case 6:
                $this->widget('zii.widgets.grid.CGridView', array(
                    'id' => 'knowledge-grid',
                    'dataProvider' => $data,
                    'itemsCssClass' => 'table table-striped',
                    'columns' => array(
                        array(
                            'name' => 'daily_date',
                            'headerHtmlOptions' => array(
                                'style' => 'width:240px',
                                'nowrap' => 'nowrap'
                            ),
                            'type' => 'raw',
                            'value' => '$data->daily_date'
                        ),
                        'order_id',
                        'vipcard',
                        'phone',
                        'insurance'
                    ),
                ));
                break;
            case 7:
                $this->widget('zii.widgets.grid.CGridView', array(
                    'id' => 'knowledge-grid',
                    'dataProvider' => $data,
                    'itemsCssClass' => 'table table-striped',
                    'columns' => array(
                        array(
                            'name' => 'daily_date',
                            'headerHtmlOptions' => array(
                                'style' => 'width:240px',
                                'nowrap' => 'nowrap'
                            ),
                            'type' => 'raw',
                            'value' => '$data->daily_date'
                        ),
                        'order_id',
                        'vipcard',
                        'phone',
                        'balance'
                    ),
                ));
                break;
            case 8:
                $this->widget('zii.widgets.grid.CGridView', array(
                    'id' => 'knowledge-grid',
                    'dataProvider' => $data,
                    'itemsCssClass' => 'table table-striped',
                    'columns' => array(
                        array(
                            'name' => 'daily_date',
                            'headerHtmlOptions' => array(
                                'style' => 'width:240px',
                                'nowrap' => 'nowrap'
                            ),
                            'type' => 'raw',
                            'value' => '$data->daily_date'
                        ),
                        'order_id',
                        'vipcard',
                        'phone',
                        'Invoice_money'
                    ),
                ));
                break;

        }?>
    </div>
</div>



