<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl("pageConfig/create"), 'method' => 'post',)); ?>
    <div class="span12">
        <div>
            <?php echo "弹窗时间点：" ?>
            <?php
                $trigger_time = PageConfig::$trigger_time;
                foreach ($trigger_time as $key => $item) {
                    echo CHtml::checkBox("trigger_time[]", false, array("value" => $key)) . $item . '&nbsp;&nbsp;';
                }
            ?>
        </div>
        <br>
        <div>
            <?php echo "活动标题：" ?>
            <?php echo $form->textField($model, 'title', array('size' => 10, 'maxlength' => 50)); ?>
        </div>
        <div>
            <?php
            echo "活动时间：";
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'PageConfig[begintime]',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('style' => "width:123px")
            ));
            echo "&nbsp;&nbsp;&nbsp;至&nbsp;&nbsp;&nbsp;";
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'PageConfig[endtime]',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('style' => "width:123px")
            ));

            ?>
        </div>
        <div>
            <?php
            echo "订单有效时间：";
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'PageConfig[order_begin]',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('style' => "width:123px")
            ));
            echo "&nbsp;&nbsp;&nbsp;至&nbsp;&nbsp;&nbsp;";
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'PageConfig[order_end]',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('style' => "width:123px")
            ));

            ?>
        </div>
        <div>
            <?php echo "页面地址：" ?>
            <?php echo $form->textField($model, 'url', array('size' => 10, 'maxlength' => 255)); ?>
        </div>

        <div>
            <?php echo "适用地区" ?>
            <input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;
            <input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选<br><br>
            <?php
                $city = explode(',', $model->city_ids);
                $citys = Dict::items('city');
                unset($citys[0]);
                foreach ($citys as $key => $item) {
                    echo CHtml::checkBox("city[]", false, array("value" => $key, 'class' => 'city_id')) . $item . '&nbsp;&nbsp;';
                }
            ?>
        </div>
        <div>
            <a class="btn btn-info" href="javascript:;" id="btn">确认提交</a>
        </div>

    </div>
    <?php $this->endWidget(); ?>

</div>
