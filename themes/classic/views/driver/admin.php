<?php
$this->pageTitle = '司机管理';
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'mydialog', 
	'options'=>array(
		'title'=>'屏蔽原因', 
		'autoOpen'=>false, 
		'width'=>'750', 
		'height'=>'350', 
		'modal'=>true, 
		'buttons'=>array(
			'确认'=>'js:function(){dialogClose($("#DriverExt_driver_id").val(), $("#DriverExt_mark").val(), $("#DriverExt_mark_reason").val(),$("#limit_day").val())}',
			'关闭'=>'js:function(){$("#mydialog").dialog("close");}'))));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'update_driver_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array(
		'title'=>'修改司机信息', 
		'autoOpen'=>false, 
		'width'=>'850',
		'height'=>'580',
		'modal'=>true, 
		'buttons'=>array(
			'关闭'=>'js:function(){$("#update_driver_dialog").dialog("close");}'))));
echo '<div id="update_driver_dialog"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_update = <<<EOD
function(){
	$("#cru-frame").attr("src",$(this).attr("href"));
	$("#update_driver_dialog").dialog("open");
	return false;
}
EOD;

$click_reset_password = <<<EOD
function(){
	var ajax_url = jQuery(this).attr('href');
	jQuery.get(
		ajax_url,
		function(d) {
			alert(d.msg);
		},
		'json'
	);
	return false;
}
EOD;

$opendebug = <<<EOD
function(){
	var ajax_url = jQuery(this).attr('href');
	jQuery.get(
		ajax_url,
		function(d) {
			alert(d.msg);
		},
		'json'
	);
	return false;
}
EOD;

$unblock = <<<EOD
function(){
	var ajax_url = jQuery(this).attr('href');
	jQuery.get(
		ajax_url,
		function(d) {
			alert(d.msg);
		},
		'json'
	);
	return false;
}
EOD;

$app = <<<EOD
function(){
	var ajax_url = jQuery(this).attr('href');
	jQuery.get(
		ajax_url,
		function(d) {
			alert(d.msg);
		},
		'json'
	);
	return false;
}
EOD;

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'view_driver_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array(
		'title'=>'查看司机信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array(
			'关闭'=>'js:function(){$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_view = <<<EOD
function(){
	$("#view_driver_frame").attr("src",$(this).attr("href"));
	$("#view_driver_dialog").dialog("open");
	return false;
}
EOD;
?>
<h1><small><?php
echo $this->pageTitle;
?></small></h1>

<div class="search-form">
<?php
$this->renderPartial('_search', array(
	'model'=>$model));
