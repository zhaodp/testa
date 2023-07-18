<?php 
$this->pageTitle = $driver->name . ' - 信息';
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery');
$cs->registerScriptFile(SP_URL_STO . 'www/js/Location.js');
?>

<style type="text/css">
.table-view {
	padding: 12px;
	width: 680px;
	text-align: left;
}

.table-view table.items {
	width: 680px;
}

.table-view table.items td {
	border: 1px solid green;
	font-size: 15px;
	padding: 2px;
	width: 106px;
}

.table-view table.items th {
	font-size: 15px;
	font-wight: blod;
}
</style>

<div class="view">
	<div class="table-view">
	<?php
	echo $driver->name . ' - 信息';
	
	?>
		<table class="items">
			<tr>
				<td><?php echo $driver->getAttributeLabel('user');?></td>
				<td><?php echo $driver->user;?></td>
				<td rowspan="15"><img width='250px' src='<?php echo Driver::getPictureUrl($driver->user, $driver->city_id); ?>'></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('name');?></td>
				<td><?php echo $driver->name;?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('gender');?></td>
				<td><?php echo Dict::item('gender', $driver->gender); ?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('imei');?></td>
				<td><?php echo $driver->imei;?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('phone');?></td>
				<td><?php echo Common::parseDriverPhone($driver->phone);?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('id_card');?></td>
				<td><?php echo $driver->id_card;?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('id_driver_card');?></td>
				<td><?php echo $driver->id_driver_card;?></td>
			</tr>
            <tr>
                <td><?php echo $driver->getAttributeLabel('assure'); ?></td>
                <td><?php echo Driver::$assure_dict[$driver->assure] ? Driver::$assure_dict[$driver->assure] : '';?></td>
            </tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('domicile');?></td>
				<td><?php echo $driver->domicile;?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('car_card');?></td>
				<td><?php echo $driver->car_card;?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('year');?></td>
				<td><?php echo $driver->year;?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('level');?></td>
				<td><?php echo $driver->level;?></td>
			</tr>
			<tr>
				<td>状态</td>				
				<td><?php echo $status; ?></td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('city_id');?></td>
				<td>
				<?php echo Dict::item('city', $driver->city_id); ?>
				</td>
			</tr>
			<tr>
				<td><?php echo $driver->getAttributeLabel('ext_phone');?></td>
				<td><?php echo Common::parseDriverPhone($driver->ext_phone);?></td>
			</tr>
			<tr>
				<td>代驾次数</td>
				<td colspan="2"><?php if (isset($ext)) echo $ext->service_times;?></td>
			</tr>
			<tr>
				<td>未报单</td>
				<td colspan="2"><?php echo Driver::getDriverReadyOrder($driver->user);?></td>
			</tr>
			<tr>
				<td>好评次数</td>
				<td colspan="2"><?php if (isset($ext)) echo $ext->high_opinion_times; ?></td>
			</tr>
			<tr>
				<td>差评次数</td>
				<td colspan="2"><?php if (isset($ext)) echo $ext->low_opinion_times; ?></td>
			</tr>
			<tr>
				<td>推荐人</td>
				<td colspan="2"><?php echo $driver->recommender;?></td>
			</tr>
		</table>
	</div>
    <?php
    if($recruitment_log){
        echo '<pre>司机追溯</pre>';
        foreach($recruitment_log as $item){
            echo '<pre>'.date('Y-m-d H:i',$item['time']).'&nbsp;&nbsp;'.$item['message'].'</pre>';
        }
    }else{ echo '暂无日志';}
    ?>
	<div class="table-view">
    <?php
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-grid',
	'dataProvider'=>$logs,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'htmlOptions'=>array('class'=>'row span11'),
	'columns'=>array(
		array(
			'name'=>'type',
			'value'=>'Dict::item("driver_log_status", $data->type)'
		),
		array (
			'name'=>'记录',
			'headerHtmlOptions'=>array (
				//'width'=>'300px',
			),
			'type'=>'raw',
			'value'=>array($this,'driver_mark_reason')
		),
		'operator',
		array(
			'name'=>'created',
			'value'=>'date("Y-m-d H:i",$data->created)'),
		)));

    ?>
	</div>	
</div>

<script >
    var config = {
        provincial_option : {
            style : 'width : 150px'
        },
        city_option : {
            style : 'width : 250px'
        }
    }
    var s = new Location(config);
</script>
