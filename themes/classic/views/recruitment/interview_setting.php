<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-9
 * Time: 下午12:06
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '面试时间设置';
?>

<h3><?php echo $this->pageTitle; ?></h3>

<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('vip-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#home">设置</a></li>
        <li class=""><a data-toggle="tab" href="#profile">展现</a></li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div id="home" class="tab-pane active">
            <div class="row-fluid">
                <div class="span7">
                    <!--Left content-->
                    <strong>计划面试时间</strong>
                    <form id="left_container">

                    </form>
                </div>
                <div class="span1"></div>
                <div class="span4">
                    <!--Right content-->
                    <div class="row">
                        <p>通知短信内容</p>
                    </div>

                    <div class="row">
                        <div class="control-group">
                            <label><span class="label label-info">面试地点</span></label>
                            <div class="controls">
                                <textarea rows="3" id="interview_address"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="control-group">
                            <label><span class="label label-info">面试注意事项</span></label>
                            <div class="controls">
                                <textarea rows="3" id="interview_remark"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="control-group">
                            <label><span class="label label-info">面试短信发送内容</span></label>
                            <div class="alert alert-success">
                                重要短信，请注意保留：您已选择与X年X月X日X时进行面试，面试地点为：<span id="show_address">XXXXX</span>，面试注意事项为<span id="show_remark">XXXX</span>，您的报名流水为XXXXXXXX。【e代驾】
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="span8" style="text-align:center">
                    <?php if($user_city_id) {?>
                    <button class="btn btn-success" type="button" id="add" style="margin-right : 20px">点击添加日期</button>
                    <button class="btn btn-success" type="button" id="submit">保存</button>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div id="profile" class="tab-pane fade">
            <div class="search-form">
                <?php
                $form=$this->beginWidget('CActiveForm', array(
                    'action'=>Yii::app()->createUrl($this->route),
                    'method'=>'post',
                ));
                ?>
                <div class='row-fluid'>
                    <div class='span2'>
                        <?php echo CHtml::label('选择城市','city_id')?>
                        <?php echo $form->dropDownList($interview_model,'city_id', Dict::items('city'), array('style'=>'width:120px'));?>
                    </div>
                    <div class="span2">
                        <?php echo CHtml::label('面试日期','interview_date')?>
                        <?php
                        $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                            'attribute'=>'visit_time',
                            'language'=>'zh_cn',
                            'name'=>"DriverInterviewTime[interview_date]",
                            'options'=>array(
                                'showAnim'=>'fold',
                                'showOn'=>'both',
                                //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                                'buttonImageOnly'=>true,
                                //'minDate'=>'new Date()',
                                'dateFormat'=>'yy-mm-dd',
                                'changeYear'=>true,
                                'changeMonth'=> true,
                            ),
                            'htmlOptions'=>array(
                                'style'=>'width:100px',
                            ),
                        ));
                        ?>
                    </div>
                </div>
                <div class='row-fluid'>
                    <div class='span3'>
                        <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <?php $this->widget('zii.widgets.grid.CGridView', array(
                'id'=>'vip-grid',
                'dataProvider'=>$dataProvider,
                //'cssFile'=>SP_URL_CSS . 'table.css',
                //'itemsCssClass'=>'table table-striped',
                'pagerCssClass'=>'pagination text-center',
                'pager'=>Yii::app()->params['formatGridPage'],
                'columns'=>array(

                    array(
                        'name' => '面试时间',
                        'value' => '$data->interview_date',
                        'headerHtmlOptions' => array(
                            'width' => '55px'
                        ),
                    ),

                    array(
                        'name' => '城市',
                        'value' => 'Dict::item("city",$data->city_id)',
                        'headerHtmlOptions' => array(
                            'width' => '55px'
                        ),
                    ),

                    array(
                        'name' => '每小时面试人数',
                        'value' => '$data->interview_num',
                        'headerHtmlOptions' => array(
                            'width' => '100px'
                        ),
                    ),

                    array(
                        'name' => '总面试小时数',
                        'value' => '($data->moring && $data->afternoon) ? 5 : ($data->moring ?  2: 3)',
                        'headerHtmlOptions' => array(
                            'width' => '90px'
                        ),
                    ),

                    array(
                        'name' => '预计面试人数',
                        'value' => '(($data->moring && $data->afternoon) ? 5 : ($data->moring ?  2: 3))*$data->interview_num',
                        'headerHtmlOptions' => array(
                            'width' => '90px'
                        ),
                    ),

                    array(
                        'name' => '已经报名人数',
                        'value' => array($this, 'getInterviewCount'),
                        'headerHtmlOptions' => array(
                            'width' => '90px',
                            'id' => '$data->interview_date'.'_'.'$data->city_id',
                        ),
                    ),

                    array(
                        'name' => '短信内容',
                        'type'=>'raw',
                        'value' => '"重要短信，请注意保留：<br>您已选择与X年X月X日X时进行面试，<br>面试地点为：".$data->address."，<br>面试注意事项为:". $data->remark."，<br>您的报名流水为XXXXXXXX。【e代驾】"',
                        'headerHtmlOptions' => array(
                            'width' => '320px'
                        ),
                    ),

                    array(
                        'name' => '状态',
                        'value' => '$data->status==0 ? "未发布" : "已经发布"'
                    ),

                    array(
                        'name' => '操作',
                        'type' => 'raw',
                        'value' => array($this,'showInterviewButton')
                    )
                )
            ));
            ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Modal header</h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        <button class="btn btn-primary" id="update">保存</button>
    </div>
