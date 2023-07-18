<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-10-24
 * Time: 下午12:52
 * To change this template use File | Settings | File Templates.
 */

    $this->widget('zii.widgets.grid.CGridView', array (
        'id'=>'order-grid',
        'dataProvider'=>$data,
        'cssFile'=>SP_URL_CSS . 'table.css',
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'itemsCssClass'=>'table table-condensed',
        'rowCssClassExpression'=>array($this,'orderStatus'),
        'htmlOptions'=>array('class'=>'row-fluid'),
        'columns'=>array (
            array(
                'name' => '订单城市',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                //'type'=>'raw',
                'value'=>'Dict::item("city", $data->city_id)'
            ),
            array (
                'name'=>'订单编号',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($this, 'getOrderInfoLink')
            ),
            array (
                'name'=>'司机信息',
                'headerHtmlOptions'=>array (
                    'width'=>'80px'
                ),
                'type'=>'raw',
                'value'=>'$data->driver_id',
            ),
            array (
                'name'=>'客户信息',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->contact_phone'
            ),

            array (
                'name'=>'订单时间',
                'headerHtmlOptions'=>array (
                    'style'=>'width:120px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($this,'orderTime')
            ),

            array (
                'name'=>'起始地点',
                'headerHtmlOptions'=>array (
                    'style'=>'width:120px',
                    'nowrap'=>'nowrap'
                ),
                'type' => 'raw',
                'value' => array($this,'OrderAddr')
            ),

            array (
                'name'=>'收费',
                'headerHtmlOptions'=>array (
                    'style'=>'width:120px',
                    'nowrap'=>'nowrap'
                ),
                'visible' => $price_visible,
                'type' => 'raw',
                'value' => array($this, 'orderFee')
            ),

            array (
                'header'=>'销单原因',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($this,'orderCancel')
            ),
            array (
                'header'=>'状态',
                'headerHtmlOptions'=>array (
                    'width'=>'40px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($this,'confirmOrderCacnel')
            ),
            /*
            array(
                'name'=>'合作商',
                'value'=>'$data->channel'
            ),
            */
        )
    ));
