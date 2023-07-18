  <?php echo render1Row($model, $phone, $driver);  ?>
  <?php echo render2Row($phone);  ?>

<?php function render1Row($model, $phone, $driver) { ?>
<div>
    <span>
        <?php echo CHtml::link('添加投诉',
                               Yii::app()->createUrl('complain/new',
                                                     array('phone'=>trim($model->phone),'city_id'=>(int)$model->city_id)), 
        		                                     array ('class'=>'btn','target'=>'_blank')); ?>
    </span>
    
    <span>
        <?php
            if ($phone != null) {
                echo CHtml::link('优惠券查询',
                                 array("bonusCode/bonus_admin",
                                       "CustomerBonus" => array('customer_phone' => $phone)),
                                 array('class'=>"btn", 'target' => '_blank'));
            } 
      ?>
    </span>
    
     <span>
        <?php 
            if ($phone != null) {
                echo CHtml::link('订单查询',
                                 array("order/admin",
                                       "Order"=>array('phone' => Common::phoneEncode($phone))),
                                 array('class'=>"btn", 'target' => '_blank'));
            }
        ?>
    </span>
    
    <span>
        <?php 
            if (!empty($driver)) {
                echo CHtml::link('切换至司机界面',
                                 array("client/service",
                                       "phone"=> $phone),
                                 array('class'=>"btn"));
            }
        ?>
    </span>
</div>
<?php } ?>

<?php function render2Row($phone) { ?>
<?php 
//  TODO 第二排功能按钮placeholder
?>
<div>
    <span>
    </span>
</div>
<?php }?>