<?php
$this->pageTitle = Yii::app()->name . ' - 定位订单';
?>

<h1>定位订单</h1>
<!--<label><a href="--><?php //echo Yii::app()->createUrl('complain/list');?><!--">投诉管理</a> ->定位订单</label>-->
<div class="search-form">
    <?php
//    $this->renderPartial('_search_order',array(
//        'model'=>$model,));
    ?>
</div><!-- search-form -->

<?php
$this->renderPartial('_view_order',array(
    'model'=>$model,
    'ordermodel'=>$ordermodel
));
?>