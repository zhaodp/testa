<?php
$this->pageTitle = '司机单日在线详情';
?>
<h1>司机单日在线详情</h1>
<table class="table-condensed">
    <tr>
        <td>司机工号：<?php echo $driver_id;?></td>
        <td>司机姓名: <?php echo $driver_name;?></td>
    </tr>
</table>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'online',
    'dataProvider'=>$dataProvider,
    //'cssFile'=>SP_URL_CSS . 'table.css',
    'itemsCssClass'=>'table  table-condensed',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(
        'date' => array(
            'header'=>'日期',
            'name'=>'record_date',
        ),
        array(
            'name'=>'00:00-07:00',
            'value'=>'$data["twentyfour"]+$data["one"]+$data["two"]+$data["three"]+$data["four"]+$data["five"]+$data["six"]',
        ),
        array(
            'name'=>'07:00-18:00',
            'value'=>'$data["seven"]+$data["eight"]+$data["nine"]+$data["ten"]+$data["eleven"]+$data["twelve"]+$data["thirteen"]+$data["fourteen"]+$data["fifteen"]+$data["sixteen"]+$data["seventeen"]',
        ),
        array(
            'name'=>'18:00-19:00',
            'value'=>'$data["eighteen"]',
        ),
        array(
            'name'=>'19:00-20:00',
            'value'=>'$data["nineteen"]',
        ),
        array(
            'name'=>'20:00-21:00',
            'value'=>'$data["twenty"]',
        ),
        array(
            'name'=>'21:00-22:00',
            'value'=>'$data["twentyone"]',
        ),
        array(
            'name'=>'22:00-23:00',
            'value'=>'$data["twentytwo"]',
        ),
        array(
            'name'=>'23:00-24:00',
            'value'=>'$data["twentythree"]',
        ),
    ),
));
?>