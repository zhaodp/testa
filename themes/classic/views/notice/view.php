<?php
if ( empty( $model)  ){
	echo "该公告已过期";
}else{

?>
	
<?php
$this->breadcrumbs=array(
	'Notices'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List Notice', 'url'=>array('index')),
	array('label'=>'Create Notice', 'url'=>array('create')),
	array('label'=>'Update Notice', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Notice', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Notice', 'url'=>array('admin')),
);
?>
<table width="700">
	<tr>
		<td><h1 align="center"><?php echo $model->title; ?></h1></td>
	</tr>
	<tr>
		<td><?php echo $model->content;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo date('Y-m-d H:i', $model->created);?><input id="n_id" type="hidden" value="<?php echo $model->id;?>" /></td>
	</tr>
</table>

<?php 
}

?>