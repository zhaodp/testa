<?php
Yii::app()->getClientScript()->registerScriptFile(SP_URL_IMG . "WdatePicker/WdatePicker.js");
$this->pageTitle = '定时任务';
?>
<h1><?php echo $this->pageTitle;?></h1>

<script>
function log_error(obj) {

    var tmp = $(obj).attr('href').split('|');
    var host = tmp[0];
    var filename = tmp[1];
    var filetmps = filename.split(".");
    var log_error_file = "";

    for(var i=0; i<filetmps.length; i++) {
        if (i == filetmps.length-1) {
        	log_error_file += "_error" + "." + filetmps[i];
        }
        else if (i != 0 ) {
        	log_error_file += filetmps[i] + ".";
        }
        else {
        	log_error_file += filetmps[i];
        }
    }
    window['log_error_file'] = log_error_file;
    window['host'] = host;
    WdatePicker({el:'log_date', dateFmt:'yyyy/MM/dd',onpicked:log_error_picked_func});
}

function log(obj) {

    var tmp = $(obj).attr('href').split('|');
    var host = tmp[0];
    var filename = tmp[1];

    window['log_file'] = filename;
    window['host'] = host;
    WdatePicker({el:'log_date', dateFmt:'yyyy/MM/dd',onpicked:log_picked_func});
}

function log_error_picked_func(dp) {
    var url= window['host'] + '/' + $("#log_date").val() + '/' + window['log_error_file'];
    window.open(url, '_blank');
}

function log_picked_func(dp) {
    var url= window['host'] + '/' + $("#log_date").val() + '/' + window['log_file'];
    window.open(url, '_blank');
}

</script>
<div class="search-form">
<input type="hidden" id="log_date" value="<?php echo date("Y/m/d");?>">
<?php
    $host = isset($_REQUEST['host']) ? $_REQUEST['host'] : '';
    $task = isset($_REQUEST['task']) ? strip_tags($_REQUEST['task']) : '';
    $command = isset($_REQUEST['command']) ? strip_tags($_REQUEST['command']) : '';
    $active = isset($_REQUEST['active']) ? strip_tags($_REQUEST['active']) : '';
    $owner = isset($_REQUEST['owner']) ? strip_tags($_REQUEST['owner']) : '';
    $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
<div class="row-fluid">
    <div class="span3">
        <?php echo CHtml::label('主机','host',array('style'=>"display:inline"));  ?>
        <?php echo CHtml::dropDownList('host', $host, ScronHost::model()->getStartHost(),array('empty'=>''));?>
    </div>
   <div class="span3">
    <label style="display:inline" for="task">任务</label>
    <?php echo CHtml::textField('task',$task) ?>
    </div>
    <div class="span3">
        <label style="display:inline" for="owner">责任人</label>
        <?php echo CHtml::textField('owner',$owner) ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span3">
    <label style="display:inline" for="command">命令</label>
    <?php echo CHtml::textField('command',$command) ?>
    </div>
    <div class="span3">
        <?php echo CHtml::label('状态','host',array('style'=>"display:inline"));  ?>
        <?php echo CHtml::dropDownList('active', $active, array('1'=>'已激活','0'=>'未激活'),array('empty'=>''));?>
    </div>

    <div class="span2">
        <?php echo CHtml::submitButton('查询');?>
	&nbsp;&nbsp;
	<?php echo CHtml::link('重置',array('scron/index')); ?>
    </div>


</div>
    <?php $this->endWidget(); ?>
</div>

    </div><!-- search-form -->


<div class="btn-group">
	<?php echo CHtml::link('添加任务', array("create"),array('class'=>"search-button btn-primary btn",'style'=>'margin-right:5px'));?>

    <?php echo CHtml::link('主机管理', array("scronHost/index"),array('class'=>"search-button btn-primary btn"));?>

</div>
 <br >
<div class="row-fluid">
    <h3><?php
        if(Yii::app()->user->hasFlash("host_empty")){
            echo Yii::app()->user->getFlash("host_empty");
        }
        ?></h3>
