<div class="row-fluid" style="width: 100%;max-height:400px;">
    <?php
    $a = $this->widget('application.widgets.order.OrderPathWapWidget', array(
        'orderId' => $orderId,
//        'orderIdEncrypt' => TRUE,
        'htmlOptions' => array(
            'style' => 'margin:-8px;height:400px;'
        ),
    ), TRUE);
    if($a){
        echo $a;
    }else{
        echo '没有数据';
    }
    ?>
</div>