<?php
/* @var $this UserNotifyController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'User Notifies',
);

$this->menu=array(
	array('label'=>'Create UserNotify', 'url'=>array('create')),
	array('label'=>'Manage UserNotify', 'url'=>array('admin')),
);
?>

<h1>列表</h1>
<div class="row buttons">


    <?php echo CHtml::Button('新建通知',array('class'=>'btn btn-success','id'=>'add_user_notify'));?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
    'columns' => array(
        array(
            'name' => '名称',
            'value' => '$data->Id'
        ),
        array(
            'name' => '用户属性',
            'value' =>'UserNotifyAction::getUserTypes($data->user_type)'

        ),
        array(
            'name' => '城市',
            'headerHtmlOptions'=>array (
                'width'=>'200px'
            ),
            'type' => 'raw',
            'value' =>'UserNotifyAction::getCityNames($data->city_id)'
        ),
        array(
            'name' => '触发条件',
            'value' => 'isset($data["UserNotifyMsg"]) ?UserNotifyAction::getTriggerNames($data->UserNotifyMsg->trigger_condition): ""'
        ),
        array(
            'name' => '通知',
            'type' => 'raw',
            'value' =>'CHtml::link(Dict::item("notify_type", $data->notify_type),Yii::app()->createUrl("/customer/userNotify",array("id"=>$data->Id,"action"=>"view")), array("target" => "_blank"))'

        ),
        array(
            'name' => '操作系统',
            'value'=>'Dict::item("client_os_type", $data->client_os_type)'
        ),
        array(
            'name' => '版本号',
            'value' =>'$data->client_version_lowest'
        ),
        array(
            'name' => '生效时间',
            'type' => 'raw',
            'value' =>'date("Y-m-d H:i",($data->sdate))."至".date("Y-m-d H:i",($data->edate))'
        ),
        array(
            'name' => '配置日期',
            'value' =>'date("Y-m-d H:i",($data->ope_time))'
        ),
        array(
            'name' => '操作人',
            'value' =>'$data->ope_people'
        ),
        array(
            'name' => '操作',
            'type' => 'raw',
            'value' => ' ($data->status==UserNotify::$NOTIFY_STATUS_ON)?
             CHtml::link("停用",Yii::app()->createUrl("customer/userNotify", array("id" => $data[\'Id\'],"action"=>"stop"))):"已经停用"',
        ),
    ),
)); ?>
<script>
    $(function(){
        //新建
        $("#add_user_notify").click(function(){
            window.location.href="<?php echo Yii::app()->createUrl('customer/userNotify&action=new'); ?>";
        });
    });

</script>