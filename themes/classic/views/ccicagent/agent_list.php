<?php
$this->pageTitle = '坐席列表';
echo "<h1>".$this->pageTitle."</h1><br />";

echo "<div class='search-form'>";
echo '<div class="span12">';
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
));
echo '工号：';
echo "<input type='text' name='agent_num' max='20'>&nbsp;&nbsp;";
echo '账号：';
echo "<input type='text' name='name' max='20'>&nbsp;&nbsp;";
echo CHtml::submitButton('Search');
$this->endWidget();

echo '</div>';
echo '</div>';
echo CHtml::Button('增加工号',array('class'=>'btn btn-success','id'=>'add'));
$this->widget('zii.widgets.grid.CGridView', array(
    'id'            => 'sms-grid',
    'dataProvider'  => $dataProvider,
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name'=>'工号',
            'headerHtmlOptions' => array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["agent_num"]'
	),
        array(
            'name'=>'账号',
            'headerHtmlOptions' => array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["name"]'
	),
        array(
            'name'=>'坐席密码',
            'headerHtmlOptions' => array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["password"]'
	),
        array(
            'name'=>'操作',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => 'CHtml::Button("删除",array("class"=>"btn","id"=>"search_agent_num","style"=>"width:60px;height:30px;","onclick"=>"del($data[agent_num])"))'
	),
    ),
));

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('sms-grid', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<script>

$(function(){
    $("#add").click(function(){
        window.location.href="<?php echo Yii::app()->createUrl('ccicagent/addagent'); ?>";
    });
});

function del(agent_num) {
    if(window.confirm("确定删除"+agent_num+"？")) {
        url="<?php echo Yii::app()->createUrl('ccicagent/delagent'); ?>";
        window.location.href = url+"&agent_num="+agent_num;
    }
}
</script>
