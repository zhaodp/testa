<div class="well span12">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <input type="hidden" name="Order_page" value="1"/>

    <?php if ($callCenterUserType == 1) { ?>
        <div class="row span12">
            <div class="span3">
                <label><?php echo $form->label($model, 'order_id'); ?></label>
                <?php echo $form->textField($model, 'order_id', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[order_id]')); ?>
            </div>
            <div class="span3">
                <label><?php echo $form->label($model, 'order_number'); ?></label>
                <?php echo $form->textField($model, 'order_number', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[order_number]')); ?>
            </div>
            <div class="span3">
                <label>订单渠道</label>
                <?php 
                $channels = array('' => '全部') + Dict::items('order_channel');
                echo $form->dropDownList($model,
                    'channel',
                    $channels,
                    array('class' => "span12", 'name' => 'Order[channel]')
                ); ?>
            </div>
        </div>
        <div class="row span12">
            <div class="span3">
                <label><?php echo $form->label($model, 'driver_phone'); ?></label>
                <?php echo $form->textField($model, 'driver_phone', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[driver_phone]')); ?>
            </div>
            <div class="span3">
                <label><?php echo $form->label($model, 'driver_id'); ?></label>
                <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10, 'class' => "span12", 'name' => 'Order[driver_id]')); ?>
            </div>

            <?php if(Yii::app()->user->city == 0) {
            echo '<div class="span3">
                <label>订单城市</label>';
            echo $form->dropDownList($model,
                'city_id',
                Common::getOpenCity(),
                array('class' => "span12", 'name' => 'Order[city_id]')
            );
            echo '</div>'; }?>
        </div>
        <div class="row span12">
            <!--
            <div class="span3">
                <label><?php echo $form->label($model, 'name'); ?></label>
                <?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[name]')); ?>
            </div>
            -->
            <div class="span3">
                <label><?php echo $form->label($model, 'phone'); ?></label>
                <?php echo $form->textField($model, 'phone', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[phone]')); ?>
            </div>
            <div class="span3">
                <label><?php echo $form->label($model, 'vipcard'); ?></label>
                <?php echo $form->textField($model, 'vipcard', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[vipcard]')); ?>
            </div>
        </div>
        <div class="row span12">
            <div class="span3">
                <label><?php echo $form->label($model, 'status'); ?></label>
                <?php echo $form->dropDownList($model,
                    'status',
                    array(
                        '' => '全部',
                        '0' => '未报单的订单',
                        '1' => '完成报单的订单',
                        '2' => '销单待审核',
                        '3' => '已销单',
                        '4' => '销单审核不通过',
                        '5' => '司机拒绝订单',
                        '6' => '用户取消订单',
                        '7' => '未派出订单',
                        '8' => '司机拒绝未派出',
                    ),
                    array('class' => "span12", 'name' => 'Order[status]')
                ); ?>
            </div>
            <div class="span3">
                <label>订单来源</label>
                <?php echo $form->dropDownList($model,
                    'source',
		    Order::SourceToString('', true),
                    array('class' => "span12", 'name' => 'Order[source]')
                ); ?>
            </div>
            <div class="span3">
                <label><?php echo $form->label($model, 'income'); ?></label>
                <?php echo $form->textField($model, 'income', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[income]')); ?>
            </div>
        </div>
        <div class="row span12">
            <div class="span3">
                <label>呼叫开始时间</label>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'Order[call_time]',
                    'model' => $model, //Model object
                    'value' => '',
                    'mode' => 'datetime', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                    'htmlOptions' => array('class' => "span12")
                ));
                ?>
            </div>
            <div class="span3">
                <label>呼叫结束时间</label>
                <?php
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'Order[booking_time]',
                    'model' => $model, //Model object
                    'value' => '',
                    'mode' => 'datetime', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                    'htmlOptions' => array('class' => "span12")
                ));
                ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="row span12">
            <div class="span3">
                <label><?php echo $form->label($model, 'driver_id'); ?></label>
                <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10, 'class' => "span12", 'name' => 'Order[driver_id]')); ?>
            </div>

            <div class="span3">
                <label><?php echo $form->label($model, 'phone'); ?></label>
                <?php echo $form->textField($model, 'phone', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[phone]')); ?>
            </div>

            <div class="span3">
                <label><?php echo $form->label($model, 'vipcard'); ?></label>
                <?php echo $form->textField($model, 'vipcard', array('size' => 20, 'maxlength' => 20, 'class' => "span12", 'name' => 'Order[vipcard]')); ?>
            </div>
        </div>


    <?php } ?>
    <div class="row span12">
        <?php echo CHtml::submitButton('搜索', array('class' => 'btn span2')); ?>
    </div>
    <?php $this->endWidget(); ?>

</div>
