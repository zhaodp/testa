<?php
$this->pageTitle = '白名单列表';
echo "<h1>".$this->pageTitle."</h1><br />";

echo "<div class='search-form'>";
echo '<div class="span12">';
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
));
echo '电话号：';
echo "<input type='text' name='phone' max='20'>&nbsp;&nbsp;";
echo CHtml::submitButton('Search');
$this->endWidget();

echo '</div>';
echo '</div>';
echo CHtml::Button('增加白名单用户',array('class'=>'btn btn-success','id'=>'add'));
$this->widget('zii.widgets.grid.CGridView', array(
    'id'            => 'sms-grid',
    'dataProvider'  => $dataProvider,
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name'=>'电话号',
            'headerHtmlOptions' => array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => 'Common::parseCustomerPhone($data["phone"])'
	),
        array(
            'name'=>'操作员',
            'headerHtmlOptions' => array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => 'AdminUser::model()->getName($data["user_id"])'
	),
        array(
            'name'=>'创建时间',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["created"]'
	),
        array(
            'name'=>'原因',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => '$data["remarks"]'
	),
        array(
            'name'=>'操作',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => 'CHtml::Button("删除",array("class"=>"btn","id"=>"search_order_num","style"=>"width:60px;height:30px;","onclick"=>"del($data[id] , \'$data[phone]\')"))'
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
        window.location.href="<?php echo Yii::app()->createUrl('customer/addwhite'); ?>";
    });
});

function del(id , phone) {
    if(window.confirm("确定要将电话号"+phone+"移除白名单列表么？")) {
        url="<?php echo Yii::app()->createUrl('customer/delwhite'); ?>";
        window.location.href = url+"&phone="+phone;
    }
}
</script>
