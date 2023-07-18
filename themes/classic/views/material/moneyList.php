<?php
$this->renderPartial('tab',array('tab'=> 3));
//Yii::app()->clientScript->registerScript('search', "
//$('.search-form form').submit(function(){
//	$.fn.yiiGridView.update('material-money-grid', {
//		data: $(this).serialize()
//	});
//	return false;
//});
//");
?>

<div class="search-form form row">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>
    <div class="row span2">
        <?php echo $form->label($model,'city_id');
        $user_city_id = Yii::app()->user->city;

        if ($user_city_id != 0) {
            $city_list = array(
                '城市' => array(
                    $user_city_id => Dict::item('city', $user_city_id)
                )
            );
            $city_id = $user_city_id;
        } else {
            $city_id =  $model->city_id;
            $city_list = CityTools::cityPinYinSort();
        }
        $this->widget("application.widgets.common.DropDownCity", array(
            'cityList' => $city_list,
            'name' => 'MaterialMoneyLog[city_id]',
            'value' => $city_id,
            'type' => 'modal',
            'htmlOptions' => array(
                'style' => 'width: 134px; cursor: pointer;',
                'readonly' => 'readonly',
            )
        ));
        ?>
    </div>


    <div class="row span2">
        <?php echo CHtml::label('开始日期','start_time');?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'MaterialMoneyLog[start_time]',
            'value'=>$query['start_time'],
            'mode'=>'date',  //use "time","date" or "datetime" (default)
            'options'=>array (
                'dateFormat'=>'yy-mm-dd'
            ),
            'language'=>'zh',
            'htmlOptions'=>array(
                'placeholder'=>date('Y-m-').'01',
                'style'=>'width:100px;'
            ),


        ));?>
    </div>

    <div class="row span2">
        <?php echo CHtml::label('结束日期','start_time');?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'MaterialMoneyLog[end_time]',
            'value'=>$query['end_time'],
            'mode'=>'date',  //use "time","date" or "datetime" (default)
            'options'=>array (
                'dateFormat'=>'yy-mm-dd'
            ),
            'language'=>'zh',
            'htmlOptions'=>array(
                'placeholder'=>date('Y-m-d'),
                'style'=>'width:100px;'
            ),


        ));?>
    </div>

    <div class="row span2" style="width:100px;">
        <?php echo $form->label($model, 'status');
        echo CHtml::dropDownList( 'MaterialMoneyLog[status]', ($query['status'] === '' ? '' : $query['status'] ),MaterialMoneyLog::getStatus(''),array('empty'=>'全部','style'=>'width:100px')); ?>
    </div>



    <div class="row span2">
        <label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>
<div class="row-fluid" id="item_count_string" name="item_count_string"><h3><?php echo $stat; ?></h3></div>

<?php
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'material-money-grid',
    'itemsCssClass'=>'table table-striped',
    'dataProvider'=>$model->search(),
    'columns'=>array (
        'id',
        'mark_time',
        'operator',
        array(
            'name'=>'status',
            'value'=>'MaterialMoneyLog::getstatus($data->status)',
        ),
        'money',
        'equipment_deposit',
        'cellphone_deposit',
        'simcard_deposit',
        'cash_deposit',
        'invoice',
        'remark',
    )));

?>

