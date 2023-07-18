<?php
/* @var $this ZhaopinController */
$this->pageTitle = '报名查询 - e代驾招募';
?>

<div class="block">
	<div style="height:67px;"></div>
	<section id="agreement" class="agreement">
		<div class="page-header">
			<h2>报名查询</h2>
		</div>
		<div>
		</div>
	</section>
<div>
<div>
<?php 
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
)); 
?>
<label for="id_card">请输入身份证号</label>
<div style="width:225px; float:left">
	<input type="text" id="id_card" name="id_card" value="<?php //echo $this->id_card;?>" />
</div>
<div style="width:75px; float:left">
<?php 
	echo CHtml::submitButton('查询',array('class'=>'btn'));
?>
</div>
<?php 
$this->endWidget(); 
?>
</div>
<div style="min-height:200px; _height:200px;">
<?php
    if (is_array($driver_info) && count($driver_info)) {
?>
    <div class="grid-view" id="order-grid">
        <table class="table table-striped" style="clear: both">
            <thead>
            <tr>
                <th>流水号</th><th id="order-grid_c0">姓名</th><th id="order-grid_c1">手机</th><th id="order-grid_c3">状态</th></tr>
            </thead>
            <tbody>
                <td ><?php echo $driver_info['serial_number'];?></td>
                <td id="order-grid_c0"><?php echo $driver_info['name'];?></td>
                <td id="order-grid_c1"><?php echo preg_replace("/(1\d{1,2})\d\d(\d{0,3})/","\$1****\$3",$driver_info['mobile']);?></td>
                <td id="order-grid_c3"><?php echo $driver_info['status_cn'];?></td></tr>
            </tbody>
        </table>
        <div title="/queue" style="display:none" class="keys"></div>
    </div>
<?php } else {
    echo $notice;
} ?>
</div>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'各地分公司联系方式',
        'autoOpen'=>false,
        'width'=>'500',
        'height'=>'360',
        'modal'=>true,
        'buttons'=>array(
            '关闭'=>'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv">';
echo '<p>北京分公司：010-58694525</p>';
echo '<p>上海分公司：021-61358339</p>';
echo '<p>杭州分公司：0571-88134859</p>';
echo '<p>广州分公司：020-38476915</p>';
echo '<p>深圳分公司：0755-83594832</p>';
echo '<p>重庆分公司：023-63037942</p>';
echo '<p>成都分公司：028-85579527</p>';
echo '<p>南京分公司：025-82220393</p>';
echo '<p>西安分公司: 029-88608044</p>';
echo '</div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<script>
    function openDialog() {
        $("#mydialog").dialog("open");
    }
</script>