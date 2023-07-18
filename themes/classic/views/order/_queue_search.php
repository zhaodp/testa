<div class="well" style="margin:0px;padding:8px 0 0 8px;">
    
<style>
    .hide_overflow {height:25px;overflow:hidden;}
</style>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('style'=>'margin:0px;'),
)); ?>
<div class="row">
	<div class="span12">
	    <div class="input-prepend input-append">
			<span class="add-on""><?php echo $form->label($model,'phone'); ?></span>
			<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20,'class'=>"span2", 'name' => 'OrderQueue[phone]')); ?>
			<span class="add-on""><?php echo $form->label($model,'address'); ?></span>
			<?php echo $form->textField($model,'address',array('size'=>20,'maxlength'=>20,'class'=>"span2", 'name' => 'OrderQueue[address]')); ?>

			<?php echo CHtml::submitButton('搜索',array('class'=>'btn')); ?>
	    </div>
	</div>
    <div class="span12">
        <label style="float:left;padding-right:15px;">派单状态:</label>
        <?php
           $flags =  array(
                '0'=>'等待派单',
                '1'=>'已发调度',
                //'2'=>'正在派单',
                '3'=>'取消',
                '4'=>'派单完成',
            );
        echo $form->checkBoxList($model,'flag',$flags,array('checkAll'=>'全部','uncheckValue'=>'','template'=>"{input}{label}\n",'separator'=>'','labelOptions'=>array('class'=>'checkbox inline','style'=>'margin:0px;padding:0px 2px 2px 0px;'), 'name' => 'OrderQueue[flag]'));
        ?>
    </div>
    <div id="search_div_city" class="span12 hide_overflow">
        <label style="float:left;padding-right:15px;" onclick="$('#search_div_city').toggleClass('hide_overflow');">订单城市<i class="icon-chevron-down"></i>:</label>
		<?php
//			$citys = Dict::items('city');
            $citys = RCityList::model()->getOpenCityList();    //修改为获取已开通城市列表 2014-3-12
			unset($citys[0]);
			echo $form->checkBoxList($model,'city_id',$citys,array('checkAll'=>'全部','uncheckValue'=>'','template'=>"{input}{label}\n",'separator'=>'','labelOptions'=>array('class'=>'checkbox inline','style'=>'margin:0px;padding:0px 2px 2px 0px;'), 'name' => 'OrderQueue[city_id]'));
			//将checkbox设为checked
			Yii::app()->clientScript->registerScript('checkAll_city_id', "jQuery(\"input[name='OrderQueue\[city_id\]\[\]']\").prop('checked', true);jQuery('#OrderQueue_city_id_all').prop('checked',true);");
		?>
    </div>
    <div id="search_div_diaodu" class="span12 hide_overflow">
		<label style="float:left;padding-right:15px;" onclick="$('#search_div_diaodu').toggleClass('hide_overflow');">调度人员<i class="icon-chevron-down"></i>:</label>
		<input type="button" value="刷新" name="refash" onclick="javascript:location.reload(true);" class="btn btn-danger">
		<?php 
		//查询 7:00到第二天7:00所有的调度名字
		
//		$begin_time = date('Y-m-d 07:00:00', time()-7*60*60);
//		$end_time = date('Y-m-d H:i:s', time()+2*86400);
		$begin_time = $model->begin_booking_time;
		$end_time = $model->end_booking_time;
		
//		$sql = "select distinct agent_id 
//				from t_order_queue
//				where booking_time >= '$begin_time'
//				order by agent_id";
//		$all_agents = Yii::app()->db_readonly->createCommand($sql)->queryAll();
		// 改用cache展示
                $all_agents = AdminCache::model()->agent_id_list();

		$agents = array('客户自助'=>'客户自助');
		foreach($all_agents as $item){
			$agents[$item] = $item;
		}
		echo $form->checkBoxList($model,'agent_id',$agents,array('checkAll'=>'全部','uncheckValue'=>'','template'=>"{input}{label}\n",'separator'=>'','labelOptions'=>array('class'=>'checkbox inline','style'=>'margin:0px;padding:0px 2px 2px 0px;'), 'name' => 'OrderQueue[agent_id]'));
		Yii::app()->clientScript->registerScript('checkAll_agent_id', "jQuery(\"input[name='OrderQueue\[agent_id\]\[\]']\").prop('checked', true);jQuery('#OrderQueue_agent_id_all').prop('checked',true);");

		?>	
    </div>
    <div class="span12">
		<label style="float:left;padding-right:15px;">客户类型:</label>
		<?php
			echo $form->checkBoxList($model,'is_vip',array('1'=>'只看VIP'),array('uncheckValue'=>'','template'=>"{input}{label}\n",'separator'=>'','labelOptions'=>array('class'=>'checkbox inline','style'=>'margin:0px;padding:0px 2px 2px 0px;'), 'name' => 'OrderQueue[is_vip]'));
		?>
    </div>

</div>

<?php $this->endWidget(); ?>

</div>
<script type="text/javascript">
$("#OrderQueue_city_id_all").click(function(){
    if($(this).attr('checked') == 'checked') {
        $(this).val('1');
    }
    else {
        $(this).val('0');
    }
});
$("#OrderQueue_agent_id_all").click(function(){
    if($(this).attr('checked') == 'checked') {
        $(this).val('1');
    }
    else {
        $(this).val('0');
    }
});
</script>
