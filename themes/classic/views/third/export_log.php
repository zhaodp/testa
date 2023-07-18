<?php echo CHtml::Button('返回',array('class'=>'btn btn-success','id'=>'import_card', 'onclick'=>'javascript:location.href="'.Yii::app()->createUrl('third/card').'";','style'=>'margin-left:30px'));
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'export_log',
    'ajaxUpdate' => false,
    'cssFile'=>SP_URL_CSS.'table.css',
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'dataProvider' => $model->search(),
    'columns' => array(
        'id',
        'name',
        //导出订单数量
        array(
            'name'=>'导出订单数量',
            'value'=>'$data->total'
        ),
        array(
            'name'=>'导出时间',
            'value'=>'$data->order_time'
        ),
        array(
            'name'=>'status',
            'type'=>'raw',
            'value'=>'DriverExportLog::model()->getstatus($data->status)'
        ),
        array(
            'name' => 'url',
            'type' => 'raw',
            'value' => 'CHtml::link("下载","$data->url")',
        )
    ),
));