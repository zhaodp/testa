<?php
/* @var $this UserNotifyController */
/* @var $model UserNotify */

$this->breadcrumbs=array(
	'User Notifies'=>array('index'),
	$model->Id,
);
$this->menu=array(
	array('label'=>'List UserNotify', 'url'=>array('index')),
	array('label'=>'Create UserNotify', 'url'=>array('create')),
	array('label'=>'Update UserNotify', 'url'=>array('update', 'id'=>$model->Id)),
	array('label'=>'Delete UserNotify', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->Id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage UserNotify', 'url'=>array('admin')),
);
?>

<h1>通知详情</h1>

<?php
$userNotify =
    array(
    'Id',
    array(
        'name'=>'城市',
        'value' =>UserNotifyAction::getCityNames($model->city_id)
    ),
    array(
        'name'=>'用户属性',
        'value' =>UserNotifyAction::getUserTypes($model->user_type)
    ),
    array(
        'name' => '操作系统',
        'value'=>Dict::item("client_os_type", $model->client_os_type)
    ),
    array(
        'name' => '生效时间',
        'type' => 'raw',
        'value' =>date("Y-m-d H:i",($model->sdate))."至".date("Y-m-d H:i",($model->edate))
    ),
    array(
        'name' => '配置日期',
        'value' =>date("Y-m-d H:i",($model->ope_time))
    ),
    array(
        'name' => '操作人',
        'value' =>$model->ope_people
    ),
    array(
        'name' => '最低版本号',
        'value' =>$model->client_version_lowest
    ),
    array(
        'name' => '状态',
        'value' =>  ($model->status==UserNotify::$NOTIFY_STATUS_ON)?"启用状态":"停用状态"
    ),
    array(
        'name' => '通知类型',
        'value' =>(Dict::item("notify_type", $model->notify_type))
    ),

);
$this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>$userNotify));
if(isset($model['UserNotifyBanner'])){
    $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        array(
            'name'=>'显示的文案',
            'value'=>$model->UserNotifyBanner->word,
        ),
        array(
            'name'=>'显示文案的订单状态',
            'value'=>UserNotifyAction::getOrderStatusNames($model->UserNotifyBanner->word_order_status),
        ),
        array(
            'name'=>'banner图片地址：',
            'value'=>$model->UserNotifyBanner->banner_picture_url
        ),
        array(
            'name'=>'banner跳转地址：',
            'value'=> $model->UserNotifyBanner->banner_jump_url
        ),
        array(
            'name'=>'显示banner的订单状态：',
            'value'=>UserNotifyAction::getOrderStatusNames( $model->UserNotifyBanner->banner_order_status)
        ),
    ),));
    }elseif(isset($model['UserNotifyMsg'])){
        $this->widget('zii.widgets.CDetailView', array(
            'data'=>$model,
            'attributes'=>array(
                array(
                    'name'=>'触发条件',
                    'value'=>UserNotifyAction::getTriggerNames($model->UserNotifyMsg->trigger_condition)
                ),
                array(
                    'name'=>'通知文案',
                    'value'=> $model->UserNotifyMsg->word

                ),
                array(
                    'name'=>'指定客户端页面',
                    'value'=> Dict::item('client_page',$model->UserNotifyMsg->client_page)

                ),
                array(
                    'name'=>'弹屏标题',
                    'value'=> $model->UserNotifyMsg->title

                ),

                array(
                    'name'=>'弹屏提示正文：',
                    'value'=> $model->UserNotifyMsg->content
                ),
                array(
                    'name'=>'button文案',
                    'value'=>
                       $model->UserNotifyMsg->button_text
                ),
                array(
                    'name'=>'跳转链接',
                    'value'=> $model->UserNotifyMsg->button_url
                ),
            ),));
}
?>
