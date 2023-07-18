<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-3
 * Time: 下午6:45
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '司机工号管理';
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
$form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
    'htmlOptions'=>array('class'=>'form-inline'),
));
?>
<div class="row-fluid">

    <div class="span2">
        <div><?php echo $form->label($model,'city_id'); ?></div>
        <?php
        $city_list = Dict::items('city');
        $user_city_id = Yii::app()->user->city;
        if ($user_city_id != 0) {
            $city_list = array(
                $user_city_id => $city_list[$user_city_id]
            );
        }
        echo $form->dropDownList($model,'city_id',$city_list,array('class'=>'span12'));
        ?>
    </div>

    <div class="span2">
        <div><?php echo $form->label($model, 'status'); ?></div>
        <?php echo $form->dropDownList($model, 'status', DriverIdPool::$status_dict, array('class'=>'span12'));?>
    </div>

    <div class="span2">
        <div><?php echo $form->label($model, 'driver_id'); ?></div>
        <?php echo $form->textField($model, 'driver_id', array('class'=>'span12'));?>
    </div>

    <div class="span3">
        <div>&nbsp;</div>
        <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
    </div>

</div>

<div class="row-fluid">

    <div class="span1">
        <div>&nbsp;</div>
        <?php echo CHtml::button('全选',array('class'=>'btn btn-success', 'onclick'=>'jQuery(":checkbox").attr("checked", true)')); ?>
    </div>

    <div class="span1">
        <div>&nbsp;</div>
        <?php echo CHtml::button('反选',array('class'=>'btn btn-success', 'onclick'=>'jQuery(":checkbox").each(function(){jQuery(this).attr("checked", !jQuery(this).attr("checked"))})')); ?>
    </div>
    <?php if ($model->status == DriverIdPool::STATUS_USABLE) {?>
    <div class="span2">
        <div>&nbsp;</div>
        <?php echo CHtml::button('批量删除',array('class'=>'btn btn-success', 'func'=>'batch_del')); ?>
    </div>
    <?php } else { ?>
    <div class="span2">
        <div>&nbsp;</div>
        <?php echo CHtml::button('批量恢复',array('class'=>'btn btn-success', 'func'=>'batch_recover')); ?>
    </div>
    <?php } ?>
     <div class="span2">
        <div>&nbsp;</div>
        <?php echo CHtml::link('批量生成工号', 'javascript:void(0)', array('class'=>'btn btn-success', 'func'=>'create', 'num'=>100)); ?>
    </div>

</div>

<?php $this->endWidget(); ?>
<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'driver-bonus',
    'itemsCssClass'=>'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'dataProvider'=>$dataProvider,
    'columns'=>array(

        array(
            'name'=>'<input type="checkbox" />',
            'type' => 'raw',
            'value' =>'CHtml::checkBox("cb_".$data["id"],false, array("class"=>"cb"))'
        ),

        'driver_id' => array(
            'header'=>'司机工号',
            'name'=>'driver_id',
        ),
        array(
            'name'=>'状态',
            'value'=>'DriverIdPool::$status_dict[$data["status"]]',
        ),
        array(
            'name'=>'操作',
            'type'=>'raw',
            'value'=>'($data["status"]==DriverIdPool::STATUS_USABLE) ? CHtml::link("删除", "javascript:void(0)", array("func"=>"del", "del_id"=>$data["id"])) : (($data["status"]==DriverIdPool::STATUS_DEL) ? CHtml::link("恢复", "javascript:void(0)", array("func"=>"recover", "recover_id"=>$data["id"])) : "")'
        )
    )
));

?>

<script type="text/javascript">

    var user_city_id = <?php echo Yii::app()->user->city; ?>;

    jQuery(document).ready(function() {
        /**
         * 删除单个司机工号
         */
        jQuery('[func="del"]').live('click', function(){
            var id = jQuery(this).attr("del_id");
            var id_list = new Array();
            id_list.push(id);
            deleteDriverId(id_list);
        });

        /**
         * 恢复司机工号
         */
        jQuery('[func="recover"]').click(function() {
            var id = jQuery(this).attr("recover_id");
            var id_list = new Array();
            id_list.push(id);
            recoverDriverId(id_list);
        });

        /**
         * 批量生成工号
         */
        jQuery('[func="create"]').click(function(){
            var num = jQuery(this).attr("num");
            if (user_city_id==0) {
                alert('仅分公司经理及司管有权限生成工号');
                return false;
            }
            jQuery(this).html('生成中.....');
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/driver/driverAjax'); ?>',
                {
                    batch_num : num,
                    city_id : user_city_id,
                    act : 'create_batch_driver_id'
                },
                function(d) {
                    if (d.status) {
                        alert('生成成功');
                        window.location.reload();
                    } else {
                        jQuery(this).html('批量生成工号');
                        alert(d.msg);
                    }

                },
                'json'
            );
        });

        /**
         * 批量删除工号
         */
        jQuery('[func="batch_del"]').click(function(){
            var id_list = searchSelectedCheckBox();
            if (id_list.length<=0) {
                alert('请选择要操作的工号');
                return false;
            }
            deleteDriverId(id_list);
        });

        /**
         * 批量恢复工号
         */
        jQuery('[func="batch_recover"]').click(function(){
            var id_list = searchSelectedCheckBox();
            if (id_list.length<=0) {
                alert('请选择要操作的工号');
                return false;
            }
            recoverDriverId(id_list);
        });

        /**
         * 查找被选中的checkbox
         * @returns {*}
         */
        function searchSelectedCheckBox() {
            var id_list = new Array();
            jQuery(".cb").each(function() {
                if (jQuery(this).attr('checked') == 'checked') {
                    var id = jQuery(this).attr('id').replace('cb_','');
                    if (id) {
                        id_list.push(jQuery(this).attr('id').replace('cb_',''));
                    }
                }
            });
            if (id_list.length <= 0) {
                return false;
            } else {
                return id_list;
            }
        }

        /**
         * 恢复删除工号基本方法
         * @param id_list
         */
        function recoverDriverId(id_list) {
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/driver/driverAjax'); ?>',
                {
                    address_id_list : id_list,
                    act : 'recover_driver_id'
                },
                function(d) {
                    if (!d.status) {
                        alert('有部分工号恢复失败');
                    }
                    if (d.msg) {
                        jQuery.each(d.msg, function(i,v) {
                            jQuery("#cb_"+v).parents('tr').remove();
                        });
                    }
                },
                'json'
            );
        }

        /**
         * 删除工号基本方法
         * @param id_list
         */
        function deleteDriverId(id_list) {
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/driver/driverAjax'); ?>',
                {
                    address_id_list : id_list,
                    act : 'delete_driver_id'
                },
                function(d) {
                    if (!d.status) {
                        alert('有部分工号删除失败');
                    }
                    if (d.msg) {
                        jQuery.each(d.msg, function(i,v) {
                            jQuery("#cb_"+v).parents('tr').remove();
                        });
                    }
                },
                'json'
            );
        }
    })
</script>

