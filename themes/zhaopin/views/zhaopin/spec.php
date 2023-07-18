<?php
/* @var $this ZhaopinController */
$this->pageTitle = '服务规范 - e代驾';

?>
<div class="block">
	<div style="height:90px;"></div>
	<div class="page-header">
		<h2>服务规范</h2>
	</div>
<div class="block">
<?php 
$spec = '';
/*
$id_card = '';
if (isset($_POST['id_card']))
{
	$id_card = $_POST['id_card'];
	$zhaopinModel = DriverZhaopin::model()->findByAttributes(array('id_card'=>$id_card)); 
	if ($zhaopinModel)
	{
		$city_id = $zhaopinModel->city_id;
		$spec = DictContent::item('zhaopin_spec', $city_id);
	}
	
}
*/
$spec = DictContent::item('zhaopin_spec', 1);

?>
</div>
<div class="block">
<pre>
<?php echo $spec;?>
</pre>
</div>
</div>