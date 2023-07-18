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
	$('#sms-log-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

    <h1>短信查询</h1>
    <div class="search-form">
        <div class="well span12">

            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'action' => Yii::app()->createUrl($this->route),
                'method' => 'get',
            ));
            ?>

            <div class="row span3">
                <?php echo $form->label($model, 'receiver'); ?>
                <?php echo $form->textField($model, 'receiver', array('size' => 20, 'maxlength' => 20)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'message'); ?>
                <?php echo $form->textField($model, 'message', array('size' => 80, 'maxlength' => 80)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'created'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'CarSmsLog[created]',
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
                <?php echo CHtml::submitButton('查询短信', array('class' => 'btn')); ?>
            </div>

            <?php $this->endWidget(); ?>

        </div>
        <!-- search-form -->
    </div>
    <!-- search-form -->

<?php
$data = $model->search(50);
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'sms-log-grid',
    'dataProvider' => $data,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        'receiver',
        'message',
        'result',
        'created',
    ),
));
?>