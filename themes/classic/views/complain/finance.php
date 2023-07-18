<?php
$this->pageTitle = Yii::app()->name . ' - 客户投诉管理';
?>
    <h1>司机解约退费</h1>
    <div class="search-form">
        <?php $this->renderPartial('_search_finance',array(
            'model'=>$model,
        )); ?>
    </div><!-- search-form -->

<?php $this->renderPartial('_view_finance',array(
    'data'=>$data,
)); ?>