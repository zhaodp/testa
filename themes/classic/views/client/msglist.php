<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 戴艺辉
 * Date: 13-8-29
 * Time: 下午4:28
 * To change this template use File | Settings | File Templates.
 */

?>

<h1>消息记录</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'Order-grid',
    'ajaxUpdate'=>true,
    'dataProvider'=>$dataProvider,
    'cssFile'=>SP_URL_CSS . 'table.css',
    'itemsCssClass'=>'table  table-condensed',
    'pagerCssClass'=>'pagination text-center',
    //'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(
        array(
            'name' => 'ID',
            'value'=> '$data->id'
        ),
        array(
            'name'=>'司机工号',
            'value'=>'$data->driver_id'
        ),
        array(
            'name'=>'消息内容',
            'value'=>'$data->content'
        ),
        array(
            'name' => 'APP版本',
            'value' => '$data->version'
        ),
        array(
            'name' => '推送时间',
            'value' => '$data->created'
        ),
        array(
            'name' => '操作人',
            'value' => 'AdminUserNew::model()->getName($data->user_id)'
        ),
    ),
));

?>
