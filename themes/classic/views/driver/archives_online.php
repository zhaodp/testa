<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-15
 * Time: 下午5:54
 * To change this template use File | Settings | File Templates.
 */
$this->layout = '//layouts/main_no_nav';
?>

<style>
    th {
        background-color: #D9EDF7;
    }
</style>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a data-toggle="tab" href="#day">日详情</a></li>
    <li><a data-toggle="tab" href="#week">周详情</a></li>
    <li><a data-toggle="tab" href="#month">月详情</a></li>
</ul>

<div class="tab-content" id="myTabContent">
    <div id="day" class="tab-pane fade in active">
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'day-grid',
            'dataProvider'=>$data['day'],
            'cssFile'=>SP_URL_CSS . 'table.css',
            'itemsCssClass'=>'table table-bordered',
            'pagerCssClass'=>'pagination text-center',
            'pager'=>Yii::app()->params['formatGridPage'],
            'columns'=>array(

                array(
                    'name'=>'日期',
                    'value' => '$data->record_date',
                ),

                array(
                    'name'=>'是否上线',
                    'value' => '$data->online ? "是" : "否"',
                ),

                array(
                    'name' => '峰值时段是否上线',
                    'value' => '$data->p_online ? "是" : "否"',
                ),

                array(
                    'name' => '峰值时段连续上线',
                    'value' => '$data->p_continuous ? "是":"否"',
                ),

                array(
                    'name' => '接单数',
                    'value' => '$data->i_orders+$data->c_orders'
                ),

                array(
                    'name' => '报单数',
                    'value' => '$data->c_orders'
                ),

                array(
                    'name' => '补单数',
                    'value' => '$data->e_orders'
                ),

                array(
                    'name' => '销单数',
                    'value' => '$data->i_orders'
                ),

                array(
                    'name' => '投诉数',
                    'value' => '$data->d_complain'
                ),

                array(
                    'name' => '被投诉数数',
                    'value' => '$data->c_complain'
                ),

                array(
                    'name' => '好评数',
                    'value' => '$data->high_opinion'
                ),

                array(
                    'name' => '差评数',
                    'value' => '$data->bad_review'
                ),

                array(
                    'name' => '销单率',
                    'value' => '($data->i_orders+$data->c_orders)>0 ? sprintf("%.1f%%",$data->i_orders/($data->i_orders+$data->c_orders)*100) : 0'
                ),

                array(
                    'name' => '补单率',
                    'value' => '($data->i_orders+$data->c_orders)>0 ? sprintf("%.1f%%",$data->e_orders/($data->i_orders+$data->c_orders)*100) : 0'
                ),

                array(
                    'name' => '投诉率',
                    'value' => '($data->i_orders+$data->c_orders)>0 ? sprintf("%.1f%%",$data->c_complain/($data->i_orders+$data->c_orders)*100) : 0'
                ),

                array(
                    'name' => '被投诉率',
                    'value' => '($data->i_orders+$data->c_orders)>0 ? sprintf("%.1f%%",$data->d_complain/($data->i_orders+$data->c_orders)*100) : 0'
                ),
            ),
        ));
        ?>
    </div>

    <div id="week" class="tab-pane fade">
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'week-grid',
            'dataProvider'=>$data['week'],
            'cssFile'=>SP_URL_CSS . 'table.css',
            'itemsCssClass'=>'table table-bordered',
            'pagerCssClass'=>'pagination text-center',
            'pager'=>Yii::app()->params['formatGridPage'],
            'columns'=>array(

                array(
                    'name'=>'日期',
                    'value' => '$data["record_date"]',
                ),

                array(
                    'name'=>'上线天数',
                    'value' => '$data["online"]',
                ),

                array(
                    'name' => '峰值时段上线天数',
                    'value' => '$data["p_online"]',
                ),

                array(
                    'name' => '峰值时段连续上线天数',
                    'value' => '$data["p_continuous"]',
                ),

                array(
                    'name' => '接单数',
                    'value' => '$data["i_orders"]+$data["c_orders"]'
                ),

                array(
                    'name' => '报单数',
                    'value' => '$data["c_orders"]'
                ),

                array(
                    'name' => '补单数',
                    'value' => '$data["e_orders"]'
                ),

                array(
                    'name' => '销单数',
                    'value' => '$data["i_orders"]'
                ),

                array(
                    'name' => '投诉数',
                    'value' => '$data["d_complain"]'
                ),

                array(
                    'name' => '被投诉数数',
                    'value' => '$data["c_complain"]'
                ),

                array(
                    'name' => '好评数',
                    'value' => '$data["high_opinion"]'
                ),

                array(
                    'name' => '差评数',
                    'value' => '$data["bad_review"]'
                ),

                array(
                    'name' => '销单率',
                    'value' => '($data["i_orders"]+$data["c_orders"])>0 ? sprintf("%.1f%%",$data["i_orders"]/($data["i_orders"]+$data["c_orders"])*100) : 0'
                ),

                array(
                    'name' => '补单率',
                    'value' => '($data["i_orders"]+$data["c_orders"])>0 ? sprintf("%.1f%%",$data["e_orders"]/($data["i_orders"]+$data["c_orders"])*100) : 0'
                ),

                array(
                    'name' => '投诉率',
                    'value' => '($data["i_orders"]+$data["c_orders"])>0 ? sprintf("%.1f%%",$data["c_complain"]/($data["i_orders"]+$data["c_orders"])*100) : 0'
                ),

                array(
                    'name' => '被投诉率',
                    'value' => '($data["i_orders"]+$data["c_orders"])>0 ? sprintf("%.1f%%",$data["d_complain"]/($data["i_orders"]+$data["c_orders"])*100) : 0'
                ),

            ),
        ));
        ?>
    </div>

    <div id="month" class="tab-pane fade">
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'punish-grid',
            'dataProvider'=>$data['month'],
            'cssFile'=>SP_URL_CSS . 'table.css',
            'itemsCssClass'=>'table table-bordered',
            'pagerCssClass'=>'pagination text-center',
            'pager'=>Yii::app()->params['formatGridPage'],
            'columns'=>array(

                array(
                    'name'=>'月份',
                    'value' => '$data->current_month',
                ),

                array(
                    'name'=>'上线天数',
                    'value' => '$data->online',
                ),

                array(
                    'name' => '峰值时段上线天数',
                    'value' => '$data->p_online',
                ),

                array(
                    'name' => '峰值时段连续上线天数',
                    'value' => '$data->p_continuous',
                ),

                array(
                    'name' => '接单数',
                    'value' => '$data->cancel+$data->complete'
                ),

                array(
                    'name' => '报单数',
                    'value' => '$data->complete'
                ),

                array(
                    'name' => '补单数',
                    'value' => '$data->additional'
                ),

                array(
                    'name' => '销单数',
                    'value' => '$data->cancel'
                ),

                array(
                    'name' => '投诉数',
                    'value' => '$data->d_complain'
                ),

                array(
                    'name' => '被投诉数数',
                    'value' => '$data->c_complain'
                ),

                array(
                    'name' => '好评数',
                    'value' => '$data->high_opinion'
                ),

                array(
                    'name' => '差评数',
                    'value' => '$data->bad_review'
                ),

                array(
                    'name' => '销单率',
                    'value' => '($data->cancel+$data->complete)>0 ? sprintf("%.1f%%",$data->cancel/($data->cancel+$data->complete)*100) : 0'
                ),

                array(
                    'name' => '补单率',
                    'value' => '($data->cancel+$data->complete)>0 ? sprintf("%.1f%%",$data->additional/($data->cancel+$data->complete)*100) : 0'
                ),

                array(
                    'name' => '投诉率',
                    'value' => '($data->cancel+$data->complete)>0 ? sprintf("%.1f%%",$data->c_complain/($data->cancel+$data->complete)*100) : 0'
                ),

                array(
                    'name' => '被投诉率',
                    'value' => '($data->cancel+$data->complete)>0 ? sprintf("%.1f%%",$data->d_complain/($data->cancel+$data->complete)*100) : 0'
                ),
            ),
        ));
        ?>

    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('strong').css('color', '#316AAF');
    });
</script>
