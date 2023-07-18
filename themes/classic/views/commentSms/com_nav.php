<?php $currentFun=$this->id.'/'.$this->action->id; ?>
<ul class="nav nav-pills">
    <li <?php if($currentFun=='commentSms/admin') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('commentSms/admin'); ?>">司机评价管理</a></li>
    <li <?php if($currentFun=='sms/send') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('sms/send'); ?>">短信发送队列</a></li>
    <li <?php if($currentFun=='sms/mo') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('sms/mo'); ?>">回评短信队列</a></li>
</ul>

