<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-9
 * Time: 下午10:29
 * auther mengtianxue
 */
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#customer-main-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<?php $this->renderPartial('user/user_nav'); ?>

<div class="row-fluid">
    <?php $this->renderPartial('user/_search', array(
        'model' => $model,
    )); ?>
</div><!-- search-form -->


<div class="row-fluid">
    <?php
    $this->widget('zii.widgets.grid.CGridView',
        array(
            'id' => 'customer-main-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
				array(
					'name' => 'phone',
					'header'=>'手机',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
				),
				array(
					'name' => 'name',
					'header'=>'姓名',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
				),
                array(
                    'name' => 'city_id',
					'header'=>'城市',
					'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'Dict::item("city", $data->city_id)'
                ),
                array(
                    'name' => 'gender',
					'header'=>'性别',
					'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'CarCustomerMain::$customer_gender[$data->gender]'
                ),
                array(
                    'name' => 'type',
					'header'=>'类型',
					'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'CarCustomerMain::$customer_type[$data->type]'
                ),

                array(
                    'name' => 'status',
					'header'=>'状态',
					'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'CarCustomerMain::$customer_status[$data->status]'
                ),

                array(
                    'name' => 'account_type',
					'header'=>'账户类型',
					'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'CarCustomerMain::$customer_account_type[$data->account_type]'
                ),
				array(
					'name' => 'email',
					'header'=>'Email',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
				),
				array(
					'name' => 'amount',
					'header'=>'账户余额',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
				),
				array(
					'name' => 'create_time',
					'header'=>'创建时间',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
				),
				array(
					'name' => 'update_time',
					'header'=>'操作时间',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
				),

                array(
                    'name' => '操作',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap',
                        'width' => '100'
                    ),
                    'type' => 'raw',
                    'value' => 'CHtml::link("帐户详情",Yii::app()->createUrl("customers/user_info", array("id" => "$data->id")))."&nbsp;&nbsp;"'
                ),
            ),
        )
    ); ?>
</div>
