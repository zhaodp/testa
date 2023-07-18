<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
	'Driver Zhaopins'=>array('index'),
	'Manage',
);
$this->menu=array(
	array('label'=>'List DriverZhaopin', 'url'=>array('index')),
	array('label'=>'Create DriverZhaopin', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-zhaopin-grid', {
		data: $(this).serialize()
	});
	getItemCountString($('#city_id').val());
	return false;
});
");

$selCityId = isset($_REQUEST['city_id'])? $_REQUEST['city_id'] : Yii::app()->user->city;
$selDistrictId = isset($_REQUEST['district_id'])? $_REQUEST['district_id'] : 0;
$selStatus = isset($_REQUEST['status'])? $_REQUEST['status'] : 0;
$selRoad = isset($_REQUEST['road_exam'])? $_REQUEST['road_exam'] : -1;
$selExam = isset($_REQUEST['exam'])? $_REQUEST['exam'] : -1;
$selSrc = isset($_REQUEST['src'])? $_REQUEST['src'] : '';
$id_card = isset($_REQUEST['id_card']) ? $_REQUEST['id_card'] : '';	
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';	
$mobile = isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : '';
$batch = isset($_REQUEST['batch']) ? $_REQUEST['batch'] : '';
$inform_status = isset($_REQUEST['inform_status'])?$_REQUEST['inform_status']:0;
$shownum = isset($_REQUEST['shownum'])?$_REQUEST['shownum']:50;
$rank = isset($_REQUEST['rank'])?$_REQUEST['rank']:'';
?>

<table>
    <tr>
        <td><h1>司机报名管理</h1></td>
        <td>
            <?php echo CHtml::link('面试',Yii::app()->createUrl('/recruitment/interview'),array('class'=>'btn btn-success', 'style'=>'margin-left:30px')); ?>
            <?php echo CHtml::link('签约',Yii::app()->createUrl('/recruitment/driverfastentry'),array('class'=>'btn btn-success', 'style'=>'margin-left:30px')); ?>
        </td>
    </tr>
</table>
<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get'
)); ?>
	<div class="row-fluid">
		<div class="span3">
		<?php
			$city = Dict::items('city');
			if(Yii::app()->user->city!=0){
				foreach($city as $k=>$v){
					if($k!==Yii::app()->user->city){
						unset($city[$k]);
					}
				}
				$city[-1] ='--选择城市--';
			}
			echo CHtml::label('城市选择','city'); 
			echo CHtml::dropDownList('city_id',
						$selCityId,
						$city,
				array(
					'ajax' => array(
					'type'=>'POST', //request type
					'url'=>Yii::app()->createUrl('recruitment/district'),
					'update'=>'#district_id', //selector to update
					'data'=>array('city_id'=>'js:$("#city_id").val()', 'admin'=>'1')
					))
			); 
		?>		
		</div>
		
		<div class="span3">
<?php
	//@author libaiyang 2013-05-06 增加区域选择
	echo CHtml::label('地区选择','city_id'); 
	$districts = District::model()->findAll('city_id=:city_id', array(':city_id' => $selCityId));
	$districts = CHtml::listData($districts,'id','name');
	$districts[0] = '--全部--';
	echo CHtml::dropDownList('district_id',
				$selDistrictId,
				$districts,
		array()
	); 
?>
</div>

        <div class="span3">
            <label for="id_card">报名流水号</label>
            <?php
            echo CHtml::textField('queue_number','');
            ?>
        </div>

	</div>

