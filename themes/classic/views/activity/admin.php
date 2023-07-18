<?php
/* @var $this ActivityController */
/* @var $model BActivity */

$this->pageTitle = '活动管理 - ' . $this->pageTitle;

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#bactivity-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>活动管理</h1>

<p>
    <?php echo CHtml::link('创建活动', array('activity/create'), array('target' => '_blank', 'class' => 'btn')); ?>
</p>


<?php
$dataProvider = $model->search();
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bactivity-grid',
    'itemsCssClass' => 'table',
    'enableSorting' => false,
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'dataProvider' => $dataProvider,
    'columns' => array(
        'title',
        array(
            'name' => 'begin_time',
            'header' => '活动起止时间',
            'type' => 'raw',
            'value' => 'date("Y-m-d H:i:s", $data->begin_time) . "<br>" . date("Y-m-d H:i:s", $data->end_time)',
        ),
        'start_person',
        'activity_key',
        array(
            'header' => '状态',
            'type' => 'raw',
            'htmlOptions' => array('style' => 'width:50px;'),
            'value' => '($data->status == 1 ? "<span style=\"color:green;\">已激活</span>" : "<span style=\"color:red;\">未激活</span>")."<br>".($data->end_time<time()?"<span style=\"color:red;\">已过期</span>":"<span style=\"color:green;\">未过期</span>")'
        ),
        'remark',
        array(
            'name' => 'begin_time',
            'header' => '创建修改时间',
            'type' => 'raw',
            'value' => 'date("Y-m-d H:i:s", $data->create_time) . "<br>" . date("Y-m-d H:i:s", $data->modify_time)',
        ),
        array(
            'name' => 'extra',
            'header' => '扩展信息',
            'type' => 'raw',
            'value' => 'getExtra($data)',
        ),
        /*
          'extra',
          'activity_url',
         */
        array(
            'header' => '操作',
            'type' => 'raw',
            'htmlOptions' => array('style' => 'width:30px;'),
            'value' => 'CHtml::link($data->status?"失效":"激活",array("activity/updateStatus","id"=>$data->id,"status"=>$data->status?0:1),array(
                "onclick"=>"updateStatus(this);return false;"
            ))."<br>".CHtml::link("修改", array("activity/create", "id"=>$data->id))',
        ),
    ),
));

function getExtra($data) {
    $extra = $data->getExtraIni();
    $bonusWorkTime = isset($extra['bonusWorkTime']) ? $extra['bonusWorkTime'] : 0;
    $workTimeStr = $bonusWorkTime ? ('自领取之日起有效时间' . $bonusWorkTime . '天') : '';
    $workTimeHtml = $workTimeStr ? '<i class="icon-question-sign" title="' . $workTimeStr . '"></i>' : '';
    return isset($extra['bonusSn']) ? '优惠券：<span>' . $extra['bonusSn'] . $workTimeHtml . '</span>' : '';
}
?>

<script>

    function reload() {
        $('#bactivity-grid').yiiGridView('update', {
            data: $(this).serialize()
        });
    }
    function updateStatus(eee) {
        $.ajax({
            url: $(eee).attr('href'),
            cache: false,
            success: function(data) {
                if (data) {
                    reload();
                    alert("修改成功");
                } else {
                    alert("修改失败");
                }
            }
        });
    }
</script>