?>
</div>
<!-- search-form -->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-grid', 
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS.'table.css', 
	'enableSorting'=>false,
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'itemsCssClass'=>'table table-condensed', 
	'rowCssClassExpression'=>array(
		$this, 
		'driverMark'), 
	'htmlOptions'=>array(
		'class'=>'row-fluid'),
	'columns'=>array(
		
		array(
			'name'=>'city_id', 
			'headerHtmlOptions'=>array(
				//'width'=>'50px',
				'nowrap'=>'nowrap'),
 			'type'=>'raw',
			'value'=>'Dict::item("city", $data->city_id);'),
        array(
            'name'=>'picture',
            'headerHtmlOptions'=>array(
                //'width'=>'50px',
                'nowrap'=>'nowrap'),

            'type'=>'raw',
            'value'=>'"<span style=\'overflow:hidden;border-radius:60px;width:120px;height:120px;display:inline-block;*zoom:1;background:#fff url(".Driver::getPictureUrl($data->user,$data->city_id, Driver::PICTURE_SMALL, true).") center no-repeat; background-size:100%;\'></span>"'
        ),
		array(
			'name'=>'user', 
			'headerHtmlOptions'=>array(
				//'width'=>'50px',
				'nowrap'=>'nowrap'), 
			'type'=>'raw', 
			'value'=>'$data->user."<br />".$data->name',
        ),
		/*array(
			'name'=>'name', 
			'headerHtmlOptions'=>array(
				//'width'=>'50px',
				'nowrap'=>'nowrap')
        ),*/

		array(
			'name'=>'司机电话',
			'headerHtmlOptions'=>array(
                'width' => '150px',
				'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => '"工作：".Common::parseDriverPhone($data->phone)."<br />备用：".Common::parseDriverPhone($data->ext_phone)'
        ),

		/*array(
			'name'=>'imei', 
			'headerHtmlOptions'=>array(
				//'width'=>'50px',
				'nowrap'=>'nowrap'), 
			'type'=>'raw', 
			'value'=>'$data->imei'), */
		/*
        array(
			'name'=>'ext_phone', 
			'headerHtmlOptions'=>array(
				'width'=>'50px', 
				'nowrap'=>'nowrap')),
		*/
		array(
			'name'=>'level', 
			'headerHtmlOptions'=>array(
				//'width'=>'50px',
				'nowrap'=>'nowrap')
        ),

        array(
            'name'=>'created',
            'headerHtmlOptions'=>array(
                'nowrap'=>'nowrap'
            ),
            'value' => 'date("Y-m-d", strtotime($data->created))'
        ),
		array(
			'name'=>'标记(DB)', 
			'headerHtmlOptions'=>array(
				//'width'=>'50px',
				'nowrap'=>'nowrap'), 
			'type'=>'raw', 
			'value'=>'($data->mark == Employee::MARK_DISNABLE) ? "已屏蔽".(($data->block_at != 0) ? "<br/>欠费屏蔽" : "")
			.(($data->block_mt != 0) ? ($data->block_mt == 1 ? "<br/>手动屏蔽": ($data->block_mt == 2 ? "<br/>系统屏蔽": ($data->block_mt==3 ? "<br/>手动屏蔽<br/>系统屏蔽":""))) : "") : (($data->mark == Employee::MARK_LEAVE) ? "已解约" : "正常")'
        ),
        array(
            'name'=>'标记(Redis)', 
            'headerHtmlOptions'=>array(
                //'width'=>'50px',
                'nowrap'=>'nowrap'), 
            'type'=>'raw', 
            'value'=>array($this,'getDriverRedisMark'),
        ),
        array(
            'name'=>'司管app权限',
            'headerHtmlOptions'=>array(
                //'width'=>'50px',
                'nowrap'=>'nowrap'),
            'type'=>'raw',
            'value'=>'($data->driver_manager==1)?"有":"无"',
        ),
//		array(
//			'name'=>'is_andriod',
//			'headerHtmlOptions'=>array(
//				'width'=>'50px',
//				'nowrap'=>'nowrap'),
//			'type'=>'raw',
//			'value'=>'DriverPhone::model()->existsBindDriver($data->user);'),
//    修改 屏蔽和解约按钮的展示   防止司机管理部门再点错  mengtianxue   2013-06-03
		array(
			'name'=>'状态修改', 
			'headerHtmlOptions'=>array(
				//'width'=>'80px',
				'nowrap'=>'nowrap'), 
			'type'=>'raw',
			'value'=>array($this,'getDriverStatus'),
        ),

        /*array(
            'name' => '图片资料',
            'headerHtmlOptions'=>array(
				//'width'=>'80px',
				'nowrap'=>'nowrap'),
			'type'=>'raw',
			'value'=>'CHtml::button("查看",  array("func"=>"upload","class"=>"btn", "link"=>Yii::app()->createUrl("/recruitment/uploadimage", array("driver_id"=>$data->user, "js_callback"=>"modal_hide"))))',
        ),*/

		array(
            'header' => '操作',
			'class'=>'CButtonColumn',
            'htmlOptions' => array(
                'width' => '230px',
            ),
			'template'=>'{archives} {modify} {reset} {open} {detail} {unblock} {app} {upload} {upimg}',
			'buttons'=>array(
				'archives'=>array(
					'label'=>'详情',
                    'url'=>'$this->grid->controller->createUrl("driver/archives",array("id"=>$data->user));',
					'options' => array('target'=>'_blank', 'class'=>'btn' ),
					//'url'=>'$this->grid->controller->createUrl("driver/view",array("id"=>$data->user,"dialog"=>1,"grid_id"=>$this->grid->id));',
					//'click'=>$click_view,
					'visible'=>'AdminActions::model()->havepermission("driver", "view")'),
				'modify'=>array(
					'label'=>'修改',
					'url'=>'$this->grid->controller->createUrl("driver/update",array("id"=>$data->user,"dialog"=>1,"grid_id"=>$this->grid->id));',
                    'options' => array('func'=>'update','class'=>'btn'/*, "data-toggle" => "modal", "data-target" => "#updateModal"*/),
					'visible'=>'AdminActions::model()->havepermission("driver", "update")'
                ),

                'reset' => array(
                    'label'=>'重置密码<br /><br />',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("driver/resetpassword",array("id"=>$data->user))',
					'click'=>$click_reset_password,
					'option' => array('ajax'=> 'Yii::app()->controller->createUrl("driver/resetpassword",array("id"=>$data->user))'),
					'visible'=>'AdminActions::model()->havepermission("driver", "update")'
                ),
                'open' => array(
                    'label'=>'调试',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("driver/opendebug",array("driverId"=>$data->user))',
					'click'=>$opendebug,
					'option' => array('ajax'=> 'Yii::app()->controller->createUrl("driver/opendebug",array("driverId"=>$data->user))'),
					'visible'=>'AdminActions::model()->havepermission("driver", "update")'
                ),
                'detail' => array(
                    'label'=>'详情',
                    'url'=>'$this->grid->controller->createUrl("driver/detail",array("id"=>$data->user));',
					'options' => array('target'=>'_blank'),
					'visible'=>'AdminActions::model()->havepermission("driver", "view")'
				),
                'unblock' => array(
                    'label'=>'强制解屏蔽<br /><br />',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("driver/unblock",array("driverId"=>$data->user))',
					'click'=>$unblock,
					'option' => array('ajax'=> 'Yii::app()->controller->createUrl("driver/unblock",array("driverId"=>$data->user))'),
					'visible'=>'AdminActions::model()->havepermission("driver", "update")'
                ),
                'app' => array(
                    'label'=>'更改司管app权限<br /><br />',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("driver/app",array("driverId"=>$data->user,"manager"=>$data->driver_manager))',
                    'click'=>$app,
                    'option' => array('ajax'=> 'Yii::app()->controller->createUrl("driver/app",array("driverId"=>$data->user,"manager"=>$data->driver_manager))'),
                    'visible'=>'AdminActions::model()->havepermission("driver", "update")'
                ),
                'upload' => array(
                    'label' => '上传头像',
                    'url' => 'Yii::app()->createUrl("/recruitment/uploadimage", array("driver_id"=>$data->user, "js_callback"=>"modal_hide"))',
                    'options' => array(
                        'class' => 'btn',
                        'func' => 'upload',
//                        'style'=> 'margin-top:20px;'
                    ),
                ),
                'upimg' => array(
                    'label' => '上传其它资料',
                    'url' => 'Yii::app()->createUrl("/recruitment/uploadImageAdmin", array("driver_id"=>$data->user, "js_callback"=>"modal_hide"))',
                    'options' => array(
                        'class' => 'btn',
                        'func' => 'upimg',
                    ),
                )
			),


		))));
