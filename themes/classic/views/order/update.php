<?php
$this->pageTitle = Yii::app()->name . ' - 报单';

?>

<h1>报单</h1>

<?php
$data = array('model'=>$model);
if (isset($modelExt))
	$data['modelExt'] = $modelExt;
$data['money'] = $money;
$data['parameter'] = $parameter;
echo $this->renderPartial('_form', $data);
 ?>