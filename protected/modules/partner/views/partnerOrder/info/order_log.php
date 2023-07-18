<div class="tr">
<tr class="tr">
    <td style="border:0">
        <?php if(!empty($order->order_log)){ ?>
            <?php
                $data = $order->order_log;
                $dataProvider=new CArrayDataProvider($data, array(
                    'id'=>'order_log',
                    'sort'=>array(
                        'attributes'=>array(
                             'id',
                        ),
                    ),
                ));

                $this->widget('zii.widgets.grid.CGridView', array(
                    'id' => 'order-grid',
                    'dataProvider' => $dataProvider,
                    'showTableOnEmpty' => FALSE,
                    'cssFile' => SP_URL_CSS . 'table.css',
                    'template' => '{items}',
                    'pagerCssClass' => 'pagination text-center',
                    'pager' => Yii::app()->params['formatGridPage'],
                    'itemsCssClass' => 'table table-condensed',
                    'htmlOptions' => array('class' => 'row-fluid'),
                    'enableSorting' => FALSE,
                    'columns' => array(
                        array(
                            'name'=>'operator',
                            'header'=>'操作人',
                        ),
                        array(
                            'name'=>'created',
                            'header'=>'操作时间',
                            'value'=>'date("Y-m-d H:i:s", $data->created)',
                        ),
                        array(
                            'name'=>'description',
                            'header'=>'操作记录',
                        ),
                    )
                    
                ));
            ?>
        <?php }else{ ?>
        <p>暂无数据</p>
        <?php } ?>
    </td>
</tr>
</div>