?>
<iframe name="yframe" src="" style="display:none; border:none;"></iframe>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">上传司机照片</h3>
    </div>
    <div class="modal-body">
        <p>努力加载中…</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
</div>
</div>

<iframe name="updateFrame" src="" style="display:none; border:none;"></iframe>
<!-- Modal -->
<div id="updateModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header" style="padding: 0px 5px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 id="myModalLabel">修改司机信息</h4>
    </div>
    <div class="modal-body">
        <p id="driver_loading" load_status='no_loading'>努力加载中…</p>
    </div>
    <div class="modal-footer" style="padding: 5px 5px;">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<script>

jQuery(document).ready(function(){

    jQuery("[func='upimg']").live('click', function(){
        var url = jQuery(this).attr('href');
        jQuery.get(
            url,
            function(d) {
                var o = jQuery(d);
                jQuery('#myModal').find('.modal-body').html('').append(o);
                $('#myModal').modal('show');
                return false;
            }
        );
        return false;
    });

    jQuery("[func='upload']").live('click', function(){
        var url = jQuery(this).attr('href');
        jQuery.get(
            url,
            function(d) {
                var o = jQuery(d);
                o.find('#driver-form').attr('target', 'yframe');
                jQuery('#myModal').find('.modal-body').html('').append(o);
                $('#myModal').modal('show');
                return false;
            }
        );
        return false;
    });


    $('[func="update"]').live('click', function(){
        if($(this).attr("func") === "update"){
            var url = $(this).attr("href");
            var new_width = $(this).attr("new_width");
            if(new_width==null) new_width="720px";
            if(url!=null){
                $("#updateModal").css({"width":new_width,"margin-left": function () {return -($(this).width() / 2);}});
                if($("#driver_loading").attr('load_status') != 'no_loading'){
                    $('#updateModal').find('.modal-body').html('<p id="driver_loading" load_status="no_loading">努力加载中…</p>')
                }
                $('#updateModal').modal('show');
                $.get(url,
                    function(data) {
                        var obj = jQuery(data);
                        obj.find('#driver-form').attr('target', 'updateFrame');
                        $('#updateModal').find('.modal-body').html('').append(obj);
                        return false;
                    }
                );
            }
        }
        return false;
    });
});

