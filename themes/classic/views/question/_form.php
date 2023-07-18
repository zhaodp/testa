<?php
/* @var $this QuestionController */
/* @var $model Question */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'question-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class='grid-view'>
	<?php echo $form->errorSummary($model); ?>
		
		<div style='display:none;'>
			<?php echo $form->labelEx($model,'type'); ?>
			<?php echo $form->dropDownList($model, 'type', array('0'=>'单选','1'=>'多选'));?>
			<?php echo $form->error($model,'type'); ?>
		</div>

		<?php echo $form->labelEx($model, 'question_type');?>
		<?php echo $form->dropDownList($model, 'question_type', array('1'=>'调查问卷','2'=>'司机考卷'));?>
		<?php echo $form->error($model,'question_type');?>
		
		<?php
			if ($model->question_type == '2'){
		?>
		<div id='hiden_div'>
		<?php 
		}else {
		?>
		<div id='hiden_div' style='display:none;'>
		<?php 
		}
		?>

        <?php //echo $form->labelEx($model, 'compliant')?>
		<?php //echo $form->dropDownList($model, 'compliant', array('0'=>'全部','1'=>'新司机','2'=>'老司机'));?>
		<?php //echo $form->error($model,'compliant');?>

        <?php //echo $form->labelEx($model, 'city_id');?>
		<?php //echo $form->dropDownList($model, 'city_id', Dict::items('city'))?>

		
		<?php echo $form->labelEx($model, 'track');?>
        <?php
            if (Yii::app()->user->city == 0) {
                $track = array('0'=>'请选择','1'=>'服务规范','2'=>'交通规则','3'=>'地理地图','4'=>'费用计算','5'=>'财务制度','6'=>'VIP','7'=>'优惠券使用','8'=>'奖罚制度','9'=>'手机应用','10'=>'报单');
            } else {
                $track = array('3'=>'地理地图');
            }
        ?>
		<?php echo $form->dropDownList($model, 'track', $track);?>
		<?php echo $form->error($model,'track')?>
		</div>
        <div id="city_container" style="display: none">
        <label for="Question_city_id">适用城市：</label>
		<?php
            $citys = Dict::items('city');
            $disabled = Yii::app()->user->city == 0 ? '' : "return false";
            foreach ($citys as $key=>$item){
                //分公司只有分公司所在地被选中，总部全部选中
                $checked = '';
                $id = '';
                if ($this->getAction()->getId() == 'create') {
                    if (Yii::app()->user->city == $key || Yii::app()->user->city == 0) {
                        $checked = 'checked="checked"';
                    }
                } else if ($this->getAction()->getId() == 'update') {
                    $model_city_str = $model->city_id;
                    $model_city_arr = explode(',', $model_city_str);
                    foreach ($model_city_arr as $k=>$v) {
                        if ($v == '' || $v==null) {
                            unset($model_city_arr[$k]);
                        }
                    }
                    if (in_array($key, $model_city_arr)) {
                        $checked = 'checked="checked"';
                    }
                }
                if ($key == 0) {
                    $id = 'id="Question_city_id"';
                }
                if (Yii::app()->user->city != 0) {
                    echo '<input type="checkbox" name="Question[city_id][]" onclick="return false;" value="'.$key.'" '.$checked.' '.$id.'/>'.$item."&nbsp;";
                } else {
                    echo '<input type="checkbox" name="Question[city_id][]" value="'.$key.'" '.$checked.' '.$id.'/>'.$item."&nbsp;";
                }
            }
		?>
        </div>
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textArea($model, 'title',array('cols'=>50,'rows'=>2,'maxlength'=>255));?>
		<?php echo $form->error($model,'title'); ?>
		
		
		<?php //echo $form->labelEx($model, 'status');?>
		<?php //echo $form->dropDownList($model, 'status', array('0'=>'正常','1'=>'屏蔽'));?>
		<?php //echo $form->error($model,'status');?>
	
	<div>
		<label for="CustomerQuestion_contents">选项列表 <a href="javascript:;" onclick="add_list();">添加选项</a></label>
		<div id="item_list">
		<?php
			$item = array('A','B','C','D','E','F','G','H','I','J');
			$list_contents = json_decode($model['contents']);
			$count = count($list_contents);
			if($count>0){
				foreach ($list_contents as $k=>$content){
		?>
			<div id="list_<?php echo $k;?>" class="customer_list">
				<label for="CustomerQuestion_contents"><?php echo $item[$k];?>选项</label>
				<input type="checkbox" name="ischecked[]" value="<?php echo $item[$k];?>" />&nbsp;&nbsp;
				<input style="width:480px;" size="80" maxlength="200" name="item[]"  type="text" value="<?php echo $content;?>">
				<?php if($k>0){?>[<a href='javascript:;' onclick='remove_list(<?php echo $k;?>)'>去除</a>]<?php }?>
			</div>
		<?php }}else{?>
			<div id="list_0" class="customer_list">
				<label for="CustomerQuestion_contents">A选项</label>	
				<?php 
					if(isset($model->question_type)&&$model->question_type==2){
						echo '<input type="checkbox" name="ischecked[]" value="A" />&nbsp;&nbsp;';
					}
				?>
				<input size="80" style='width:480px;' maxlength="200" name="item[]"  type="text" value="">
			</div>
		<?php }?>
		</div>
	</div>
	<?php
		if ($model->question_type == '2') {
	?>
	<div id='answer' style='display:none;'>
	<?php 
		}else {
	?>
	<div id='answer' style='display:none;'>
	<?php 
		}
	?>
	<?php echo $form->labelEx($model, 'correct')?>
	<?php echo $form->textField($model, 'correct');?>答案多个的话请用逗号","分开
	</div>
	<div class="buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '更新'); ?>
		<?php echo CHtml::button('取消',array('onclick'=>'window.open("'.Yii::app()->createUrl('question/index').'","_self","param")'));?>
		<?php 
			if ($model->isNewRecord != 1){
				echo CHtml::submitButton('更新并转至下一题');
			}
		?>
		
	</div>