</div>
<br >
<?php
$this->widget ('zii.widgets.grid.CGridView', array (
		'id' => 'scron-grid',
		'dataProvider' => $dataProvider,
		'cssFile'=>SP_URL_CSS . 'table.css',
		'pagerCssClass'=>'pagination text-center', 
		'pager'=>Yii::app()->params['formatGridPage'], 
		'itemsCssClass'=>'table table-condensed',
		'htmlOptions'=>array('class'=>'row span11'),
		'columns' => array (
			'cronId'=>array(
                'header'=>'id',
                'name'=>'cronId',
            ),
			'task'=>array(
                'header'=>'任务名',
                'name'=>'task',
                'htmlOptions'=>array(
                    'width'=>'50'
                )
            ),
            'owner'=>array(
                'header'=>'责任人',
                'name'=>'owner',
                'htmlOptions'=>array(
                    'width'=>'50'
                )
            ),
			'host'=>array(
                'header'=>'host',
                'value'=>'$data->getHostName($data->host)'
            ),
            array(
                'header'=>'crontab',
                'value'=>'$data->getCron()',
                'htmlOptions'=>array(
                    'width'=>'300'
                )
            ),
            array(
                'name'=>'logFile',
                'header'=>'日志',
                'htmlOptions'=>array(
                    'width'=>'30'
                )
            ),
            array(
                'name'=>'active',
                'type'=>'raw',
                'value'=>'$data->getActiveName()',//'$data->active==1?"已激活":"未激活"',
            ),
			'process'=>array(
                'header'=>'maxProc',
                'name'=>'process',
            ),
			/*array(
                'header'=>'标识',
				'name'=>'isQueue',
				'value'=>'$data->isQueue==1?"队列":"不是队列"',
			),*/
			array(
                'htmlOptions'=>array(
                    'width'=>'150'
                ),
				'name'=>'runAt',
				'value'=>'$data->runAt!=0?date("Y-m-d H:i:s",$data->runAt):0',

			),
			/*array(
                'header'=>'超时时间',
				'name'=>'timeout',
                'value'=>'$data->timeout."分钟"',
				'htmlOptions'=>array(
					'width'=>'60'
				)
			),*/
			/*array(
                'header'=>'超时回调',
				'name'=>'callback',
				'htmlOptions'=>array(
					'width'=>'100'
				)
			),*/
            'user',
			array (
				'header' => '<span>操作</span>',
				'class' => 'CButtonColumn',
				'buttons'=>array(
					'stop'=>array(
						'label'=>'失效',
						'url'=>'Yii::app()->controller->createUrl("deal", array("id"=>$data->cronId,"type"=>"stop"))',
						'options'=>array(),
					),
					'start'=>array(
						'label'=>'激活',
						'url'=>'Yii::app()->controller->createUrl("deal", array("id"=>$data->cronId,"type"=>"start"))', 
						'options'=>array(), // HTML options for the button tag
					),
                    'copy'=>array(
                        'label'=>'拷贝',
                        'url'=>'Yii::app()->controller->createUrl("copyDeal", array("id"=>$data->cronId))',
                        'options'=>array(), // HTML options for the button tag
                    ),
                    'log'=>array(
                        'label'=>'日志',
                        'click'=>'function() {
                            log(this);
                            return false;
                        }',
                        'url'=>'"http://".$data->getLogDomain($data->host)."|".$data->logFile',
                    ),

                    'log_error'=>array(
                        'label'=>'错误',
                        'click'=>'function() {
                            log_error(this);
                            return false;
                        }',
                        'url'=>'"http://".$data->getLogDomain($data->host)."|".$data->logFile',
                    )

				),
				'htmlOptions' => array (
					'width' => '200'
				),
				//'viewButtonOptions'=>array('id'=>'','onClick'=>'return dialog(this.href);'),
				'template' => '{update} {delete}{stop} {start} {copy} {log} {log_error}'
			),
		)
));

$dataProvider->model->restDbConnection();
?>
