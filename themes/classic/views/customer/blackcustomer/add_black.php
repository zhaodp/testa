<?php
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.validate.js');
$this->pageTitle = '新增黑名单用户';
echo "<h1>".$this->pageTitle."</h1><br />";
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'cancelFrom','action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
));
?>
<table id=dis_container class="table table-striped table-bordered">
    <tr>
        <td>电话号码</td><td>屏蔽时长</td><td>屏蔽原因</td>
    </tr>
    <tbody id="dis_tbody">
    <tr>
        <td><input type="input" name="phones[]" class="span6 phones"><span class="field_notice_p"></span></td>
	<td>
	    <select name="expire_times[]">  
  		<option value ="1">7天</option>  
  		<option value ="2">一个月</option>  
  		<option value="3">三个月</option>  
  		<option value="4">六个月</option> 
		<option value="5">一年</option>
                <option value="6">三年</option>
		<option value="7">5分钟</option>
	    </select>  
	</td>
        <td><textarea name="remarks[]" class='remarks' style="width:400px;height:50px;"></textarea><span class="field_notice_r"></span></td>
    </tr>
    </tbody>
</table>
<a href="javascript:void(0)" class="btn" onclick="insert()">增加一组号码</a>
<?php
echo CHtml::link('提交', "javascript:void(0);", array('class' => 'btn btn-success', "onClick" => "submit()"));
$this->endWidget();
?>
<script>

function insert() {
    var html = '<tr>';
    html += '<td><input type="input" name="phones[]" class="span6 phones"><span class="field_notice_p"></span></td>';
    html += '<td><select name="expire_times[]"><option value ="1">7天</option><option value ="2">一个月</option><option value="3">三个月</option><option value="4">六个月</option><option value="5">一年</option><option value="6">三年</option><option value="7">5分钟</option></select></td>';
    html += '<td><textarea name="remarks[]" class="remarks" style="width:400px;height:50px;"></textarea><span class="field_notice_r"></span>';
    html += '<a class="close" style="align:left" onclick="del(this)" aria-hidden="true">×</a></td>';
    html += '</tr>';
    var o = jQuery(html);
    jQuery('#dis_tbody').append(o);
}
function del(obj) {
    lengths=$("#dis_container").find("tr").length
    if(lengths>2){
        jQuery(obj).parents('tr').remove();
    }
}
    function submit(){
        var check=true;
        var reg =/^[\d]{3}-[\d]{8}$|^[\d]{4}-[\d]{7}$|^[\d]{4}-[\d]{8}$|^[\d]{8}$|^[\d]{7}$|^[\d]{12}$|^[\d]{11}$/;
        $("#dis_tbody > tr").each(function(i){
            var p = $(this).find("input.phones");
            var r = $(this).find("textarea.remarks");
            var notice_p = $(this).find("span.field_notice_p");
            var notice_r = $(this).find("span.field_notice_r");
            try {
                p_val=$.trim(p.val());
                if(!p_val.match(reg)){notice_p.html('电话号码格式错误');check=false;}else{notice_p.html('ok');}
                if($.trim(r.val()) == ''){notice_r.html('请填写屏蔽原因');check=false;}else{notice_r.html('ok');}
            } catch(e){
                return false;
            }

        });
        if(check){$('#cancelFrom').submit();}else{return false;}
    }
</script>
