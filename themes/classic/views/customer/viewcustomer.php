<?php
/* @var $this CustomerQuestionController */
/* @var $model CustomerQuestion */

$this->breadcrumbs=array(
	'Customer Questions'=>array('index'),
	$model->title,
);
?>

<h1>问题详情统计</h1>
	<div class="row">
		<select id ="batch" onChange = "batch()">
			<option value='0'>全部</option>
			<?php
				foreach ($visitBatch as $list){
					echo "<option value=".$list->id.">".$list->comment."</option>";
				}
			?>
			
		</select>
	</div>
<hr/>
<p><b><?php echo $model->title; ?></b></p>

<p id = "content">
<?php
	echo $answer;
?>
</p>
<script type="text/javascript">
	function batch(){
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('customer/ajaxAnswer')?>',
			'data':'id=<?php echo $model->id;?>&batch='+$("#batch").val(),
			'type':'get',
			'success':function(data){
			$('#content').html(data);
			},
			'cache':false
		});
	}
</script>

