<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 14-2-21
 * Time: 下午1:35
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="container">
    <div class="row-fluid">
        <div class="span2"><strong style="font-size: 24px;">上传列表</strong></div>
        <div class="span3 offset1">
            <a class="btn" href="<?php echo Yii::app()->createUrl('companyKpi/businessReport');?>" target="_blank">添加上传</a>
        </div>
    </div>
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'driver-grid',
        'dataProvider'=>$dataProvider,
        'columns'=>array(
            array(
                'name'=>'城市',
                'value'=>'Dict::item("city", $data->city_id);'
            ),

            array(
                'name'=>'月份',
                'value'=>'$data->use_date'
            ),

            array(
                'name'=>'商家名称',
                'value'=>'$data->name'
            ),

            array(
                'name'=>'上传时间',
                'value'=>'$data->created',
            ),

            array(
                'name' => '状态',
                'value' => 'CompanyBusinessInfo::$status_dict[$data->status]'
            ),

            array(
                'header' => '操作',
                'class'=>'CButtonColumn',
                'htmlOptions' => array(
                    'width' => '180px',
                ),
                'template'=>'{archives} {modify} {upload}',
                'buttons'=>array(
                    'archives'=>array(
                        'label'=>'详情',
                        'url' => 'Yii::app()->createUrl("companyKpi/businessView", array("id"=>$data->id))',
                        'options' => array('target'=>'_blank', 'class'=>'btn'),
                    ),

                    'modify'=>array(
                        'label'=>'修改',
                        'url' => 'Yii::app()->createUrl("companyKpi/businessModule", array("act"=>"update", "id"=>$data->id))',
                        'options' => array('target'=>'_blank', 'class'=>'btn'),
                        'visible'=>'$data->status == 0 ? true : false;'
                    ),

                    'upload' => array(
                        'label' => '确认上传',
                        'url' => 'Yii::app()->createUrl("companyKpi/businessAjax", array("id"=>$data->id, "act"=>"change_status"))',
                        'options' => array('func'=>'change_status', 'class'=>'btn'),
                        'visible'=>'$data->status == 0 ? true : false;'

                    ),
                ),
            )
        )
    ));
    ?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('[func="change_status"]').live('click', function(){
            if (confirm('确认上传?')) {
                var url = jQuery(this).attr('href');
                var status = <?php echo CompanyBusinessInfo::STATUS_UPLOAD;?>;
                jQuery.get(
                    url,
                    {
                        'status' : status
                    },
                    function(d) {
                        if (d.status) {
                            $.fn.yiiGridView.update('driver-grid', {
                                data: $(this).serialize()
                            });
                        }
                        return false;
                    },
                    'json'
                );

            }
            return false;
        });
    });
</script>