<div class="row-fluid">
    <div class="span3">
        <?php
        $status = DriverRecruitment::$status_dict;
        $userCity = Yii::app()->user->city;
        $open = DriverOrder::model()->checkOpenCity($selCityId);
        if(empty($open)){
            //去掉装备
            unset($status[DriverRecruitment::STATUS_SIGNED]);
        }else{
            //去掉路考、在线考核、面试通过
            unset($status[DriverRecruitment::STATUS_INTERVIEW_PASS]);
            unset($status[DriverRecruitment::STATUS_ROAD_PASS]);
            unset($status[DriverRecruitment::STATUS_EXAM_PASS]);

        }
        echo CHtml::label('状态选择','status');
        echo CHtml::dropDownList('status',
            $selStatus,
            $status,
            array('class'=>'span12')
        );
        ?>
    </div>
    <?php
    $roadList = DriverRecruitment::$road_dict;
    $examList = DriverRecruitment::$exam_dict;
    $userCity = Yii::app()->user->city;
    $open = DriverOrder::model()->checkOpenCity($userCity);
    echo '<div class="span3">';
    echo CHtml::label('路考状态(测试)', 'road_new');
    echo CHtml::dropDownList('road_new',
        $selRoad,
        $roadList,
        array('class' => 'span12')
    );
    echo '</div>';
    echo '<div class="span3">';
    echo CHtml::label('在线考核状态(测试)', 'exam');
    echo CHtml::dropDownList('exam',
        $selExam,
        $examList,
        array('class' => 'span12')
    );
    echo '</div>';
    ?>

</div>

	<div class="row-fluid">
		<div class="span3">
		<?php
			$src = $status = Dict::items('recruitment_src');;
			$src[''] = '--全部--';
			ksort($src);
			echo CHtml::label('来源渠道选择','src'); 
			echo CHtml::dropDownList('src',
						$selSrc,
						$src,array('class'=>'span12')
			); 
		?>
		</div>
		<div class="span3">
			<label for="mobile">报名时间</label>
			<?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>'apply_start',
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
                    'style'=>'width:70px',
                ),
            ));
            ?>
            到
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>'apply_end',
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
                    'style'=>'width:70px',
                ),
            ));
            ?>
		</div>
		<div class="span3">
			<label for="mobile">通知次数</label>
			<?php
			$exam_times_list = array('-1'=>"--全部--",'0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4, '5'=>'大于4');
			echo CHtml::dropDownList('exam_times',$exam_times, $exam_times_list);
		?>
		</div>			
	</div>
	<div class="row-fluid">
		<div class="span3">
			<label for="id_card">身份证号</label>
			<input type="text" id="id_card" class="span12" name="id_card" value="<?php echo $id_card;?>" />		
		</div>
		<div class="span3">
			<label for="name">姓名</label>
			<input type="text" id="name" class="span12" name="name" value="<?php echo $name;?>" />		
		</div>
		<div class="span3">
			<label for="mobile">手机号</label>
			<input type="text" id="mobile" class="span12" name="mobile" value="<?php echo $mobile;?>" />		
		</div>

	</div>
	<div class="row-fluid">
        <div class="span3">
			<label for="mobile">面试时间</label>
			<?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>'batch_start',
                'options'=>array(
                    'showAnim'=>'fold',
                    'showOn'=>'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly'=>true,
                    //'minDate'=>'new Date()',
                    'dateFormat'=>'yymmdd',
                    'changeYear'=>true,
                    'changeMonth'=> true,
                ),
                'htmlOptions'=>array(
                    'style'=>'width:70px',
                ),
            ));
            ?>
            到
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>'batch_end',
                'options'=>array(
                    'showAnim'=>'fold',
                    'showOn'=>'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly'=>true,
                    //'minDate'=>'new Date()',
                    'dateFormat'=>'yymmdd',
                    'changeYear'=>true,
                    'changeMonth'=> true,
                ),
                'htmlOptions'=>array(
                    'style'=>'width:70px',
                ),
            ));
            ?>
		</div>
        <div class="span3">
			<label for="mobile">显示数量</label>
            <?php //echo CHtml::dropDownList('shownum', $shownum, array(30=>30, 50=>50, 100=>100)); ?>
			<!-- changed by aiguoxin -->
			<?php
            echo CHtml::textField('shownum','50');
			?>
		</div>

        <div class="span3">
            <label for="id_card">工作方式</label>
            <?php
            //$work_type_list = Dict::items('work_type');
            $work_type_list = array('-1'=>'全部','0'=>'全职','1'=>'兼职');
            echo CHtml::dropDownList('work_type',$work_type,$work_type_list);
            ?>
        </div>
	</div>
    <div class="row-fluid">
    	<div class="span3">
			<label for="id_card">司机等级</label>
			<?php
			$ranks = array(''=>'全部','A'=>'A','B'=>'B','C'=>'C');
			echo CHtml::dropDownList('rank',
						$rank,
						$ranks,
				array('class'=>'span12')
			);
			?>
		</div>

    </div>
