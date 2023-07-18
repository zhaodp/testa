<?php
$t = empty($_GET['t'])? 0:$_GET['t']; //时间
$str_date = array('0'=>'昨日','7'=>'上周','30'=>'上月');
$num = 1;
echo '<h2>'.$str_date[$t];
echo $t==0 ?'（'.date('m.d H:i',$dateBegin).' -- '.date('m.d H:i',$dateEnd).'）' : '（'.date('m.d',$dateBegin).' -- '.date('m.d',$dateEnd).'）';
echo '订单数据汇总</h2>';
echo '<div class="btn-group">';
echo $t == 0? CHtml::link('昨日订单','javascript:;',array('class'=>"search-button btn-primary btn",'onclick'=>'order(0)')):CHtml::link('昨日订单','javascript:;',array('class'=>"btn",'onclick'=>'order(0)'));
//echo $t == 7? CHtml::link('上周订单',array("notice/index","category"=>0, "t" =>7),array('class'=>"search-button btn-primary btn")):CHtml::link('上周订单',array("notice/index","category"=>0, "t" =>7),array('class'=>"btn"));
echo $t == 30? CHtml::link('上月订单','javascript:;',array('class'=>"search-button btn-primary btn",'onclick'=>'order(30)')):CHtml::link('上月订单','javascript:;',array('class'=>"btn",'onclick'=>'order(30)'));
echo '</div>';
?>
<div id="search_from">
<div class="span3">
	<select name="city_id" id="city_id">
		<option value="1">北京</option>
		<option value="3">上海</option>
		<option value="4">杭州</option>
		<option value="5">广州</option>
		<option value="6">深圳</option>
		<option value="7">重庆</option>
	</select>
</div>
<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
</div>
<?php
if(!empty($count['driver_count'])){
	if($t == 0){
		echo '<p><h3>'.Dict::item('city', $city_id).'订单总数:<font color="red">'.$count['driver_order_count'].'</font>单，接单司机人数：<font color="red">'.$count['driver_count'].'</font>,    平均接单数：'. sprintf("<font color='red'>%1\$.1f</font>单/人",$count['driver_order_count']/$count['driver_count']/$count['ang_day']).'</h3></p>';
	}else{
		echo '<p><h3>'.Dict::item('city', $city_id).'订单总数:<font color="red">'.$count['driver_order_count'].'</font>单，接单司机人数：<font color="red">'.$count['driver_count'].'</font>,    平均上线天数： '.sprintf("<font color='red'>%1\$.1f</font>天/人",$count['ang_day']).'    平均每天接单数：'. sprintf("<font color='red'>%1\$.1f</font>单/人",$count['driver_order_count']/$count['driver_count']/$count['ang_day']).'</h3></p>';
	}
}
echo '<h2>排行榜</h2>';
echo '<div class="grid-view"><table class="table table-striped">
<thead>
<tr>
<th width="10px">序号</th><th width="60px">司机姓名</th><th width="60px">司机工号</th>';
echo $t == 0? '':'<th width="60px">出勤天数</th>';
echo '<th width="60px">总接单量</th><th width="60px">呼叫中心派单量</th><th width="60px">客户直接呼叫量</th><th width="60px">收入</th></tr>
</thead>
<tbody>';
if(!empty($rank_List)){
	foreach($rank_List as $data)
	{
		echo '<tr><td width="10px">'.$num++.'</td><td>'.$data->name.'</td><td>'.$data->driver_id.'</td>';
		echo $t == 0? '':'<td width="60px">'.$data->order_date.'</td>';
		echo '<td>'.$data->order_id.'</td><td>'.$data->distance.'</td><td>'.$data->charge.'</td><td>'.$data->income.'</td></tr>';
	}
}else{
	echo '<tr><td class="empty" colspan="7"><span class="empty">没有找到数据.</span></td></tr>';
}
echo '</tbody>
</table>';
echo '<p>&nbsp<span style="float:right;">';
echo $t==0 ? CHtml::link('更多司机',"javascript:void(0);",array("onclick"=>"{openDialog_rank('index.php?r=notice/rank&city_id=$city_id&t=0');}")):CHtml::link('更多司机',"javascript:void(0);",array("onclick"=>"{openDialog_rank('index.php?r=notice/rank&city_id=$city_id&t=$t');}"));
echo '</span></p>';

$dialog_title = $t==0 ?'日统计排行榜' : '月统计排行榜';
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>$dialog_title, 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){closedDialog_rank("view_exam_dialog")}'))));
echo '<div id="view_exam_dialog"></div>';
echo '<iframe id="view_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<script>
$(document).ready(function(){
	$("#city_id").val(request("city_id"));
});
$(".btn-success").click(function(){
	var url = "/v2/index.php?r=notice/ranking";
	var t=request("t");
	var city_id=$("#city_id").val();
	url +="&t="+t+"&city_id="+city_id;
	location.href =url;
})
function order(t){
	var url = "/v2/index.php?r=notice/ranking";
	var city_id=request("city_id");
	url +="&t="+t;
	if(city_id!="")
		url +="&city_id="+city_id;
	location.href =url;
}
function request(paras)
{ 
    var url = location.href; 
    var paraString = url.substring(url.indexOf("?")+1,url.length).split("&"); 
    var paraObj = {} 
    for (i=0; j=paraString[i]; i++){ 
    paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length); 
    } 
    var returnValue = paraObj[paras.toLowerCase()]; 
    if(typeof(returnValue)=="undefined"){ 
    return ""; 
    }else{ 
    return returnValue; 
    } 
}
function closedDialog_rank(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
}
function openDialog_rank(url){
	$("#view_exam_frame").attr("src",url);
	$("#view_exam_dialog").dialog("open");
	return false;
}
</script>