
<h1>我的工作日志 列表</h1>
<div class="search-form">
    <?php $hasCreate = AdminWorkLog::model()->hasCreate(); ?>
    <?php $this->renderPartial('_search',array(
        'model'=>$model,
        'hasCreate'=>$hasCreate,
    )); ?>
</div>

<?php
    $this->widget('zii.widgets.CListView', array(
        'id' => 'list_item',
        'dataProvider' => $dataProvider,
        'itemView' => '_view',
        'ajaxUpdate' => FALSE,
        'pagerCssClass' => 'pagination text-center',
        'pager' => Yii::app()->params['formatGridPage'],
    ));
?>