<!--测试城市才显示-->
</div>

	<div class="row-fluid">
		<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
    </div>
		
</div>
<?php $this->endWidget(); ?>

<div class="row-fluid" id ="item_count_string" name="item_count_string">	
<h3><?php echo $this->getItemCountString($selCityId);?></h3>
</div>

<?php 

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'报名信息',
        'autoOpen'=>false,
		'width'=>'800',
		'height'=>'600',
		'modal'=>true,
		'buttons'=>array(
        	'Close'=>'js:function(){$("#mydialog").dialog("close");}'
		),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_informexam_frame" width="100%" height="100%" style="border:0px"></iframe>';

$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php //echo CHtml::Button('创建批次',array('class'=>'btn btn-success','id'=>'batch_btn')); ?>
<?php //echo CHtml::Button('通知路考',array('class'=>'btn btn-success','id'=>'inform_btn', 'func'=>'send_msg')); ?>
<?php //echo CHtml::Button('签约',array('class'=>'btn btn-success','id'=>'entry_btn')); ?>
<?php //echo CHtml::Button('激活',array('class'=>'btn btn-success','id'=>'activate_btn')); ?>
<?php //echo CHtml::dropDownList('rank', '0', array(''=>'修改司机等级','A'=>'A','B'=>'B','C'=>'C'), array('id'=>'change_driver_type')); ?>

<?php echo CHtml::Button('发送短信',array('class'=>'btn btn-success','id'=>'send_msg_btn', 'act'=>'send_msg_btn', 'func'=>'send_msg')); ?>
<?php echo CHtml::Button('面试通知',array('class'=>'btn btn-success','id'=>'inform_btn', 'act'=>'inform_btn','func'=>'send_msg','style'=>'margin-left:30px')); ?>
<?php echo CHtml::Button('删除',array('class'=>'btn btn-success','id'=>'delete_btn', 'act'=>'delete_btn','func'=>'del_driver','style'=>'margin-left:30px')); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    'selectableRows'=>2,
	'columns'=>array(
        /*
		array(
				'name'=>'<input type="checkbox" />',
				'type' => 'raw',
				//'value' =>'Yii::app()->user->city==0 ? CHtml::checkBox("recruitment_id[]",false,array("value"=>"$data->id","disabled"=>"disabled")) : CHtml::checkBox("recruitment_id[]",false,array("value"=>"$data->id"))'
				'value' =>'Yii::app()->controller->getCheckboxDisabled($data->city_id,$data->id)'
		),
        */
        array(
            'class' => 'CCheckBoxColumn',
            'checkBoxHtmlOptions' => array(
                'name' => 'recruitment_id[]'
            )
        ),
		array(
			'name'=>'报名流水号',
			'type' => 'raw',
			'value'=>'CHtml::link(Yii::app()->controller->getRecruitmentQueueNumber($data->id, $data->city_id), "javascript:void(0);", array (
			"onclick"=>"{zhaopinDialogdivInit($data->id);}"));',
        ),
		array (
			'name' => 'name',
			'type' => 'raw',
			'value' => 'CHtml::link(CHtml::encode($data->name), "javascript:void(0);", array (
			"onclick"=>"{zhaopinDialogdivInit($data->id);}"));'
		),
        'id_card',
		array(
			'name'=>'居住城市',
			'value'=>'Yii::app()->controller->getRecruitmentCity($data->city_id)',
        ),
		/* 
		array(
			'name'=>'居住地区',
			'value'=>'Yii::app()->controller->getRecruitmentDistrict($data->district_id)',
		),
		array(
			'name'=>'担保状态',
			'value'=>'Yii::app()->controller->getRecruitmentAssure($data->assure)',
		),
		*/
		array(
			'name'=>'司机状态',
			'value'=>'Yii::app()->controller->getRecruitmentStatus($data->status)',
		),
		array(
			'name'=>'面试时间',
			'type'=>'raw',
			'value'=>'($data->inform_time == 0 || $data->inform_time == "") ? "未通知面试" : date("Y-m-d", $data->inform_time)',
		),
        /*
		array(
			'name'=>'路考通过',
			'type'=>'raw',
			'value'=>'Yii::app()->controller->getDriverExamStatus($data->id,$data->status,$data->city_id)',
			),
		array(
			'name'=>'重新路考',
			'type'=>'raw',
			'value'=>'Yii::app()->controller->getDriverResetExamStatus($data->id,$data->status,$data->city_id)',
		),
			array(
			'name'=>'重新考试',
			'type'=>'raw',
			'value' => 'Yii::app()->controller->getDriverResetSignup($data->id,$data->status,$data->city_id)',
			),
		array(
			'name'=>'加入批次',
			'type'=>'raw',
			'value' => '($data->status != 7) ? "不可加入" : CHtml::link("加入批次", "javascript:void(0);", array (
			"onclick"=>"{addbatch($data->id);}"));'
		),
        array(
            'name'=>'重新排队',
            'type'=>'raw',
            'value' => 'CHtml::link("重新排队", "javascript:void(0);", array (
					"onclick"=>"{resetqueue($data->id);}"));'
        ),
        */
        array(
            'name'=>'通知次数',
            'type'=>'raw',
            'value' => '($data->exam_times < 1) ? "未通知" : $data->exam_times."次"'
        ),
