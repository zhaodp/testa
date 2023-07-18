<?php
$this->pageTitle = 'App意见反馈';
?>

<h1><?php echo $this->pageTitle;?></h1>
 <?php
 Yii::app()->getClientScript()->registerScriptFile(SP_URL_IMG . "WdatePicker/WdatePicker.js");
 ?>
<div>
    <div class="nav nav-pills">
        <li <?php if($type!='other') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('client/feedback'); ?>">司机意见反馈</a></li>
        <li <?php if($type=='other') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('client/feedback', array('Feedback'=>array('source'=>'other'))); ?>">客户意见反馈</a></li>
    </div>
    <div class="search-form">
        <div class="well span12">
        <?php
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>'client-feedback-search',
            'action'=>Yii::app()->createUrl('client/feedback', array('Feedback'=>array('source'=>$type))),
            'method'=>'get',
        )); ?>
            <div class="span2">
                <?php echo $form->label($model,'version');?>
                <?php echo $form->textField($model,'version',array('class'=>'info','style' => 'width:150px;')); ?>
            </div>
            <!--<div class="span2">
                <?php /*echo CHtml::label('来源渠道','source');*/?>
                <?php /*echo CHtml::textField('source',$source,array('class'=>'info')); */?>
            </div>-->
            <div class="span2">
                <?php echo $form->label($model,'driver_id');?>
                <?php echo $form->textField($model,'driver_id',array('class'=>'info','value' => $driver_id,'style' => 'width:150px;')); ?>
            </div>
            <div class="span2">
                <?php echo $form->label($model,'device');?>
                <?php echo $form->textField($model,'device',array('style' => 'width:150px;')); ?>
            </div>
            <div class="span2">
				<?php echo CHtml::label('开始日期','btime'); ?>
		        <?php echo $form->textField($model,'btime',array('onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
		    </div>
		    
		    <div class="span2">
		        <?php echo CHtml::label('结束日期','etime'); ?>
		        <?php echo $form->textField($model,'etime',array('onclick'=>"WdatePicker({'dateFmt':'yyyy-MM-dd'})",'style' => 'width:150px;')); ?>
		    </div>
            <div class="span2">
                <?php echo $form->label($model,'type');?>
                <?php echo $form->dropDownList($model,'type',CustomerFeedback::model()->getType(),array('empty'=>'全部','style' => 'width:150px;')); ?>
            </div>
            <div class="span2">
                <?php echo $form->label($model,'status');?>
                <?php echo $form->dropDownList($model,'status',array(0=>'未处理',1=>'已处理'),array('empty'=>'全部','style' => 'width:150px;')); ?>
            </div>
            <div class="span2">
                <?php echo $form->label($model,'reply_status');?>
                <?php echo $form->dropDownList($model,'reply_status',array(0=>'否',1=>'是'),array('empty'=>'全部','style' => 'width:150px;')); ?>
            </div>
            <div class="span2">
                <?php echo $form->label($model,'follow_up');?>
                <?php echo $form->dropDownList($model,'follow_up',CustomerFeedback::model()->getHeadArray('name'),array('empty'=>'全部','style' => 'width:150px;')); ?>
            </div>

            <div class="span2">
                <?php echo CHtml::label('&nbsp;','');?>
                <button class="btn btn-primary" type="submit" name="search">搜索</button> 
            </div>
			<div class="span" ><br/>注：默认显示30天内反馈，点选日期查看更多</div>
        <?php $this->endWidget(); ?>
        
        </div>
    </div>
    <!-- 搜索结束 -->
    <?php $classArr = CustomerFeedback::model()->getType(); ?>
    <?php echo CHtml::dropDownList('toClass', '', $classArr, array('onchange'=>'toComplain("toClass")','class'=>'btn btn-success', 'empty'=>'批量设置分类', 'style'=>'width:140px;')) ?>
    <input class="btn btn-success" id="send_msg_btn" onclick="toComplain()" type="button" value="批量转投诉">
    <input class="btn btn-success" id="toStatus" onclick="toComplain('toStatus')" type="button" value="批量设置已处理">
    <?php
    $gridId = 'customer-feedback-grid';
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>$gridId,
        'itemsCssClass'=>'table table-striped',
        'dataProvider'=>$dataProvider,
        'ajaxUpdate' => false,
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'columns'=>array(
                array(
                    'class' => 'CCheckBoxColumn',
                    'selectableRows' => 2,
                    'value' => '$data->id',
                ),
            array(
                'name' =>'created',
                'value'=>'($data->created > 0) ?date("Y-m-d H:i",$data->created) : ""'
            ),
            /*array(
                'name' => 'driver_id',
                'headerHtmlOptions'=>array (
                    'style'=>'width:110px',
                    'nowrap'=>'nowrap'
                ), 'type'=>'raw',
                'value' => array($this ,'getDriverId'),
            ),*/
            array(
                'name'=>'content',
                'headerHtmlOptions'=>array (
                    'style'=>'width:260px',
                    'nowrap'=>'nowrap'
                ),
                'value'=>'$data["content"]'
            ),
            array(
                'name'=>'email',
                'headerHtmlOptions'=>array (
                    'style'=>'width:110px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($this, 'getDriverId'),
            ),
            'device',
            'version',
	    'os',
            'source',
            array(
                'name'=>'status',
                'value'=>'$data->status == 1 ? "已处理" : "未处理"',
            ),
            array (
            'header'=>'是否回复',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->reply_status==0?"否":"是"'
        ),
            array(
                'name'=>'type',
                'type'=>'raw',
                'value'=>'CHtml::dropDownList("sdf",$data->type,CustomerFeedback::model()->getType(),array("style"=>"width:95px;border:0px;","onchange"=>"
                    toComplain(\'toClass\',$data->id,$(this).val())
                "))',
            ),
            array(
                'header' => '负责人',
                'type' => 'raw',
                'value' => '$data->follow_up', //CHtml::dropDownList("head_select",$data->head,$data->getHeadArray("name"))
            ),
            array(
                'header' => '操作',
                'type'=>'raw',
                'headerHtmlOptions' => array(
                    'style' => 'width:75px',
                    'nowrap' => 'nowrap'
                ),
                'value' => array($this,'getFeedbackOprates')

            ),
        )
    ));
    ?>
    <?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'mydialog',
        // additional javascript options for the dialog plugin
        'options'=>array(
            'title'=>'司机信息',
            'autoOpen'=>false,
            'width'=>'900',
            'height'=>'500',
            'modal'=>true,
            'buttons'=>array(
                '关闭'=>'js:function(){$("#mydialog").dialog("close");} '
            ),
        ),
    ));
    echo '<div id="dialogdiv"></div>';
    echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
    $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="modal-body">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        <button class="btn btn-primary" id="message_id">发送</button>
    </div>
