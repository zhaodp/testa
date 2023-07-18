<?php
/* @var $this RestaurantController */
/* @var $model Restaurant */

$this->pageTitle = '商家数据采集管理';

echo "<h1>$this->pageTitle</h1>";


Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#restaurant-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>


<?php echo CHtml::link('自定义筛选','#',array('class'=>'search-button')); ?>
<div class="search-form">
    <?php $this->renderPartial('_search',array(
        'model'=>$model,
    )); ?>
</div>

    <div class="tabbable" style="float: right">
        <ul class="nav nav-tabs" id="my_id">
            <li class="active"><a href="#tab0" data-toggle="tab">区域信息</a></li>
            <li><a href="#tab1" data-toggle="tab">商家详情</a></li>
            <li><a href="#tab2" data-toggle="tab">联系方式</a></li>
            <li><a href="#tab3" data-toggle="tab">物料信息</a></li>
            <li><a href="#tab4" data-toggle="tab">竞品信息</a></li>
            <li><a href="#tab5" data-toggle="tab">备注</a></li>
            <li><a href="#tab6" data-toggle="tab">照片</a></li>
        </ul>
    </div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'restaurant-grid',
    'summaryText'=>'',
    'dataProvider'=>$model->search(),
    'afterAjaxUpdate'=>'js:function (id, data) { tab(window["index"]); },',
    //'filter'=>$model,
    'columns'=>array(
        'id',
        'name',
        array(
          'header'=>'物料概况',
          'value' => '$data->getRestaurantAttrInfo(3,"restaurant_info")',
        ),
        array(
            'header' =>'竞品概况',
            'value' => '$data->getRestaurantAttrInfo(1,"restaurant_info")',
        ),
        'updated',
        array(
            'name'=>'city',
            'headerHtmlOptions'=>array(
                'class'=>'c0'
            ),
            'htmlOptions'=>array(
                'class'=>'c0'
            ),
            'value'=>'$data->getCitiesName("city")',
        ),

        array(
            'name'=>'district',
            'headerHtmlOptions'=>array(
                'class'=>'c0'
            ),
            'htmlOptions'=>array(
                'class'=>'c0'
            ),
            'value'=>'$data->getCitiesName("district")',
        ),
        array(
            'name'=>'zone',
            'headerHtmlOptions'=>array(
                'class'=>'c0'
            ),
            'htmlOptions'=>array(
                'class'=>'c0'
            ),
            'value'=>'$data->getCitiesName("zone")',
        ),

        array(
            'name'=>'tables',
            'headerHtmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'value'=>'$data->getTablesTypeName("$data->tables_type")',
        ),

        array(
            'name'=>'type',
            'headerHtmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'value'=>'$data->getBusinessTypeName("$data->type")',
        ),

        array(
            'name'=>'cost',
            'headerHtmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
        ),

        array(
            'header'=>'渠道',
            'headerHtmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'value' => '$data->getRestaurantAttrInfo(0,"restaurant_info")',
        ),

        array(
            'name'=>'demand_index',
            'headerHtmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c1',
                'style'=>' display:none',
            ),
            'value' => '$data->getDemandIndexName("$data->demand_index")',
        ),


        array(
            'name'=>'contact',
            'headerHtmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
        ),
        array(
            'name'=>'title',
            'headerHtmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
            'value' => '$data->getTitleName("$data->title")',
        ),
        array(
            'name'=>'telephone',
            'headerHtmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
        ),
        array(
            'name'=>'mobile',
            'headerHtmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
        ),
        array(
            'name'=>'address',
            'headerHtmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c2',
                'style'=>' display:none',
            ),
        ),

        array(
            'header'=>'前台卡托',
            'headerHtmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'value'=>'$data->getRestaurantAttrInfo(0,"materials_info")',
        ),
        array(
            'header'=>'前台特约商户牌',
            'headerHtmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'value'=>'$data->getRestaurantAttrInfo(1,"materials_info")',
        ),
        array(
            'header'=>'卫生间贴牌',
            'headerHtmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'value'=>'$data->getRestaurantAttrInfo(2,"materials_info")',
        ),
        array(
            'header'=>'推拉门贴',
            'headerHtmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'value'=>'$data->getRestaurantAttrInfo(3,"materials_info")',
        ),
        array(
            'header'=>'牙签筒',
            'headerHtmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'value'=>'$data->getRestaurantAttrInfo(4,"materials_info")',
        ),
        array(
            'header'=>'烟灰缸',
            'headerHtmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'value'=>'$data->getRestaurantAttrInfo(5,"materials_info")',
        ),
        array(
            'header'=>'餐巾盒',
            'headerHtmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c3',
                'style'=>' display:none',
            ),
            'value'=>'$data->getRestaurantAttrInfo(6,"materials_info")',
        ),

        array(
            'header'=>'竞品物料',
            'headerHtmlOptions'=>array(
                'class'=>'c4',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c4',
                'style'=>' display:none',
            ),
            'value' => '$data->getRestaurantAttrInfo(1,"restaurant_info")',
        ),

        array(
            'header'=>'竞品物料清除情况',
            'headerHtmlOptions'=>array(
                'class'=>'c4',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c4',
                'style'=>' display:none',
            ),
            'value' => '$data->getRestaurantAttrInfo(2,"restaurant_info")',
        ),
        array(
            'header'=>'最后清除时间',
            'headerHtmlOptions'=>array(
                'class'=>'c4',
                'style'=>' display:none',
            ),
            'htmlOptions'=>array(
                'class'=>'c4',
                'style'=>' display:none',
            ),
            'name'=>'updated',
        ),


        array(
            'name'=>'remark',
            'headerHtmlOptions'=>array(
                'class'=>'c5',
                'style'=>' display:none',
                'nowrap'=>'nowrap',
            ),
            'htmlOptions'=>array(
                'class'=>'c5',
                'style'=>' display:none',
            ),
        ),

        array(
            'header'=>'点击查看大图',
            'headerHtmlOptions'=>array(
                'class'=>'c6',
                'style'=>' display:none',
                'nowrap'=>'nowrap',
            ),
            'htmlOptions'=>array(
                'class'=>'c6',
                'style'=>' display:none',
            ),
            'type'=>'raw',
            'value'=>'$data->getPhotoList()',
        ),

        array (
            'header' => '<span>操作</span>',
            'class' => 'CButtonColumn',
            'htmlOptions' => array (
                'width' => '10'
            ),
            'template' => ' {delete} '
        ),
    ),
)); ?>

<script>
    window['index'] = 0;
    $(document).ready(function(){
        jQuery("#my_id li").click(function(){
            window['index'] = $(this).index();
            tab(window['index']);
        });
    });

    function tab(index) {
        for(var i = 0 ; i<=7 ; i++){
            if(index == i){
                $(".c"+i).show();
            }else{
                $(".c"+i).hide();
            }
        }
    }

    jQuery('[func="click"]').live('click', function(){
        var url = jQuery(this).attr('middle');
        window.open(url, '_blank');
        return false;
    });





</script>