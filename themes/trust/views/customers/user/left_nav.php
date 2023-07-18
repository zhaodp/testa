<?php $action = $this->getAction()->getId(); ?>
<ul class="nav nav-tabs" style="margin-bottom: 0px;">
    <li <?php
        if ($action == 'user_info') {
            echo 'class="active"';
        }
        ?>><a href='<?php echo Yii::app()->createUrl('/customers/user_info', array('id' => $id)); ?>'>代驾服务</a></li>

    <li <?php
    if ($action == 'user_history') {
        echo 'class="active"';
    }
    ?>><a href='<?php echo Yii::app()->createUrl('/customers/user_history', array('id' => $id)); ?>'>充值历史</a></li>
</ul>