//        array(
//            'name'=>'照片',
//            'type'=>'raw',
//            'value' => 'Yii::app()->controller->showUploadLink($data->driver_id,$data->status)'
//        ),

        array(
            'name'=>'路考状态(测试)',
            'type'=>'raw',
            'value'=>array($this,'getRoadState'),
        ),
        array(
            'name'=>'在线考核状态(测试)',
            'type'=>'raw',
            'value'=>array($this,'getExamState'),
        ),
		array(
			'class'=>'CButtonColumn',
            'header'=>'操作',
            'template'=>'{edit} {del} {msg} {sort} {status}',
			'buttons'=>array(
					'edit' => array(
							'label'=>'修改',
							'url'=>'Yii::app()->createUrl("/recruitment/update",array("id"=>$data->id))',
                            'options'=>array('target'=>'_blank'),
                    ),
                    'del' => array(
                            'label'=>'删除',
                            'url'=>'"javascript:del_user($data->id)"',
                            'options'=>array('style'=>'cursor:pointer;'),
                    ),
                    'msg' => array(
                            'label' => '发送短信',
                            'url' => '"javascript:sendMsgSingle($data->id)"',
                    ),
                    'sort' => array(
                            'label' => '重新排队',
                            'url'=>'"javascript:resetqueue($data->id)"',
                            'options' => array('style'=>'cursor:pointer'),
                            'visible' => '$data->status == 1 ? true : false',
                    ),
                    'status' => array(
                            'label' => '状态',
							'url'=>'Yii::app()->createUrl("/recruitment/status",array("id"=>$data->id))',
                            'options'=>array('target'=>'_blank'),
                    )
				),
		),
	),
)); 


?>
<script>

window.onload = function() {
    jQuery('.ui-datepicker-trigger').remove();
}

function getItemCountString(cityId){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/recruitment/getitemcount');?>',
		'data':'id='+cityId,
		'type':'get',
		'success':function(data){
			$('#item_count_string').html(data);
		},
		'cache':false		
	});
	return false;
}

