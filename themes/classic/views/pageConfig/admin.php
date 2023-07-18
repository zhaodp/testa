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
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'view_city_dialog',
    'options' => array(
        'title' => '适用城市',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#view_city_dialog").dialog("close");} '
        ),
    ),      
));         
echo '<div id="view_city_div"></div>';
echo '<iframe id="cru-frame-view-city" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h3>客户端页面分享配置</h3>
<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'tc-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '活动标题',
                'value' =>'$data->title'
            ),
            array(
                    'name' => '活动时间',
                    'value' =>array($this, 'getActTime')
                ),
            array(
                    'name' => '订单有效时间',
                    'value' =>array($this, 'getOrderTime')
                ),
            array(
                    'name' => '页面地址',
                    'type'=>'raw',
                    'value'=>array($this,'getUrl')
             ),
            array(
                'name' => '触发时机',
                'type'=>'raw',
                'value'=>array($this,'getTriggerTime')
            ),
            array(
                    'name' => '适用地区',
                    'headerHtmlOptions'=>array (
                        'width'=>'80px',
                        'nowrap'=>'nowrap'
                     ),
                    'type'=>'raw',
                    'value'=>array($this,'getCityName')
                ),
            array(
                    'name' => '弹窗次数',
                    'value' =>'$data->pop_times'
                ),
                array(
                    'name' => '分享人数',
                    'value' =>'$data->share_times'
                ),
            array(
                    'name' => '页面访问次数',
                    'value' =>'$data->visit_times'
                ),
            array(
                    'name' => '操作',
                    'value' => array($this, 'enablePageConfig')
                ),
       ),
    ));?>
<div class="search-form" style="display:block">
        <?php $this->renderPartial('create',array('model'=>$model,)); ?>
</div>

<script type="text/javascript">
$('#btn').click(function(){
    if($("input[name='trigger_time[]']:checked").size()==0){
        alert("请先选择弹窗时间点！");
        return;
    }
	var title = $("#PageConfig_title").val();
	if(title == ''){
        alert('活动标题不能为空');
        retrun;
    }
	var begintime = $("#PageConfig_begintime").val();
	if(begintime == ''){
		alert('活动开始时间不能为空');
		retrun;
	}
	var endtime = $("#PageConfig_endtime").val();
	if(endtime == ''){
        alert('活动结束时间不能为空');
         retrun;
    }
	var order_begin = $("#PageConfig_order_begin").val();
	if(order_begin == ''){
        alert('订单有效开始时间不能为空');
        retrun;
   }
	var order_end = $("#PageConfig_order_end").val();
        if(order_end == ''){
            alert('订单有效结束时间不能为空');
            retrun;
        }
	if($("#PageConfig_url").val()==''){
        alert('页面地址不能为空');
        retrun;
     }else if($("#PageConfig_url").val().indexOf('http://')==-1){
		alert('页面地址应该以http://开头');
        retrun;
	}
	if($("input[name='city[]']:checked").size()==0){
		alert("请先勾取城市！");
		return;
	}
	
	var city_ids = ''; 
  	$('input[name="city[]"]:checked').each(function(){ 
        city_ids+=$(this).val()+',';
  	}); 
	$.ajax({
            'url':'<?php echo Yii::app()->createUrl('/pageConfig/CheckAct');?>',
            'data':{begintime:begintime,endtime:endtime,city_ids:city_ids},
            'type':'post',
            'success':function(data){
               if(data == '0'){
                   $('#yw1').submit();
               }else{
                   alert(data);
               }
            },
            'cache':false
        });
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
function viewCityDialogdivInit(href) {
    $("#cru-frame-view-city").attr("src", href);
    $("#view_city_dialog").dialog("open");
    return false;
}

</script>
