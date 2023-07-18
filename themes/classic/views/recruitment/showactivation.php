
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array(
			'name'=>'ID',
			'type' => 'raw',
			'value'=>'$data->id',
        ),
		array(
			'name'=>'报名流水号',
			'type' => 'raw',
			'value'=>'Yii::app()->controller->getRecruitmentQueueNumber($data->id, $data->city_id)',
        ),
		array (
			'name' => 'name',
			'type' => 'raw',
			'value' => '$data->name'
		),
		'mobile',
        'id_card',
		array(
			'name'=>'居住城市',
			'value'=>'Yii::app()->controller->getRecruitmentCity($data->city_id)',
        ),
		array(
			'name'=>'状态',
			'value'=>'Yii::app()->controller->getRecruitmentStatus($data->status)',
		),
		array(
			'name'=>'可否激活',
			'type'=>'raw',
			'value'=>'($data->status == 6) ? "可以激活" : "财务未收款"',
		),
		array(
			'name'=>'取消',
			'type'=>'raw',		
			'value'=>'CHtml::link("取消", "javascript:void(0);", array (
			"class"=>"deluser"))',		
        ), 
	
	),
)); 
?>
<center>
<input type="submit" value="确认激活" class="btn btn-success" id="submit_btn">
</center>
<script>
$(function(){

	var other_id = new Array();
	<?php 
		$tmpData = $dataProvider->getData();
		$i=0;
		foreach ($tmpData as $item){
			
			if($item['status']!=6){?>
			other_id.push('<?php echo $item['id']?>');
			$("tbody tr").eq('<?php echo $i;?>').children('td').css("background","#FF0000");
			<?php }
			$i++;
		}
	?>
	$("#submit_btn").click(function(){
		arrID = new Array();
		num = $(".table tr").length;
		batch=$("#batch").val();
		sms_content = $('#sms_content').val();
		if(other_id.length!=0){ alert("目前有"+other_id.length+"位司机目前不能激活，请重新确认列表里的司机，");return false;}
		for(i=0;i<num;i++){
			if(i!=0){
				id = $(".table tr").eq(i).children('td').eq(0).html();
				arrID.push(id);
			}
		}
		//ajax
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/recruitment/batchactivation');?>',
			'data':'id='+arrID,
			'type':'get',
			'beforeSend':function(){
			},
			'success':function(data){
				if(data==0)
					alert("没有需要激活的司机！");
				else
					alert("已激活。");
				$(window.parent.document).find(".ui-dialog-buttonset button").click();
			},
			'cache':false		
		});
		//ajax end
	});

	
	$(".deluser").click(function(){
		curr_num = $(".table tr").index($(this).parent().parent());
		id = $(this).parent().parent().children('td').eq(0).html();
		other_id.remove(id);
		$(".table tr").eq(curr_num).fadeOut("slow",function(){
			$(".table tr").eq(curr_num).replaceWith('');
		});
	});
}); 

Array.prototype.indexOf = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) return i;
    }
    return -1;
};
Array.prototype.remove = function(val) {
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
};
</script>