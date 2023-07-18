<style type="text/css">
.grid-view {
	padding: 12px;
	text-align: left;
}
</style>
<div class="view">
	<div class="grid-view">
	<?php
		echo $data->content;				
		echo CHtml::encode(date('Y-m-d H:i', $data->created));
	?>		
	</div>
</div>