</div>


<script type="text/javascript">
    $(function(){
        $("a[data-toggle='modal']").click(function(){
            var datatype = $(this).attr('data-type');
            if(datatype == 'push_message_id'){
                var target = $(this).attr('data-target');
                var url = $(this).attr('url');
                var mewidth = $(this).attr('mewidth');
                if(mewidth==null) mewidth='550px';

                if(url!=null){
                    $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
                    $('#myModal').modal('show');
                    $('#modal-body').load(url);
                    $('#message_id').hide();
                }
            }else{
                var target = $(this).attr('data-target');
                var url = $(this).attr('url');
                var data_id = $(this).attr('data-id');
                var data_feed_id = $(this).attr('data-feed-id');
                var mewidth = $(this).attr('mewidth');
                if(mewidth==null) mewidth='550px';
                if(url!=null){
                    $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
                    $('#myModal').modal('show');
                    $('#modal-body').html('<div class="control-group">\
                    <label class="control-label">消息内容：</label>\
                    <div class="controls">\
                    <textarea  id="content_id" name="message" rows="6" style="width:505px;"></textarea>\
                    </div></div>');
                    $("#message_id").attr('url',url);
                    $("#message_id").attr('data-id',data_id);
                    $("#message_id").attr('data-feed-id',data_feed_id);
                    $('#message_id').show();
                }
            }

            return false;
        });
        $("#message_id").click(function(){
            var sendType = $(this).html()
            var objButton = $(this);
            var content =  $("#content_id").val();
            var carId = $(this).attr('data-id');
            var feedId = $(this).attr('data-feed-id');
            if(content == '') return false;
            if(sendType == '发送')
            {
                url = '<?php echo Yii::app()->createUrl('/client/PushMessage');?>'
                data = {'feedId':feedId, 'driver_id':carId, 'content':content}
            }
            if(sendType == '指定'){
                url = '<?php echo Yii::app()->createUrl('/client/toHead');?>'
                data = {'feedId':feedId,'head':$("#select_head").val()}
            }

            $.ajax({
                'url':url,
                'data':data,
                'type':'get',
                'dataType':'json',
                'cache':false,
                'beforeSend':function(){
                    objButton.text('发送中').attr('disabled', true);
                },
                'success':function(data){
                    $('#myModal').modal('hide')
                    alert(data.msg);
                    $("#push_message_id").attr('data-msgcount', data.msgcount);
                    window.location.reload();
                },
                'error':function(data){
                    if(sendType != '指定'){
                        alert(data.msg);
                    }else{
                        alert("操作成功！");
                    }
                    window.location.reload();
                },
                'complete':function(){
                    objButton.text('发送').attr('disabled', false);
                }
            });
            return false;
        });
    });
    function getDriverInfo(url){
        $("#cru-frame").attr("src", url);
        $("#mydialog").dialog("open");
        return false;
    }

    function opToComplain(bid){
        var url = '<?php echo Yii::app()->createUrl("client/toComplain");?>' + '&id='+bid;
        $.get(url, function(data){
            if(data != 1){
                alert(data);
                return false;
            }
            ereload();
        });
        return false;
    }
    function opToHead(bid){
        var mewidth = '550px';
        $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
        $('#myModal').modal('show');
        $('#modal-body').html('<div class="control-group">\
                    <label class="control-label">责任人：</label>\
                    <div class="controls">\
                    <select name="select_head" id="select_head"><?php echo $this->getHeadList();?></select>\
                    </div></div>');

        var url = '<?php echo Yii::app()->createUrl("client/toHead");?>';
        $("#message_id").html('指定');
        $("#message_id").attr('url',url);
        $("#message_id").attr('data-feed-id',bid);
        $('#message_id').show();
    }
    function opToFinish(bid){
        var url = '<?php echo Yii::app()->createUrl("client/toFinish");?>' + '&id='+bid;
        $.get(url, function(data){
            if(data !== "1"){
                alert(data);
                return false;
            }
            ereload();
        });
    }
