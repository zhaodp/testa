<?php
$this->pageTitle = Yii::app()->name . ' - 今日可激活司机';
?>
<h1>今日可激活司机</h1>
<form class="form-inline" action="" method="post">
    <label>城市</label>
    <?php echo CHtml::dropDownList('city_id', $city_id, Dict::items('city'), array('style'=>'width:110px'));?>
    <?php echo CHtml::submitButton('查询');?>
</form>

<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'active-driver-grid',
    'dataProvider'=>$model,
    'columns'=>array (
        array (
            'name'=>'司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'driverInfo'),//'$data->driver_id',
        ),
        array (
            'name'=>'屏蔽天数',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->limit_time'
        ),

        array (
            'name'=>'屏蔽理由',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->mark'
        ),
        array (
            'name'=>'屏蔽时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->create_time'
        ),
        array (
            'name'=>'到期时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->un_punish_time'
        ),
        array (
            'header'=>'操作',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'active_opt')
        ),


    )
));

?>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="modal-body">

    </div>
    <div class="modal-footer">
        <button  class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        <button class="btn btn-primary" id="btn_active" style="display:none;" >激活</button>
    </div>
</div>
<!-- Modal -->



<script type="text/javascript">

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

    $('#myModal').on('hidden', function () {
        $('#btn_active').hide();
    })

    $('#btn_active').click(function(){
        var reason=$('#DriverExt_mark_reason').val();
        var driverId=$('#DriverExt_driver_id').val();
        var mark=$('#DriverExt_mark').val();
        if(reason==''){
            alert('请填写激活原因');
            return false;
        }else {
            $.ajax({
                'url':'<?php echo Yii::app()->createUrl('/driver/domark');?>',
                'data':{'id':driverId, 'mark':mark, 'reason':reason ,'days':0},
                'type':'get',
                'success':function(data){
                    $('#myModal').modal('hide');
                    $.fn.yiiGridView.update('active-driver-grid');
                },
                'cache':false
            });
            return false;
        }
    });


    function dialogInit(id, mark){
        var assure = false;
        jQuery.get(
            '<?php echo Yii::app()->createUrl('/driver/driverAjax'); ?>',
            {
                act : 'get_driver',
                id : id
            },
            function (d) {
                if (d.status) {
                    var driver = d.msg;
                    var assure = driver['assure'];
                    if (assure==0 && mark==<?php echo Employee::MARK_ENABLE;?>) {
                        if (!confirm('该司机担保状态为【担保待定】，确认要激活该司机？')) {
                            return false;
                        }
                    } else if (assure==8 && mark==<?php echo Employee::MARK_ENABLE;?>) {
                        alert('该司机担保状态为【未担保】，不能激活');
                        return false;
                    }
                    $.ajax({
                        'url':'<?php
		                echo Yii::app()->createUrl('/driver/mark');
		                ?>',
                        'data':{'id':id, 'mark':mark},
                        'type':'get',
                        'success':function(data){
                            $('#modal-body').html(data);
                            $('#myModal').modal('toggle').css({'width':'500px','margin-left': function () {return -($(this).width() / 2);}});
                            $('#myModal').modal('show');
                            $('#btn_active').show();
                        },
                        'cache':false
                    });

                    return false;

                }
            },
            'json'
        );
    }


</script>