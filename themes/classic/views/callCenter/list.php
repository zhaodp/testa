<?php $this->pageTitle = '错误记录'; ?>

<?php echo $this->renderPartial('_error_search', array('model'=>$model)); ?>

<?php echo $this->renderPartial('_error_view', array('errorData'=>$errorData)); ?>