function zhaopinDialogdivInit(zhaopinId){
	$(".ui-dialog-title").html("查看信息");
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/recruitment/view');?>',
		'data':'id='+zhaopinId,
		'type':'get',
		'success':function(data){
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	jQuery("#mydialog").dialog("open");
	return false;
}

function changestatus(zhaopinId, status) {
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/recruitment/changestatus');?>',
		'data':'id='+zhaopinId + '&status=' + status + '&city_id=' + $('#city_id').val() + 
				'&id_card=' + $('#id_card').val() + '&name=' + $('#name').val() + '&mobile=' + $('#mobile').val() +
				'&exam=' + $('#exam').val() + '&src=' + $('#src').val(),
		'type':'get',
		'success':function(data){
			$.fn.yiiGridView.update('driver-zhaopin-grid', {
				data: data
			});			
		},
		'cache':false		
	});
	return false;	
}

$(function(){
	//全选反选
	$("#checkall").live("click",function(){
		$("input[name='recruitment_id[]']:not(:disabled)").attr('checked', this.checked);
	});

	//batch delete add by aiguoxin
	$("[func='del_driver']").click(function(){
		$(".ui-dialog-title").html("删除理由");
		id_length = $("input[name='recruitment_id[]']:checked").length;
		if(id_length<=0){
			alert("请选择需要删除的司机！");
			return false;
		}
		id_str = '';
		for(i=0;i<id_length;i++)
		{
			id_str += $("input[name='recruitment_id[]']:checked").eq(i).val()+',';
		}
		url = '<?php echo Yii::app()->createUrl('/recruitment/batchrecyclereason');?>&ids_str='+id_str;
		$("#view_informexam_frame").attr("src",url);
		$("#mydialog").dialog("open");
    });


    //通知路考
	$("[func='send_msg']").click(function(){
   		var id = jQuery(this).attr('act');
		if (id == 'inform_btn') {
			$(".ui-dialog-title").html("面试通知");
			var action = '<?php echo DriverRecruitment::SMS_TYPE_EXAM;?>';
		} else if (id == 'send_msg_btn') {
			$(".ui-dialog-title").html("发送短信");
			var action = '<?php echo DriverRecruitment::SMS_TYPE_COMMON;?>';
		} 
		id_length = $("input[name='recruitment_id[]']:checked").length;
		if(id_length<=0){
			if (id == 'inform_btn') {
				alert("请选择需要通知路考的司机！");
			} else if (id == 'send_msg_btn') {
				alert("请选择需要发送短信的司机！");
			}
			return false;
		}
		id_str = '';
		for(i=0;i<id_length;i++)
		{
			id_str += $("input[name='recruitment_id[]']:checked").eq(i).val()+',';
		}
		url = '<?php echo Yii::app()->createUrl('/recruitment/informexam');?>&ids_str='+id_str+'&batch='+$("#batch").val()+'&action='+action;
		$("#view_informexam_frame").attr("src",url);
		$("#mydialog").dialog("open");
    });
	//签约
	$("#entry_btn").click(function(){
		$(".ui-dialog-title").html("司机签约");
		id_length = $("input[name='recruitment_id[]']:checked").length;
		if(id_length<=0){ alert("请选择需要签约的司机！");return false;	}
		id_str = '';
		for(i=0;i<id_length;i++)
		{
			id_str += $("input[name='recruitment_id[]']:checked").eq(i).val()+',';
		}
		url = '<?php echo Yii::app()->createUrl('/recruitment/showdriverentry');?>&ids_str='+id_str;
		$("#view_informexam_frame").attr("src",url);
		$("#mydialog").dialog("open");
	});
	//激活
	$("#activate_btn").click(function(){
		$(".ui-dialog-title").html("司机激活");
		id_length = $("input[name='recruitment_id[]']:checked").length;
		if(id_length<=0){ alert("请选择需要激活的司机！");return false;	}
		id_str = '';
		for(i=0;i<id_length;i++)
		{
			id_str += $("input[name='recruitment_id[]']:checked").eq(i).val()+',';
		}
		url = '<?php echo Yii::app()->createUrl('/recruitment/showactivation');?>&ids_str='+id_str;
		$("#view_informexam_frame").attr("src",url);
		$("#mydialog").dialog("open");
	});
	//点击关闭按钮更新列表
	$(".ui-dialog-buttonset button").live('click',function(){
		$.fn.yiiGridView.update('driver-zhaopin-grid');
		$("#view_informexam_frame").attr("src","");
		
		$('#dialogdiv').html('');
	});
    //创建批次
	$("#batch_btn").click(function(){
		$(".ui-dialog-title").html("创建批次");
		url = '<?php echo Yii::app()->createUrl('/recruitment/driverbatchcreate');?>';
		$("#view_informexam_frame").attr("src",url);
		$("#mydialog").dialog("open");
	});
	//批量修改司机准驾类型
	jQuery('#change_driver_type').change(function(){
		//修改后的准驾类型
		var driver_type = jQuery('#change_driver_type').val();
		var dirver_list = [];
		var selected_length = jQuery("input[name='recruitment_id[]']:checked").length;
		if (selected_length<=0) {
			alert('请选择要修改等级的司机');
			return false;
		} else if (confirm('确认修改选中司机等级吗？')){
			if (driver_type=='') {
				alert('请选择司机等级');
				return false;
			}
			jQuery.each(jQuery("input[name='recruitment_id[]']:checked"), function(i,v){
				dirver_list.push(jQuery(this).val());
			});
			jQuery.post(
				'<?php echo Yii::app()->createUrl('/recruitment/changesdrivertype'); ?>',
				{
					'driver_list' : dirver_list,
					'driver_type' : driver_type
				},
				function(d) {
					if (d.status) {
						alert('修改成功');
					} else {
						alert(d.msg);
					}
				},
				'json'
			);
		}	
	});
	
});

