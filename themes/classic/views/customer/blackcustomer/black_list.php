<?php
$city = Dict::items('city');
$this->pageTitle = '黑名单列表';
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
echo CHtml::Button('新建黑名单用户',array('class'=>'btn btn-success','id'=>'add'));
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'sms-grid',
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
         array(
            'name'=>'电话号',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => 'Common::parseCustomerPhone($data["phone"])'),
        array(
            'name'=>'操作员',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => 'AdminUserNew::model()->getName($data["user_id"])'),
        array(
            'name'=>'创建时间',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["created"]'),
	array(
            'name'=>'到期时间',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["expire_time"]'),

        array(
            'name'=>'屏蔽原因',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => 'CHtml::link("查看", "javascript:void(0);", array ("onclick"=>"{showRemark(\'$data[phone]\');}"));'
            ),
        array(
            'name'=>'操作',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => 'CHtml::Button("删除",array("class"=>"btn","id"=>"search_order_num","style"=>"width:40px;height:30px;","onclick"=>"del($data[id] , \'$data[phone]\')"))'),
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
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'user_remark_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'查看原因',
        'autoOpen'=>false,
        'width'=>'700',
        'height'=>'550',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#user_remark_dialog").dialog("close");}'))));
echo '<div id="user_remark_dialog"></div>';
echo '<iframe id="user-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<script>
$(function(){
    //新建发送短信
    $("#add").click(function(){
        window.location.href="<?php echo Yii::app()->createUrl('customer/addblack'); ?>";
    });
});
function del(id , phone) {
    if(window.confirm("确定要将电话号"+phone+"移除黑名单列表么？")) {
        url="<?php echo Yii::app()->createUrl('customer/delblack'); ?>";
        window.location.href = url+"&id="+id;
    }
}
function showRemark(phone){
    $(".ui-dialog-title").html("查看屏蔽原因");
    url = '<?php echo Yii::app()->createUrl('/customer/getuserremark');?>&phone='+phone;
    $("#user-frame").attr("src",url);
    $("#user_remark_dialog").dialog("open");
}
</script>
