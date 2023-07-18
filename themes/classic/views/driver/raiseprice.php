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
$this->pageTitle = '恶劣天气调价方案管理';
echo "<h1>".$this->pageTitle."</h1><br />";
?>

<div class="well span12">
<?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl("driver/raiseprice"),'method' => 'post',)); ?>
   
    <div class="span12">
    	<div class="row_fluid">
          <div class="span3">
            <?php echo CHtml::label('开始时间','');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $now = date('Y-m-d H:i');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'start_time',
                'value'=>$now,
                'mode'=>'datetime',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd '
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"",
                ),


            ));?>
          </div>
          <div class="span3">
            <?php echo CHtml::label('结束时间','');?>
            <?php
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'end_time',
                'value'=>'',
                'mode'=>'datetime',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd '
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"",
                ),


            ));?>
          </div>
        </div>
        </div>
        <div>
            <?php echo "加价地区" ?>
            <input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;<input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选
            <br><br>
            <?php
            $citys = RCityList::model()->getOpenCityList();
            foreach ($citys as $key=>$item){
                $checked = false;
                echo CHtml::checkBox("city[]",$checked,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
            }

            ?>
      <div >
          <?php echo "App提示短信: "?>
          <input name="app_message" id="app_message" value="" size="70" maxlength=70>
      </div>
      <br/>
      <div >
          <?php echo "接单提示短信: "?>
          <input name="offer_message" id="offer_message" value="" size="70" maxlength=70>
      </div>
      <br/>
      <div>
          <?php echo "加价方案:"?>
          <input type="radio" name="add_price" id="add_price" value="39" checked> 39元
          <input type="radio" name="add_price" id="add_price" value="59"> 59元
          <input type="radio" name="add_price" id="add_price" value="79"> 79元
          <input type="radio" name="add_price" id="add_price" value=""> 自定义
          <input name="my_price" id="my_price" value="" size="3">
      </div>
      <br/>

      
        </div>
      
    <div>
<a class="btn btn-info" href="javascript:;" id="btn">确认提交</a>
</div>

</div>
<?php $this->endWidget(); ?>

</div>
</div>
<?php
$this->pageTitle = '加价列表';
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
        array(
            'name'=>'城市',
            'headerHtmlOptions'=>array (
                'style'=>'width:160px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'getCityName')
          ),
        array (
            'name'=>'开始时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:160px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->start_time',
        ),
        array (
            'name'=>'结束时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:160px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->end_time',
        ),
        array (
            'name'=>'加价金额',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->add_price."元"',
        ),
        array (
            'name'=>'状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->status==0?"启用":"停止"',
        ),
        /*
         array (
            'name'=>'创建时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:130px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->create_time',
        ),
        */
        array (
            'name'=>'操作',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value' => 'CHtml::Button("切换",array("class"=>"btn","id"=>"start_raise","style"=>"width:40px;height:30px;","onclick"=>"operate($data[id] , \'$data[status]\')"))',
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
  $start_time = $('#start_time').val();
  if($start_time == ''){
    alert('请选择开始日期');
    return;
  }
  $end_time =$('#end_time').val();
  if($end_time == ''){
    alert('请选择结束日期');
    return;
  }

  $('#yw0').submit();
});



function operate(id , status) {
  if(status == 1){
    if(window.confirm("确定要开启恶劣天气加价？")) {
        url="<?php echo Yii::app()->createUrl('driver/changeraiseprice'); ?>";
        window.location.href = url+"&id="+id+"&status=0";
    }
  }else{
    if(window.confirm("确定要关闭恶劣天气加价？")) {
        url="<?php echo Yii::app()->createUrl('driver/changeraiseprice'); ?>";
        window.location.href = url+"&id="+id+"&status=1";
    }
  }
}

$('#che_all').click(function(){
	if($(this).attr("checked")){

	$("input:enabled[name='city[]']").each(function(){
	    $(this).attr("checked","true");
	    $('#unche_all').removeAttr("checked");
	    });
	}
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