function addbatch(user_id){

	url = '<?php echo Yii::app()->createUrl('/recruitment/informexam');?>&ids_str='+user_id+'&batch='+$("#batch").val();
	$("#view_informexam_frame").attr("src",url);
	$("#mydialog").dialog("open");
}
function changestatus(zhaopinId, status) {
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/recruitment/changestatus');?>',
		'data':'id='+zhaopinId + '&status=' + status,
		'type':'get',
		'success':function(data){
			alert("操作成功");
			$.fn.yiiGridView.update('driver-zhaopin-grid');
		},
		'cache':false		
	});
	return false;	
}
function resetqueue(zhaopinId){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/recruitment/resetqueue');?>',
		'data':'id='+zhaopinId,
		'type':'get',
		'success':function(data){
			if(data==1){
				alert("操作成功");
				$.fn.yiiGridView.update('driver-zhaopin-grid');
			}else{
				alert('操作失败，请确定您有操作权限');
			}
		},
		'cache':false		
	});
}
function del_user(id){
	$(".ui-dialog-title").html("删除理由");
	if(id==0||id==""){ alert("参数错误，请刷新页面！");return false;	}

	url = '<?php echo Yii::app()->createUrl('/recruitment/recyclereason');?>&id='+id;
	$("#view_informexam_frame").attr("src",url);
	$("#mydialog").dialog("open");
}
function uploadimage(driver_id){
	$(".ui-dialog-title").html("上传照片");
	url = '<?php echo Yii::app()->createUrl('/recruitment/uploadimage');?>&driver_id='+driver_id;
	$("#view_informexam_frame").attr("src",url);
	$("#mydialog").dialog("open");
}

function sendMsgSingle(id) {
    url = '<?php echo Yii::app()->createUrl('/recruitment/informexam');?>&ids_str='+id+'&batch='+$("#batch").val()+'&action='+'<?php echo DriverRecruitment::SMS_TYPE_COMMON;?>';
	$("#view_informexam_frame").attr("src",url);
	$("#mydialog").dialog("open");
}
</script>
