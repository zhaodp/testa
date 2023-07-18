<style>
    .attention {background:rgb(255,204,0);}
</style>
<?php


//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'complain-grid',
    'dataProvider'=>$model,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
    'rowCssClassExpression'=>'"item_".$data->id." ".($data->attention%3==2 ? "success" : ($data->attention%3== 1 ? "info":($data->attention%3==0 ?($data->attention!=0?"warning":""):"")))',
    'columns'=>array (
        array (
            'name'=>'ID',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->id',
        ),
        array (
            'name'=>'投诉来源',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->source?CustomerComplain::$source[$data->source]:""',
        ),
        array (
            'name'=>'投诉详情',
            'headerHtmlOptions'=>array (
                'style'=>'width:200px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->detail'
        ),
        array(
            'name'=>'申诉状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:20px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'getAppealStatus')
        ),
        array (
            'name'=>'投诉时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->create_time'
        ),
        array (
            'name'=>'投诉类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'getType')
        ),
        array(
            'name'=>'投诉任务人',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'taskUser'),
        ),
        array (
            'name'=>'投诉人',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'complainUser'),
        ),
        array (
            'name'=>'预约电话',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'customer_phone'),
        ),
//        array (
//            'name'=>'创建人',
//            'headerHtmlOptions'=>array (
//                'style'=>'width:40px',
//                'nowrap'=>'nowrap'
//            ),
//            'type'=>'raw',
//            'value'=>'$data->created'
//        ),
//        array (
//            'name'=>'操作人',
//            'headerHtmlOptions'=>array (
//                'style'=>'width:40px',
//                'nowrap'=>'nowrap'
//            ),
//            'type'=>'raw',
//            'value'=>'$data->operator'
//        ),
        array(
            'name'=>'操作人员',
            'headerHtmlOptions'=>array (
                'style'=>'width:70px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'opera')
        ),
        array (
            'name'=>'司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'driverInfo'),
        ),
        array (
            'name'=>'订单编号',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'orderIdAndNumber')
        ),
        array (
            'name'=>'处理状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'processStatus')
        ),
        array (
            'name'=>'处理节点',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->pnode?CustomerComplain::$pnode[$data->pnode]:""',
        ),
        array (
            'name'=>'是否回复',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->reply_status==0?"否":"是"'
        ),
        array (
            'header'=>'操作',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'opt')
        ),


    )
));


?>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->

<script type="text/javascript">
    $(function(){
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
    function addAttention(ts){
        $.ajax({
            url:$(ts).attr('href'),
            cache:false,
            success:function(data){
                if(data.success){
                    var iii = ".item_"+data.item_id;
                        if(data.attention%3 == 1){
                            $(iii).removeClass("success warning");
                            $(iii).addClass("info");
                        }else if(data.attention%3 == 2){
                            $(iii).removeClass("info warning");
                            $(iii).addClass("success");
                        }else if(data.attention!=0 && data.attention%3 == 0){
                            $(iii).removeClass("info success");
                            $(iii).addClass("warning");
                        }else{
                            $(iii).removeClass("info success warning");
                        }
                        
                        for (var i = 0; i<10; i++) {
                            if(i != data.attention){
                                $("#ajaxLink"+i+"_" + data.item_id).html('<a class="attentionLink" onclick="addAttention(this);return false" style="padding:0px 10px " href="<?php echo Yii::app()->createUrl('complain/list');?>&attention_id='+ data.item_id +'&attention_status='+i+'">'+i+'</a>');
                            }
                        };
                    $("#ajaxLink" + data.attention + "_" + data.item_id).html(data.url);
                }else{
                    if(data.msg){
                        alert(data.msg);
                    }
                }
            },
            dataType:'json'
        });
    }

</script>