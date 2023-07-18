<?php
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.validate.js');
$this->pageTitle = '新增工号';
echo "<h1>".$this->pageTitle."</h1><br />";
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'cancelFrom','action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
));
?>
<table id=dis_container class="table table-striped table-bordered">
    <tr>
        <td>工号</td><td>v2后台账号</td><td>坐席密码</td>
    </tr>
    <tbody id="dis_tbody">
    <tr>
        <td><input type="input" name="agent_num[]"></td>
        <td><input type="input" name="name[]"></td>
        <td><input type="input" name="password[]" value="123456"></td>
    </tr>
    </tbody>
</table>
<a href="javascript:void(0)" class="btn" onclick="insert()">增加一个工号</a>
<?php
echo CHtml::link('提交', "javascript:void(0);", array('class' => 'btn btn-success', "onClick" => "submit()"));
$this->endWidget();
?>
<script>

function insert() {
    var html = '<tr>';
    html += '<td><input type="input" name="agent_num[]"></td>';
    html += '<td><input type="input" name="name[]"></td>';
    html += '<td><input type="input" name="password[]" value="123456"></td>';
    html += '<td><a class="close" style="align:left" onclick="del(this)" aria-hidden="true">×</a></td>';
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
        if(check){$('#cancelFrom').submit();}else{return false;}
    }
</script>
