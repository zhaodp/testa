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
$this->pageTitle = '恶劣天气高峰奖励补贴';
echo "<h1>".$this->pageTitle."</h1><br />";
?>

<div class="well span12">
<?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl("driver/weather"),'method' => 'post',)); ?>
   
    <div class="span12">
    	<div>
            <?php echo CHtml::label('奖励日期','');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'weather_day',
                'value'=>'',
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"",
                ),


            ));?>
        </div>
        <div>
            <?php echo "奖励地区" ?>
            <input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;<input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选
            <br><br>
            <?php
            $citys = RCityList::model()->getOpenCityList();
            foreach ($citys as $key=>$item){
                $checked = false;
                echo CHtml::checkBox("city[]",$checked,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
            }

            ?>
        </div>
    <div>
<a class="btn btn-info" href="javascript:;" id="btn">确认提交</a>
</div>

</div>
<?php $this->endWidget(); ?>

</div>
</div>
<?php
$this->pageTitle = '奖励列表';
echo "<h1>".$this->pageTitle."</h1><br />";
?>
<div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'score-driver-grid',
    'cssFile' => SP_URL_CSS . 'table.css',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
//	'filter'=>$model,
    'columns' => array(

        array (
            'name'=>'id',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->id',
        ),
        array('name'=>'城市','value'=>array($this,'getCityName')),
        array (
            'name'=>'奖励日期',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->weather_day',
        ),
        array (
            'name'=>'是否已执行',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->status==0?"否":"是"',
        ),
         array (
            'name'=>'创建时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->create_time',
        ),
        array (
            'name'=>'执行时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->status==0?"":$data->update_time',
        ),
        array (
            'name'=>'操作人',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->operator',
        ),
    ),
)); ?>
</div>

<script>

$('#btn').click(function(){
	if($("input[name='city[]']:checked").size()==0){
	alert("请先勾取城市！");
	return;
	}
    $weather_day=$('#weather_day').val();
    if($weather_day==''){
        alert('请选择奖励日期');
        return;
    }

	$('#yw0').submit();
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
