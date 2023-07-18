<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('#search-form').submit(function(){
    $('#support-ticket-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<?php
$city = Dict::items('city');
$this->pageTitle = '城市版本列表';
echo "<h1>".$this->pageTitle."</h1><br />";

echo "<div class='search-form'>";
echo '<div class="span12">';
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
));
echo '城市：';
echo "<input type='text' name='city_name' max='20'>&nbsp;&nbsp;";
echo CHtml::submitButton('Search');
$this->endWidget();

echo '</div>';
echo '</div>';
echo CHtml::Button('新建司机版本',array('class'=>'btn btn-success','id'=>'add_version'));

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'sms-grid',
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
         array(
            'name'=>'城市',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["city_name"]'),
        array(
            'name'=>'版本号',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' =>array($this,'getVersionName'),
            ),
        array(
            'name'=>'操作者',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["operator"]'),
        array(
            'name'=>'创建时间',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["create_time"]'),
        array(
            'name'=>'操作',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => 'CHtml::Button("删除",array("class"=>"btn","id"=>"search_order_num","style"=>"width:40px;height:30px;","onclick"=>"del($data[id] , \'$data[city_name]\')"))'),
     ),
));

echo '<div id="user_remark_dialog"></div>';
echo '<iframe id="user-frame" width="100%" height="100%" style="border:0px"></iframe>';
?>
<div class="search-form" style="display:block">
        <?php $this->renderPartial('_driver_version_add',array('model'=>$model,'version_list'=>$version_list,'city_array'=>$city_array)); ?>
</div>

<script>

$('#btn').click(function(){
    if($("input[name='city[]']:checked").size()==0){
        alert("请先勾取城市！");
        return;
    }
    $('#yw2').submit();
});

function del(id , city_name) {
    if(window.confirm("确定要将城市"+city_name+"版本配置删除？")) {
        url="<?php echo Yii::app()->createUrl('driver/delcityversion'); ?>";
        window.location.href = url+"&id="+id;
    }
}

$(function(){
    //新建发送短信
    $("#add_version").click(function(){
        window.location.href="<?php echo Yii::app()->createUrl('driver/addversion'); ?>";
    });
});


$(function(){
    //新建发送短信
    $("#add_city_version").click(function(){
        window.location.href="<?php echo Yii::app()->createUrl('driver/addcityversion'); ?>";
    });
});

        
$('#che_all').click(function(){
        if($(this).attr("checked")){

                $("input:enabled[name='city[]']").each(function(){
                       $(this).attr("checked","true");
               $('#unche_all').removeAttr("checked");
                });
        }//else{
         //       $("input[name='city[]']").each(function(){
           //            $(this).removeAttr("checked");
             //   });
     //   }
});

$('#unche_all').click(function(){ 
        if($(this).attr("checked")){
                $("input:enabled[name='city[]']").each(function(){
            if($(this).attr("checked")){
                $(this).removeAttr("checked");
                $('#che_all').removeAttr("checked");
            }else{
                $(this).attr("checked","true")
            }
                }); 
        }//else{
           //     $("input[name='city[]']").each(function(){
             //          $(this).removeAttr("checked");
               // }); 
       // }
});

$('.city_id').click(function(){
            if(this.checked==false){
                $('#che_all').attr('checked',false);
            }else if($(".city_id:checked").size()==$('.city_id').length){
                $('#che_all').attr('checked',true);
            }
        });


</script>