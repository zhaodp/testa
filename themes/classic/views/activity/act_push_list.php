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
<h3>活动推送配置</h3>
<h5>推送列表</h5>
<?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'tc-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '标题',
                'value' => '$data->title'
            ),
            array(
                'name' => '内容',
                'value' => '$data->content'
            ),
            array(
                'name' => '链接',
                'value' =>'$data->url'
            ),
        array(
                'name' => '城市',
                'headerHtmlOptions'=>array (
                                'width'=>'80px',
                                'nowrap'=>'nowrap'
                 ),
                'type'=>'raw',
                'value'=>array($this,'getCitys')
            ),
        array(
                'name' => '客户类型限制',
                'value' =>'MarketingActivity::$customers[$data->customer_type]'
            ),
        array(
                'name' => '适用平台',
                'value' =>'MarketingActivity::$platforms[$data->platform]'
            ),
        array(
                'name' => '适用版本',
                'value' =>'empty($data->app_ver)?"所有版本":$data->app_ver'
            ),
        array(
                'name' => '推送时间',
                'value' =>'$data->push_time'
            ),
        array(
                'name' => '状态',
                'value' =>'ActPush::$status[$data->status]'
            ),
        
        array(
                'name' => '操作',
                'value' => array($this, 'enablePush')
            ),
       ),
    ));?>
<div class="search-form" style="display:block">
        <?php $this->renderPartial('_add_push_act',array('model'=>$model,)); ?>
</div>

<script type="text/javascript">
$('#btn').click(function(){
    if($("#ActPush_content").val()==''){
                alert('活动内容不能为空');
                retrun;
        }
    if($("#ActPush_url").val()!=''){
        if($("#ActPush_url").val().indexOf('http://')==-1){
            alert('活动地址应该以http://开头');
            retrun;
        }
    }
    if($("#ActPush_app_ver").val()!='' && !/^(?:\d+\.)+\d+$/.test($('#ActPush_app_ver').val())){
                    alert('请输入正确的版本格式！');
                    return false;
                }
    /**if($("#MarketingActivity_version").val()==''){
                alert('适用版本不能为空');
                retrun;
        }**/
    if($("input[name='city[]']:checked").size()==0){
        alert("请先勾取城市！");
        return;
    }
    $('#yw1').submit();
})
        
$('#che_all').click(function(){
        if($(this).attr("checked")){
                $("input[name='city[]']").each(function(){
                       $(this).attr("checked","true");
               $('#unche_all').removeAttr("checked");
                });
        }//else{
         //       $("input[name='city[]']").each(function(){
           //            $(this).removeAttr("checked");
             //   });
     //   }
})

$('#unche_all').click(function(){ 
        if($(this).attr("checked")){
                $("input[name='city[]']").each(function(){
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
})

$('.city_id').click(function(){
            if(this.checked==false){
                $('#che_all').attr('checked',false);
            }else if($(".city_id:checked").size()==$('.city_id').length){
                $('#che_all').attr('checked',true);
            }
        });

</script>
