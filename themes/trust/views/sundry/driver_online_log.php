<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-29
 * Time: 下午11:51
 * auther mengtianxue
 */
Yii::app()
?>

<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#driver-online-log-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

    <h1>司机在线时长查询</h1>
    <div class="search-form">
        <div class="well span12">

            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'action' => Yii::app()->createUrl($this->route),
                'method' => 'get',
            ));
            ?>

            <div class="row span3">
                <?php echo $form->label($model, 'driver_id'); ?>
                <?php echo $form->textField($model, 'driver_id', array('size' => 20, 'maxlength' => 20)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'create_time'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'DriverOnlineLog[create_time]',
                    'model' => $model, //Model object
                    'value' => '',
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                ));
                ?>
            </div>

            <div class="row buttons">
                <br>
                <?php echo CHtml::submitButton('查询', array('class' => 'btn')); ?>
            </div>

            <?php $this->endWidget(); ?>

        </div>
        <!-- search-form -->
    </div>
    <!-- search-form -->

<?php
$data = $model->search(50);
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'driver-online-log-grid',
    'dataProvider' => $data,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        'driver_id',
        'online_time',
        'hot_time',
        array(
            'name'=>'在线开始时间',
            'type'=>'raw',
            'value'=>'date("Y-m-d H:i:s", strtotime($data->create_time)-$data->online_time/1000)'
        ),
        'create_time',
    ),
));
?>