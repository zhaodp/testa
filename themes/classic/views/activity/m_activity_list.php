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
<h3>市场活动配置</h3>
<h5>未结束市场活动</h5>
<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'tc-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '状态',
                'value' => '$data->begintime<date("Y-m-d H:i:s",time())?"进行中":"排队中"'
            ),
            array(
                'name' => '活动标题',
                'value' =>'$data->title'
            ),
	    array(
                'name' => '开始时间',
                'value' =>'$data->begintime'
            ),
		array(
                'name' => '结束时间',
                'value' =>'$data->endtime'
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
                'name' => '新老客户限制',
                'value' =>'MarketingActivity::$customers[$data->customer]'
            ),
            array(
                'name' => '适用平台',
                'value' =>'MarketingActivity::$platforms[$data->platform]'
            ),
	    array(
                'name' => '适用版本',
                'value' =>'empty($data->version)?"所有版本":$data->version'
            ),
	    array(
                'name' => '页面预览',
		'type'=>'raw',
                'value'=>array($this,'getUrl')
            ),
	    array(
            	'name' => '操作',
            	'value' => array($this, 'enableActivity')
            ),
       ),
    ));?>
<div class="search-form" style="display:block">
        <?php $this->renderPartial('_add_m_activity',array('model'=>$model,)); ?>
</div>

<script type="text/javascript">
$('#btn').click(function(){
	if($("#MarketingActivity_begintime").val()==''){
		alert('活动开始时间不能为空');
		retrun;
	}
	 if($("#MarketingActivity_endtime").val()==''){
                alert('活动结束时间不能为空');
                retrun;
        }
	if($("#MarketingActivity_title").val()==''){
                alert('活动标题不能为空');
                retrun;
        }
	if($("#MarketingActivity_url").val()==''){
                alert('活动地址不能为空');
                retrun;
        }else if($("#MarketingActivity_url").val().indexOf('http://')==-1){
		alert('活动地址应该以http://开头');
                retrun;
	}
	if($("#MarketingActivity_version").val()!='' && !/^(?:\d+\.)+\d+$/.test($('#MarketingActivity_version').val())){
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
