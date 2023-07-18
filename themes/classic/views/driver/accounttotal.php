<?php
$this->pageTitle = '司机台账汇总';
?>

<h1><?php echo $this->pageTitle;?></h1>
<hr class="divider"/>

<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'account-form', 
	'enableAjaxValidation'=>false,
));
?>
城市：<?php echo $form->dropDownList($model,'city_id', Dict::items('city'));?>
<?php 
echo CHtml::submitButton('查询'); 

$this->endWidget();
?>
<h4>信息费总余额：<?php echo $total->t0;?></h4>
<h4>VIP账户总余额：<?php echo $totalvip->balance;?>（注：当前阶段未区分VIP所在城市）</h4>
<table class="table table-striped">
	<thead>
		<tr>
			<th width="30px" nowrap="nowrap" rowspan=2>年月</th>
			<th width="30px" nowrap="nowrap" colspan=5 style="text-align:center">信息费</th>
			<th width="30px" nowrap="nowrap" rowspan=2>发票扣税</th>
			<th width="30px" nowrap="nowrap" rowspan=2>保险费</th>
			<th width="30px" nowrap="nowrap" rowspan=2>罚金</th>
			<th width="30px" nowrap="nowrap" rowspan=2>总计</th>
			<th width="30px" nowrap="nowrap" rowspan=2>VIP费用转信息费</th>
			<th width="30px" nowrap="nowrap" rowspan=2>优惠券转信息费</th>			
			<th width="30px" nowrap="nowrap" rowspan=2>信息费充值</th>
		</tr>
		<tr>
			<th width="20px" nowrap="nowrap">5元</th>
			<th width="20px" nowrap="nowrap">10元</th>
			<th width="20px" nowrap="nowrap">15元</th>
			<th width="20px" nowrap="nowrap">20元</th>
			<th width="20px" nowrap="nowrap">合计</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($records as $record) {?>
		<tr class="odd">
			<td><?php echo $record['current_month'];?></td>
			<td><?php echo $record['x1']/-1;?></td>
			<td><?php echo $record['x2']/-1;?></td>
			<td><?php echo $record['x3']/-1;?></td>
			<td><?php echo $record['x4']/-1;?></td>
			<td><?php echo ($record['x1'] + $record['x2'] + $record['x3'] + $record['x4']) / -1;?></td>
			<td><?php echo $record['x5']/-1;?></td>
			<td><?php echo $record['x9']/-1;?></td>
			<td><?php echo $record['x7']/-1;?></td>
			<td><?php echo ($record['x1'] + $record['x2'] + $record['x3'] + $record['x4'] + $record['x5'] + $record['x7'] + $record['x9']) / -1;?></td>
			<td><?php echo $record['x6'];?></td>
			<td><?php echo $record['x10'];?></td>			
			<td><?php echo $record['x8'];?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<table class="table table-striped">
	<thead>
		<tr>
			<th width="30px" nowrap="nowrap" rowspan=2>年月</th>
			<th width="30px" nowrap="nowrap"  colspan=5 style="text-align:center">单数</th>
			<th width="30px" nowrap="nowrap" rowspan=2>司机毛收入</th>
			<th width="30px" nowrap="nowrap" rowspan=2>接单司机数</th>
			<th width="30px" nowrap="nowrap" rowspan=2>月平均接单数</th>
			<th width="30px" nowrap="nowrap" rowspan=2>月平均毛收入</th>
			<th width="30px" nowrap="nowrap" rowspan=2>日平均接单数</th>
			<th width="30px" nowrap="nowrap" rowspan=2>日平均毛收入</th>
		</tr>
		<tr>
			<th width="20px" nowrap="nowrap">5元</th>
			<th width="20px" nowrap="nowrap">10元</th>
			<th width="20px" nowrap="nowrap">15元</th>
			<th width="20px" nowrap="nowrap">20元</th>
			<th width="20px" nowrap="nowrap">合计</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($records as $key => $record) {?>
		<tr class="odd">
			<td><?php echo $record['current_month'];?></td>
			<td><?php echo $record['x1']/-5;?></td>
			<td><?php echo $record['x2']/-10;?></td>
			<td><?php echo $record['x3']/-15;?></td>
			<td><?php echo $record['x4']/-20;?></td>
			<td><?php echo $record['x1']/-5 + $record['x2']/-10 + $record['x3']/-15 + $record['x4']/-20;?></td>
			<td><?php echo $record['x0'];?></td>
			<td><?php echo $record['userCount'];?></td>
			<td><?php echo round(($record['x1']/-5 + $record['x2']/-10 + $record['x3']/-15 + $record['x4']/-20) / $record['userCount'], 2);?></td>
			<td><?php echo round($record['x0'] / $record['userCount'], 2);?></td>
			<?php 
			foreach ($monthdaily as $value) { 
				if ($value['current_month'] == $record['current_month']){
			?>
			<td><?php echo round($value['avgCount'], 2);?></td>
			<td><?php echo round($value['avgIncome'], 2);?></td>
			<?php }}?>
		</tr>
	<?php } ?>
	</tbody>
</table>