function reEntry(driver_id) {
    if (confirm('确认将该司机重新签约？')) {
        var mark = 1;
        var mark_reason = '解约司机重新签约';
        var days = 3;
        var id = driver_id;
        $.ajax({
            'url':'<?php
			        echo Yii::app()->createUrl('/driver/reentry');
			    ?>',
            'data':{'id':id, 'mark':mark, 'reason':mark_reason ,'days':days},
            'type':'get',
            'success':function(data){
                $.fn.yiiGridView.update('driver-grid');
            },
            'cache':false
        });

    }
}

function closeUploadModal(driver_id, pic_address) {
    $('#myModal').modal('hide');
    //$.fn.yiiGridView.update('driver-grid');
    setTimeout(function(){
        jQuery('[driver_img="'+driver_id+'"]').attr('src', pic_address+"?ver="+Math.floor(Math.random() * (999999999 + 1)));
    }, 1000);
}

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
                if(mark != 3 && (driver['mark'] == 1 && driver['block_at'] == 1 && driver['block_mt'] == 0)){
                    alert('司机信息费不足，待财务划款完成后，自动解除屏蔽');
                    return false;
                }
                if(mark != 3 && (driver['mark']==1 && driver['block_mt'] == 2)){ //系统屏蔽不能激活
  					alert('该司机处于系统屏蔽，不能激活');
                    return false;
                }

                if (assure==0 && mark==<?php echo Employee::MARK_ENABLE;?>) {
                    var block_day = $("#block"+id).attr('block_day');
                    if (!confirm('该司机担保状态为【担保待定】，且屏蔽天数为'+ block_day +'天 确认要激活该司机？')) {
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
			            $('#dialogdiv').html(data);
		            },
		            'cache':false
	            });
	            $("#mydialog").dialog("open");
	            return false;

            }
        },
        'json'
    );
    /*
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
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	$("#mydialog").dialog("open");
	return false;
	*/
}

function dialogClose(id, mark, mark_reason,days){
	if (mark_reason == '') {
		alert ("请填写原因。");
		return false;
	} else {
		$.ajax({
			'url':'<?php
			echo Yii::app()->createUrl('/driver/domark');
			?>',
			'data':{'id':id, 'mark':mark, 'reason':mark_reason ,'days':days},
			'type':'get',
			'success':function(data){
				$.fn.yiiGridView.update('driver-grid');
			},
			'cache':false		
		});	
		$("#mydialog").dialog("close");
		return false;
	}
}
    window.closeModal = function(){
        $('#updateModal').modal("hide");
        $.fn.yiiGridView.update("driver-grid");
    };
</script>
