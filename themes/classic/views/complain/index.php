<?php
$this->pageTitle = Yii::app()->name . ' - 客户投诉管理';
?>
    <h1>客户投诉管理</h1>
    <?php $this->renderPartial('_com_nav'); ?>
    <div class="search-form thumbnail">
        <div class="caption">
            <?php $this->renderPartial('_search', array(
                's_time' => $start_time,
                'e_time' => $end_time,
                'h_s_time' => $handle_start_time,
                'h_e_time' => $handle_end_time,
                'model' => $vmodel,
                'typelist' => $typelist,
                'parent_id' => $parent_id,
                'child' => $child,
                'child_id' => $child_id,
                'task_gid' => $task_gid,
                'grouplist' => $grouplist,
                'task_uid' => $task_uid,
                'userlist' => $userlist
            )); ?>
        </div>
    </div><!-- search-form -->

<?php $this->renderPartial('_view_sp', array(
    'model' => $model,
)); ?>

<script>
$(function(){

    $("#down_excel_btn").click(function(){
        order_id = $("#order_id").val();
        start_time = $("#start_time").val();
        end_time = $("#end_time").val();
        city_id = $("#city_id").val();
        driver_id = $("#driver_id").val();
        handle_start_time = $("#handle_start_time").val();
        handle_end_time = $("#handle_end_time").val();
        attention = $("#attention").val();
        customer_phone = $("#customer_phone").val();
        complain_maintype = $("#complain_maintype").val();
        sub_type = $("#sub_type").val();
        source = $("#source").val();
        operator = $("#operator").val();
        status = $("#status").val();
        id=$('#id').val();
        id_tail=$('#id_tail').val();
        //新页面打开开始下载
        url = '<?php echo Yii::app()->createUrl('/complain/download')?>&order_id='+order_id+'&start_time='+start_time
        +'&end_time='+end_time+'&city_id='+city_id
        +'&driver_id='+driver_id+'&handle_start_time='+handle_start_time
        +'&handle_end_time='+handle_end_time+'&attention='+attention
        +'&customer_phone='+customer_phone+'&complain_maintype='+complain_maintype
        +'&sub_type='+sub_type+'&source='+source
        +'&operator='+operator+'&status='+status
        +'&id='+id+'&id_tail='+id_tail
        +'&search=';
        // alert(url);
        window.open(url);
    });
    
});
</script>