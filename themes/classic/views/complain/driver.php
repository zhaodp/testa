<?php
$this->pageTitle = Yii::app()->name . ' - 客户投诉-司机处理';
?>
    <h1>司机处理</h1>
    <div class="search-form">
        <?php $this->renderPartial('_search_dm',array(
            'model'=>$model,
            'typelist'=>$typelist,
            'city_id'=>$city_id,
            'status' => $status,
            'sub_type' => $sub_type,
            'complain_maintype' => $complain_maintype,
            'driver_id' => $driver_id,
            'source' => $source,
            'childTypeList' => $childTypeList,
        )); ?>
    </div><!-- search-form -->

<?php
    $this->renderPartial('_view_dm_new',array(
        'data'=>$data
    ));
?>
