<?php
/* @var $this GetuiClientController */
/* @var $dataProvider CActiveDataProvider */

?>

<h1>手机客户端注册管理</h1>
<?php
$t_driver_user =	isset($_GET['driver_user']) ? $_GET['driver_user'] : '';
$t_udid =	isset($_GET['udid']) ? $_GET['udid'] : '';
$t_version = isset($_GET['version']) ? $_GET['version'] : 'driver';
$driver_class = $t_version == 'driver' ? "btn-primary btn" : "btn";
$customer_class = $t_version == 'customer' ? "btn-primary btn" : "btn";
?>
<div class="btn-group">
    <?php echo CHtml::link('司机端', array('getuiClient/index','version'=>'driver'),array('class'=> $driver_class));?>
    <?php echo CHtml::link('用户端', array('getuiClient/index','version'=>'customer'),array('class'=> $customer_class));?>
</div>


<div class="wide form">

    <?php

    $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">
       <?php
            if($t_version == "driver"){
       ?>
        <div class="span3">
            <label for="mobile">司机工号</label>
            <input type="text" id="driver_user" class="span12" name="driver_user" value="<?php echo $t_driver_user;?>" />
        </div>
      <?php }else{?>
        <div class="span3">
            <label for="mobile">udid</label>
            <input type="text" id="udid" class="span12" name="udid" value="<?php echo $t_udid;?>" />
        </div>
    <?php }?>
    </div>

    <div class="row-fluid">
        <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'getui-client-grid',
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        'client_id',
        'udid' => array(
            'name' => 'udid',
            'header' => $t_version == 'driver' ? 'token' : 'udid',
        ),
        //'city',
        'driver_user' => array(
            'name' => 'driver_user',
            'visible' => $t_version == 'driver' ? true : false,
        ),
        'created',
    ),
)); ?>
