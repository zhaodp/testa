<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-29
 * Time: 下午3:14
 * To change this template use File | Settings | File Templates.
 */
$background_name = $type == CompanyKpiCommon::BACKGROUND_BUSINESS ? '市场' : '运营';
$this->pageTitle = '分公司绩效考核管理('.$background_name.')';
?>

<div class="container">
    <h1>分公司绩效考核管理(<?php echo $background_name;?>)</h1>
    <a href="<?php echo Yii::app()->createUrl('companyKpi/setting', array('type'=>$type)); ?>" class="btn btn-primary" target="_blank">设置考核指标</a>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'online',
    'dataProvider'=>$dataProvider,
    //'cssFile'=>SP_URL_CSS . 'table.css',
    //'itemsCssClass'=>'table  table-condensed',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(
        array(
            'name'=>'城市',
            'value'=>'Dict::item("city",$data["city_id"])',
            'headerHtmlOptions'=>array(
                'style' => 'width: 20%'
            )
        ),
        array(
            'name'=>'设置时间',
            'value'=>'$data["created"]',
            'headerHtmlOptions'=>array(
                'style' => 'width: 20%'
            )

        ),
        array(
            'name' => '使用月份',
            'value' => '$data["use_date"]',
            'headerHtmlOptions'=>array(
                'style' => 'width: 20%'
            )
        ),

        array(
            'class'=>'CButtonColumn',
            'template'=>'{archives}{index}',
            'buttons'=>array(
                'archives'=>array(
                    'label'=>'详情/修改',
                    'url'=>'$this->grid->controller->createUrl("companyKpi/view",array("city_id"=>$data["city_id"], "use_date"=>$data["use_date"]));',
                    'options' => array('target'=>'_blank', 'class'=>'btn', 'style'=>'margin-right:20px;'),
                    //'visible'=>'AdminActions::model()->havepermission("driver", "view")'),
                ),
                'index' => array(
                    'label' => '查看得分情况',
                    'url'=>'$this->grid->controller->createUrl("companyKpi/index",array("city_id"=>$data["city_id"], "use_date"=>$data["use_date"]));',
                    'options' => array('target'=>'_blank', 'class'=>'btn' ),
                ),
            )
        ),
    )
));
?>
</div>