</script>


<script>
    /**
     *  修改分类/转投诉
     * type 操作类型
     * id   单条数据操作时需要传入id
     * selectClass  修改分类时，传入分类id
     */
    function toComplain(type,id,selectClass){
        var id_seclect = $("input[name='customer-feedback-grid_c0[]']:checked");
        var toClass = $('#toClass').val();
        if(id_seclect.length<=0 && !id){
            alert("请选择需要修改的记录！");
            if(toClass){
                $('#toClass').val("");
            }
            return false;
        }
        var id_str = '';
        for(i=0;i<id_seclect.length;i++){
            id_str += id_seclect.eq(i).val()+'_';
        }
        //修改分类
        if(type == 'toClass'){
            if(id){
                id_str = id;
            }
            var toClass = $('#toClass').val();
            $.ajax({
                url:"<?php echo Yii::app()->createUrl("client/toComplain"); ?>",
                data:{id:id_str,type:'toClass',extParams:selectClass ? selectClass : toClass},
                cache:false,
                success:function(data){
                    if(id){
                        ereload();
                    }else{
                        alert('修改分类成功！');
                        window.location.reload();
                    }
                    
                }
            });
            return false;
        }
        //批量修改状态
        if(type == 'toStatus'){
            $.ajax({
                url:"<?php echo Yii::app()->createUrl("client/toComplain"); ?>",
                data:{id:id_str,type:'toStatus'},
                cache:false,
                success:function(data){
                    alert('修改状态成功！');
                    ereload();
                }
            });
            return false;
        }
        //转投诉
        $.ajax({
            url:"<?php echo Yii::app()->createUrl("client/toComplain"); ?>",
            data:{id:id_str},
            cache:false,
            success:function(data){
                ereload();
                alert('转投诉成功！')
            }
        });
    }
    function ereload(){
        $.fn.yiiGridView.update('<?php echo $gridId; ?>');
    }
</script>
