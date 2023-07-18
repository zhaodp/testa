<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 6/12/14
 * Time: 19:49
 */
//init search action
$this->pageTitle = '财务对账查询 '.$dateStart.' -- '.$dateEnd;
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
<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>

    <div class="span12">
        <?php Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker'); ?>
        <div class="span3">
            <label>开始时间</label>
            <?php  $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateStart',
                'value' =>  $dateStart,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));?>
        </div>
        <div class="span3">
            <label>结束时间</label>
            <?php  $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateEnd',
                'value' => $dateEnd,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>

    </div>
    <?php $this->endWidget(); ?>
</div>

<div class="row-fluid">
    <h3>
        <?php
            echo '本次查询区间为'.$dateStart.'到'.$dateEnd;
        ?>
    </h3>
    <h4>
        <?php
            if(!empty($summary['totalIncome'])){
                echo '总充值额 <b>'.$summary['totalIncome'].'</b>';
                echo '(';
                echo ' 司机充值额 <b>'.$summary['totalDriver'].'</b>';
                echo ' 用户充值额 <b>'.($summary['totalIncome'] - $summary['totalDriver']).'</b>';
                echo ' 测试充值额 <b>'.$summary['totalTest'].'</b>';
                echo ')';
                echo ' 扣除手续费 <b>'.$summary['totalFee'].'</b>';
                echo ' 后实际收入 <b>'.$summary['totalBalance'].'</b>';
            }else{
                echo "没有找到任何信息";
            }
        ?>
    </h4>

</div>

<div class="row-fluid">
    <?php
    $this->widget('zii.widgets.grid.CGridView',
        array(
            'id' => 'customer-main-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                    'name' => '日期',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'date\']'
                ),
                array(
                    'name' => '充值总额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'sumIn\']'
                ),
                array(
                    'name' => '用户充值总额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'customerIn\']'
                ),

                array(
                    'name' => '司机充值总额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'driverIn\']'
                ),

                array(
                    'name' => '测试充值额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'sumTest\']'
                ),

                array(
                    'name' => '测试司机充值额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'testDriverIn\']'
                ),

                array(
                    'name' => '测试用户充值额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'testCustomerIn\']'
                ),


                array(
                    'name' => '手续费',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'sumFee\']'
                ),

                array(
                    'name' => '实收金额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data[\'sumBalance\']'
                ),

                array(
                    'name' => '是否相等',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'abs($data[\'sumIn\'] - $data[\'sumDbCount\']) < 1 ? "是" : "<b style=\"color:#f00;\">否</b>"'
                ),
				array(
					'name' => '数据库的值',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '$data[\'sumDbCount\']'
				),

				array(
					'name' => '与数据库的差值',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '$data[\'sumIn\'] == $data[\'sumDbCount\'] ? 0 : printf("%.1f",($data[\'sumIn\'] - $data[\'sumDbCount\']))'
				),

                array(
                    'name' => '操作',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap',
                        'width' => '100'
                    ),
                    'type' => 'raw',
                    'value' => 'CHtml::link("帐户详情",Yii::app()->createUrl("pay/detail", array("date" => $data[\'date\'])),array("target"=>"_blank"))."&nbsp;&nbsp;"'
                ),
            ),
        )
    ); ?>
</div>
