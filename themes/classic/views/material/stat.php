<?php
$this->renderPartial('tab',array('tab'=> 2));
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('material_list', {
		data: $(this).serialize()
	});
	return false;
});
");
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
            'name' => 'MaterialLog[city_id]',
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
            'name'=>'MaterialLog[start_time]',
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
            'name'=>'MaterialLog[end_time]',
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

    <div class="row span2" ">
        <?php echo $form->label($model, 'type_id');
        echo CHtml::dropDownList( 'MaterialLog[type_id]', $query['type_id'],$material_arr,array('empty'=>'全部','style'=>'width:200px')); ?>
    </div>



    <div class="row span2">
        <label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'material_list',
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(

        array(
            'name'=>'物料名',
            'value'=>'$data["name"]',
        ),
        array(
            'name'=>'物料类型',
            'value'=>'$data["type_name"]',
        ),
        array(
            'name' => '发放数量',
            'value' => '$data["fafang_quantity"]',
        ),
        array(
            'name' => '回收数量',
            'value' => '$data["recycle_quantity"]',
        ),
        array(
            'name' => '遗失数量',
            'value' => '$data["lost_quantity"]',
        ),
        array(
            'name' => '有此物料的司机数',
            'value' => '$data["keep_driver_quantity"]',
        ),


    )
));

?>

