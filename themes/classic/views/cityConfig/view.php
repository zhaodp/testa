<h1>View cityconfig #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        'city_id',
        'city_name',
        'city_prifix',
        'bonus_prifix',
        'city_level',
        'status'=>array('name'=>'status','value'=> $model->status == 1 ? '开通':'未开通'),
        'cast_id'=> array('name'=> 'cast_id','value' => $city_cast[$model->cast_id] ),
        'fee_id' => array('name' => 'fee_id', 'value' => $fee_id_arr[$model->fee_id]),
        'pay_money',
        'screen_money',
        'bonus_back_money',
        'captital',
        'first_letter',
        'pinyin',
        'create_time',
        'update_time',
        'online_time',
        'type',
        'type_value',
    ),
)); ?>