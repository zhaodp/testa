<?php $this->pageTitle = Yii::app()->name . ' - 报单'; ?>

<h3>报单</h3>
<style>
input, textarea, select, .uneditable-input{
	width:88%; 
}
td label{
	text-align:center;
	padding-right:2px;
}

.require {
	border:1px solid red;
}
</style>
<?php
$data = array('model'=>$model);
if (isset($modelExt))
	$data['modelExt'] = $modelExt;
$data['money'] = $money;
$data['parameter'] = $parameter;
echo $this->renderPartial('_form', $data); ?>