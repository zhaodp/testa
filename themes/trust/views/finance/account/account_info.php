<?php
/**
 * 订单详情
 * User: mtx
 * Date: 13-12-4
 * Time: 下午5:37
 * auther mengtianxue
 */
?>

<h1>信息费汇总表</h1>
<ul class="nav nav-tabs">
    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/month_list',
            array('date_time' => 2013
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2013年汇总</a></li>
    </li>
    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/month_list',
            array('date_time' => 2014
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2014年汇总</a></li>
    </li>
    <li>
        <?php
        $month_url = Yii::app()->createUrl('/finance/daily_list',
            array('date_time' => date('Y-m', strtotime($params['created'])),
            ));
        ?>
        <a href="<?php echo $month_url; ?>"><?php echo date('m', strtotime($params['created']));?>月汇总</a></li>
    <li>
        <?php
        $url = Yii::app()->createUrl('/finance/daily_account',
            array('ReportFsAccountRp[city_id]' => $params['city_id'],
                'ReportFsAccountRp[account_date]' => $params['created'],
            ));
        ?>
        <a href="<?php echo $url;?>">日汇总</a>
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
            <input type="hidden" id="CarEmployeeAccount_month" name="CarEmployeeAccount[month]"
                   value="<?php echo $params['month']; ?>">

            <div class="span3">
                <?php echo $form->label($model, 'city_id'); ?>
                <?php echo $form->dropDownList($model, 'city_id', Dict::items('city'), array('value' => $params['city_id'])); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'created'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'CarEmployeeAccount[created]',
                    'model' => $model, //Model object
                    'value' => $params['created'],
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
        $channel = Dict::items('cast_channel');
        unset($channel['-1']);
        unset($channel['0']);
        unset($channel['99']);
        foreach ($channel as $k => $v) {
            $url = Yii::app()->createUrl('/finance/account_info',
                array('CarEmployeeAccount[channel]' => $k,
                    'CarEmployeeAccount[month]' => $params['month'],
                    'CarEmployeeAccount[city_id]' => $params['city_id'],
                    'CarEmployeeAccount[created]' => $params['created'],
                    'CarEmployeeAccount[operator]' => $params['operator'],
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
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'knowledge-grid',
            'dataProvider' => $data,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                    'name' => 'created',
                    'headerHtmlOptions' => array(
                        'style' => 'width:240px',
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'date("Y-m-d H:i:s",$data->created)'
                ),
                'user',
                'cast',
                'comment',
                'operator'
            ),
        )); ?>
    </div>
</div>



