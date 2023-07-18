<?php
$this->pageTitle = '司机销单控制台';
?>
<h3>司机销单控制台</h3>

<?php
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.validate.js');

$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'get',
));
?>
<div class="form-inline">
    <div class="well">
        <p>
            开始时间
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'id' => 'start_time',
                'name' => 'start_time',
                'value' => $condition['start_time'],
                'mode' => 'date',
                'options' => array(
                    'width' => '60',
                    'dateFormat' => 'yy-mm-dd'
                ),
                'htmlOptions' => array(
                    'style' => 'width:80px;margin:0px 10px 0px 5px'
                ),
                'language' => 'zh'
            ));?>
            结束日期
            <?php $this->widget('CJuiDateTimePicker', array(
                'id' => 'end_time',
                'name' => 'end_time',
                'value' => $condition['end_time'],
                'mode' => 'date',
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ),
                'htmlOptions' => array(
                    'style' => 'width:80px;margin:0px 10px 0px 5px'
                ),
                'language' => 'zh'
            ));?>
            城市
            <?php echo CHtml::dropDownList('city_id', $condition['city_id'], Common::getOpenCity(), array('id' => 'city_id', 'style' => 'width:70px;margin:0px 10px 0px 5px')); ?>
            总接单量
            <?php
            echo CHtml::dropDownList('count_area', '', array('>=' => '>=', '=' => '=', '<=' => '<='), array("style" => "width:70px;margin:0px 2px 0px 5px"));
            echo CHtml::textField('count_num', $condition['count_num'], array("style" => "width:20px;margin:0px 5px 0px 2px"));
            ?>
            <label class="field_notice"></label>
            总销单率(%)
            <?php
            echo CHtml::dropDownList('rate_area', $condition['rate_area'], array('>=' => '>=', '=' => '=', '<=' => '<='), array('style' => 'width:70px;margin:0px 2px 0px 5px'));
            echo CHtml::textField('rate_num', $condition['rate_num'], array("style" => "width:20px;margin:0px 0px 0px 0px;"));
            ?>
            <label class="field_notice"></label>
			可疑销单率(%)
            <?php
            echo CHtml::dropDownList('rate_alert', $condition['rate_alert'], array('>=' => '>=', '=' => '=', '<=' => '<='), array('style' => 'width:70px;margin:0px 2px 0px 5px'));
            echo CHtml::textField('alert_num', $condition['alert_num']?$condition['alert_num']:0, array("style" => "width:20px;margin:0px 0px 0px 0px;"));
            ?>
            <label class="field_notice"></label>
        </p>
        <p>
            <label style="margin-left: 25px;">排序</label>
            <?php echo CHtml::dropDownList('sortby',
                $condition['sortby'],
                array(
                    '' => '总销单率',
                    '1' => 'APP销单率',
                    '2' => '400销单率',
					'9' => '可疑销单率',
                    '98' => '----------------',
                    '3' => '总单数',
                    '4' => 'APP单数',
                    '5' => '400单数',
                    '99' => '----------------',
                    '6' => '销单总数',
                    '7' => 'APP销单数',
                    '8' => '400销单数',
                ),
                array('style' => 'width:95px;margin:0px 10px 0px 9px'));?>
            司机工号
            <?php echo CHtml::textField('driver_user', $condition['driver_user'], array("style" => "width:60px;margin:0px 5px 0px 5px;")); ?>
            处理状态
            <?php
            echo CHtml::dropDownList('processed', $condition['processed'], array('0' => '全部', '1' => '已处理', '2' => '未处理'), array('style' => 'width:70px;margin:0px 10px 0px 1px'));
            echo CHtml::submitButton('Search', array('func'=>'submit','class' => "btn btn-primary span1"));
            ?>
        </p>

        <p>
            <span style="color:red"><b>注:</b></span>
            <span style="margin:0px 5px 0px 5px"><b>总接单量范围</b></span>与<span style="margin:0px 5px 0px 5px"><b>总销单率范围</b></span>
            <span>需先选择条件，再在输入框输入数字</span>
        </p>
        <h4><?php echo $cancel_count; ?></h4>

    </div>
</div>
<?php $this->endWidget(); ?>
<style>
.error{color:red;}
.right{color:blue;}
</style>
<script>
jQuery(document).ready(function(){
    var validator= $('#yw0').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {
            count_num : {
                number :true
            },
            rate_num : {
                number :true
            },
            alert_num : {
                number :true
            }
        },
        messages : {
            count_num : {
                number   : '请填写数字！'
            },
            rate_num : {
                number   : '请填写数字！'
            },
            alert_num : {
                number   : '请填写数字！'
            }
        }
    });
    $('#yt0').click(function()  {
        validator.form();
        $('#yw0').submit();
    });
});
</script>
