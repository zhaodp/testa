<?php
/* @var $this BonusCodeController */
/* @var $model BonusCode */

/*$this->breadcrumbs=array(
	'Bonus Codes'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List BonusCode', 'url'=>array('index')),
	array('label'=>'Create BonusCode', 'url'=>array('create')),
	array('label'=>'Update BonusCode', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete BonusCode', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage BonusCode', 'url'=>array('admin')),
);*/
?>

<h1>优惠券详情 #<?php echo $model->id; ?></h1>

<?php
$couponRules = CJSON::decode($model->coupon_rules);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
        'name',
        'rename',
		'money',
		array(
            'name' => '渠道',
            'value' => Dict::item('bonus_channel', $model->channel),
        ),
		array(
            'name' => 'sn_type',
            'value' => $model->sn_type == 1 ? '固定码' : '区域码'
        ),
		'issued',
		array(
            'name' => 'user_limited',
            'value' => Dict::item('user_limited', $model->user_limited)
        ),
        array(
            'name' => 'repeat_limited',
            'value' => Dict::item('repeat_limited', $model->repeat_limited)
        ),
        array(
            'name' => 'channel_limited',
            'value' => Dict::item('channel_limited', $model->channel_limited)
        ),
		array(
            'label' => '城市限制',
            'value' => implode(",",BonusCode::model()->getCityById($model->id))
        ),
        array(
            'name' => 'effective_date',
            'value' => $model->effective_date
        ),
        array(
            'name' => 'binding_deadline',
            'value' => $model->binding_deadline
        ),
        array(
            'name' => 'end_date',
            'value' => $model->end_date
        ),
        array(
            'name' => 'end_date',
            'value' => $model->end_date
        ),
		'sms',
		'create_by',
		'created',
		'update_by',
		'updated',
		'remark',
        'Introducte',
	),
)); ?>