</div>


<script type="text/html" id="view">
    <div style="padding: 19px;" class="alert alert-success" use="form" id="form_{f_length}">
            <button data-dismiss="alert" class="close" type="button" id="del_{f_length}" style="margin-right:20px; display: none" >×</button>
            <div class="input-prepend">
                <span class="add-on">面试日期：</span>
                <input type="text" name="interview_date[{f_length}]" id="interview_date_{f_length}" style="width:110px;">
            </div>
            <div class="input-prepend input-append">
                <span class="add-on">面试人数：</span>
                <input type="text" class="span3" id="interview_num_{form_length}" name="interview_num[{form_length}]" value="<?php echo $default_interview_num;?>"/>
                <span class="add-on">人/小时</span>
            </div>
            <br>
            <div>
                <span class="add-on">面试时间：</span>
                <input type="checkbox" checked="checked" value="1" id="moring_{form_length}" name="moring[{form_length}]" >10点-12点(上午) &nbsp;&nbsp;<input type="checkbox" value="1" checked="checked" id="afternoon_{form_length}" name="afternoon[{form_length}]">13点-16点(下午)
            </div>
            <input type="hidden" name="address[{form_length}]" id="address_{form_length}" value=""/>
            <input type="hidden" name="remark[{form_length}]" id="remark_{form_length}" value=""/>
    </div>
</script>

<script type="text/html" id="view_update">
    <form id="update_form">
        <div id="form" class="alert alert-success" style="padding: 19px;">
            <div class="input-prepend">
                <span class="add-on">面试日期：</span>
                <input type="text" style="width:110px;" name="interview_date[]" class="hasDatepicker" value="">
            </div>
            <div class="input-prepend input-append">
                <span class="add-on">面试人数：</span>
                <input type="text" value="12" name="interview_num[]" class="span3" value="">
                <span class="add-on">人/小时</span>
            </div>
            <br>
            <div>
                <span class="add-on">面试时间：</span>
                <input type="checkbox" name="moring[]" value="1" checked="checked">10点-12点(上午) &nbsp;&nbsp;<input type="checkbox" name="afternoon[]" checked="checked" value="1">13点-16点(下午)
            </div>
        </div>
        <div class="row-fluid">
            <div class="control-group" style="float:left; margin-right: 20px;">
                <label><span class="label label-info">面试地点</span></label>
                <div class="controls">
                    <textarea name="address[]" rows="3"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label><span class="label label-info">面试注意事项</span></label>
                <div class="controls">
                    <textarea name="remark[]" rows="3"></textarea>
                </div>
            </div>
        </div>
        <input type="hidden" name="act" value="update" />
        <input type="hidden" name="data_id[]" value=""/>
        <input type="hidden" name="city_id[]" value="" />
    </form>
</script>

<script type="text/html" id="view_unpublished">
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label" for="inputEmail">联系电话</label>
            <div class="controls">
                <input type="text" id="unpublished_phone" placeholder="联系电话">
            </div>
        </div>
        <div class="control-group">
            重要短信，十分抱歉，您选择的X年X月X日X时面试时间因故取消，您可到招聘官网中的“预约面试”页面，重新选择面试时间。带来的不便，敬请谅解。联系电话：XXXXX【e代驾】
        </div>
    </form>