</div><?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
function add_list(){
	var Param = new Array('A','B','C','D','E','F','G','H','I');
	var list_len = $(".customer_list").length;
	var Serial = Param[list_len];
    var item_length = jQuery('#item_list').children('.customer_list').length;
    if (item_length < 4) {
	var str = "<div id='list_"+list_len+"' class='customer_list'>";
	str += "<label>"+Serial+"选项</label>";	
	if(question_type_value==2){
		str += "<input type='checkbox' name='ischecked[]' value='"+Serial+"' />&nbsp;&nbsp;&nbsp;"
	}	
	str += "<input style='width:480px;' size='80' maxlength='200' name='item[]'  type='text' value=''> [<a href='javascript:;' onclick='remove_list("+list_len+")'>去除</a>]";
	str += "</div>";
	$("#item_list").append(str);
    } else {
        alert('选项最多为四项');
    }
}
function remove_list(id){
	$("#list_"+id).remove();
}
var question_type_value = '<?php if(isset($model->question_type)){ echo $model->question_type;}else{ echo 2;}?>';
$(document).ready(function(){

	$('#Question_question_type').change(function(){
		var select_value = $('#Question_question_type').val();
		if (select_value == '1'){
			question_type_value=1;
			$('#hiden_div').hide();
			$('#answer').hide();
			$("input[name='ischecked[]']").remove();
		}else{
			question_type_value=2;
			$('#hiden_div').show();
			$('#answer').hide();
			$("input[name='item[]']").eq(0).before('<input type="checkbox" name="ischecked[]" value="A" />&nbsp;&nbsp');
		}
	});

    jQuery('#question-form').submit(function(){
        var track = jQuery('#Question_track').val();
        if (question_type_value==2) {
            if (track==0) {
                alert('请选择题目类型');
                return false;
            }
            var answer = jQuery('input[name="ischecked[]"]:checked').length;
            if (answer > 1) {
                jQuery('#Question_type').attr('value', 1);
            } else if (answer == 1) {
                jQuery('#Question_type').attr('value', 0);
            } else {
                alert('请设置正确答案');
                return false;
            }
        }
        if (jQuery('#Question_title').val() == '') {
            alert('请输入标题');
            return false;
        }
    });


	var citys_id = new Array();
	
	model_citys_id = '<?php if(isset($model->city_id)){ echo $model->city_id;}?>';
	if(model_citys_id!=''&&model_citys_id!=0){
		citys_id = model_citys_id.split(',');
		for(i=0;i<7;i++){
			if(citys_id[0]==0){
						$(".city_id").attr('checked','checked');
			}
		if(citys_id[i]!=''&&citys_id[i]!='undefine')
		    $(".city_id").eq(citys_id[i]).attr('checked','checked');
		}
	}
    <?php if (Yii::app()->user->city == 0 ) {?>
	$("#Question_city_id").click(function(){
		$("input[name='Question[city_id][]']").attr('checked', this.checked);
	});
    <?php } ?>
	//正确答案选中
	var true_correct = '<?php echo isset($model->correct)?$model->correct:""?>';
	if(true_correct!=''){
		true_correct = true_correct.split(',');
		for(i=0;i<true_correct.length;i++){
		    $("input[value='"+true_correct[i]+"']").attr('checked', 'checked');
		}
	}
	//END
    containerShow();
    jQuery('#Question_track').click(function(){
        containerShow();
    });

    function containerShow() {
        var exam_type = jQuery('#Question_track').val();
        if (exam_type == 3 || exam_type == 4) {
            jQuery('#city_container').show();
        } else {
            jQuery('#city_container').hide();
        }
    }
});
Array.prototype.indexOf = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) return i;
    }
    return -1;
};
</script>