<?php
/* @var $this ZhaopinController */
$this->pageTitle = '在线考试  - e代驾';

?>	
<div class="block">
	<div style="height:90px;"></div>
	<div class="page-header">
	<h2>在线考试</h2>
	</div>
<div class="row">
<div class="span6">
<?php 

$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
));

if (empty($exam_list) && $isExam == 0){
	if (isset($reminder)){
		if (!empty($reminder))
			echo $reminder;
	}
?>

<label for="id_card">请输入身份证号</label>
	<input type="text" id="id_card" name="id_card" value="" style="float:left" />&nbsp;
<?php 
echo CHtml::submitButton('开始考试',array('class'=>'btn'));
} 
else 
{
	echo "<H4>您报名的身份证号是：" . $id_card . "</H4>";
	echo "<h4 id = 'error_info' style='color:#CC0000;'>",$prompt,"</h4>";
	
}
?>
</div>
<input type='text' style='display:none;' id='id_cards' name='id_cards' value='<?php echo $id_card;?>'/>
</div>
<?php 
if (!empty($exam_list) && $isExam == 2)
{
	
	foreach ($exam_list as $qkey => $q_list){
		
?>

<div class="row">
<div class="span9">
<H4><?php echo $qkey+1,'、',$q_list['title'];?></H4>
</div>
</div>
<?php

		$item_list = array('A','B','C','D','E','F','G','H','I','J','K','L');
		$content_list = json_decode($q_list['contents'],true);
		//shuffle($content_list);
		
		if (!empty($content_list)){
			foreach ($content_list as $key => $e_list){
	
				if (!empty($e_list)){
					echo '<input type="checkbox" value="',$item_list[$key],'" name="',$q_list['id'],'[]" id="q_',$qkey,'_',$item_list[$key],'"/> ',$e_list,'<br>';
				}
				
			}
		}
	}
?>
<div>
<?php 
	echo '<br>';
	//echo '<input type="text" style="display:none;" id="q_num" value="',$q_numd,'" />';
	echo CHtml::button('完成考试',array('id'=>'sub_btn'));
}
?>
</div>
<?php
if (!empty($pass) && $pass){
	echo "<h2>恭喜您通过考试，公司将以短信形式通知面试和路考，请耐心等待。</h2>";
	echo '<input type="hidden" name="examagain" value="1" />';
	echo CHtml::submitButton('重考',array('class'=>'btn'));
}
if (!empty($arrQuestion) && $isExam == 3){
    echo '<h4>您一共答对' . $exam_true_count . '道，答错'. $exam_error_count .'道。请仔细阅读答错题目的正确答案。</h4>';
	$q_num = 1;
	foreach ($exam_lists as $qkey => $q_lists){
		
?>
<div class="row">
<div class="span9">
<?php
if (isset($arrQuestion[$qkey]['istrue']) && $arrQuestion[$qkey]['istrue'] == '0'){

?>
<!-- <H4><?php //echo $q_num,'、',$exam_lists[$qkey]['title'];?></H4> -->
<?php
} else {?>
<H4><?php
$item_list = array('A'=>0,'B'=>1,'C'=>2,'D'=>3,'E'=>4,'F'=>5,'G'=>6,'H'=>7,'I'=>8,'J'=>9,'K'=>10,'L'=>11);
$error_correct = explode(',', $arrQuestion[$qkey]['error_correct']);
$contents = json_decode($exam_lists[$qkey]['contents'],true);
$exam_type = $exam_lists[$qkey]['type'] == 0 ? 'radio' : 'checkbox';
$exam_id = $exam_lists[$qkey]['id'];
$correct = explode(',',$q_lists['correct']);
$exam_correct = '';
if(count($correct)>1){
	foreach ($correct as $item){
        if (isset($contents[$item_list[$item]])) {
		    $exam_correct.= ' ['.$contents[$item_list[$item]].']， ';
        }
	}
}else{
	$exam_correct.= ' ['.$contents[$item_list[$q_lists['correct']]].']';
}

//$correct_question =  $contents[$item_list[$q_lists['correct']]];

    echo $q_num,'、',$exam_lists[$qkey]['title'],'   ','<br>';
   		foreach ($contents as $key => $option){
            $_answer = array_search($key, $item_list);
            if (in_array($_answer, $driver_answer[$exam_id])) {
                echo "<input type='".$exam_type."' checked='checked'/>".$option."<br>";
            } else {
                echo "<input type='".$exam_type."' />".$option."<br>";
            }
        }
    echo '<font color="red">正确答案是:',$exam_correct,'</font>';


?></H4>
<?php 
}

?>

</div>
</div>
<?php
	
	
// 		foreach ($contents as $key => $e_list){
// 			$str_checkbox = '<input type="checkbox" ';
// 			if (in_array($item_list[$key], $error_correct)){
// 				$str_checkbox .= 'checked="true" ';
// 			}
// 			$str_checkbox .= ' value="'.$item_list[$key].'" name="'.$exam_lists[$qkey]['id'].'[]" id="q_'.$qkey.'_'.$item_list[$key].'"/> '.$item_list[$key].'、'.$e_list.'<br>';
// 			echo $str_checkbox;
// 		}

		$q_num++;
	}
	//echo '<br>';
	//echo '<div id = "exam">总共：30道题，答对' . $exam_true_count . '道，答错'. $exam_error_count .'道。</div>';

	//echo '<br>';
?>
<input type="hidden" name="examagain" value="1" />
<?php 
	echo CHtml::submitButton('重考',array('class'=>'btn'));
?>
<div>
<?php
}
	
$this->endWidget();
?>
</div>
<script language='javascript' type='text/javascript'>
$('#sub_btn').click(function(){
	var txt = $('#q_num').val();
	var str_txt = txt.split(',');
	
	
	for(var i=0;i<str_txt.length-1;i++){
		var ids = document.getElementsByName(''+str_txt[i]+'[]');
		var flag = false;
		
		for(var j=0;j<ids.length;j++){
			if(ids[j].checked){
				flag = true;
				break;
			}
		}

		if(!flag){
			alert('请全部完成后再点击按钮');
			return false;
		}
	}

	$('#yw0').submit();
	
});
</script>