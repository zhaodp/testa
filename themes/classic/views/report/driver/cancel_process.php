<?php
    $this->renderPartial('driver/cancel_stat_search', array(
        'condition' => $condition,
        'cancel_count' => $cancel_count
    ));
?>

<?php
    $this->renderPartial('driver/cancel_stat', array(
        'dataProvider'=>$dataProvider,
        'condition' => $condition,
        'cancel_count' => $cancel_count));
?>



<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-body" id="modal-body">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->

<script>

    $(document).ready(function(){

        $("a[data-toggle=modal]").click(function(){
            var target = $(this).attr('data-target');
            var url = $(this).attr('url');
            var mewidth = $(this).attr('mewidth');
            if(mewidth==null) mewidth='850px';
            if(url!=null){
                $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
                $('#myModal').modal('show');
                $('#modal-body').load(url);
            }
            return true;
        });

    });
    function toShield(){
        var id_seclect = $("input[name='ranking-grid_c0[]']:checked");
        if(id_seclect.length<=0){
            alert("请选择需要处理的司机！");
            return false;
        }
        var id_str = '';
        for(i=0;i<id_seclect.length;i++){
            id_str += id_seclect.eq(i).val()+'_';
        }

        var start_time=$("#report_start_time").val();
        var end_time=$("#report_end_time").val();
        var url='<?php echo Yii::app()->createUrl("driver/process"); ?>';
            url+='&driver_id='+id_str+'&stime='+start_time+'&etime='+end_time+'&batch=1';
        $('#myModal').modal('toggle').css({'width':400,'margin-left': function () {return -($(this).width() / 2);}});
        $('#myModal').modal('show');
        $('#modal-body').load(url);

    }


</script>