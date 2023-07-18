
    <div class="control-group">
        <label class="control-label" for="inputEmail">订单编号</label>
        <div class="controls">
            <?php echo CHtml::textField('order_id',$model->order_id,array('placeholder'=>'订单编号','disabled'=>'disabled')); ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">司机工号</label>
        <div class="controls">
            <?php echo CHtml::textField('driver_id',$model->driver_id,array('placeholder'=>'司机工号','disabled'=>'disabled')); ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">客人姓名</label>
        <div class="controls">
            <?php echo CHtml::textField('name',$model->name,array('placeholder'=>'客人姓名')); ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">客人联系手机</label>
        <div class="controls">
            <?php echo CHtml::textField('customer_phone',$model->customer_phone,array('placeholder'=>'客人联系手机')); ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">投诉分类</label>
        <div class="controls">
            <?php  echo CHtml::dropDownList('complain_maintype',
                $model->complain_type,
                $typelist,
                array(
                    'ajax' => array(
                        'type'=>'POST', //request type
                        'url'=>Yii::app()->createUrl('complain/getsubtype'),
                        'update'=>'#sub_type', //selector to update
                        'data'=>array('complain_maintype'=>'js:$("#complain_maintype").val()')
                    ))
            );?>
            <?php echo CHtml::dropDownList('sub_type','-1', array('-1'=>'全部')); ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">投诉来源</label>
        <div class="controls">
            <?php echo CHtml::dropDownList('source',$model->source, array_merge(array('-1'=>'全部'),CustomerComplain::$source)); ?>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="inputPassword">投诉详情</label>
        <div class="controls">
            <?php
                echo CHtml::textArea('detail',$model->detail,array('class'=>'input-xlarge','rows'=>'3'));?>
        </div>
    </div>
