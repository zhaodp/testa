<?php
//			$comment_date = date('Y-m-d', strtotime($item->insert_time));
//			if ($comment_date==date('Y-m-d', time())) {
//				$date = date('H:i:s', strtotime($item->insert_time));
//			} elseif ($comment_date==date('Y-m-d', time()-86400)) {
//				$date = date('昨天 H:i:s', strtotime($item->insert_time));
//			} else {
//				$date = date('Y-m-d', strtotime($item->insert_time));
//			}


$this->widget('zii.widgets.CListView', array (
	'id'=>'comments', 
	'dataProvider'=>$model->search(), 
	'itemsCssClass'=>'table table-stripe', 
	'itemView'=>'_comments'
));