</script>




<script type="text/javascript" >
    var show_explain_num = 4;
    jQuery(document).ready(function(){
        /**
         * 打开页面默认加载四个面试时间表单
         */
        for(var i=0; i<show_explain_num; i++) {
            getHtml();
        }

        /**
         * 增加一个时间表单
         */
        jQuery('#add').click(function(){
            getHtml();
        })

        jQuery('[func="upadte"]').live('click',function(){
            var id = jQuery(this).attr('data_id');
            jQuery.get(
                '<?php echo Yii::app()->createUrl("/recruitment/interviewSetting");?>',
                {
                    'act' : 'getinfo',
                    'data_id' : id
                },
                function(data){
                    if (data.status) {
                        var d = data.msg;
                        var html = jQuery('#view_update').html();
                        var title = '修改预约面试信息';
                        var o = jQuery(html);
                        o.find('[name="interview_date[]"]').val(d.interview_date);
                        o.find('[name="interview_num[]"]').val(d.interview_num);
                        if (d.moring) {
                            o.find('[name="moring[]"]').attr('checked', 'checked');
                        }
                        if (d.afternoon) {
                            o.find('[name="afternoon[]"]').attr('checked', 'checked');
                        }
                        o.find('[name="address[]"]').val(d.address);
                        o.find('[name="remark[]"]').val(d.remark);
                        o.find('[name="data_id[]"]').val(d.id);
                        o.find('[name="city_id[]"]').val(d.city_id);
                        initModal(title, o, function(){
                            update();
                        });
                    }
                },
                'json'
            );

        });

        jQuery('[func="delete"]').live('click', function(){
            if (confirm('确认要删除该面试时间?')) {
                var id = jQuery(this).attr('data_id');
                var obj = jQuery(this);
                jQuery.get(
                    '<?php echo Yii::app()->createUrl("/recruitment/interviewSetting");?>',
                    {
                        act : 'delete',
                        data_id : id
                    },
                    function(d) {
                        if (d.status) {
                            obj.parents('tr').remove();
                        }
                    },
                    'json'
                );
            }
        });

        jQuery('[func="publish"]').live('click', function(){
            if (confirm('确认要发布该面试时间?')) {
                var id = jQuery(this).attr('data_id');
                var obj = jQuery(this);
                jQuery.get(
                    '<?php echo Yii::app()->createUrl("/recruitment/interviewSetting");?>',
                    {
                        act : 'publish',
                        data_id : id
                    },
                    function(d) {
                        if (d.status) {
                            $.fn.yiiGridView.update('vip-grid', {
                                data: $(this).serialize()
                            });
                            return false;
                        }
                    },
                    'json'
                );
            }
        });

        jQuery('[func="unpublished"]').live('click', function(){
            var count = jQuery(this).attr('count');
            var id = jQuery(this).attr('data_id');
            if (count>0) {
                var html = jQuery('#view_unpublished').html();
                var o = jQuery(html);
                initModal('取消发布面试时间', o, function(){
                    var phone = o.find('#unpublished_phone').val();
                    if (phone.length <= 0) {
                        alert('请输入联系电话');
                        return false;
                    }
                    cancel(id, $('#myModal'), phone);
                });
            } else {
                if (confirm('确定取消该预约面试排期')) {
                    var obj = jQuery(this);
                    cancel(id, obj, '');
                }
            }
        });

        jQuery('[func="getinterviewer"]').live('click',function(){
            var city_id = jQuery(this).attr('city_id');
            var interview_date = jQuery(this).attr('interview_date');
            jQuery.get(
                '<?php echo Yii::app()->createUrl("/recruitment/interviewSetting");?>',
                {
                    act : 'getinterviewer',
                    city_id : city_id,
                    interview_date : interview_date
                },
                function(d){
                    if (d.status) {
                        var data = d.msg;
                        var html = "<table class='table'>";
                        jQuery.each(data, function(i,v){
                            if (i%6==0){
                                html += '<tr>';
                            }
                            html += '<td>'+v+'</td>';
                            if (i%6==5) {
                                html += '</tr>';
                            }
                        })
                        initModal('预约面试人员名单',jQuery(html),function(){});
                    } else {
                        alert('没有预约面试的司机');
                    }
                },
                'json'
            );
        });

        function cancel(id, obj,phone) {
            var object = obj;
            jQuery.get(
                '<?php echo Yii::app()->createUrl("/recruitment/interviewSetting");?>',
                {
                    act : 'unpublished',
                    data_id : id,
                    phone : phone
                },
                function(d) {
                    if (d.status) {
                        if (typeof(object.modal('hide')) == 'object') {
                            object.modal('hide');
                        } else {
                            object.parents('tr').remove();
                        }
                        $.fn.yiiGridView.update('vip-grid', {
                            data: $(this).serialize()
                        });
                        return false;
                    }
                },
                'json'
            );
        }

        function update() {
            var post_data = jQuery('#update_form').serialize();
            jQuery.get(
                '<?php echo Yii::app()->createUrl("/recruitment/interviewSetting");?>',
                post_data,
                function(d) {
                    if (d.status) {

                        $('#myModal').modal('hide');
                        $.fn.yiiGridView.update('vip-grid', {
                            data: $(this).serialize()
                        });
                        return false;
                    }
                },
                'json'
            )
        }


        function initModal(title, body, call_back) {
            jQuery('#myModalLabel').html(title);
            jQuery('.modal-body').html('').append(body);
            jQuery('#update').bind('click', call_back);
            $('#myModal').modal('show');
        }

        jQuery('#submit').click(function(){
            var form_length = jQuery('#left_container').find('[use="form"]').length;
            for(var i=0; i<form_length; i++) {
                var interview_date = jQuery('#interview_date_'+i).val();
                var interview_num = jQuery('#interview_num_'+i).val();
                var moring = jQuery('#moring_'+i).attr('checked');
                var afternoon = jQuery('#afternoon_'+i).attr('checked');
                if (interview_date == '') {
                    alert('面试时间不能为空');
                    location.hash = 'interview_date_'+i;
                    return false;
                }
                if (interview_num == '') {
                    alert('面试人数不能为空');
                    location.hash = 'interview_num_'+i;
                    return false;
                }
                if (moring != 'checked' && afternoon != 'checked') {
                    alert('面试时间不能为空');
                    location.hash = 'interview_moring_'+i;
                    return false;
                }
                var address = jQuery('#interview_address').val();
                var remark = jQuery('#interview_remark').val();
                if (address == '') {
                    alert('面试地点不能为空');
                    return false;
                }
                if (remark == '') {
                    alert('面试注意事项不能为空');
                    return false;
                }
                jQuery('#address_'+i).val(address);
                jQuery('#remark_'+i).val(remark);
            }
            saveData(jQuery('#left_container').serialize());
        })
    });

    function getHtml() {
        var form_length = jQuery('#left_container').find('[use="form"]').length;
        var html = jQuery('#view').html();
        html = html.replace( /\\?\{([^{}]+)\}/g, form_length);
        var o = jQuery(html);
        o.find('#interview_date_'+form_length).datepicker({
            'dateFormat' : 'yy-mm-dd'
        });
        o.find('[data-dismiss="alert"]').bind('click', function(){
            o.hide('1550');
            var length = jQuery('[use="form"]').length;
            if (length <=5) {
                jQuery('[data-dismiss="alert"]').hide();
            }
        });
        jQuery('#left_container').append(o);
        if (form_length >= 4) {
            jQuery('[data-dismiss="alert"]').show();
        }
    }

    function saveData(post_data) {
        var url = '<?php echo Yii::app()->createUrl("/recruitment/interviewSetting");?>';
        jQuery.get(
            url,
            post_data,
            function(d) {
                var data = d.msg;
                jQuery.each(data,function(i,v){
                   if(v){
                       jQuery('#form_'+i).remove();
                   }
                });
            },
            'json'
        )
    }
</script>

<!--以下方法非常恶心-->
<script>
    window.onload = function() {
        jQuery('.ui-datepicker-trigger').remove();
    }
</script>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker',array(
    'attribute'=>'visit_time',
    'language'=>'zh_cn',
    'name'=>"created_start",
    'options'=>array(
        'showAnim'=>'fold',
        'showOn'=>'both',
        //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
        'buttonImageOnly'=>true,
        //'minDate'=>'new Date()',
        'dateFormat'=>'yy-mm-dd',
        'changeYear'=>true,
        'changeMonth'=> true,
    ),
    'htmlOptions'=>array(
        'style'=>'width:100px; display:none',
    ),
));
?>


