<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-9-22
 * Time: 下午1:13
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="container">

    <h1>司机上线情况详情</h1>

    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
        'htmlOptions'=>array('class'=>'form-search')
    ));
    ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model,'record_date'); ?>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>"record_date",
                'options'=>array(
                    'showAnim'=>'fold',
                    'showOn'=>'both',
                    //'buttonImageOnly'=>true,
                    'dateFormat'=>'yy-mm-dd',
                    'changeYear'=>true,
                    'changeMonth'=> true,
                ),
                'value' => $record_date,
                'htmlOptions'=>array(
                    'class' => 'span6'
                ),
            ));
            ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model,'city_id'); ?>
            <?php
            $city_list = Dict::items('city');
            $user_city_id = Yii::app()->user->city;
            if ($user_city_id != 0) {
                $city_list = array(
                    $user_city_id => $city_list[$user_city_id]
                );
            }
            echo CHtml::dropDownList('city_id', $city_id,$city_list, array('class'=>'span6'));
            ?>
        </div>
        <div class="span4">
            <label >在线类型</label>
            <?php echo CHtml::dropDownList('type', $type, array(
                'online' => '上线',
                'p_online' => '峰值时段在线',
                'p_continuous' => '峰值时段连续在线',
                'p_active' => '峰值时段任意连续两小时在线',
            ));?>
        </div>
    </div>
    <div>
        <div class="span2">
            <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-info','style'=>'margin-top:15px;')); ?>
        </div>
    </div>
    </div>
    <?php $this->endWidget(); ?>
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'driver-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
                array(
                    'name' => 'record_date',
                    'value' => '$data->record_date'
                ),
                array(
                    'name' => 'driver_id',
                    'value' => '$data->driver_id'
                ),
                array(
                    'name' => 'city_id',
                    'value' => 'Dict::item("city", $data->city_id)'
                ),
                array(
                    'name' => 'online',
                    'value' => '$data->online',
                ),
                array(
                    'name' => 'p_online',
                    'value' => '$data->p_online',
                ),
                array(
                    'name' => 'p_continuous',
                    'value' => '$data->p_continuous'
                ),
                array(
                    'name' => '峰值时段任意连续两小时在线',
                    'value' => '$data->p_active',
                )
            )
        )
    );
?>

</div>

<script>
    jQuery(document).ready(function(){
        jQuery('.ui-datepicker-trigger').remove();
    });
</script>