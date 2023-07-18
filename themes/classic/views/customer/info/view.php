<h4><?php echo $model->phone?>的信息</h4>
<table border="1" width="100%">
	<tr><td>姓名</td><td>性别</td><td>生日</td><td>地区</td></tr>
	<tr style="background:#33FFCC;">
		<td><?php echo $model->name?$model->name:'未填'; ?></td>
		<td><?php echo $model->gender?CustomerMain::$gender_dict[$model->gender]:'未填'; ?></td>
		<td><?php echo $model->birthday?$model->birthday:'未填'; ?></td>
		<td><?php echo $model->city_id?$views['city_name']:'未填';?></td>
	</tr>
	<tr><td>手机号码</td><td>备用号码</td><td>类型</td><td>原vip卡号</td></tr>
	<tr style="background:#33FFCC;">
		<td><?php echo $model->phone?></td>
		<td><?php echo $model->backup_phone?$model->backup_phone:'无'; ?></td>
		<td><?php echo $model->type?CustomerMain::$type_dict[$model->type]:'未知'; ?></td>
		<td><?php echo $model->vip_card?$model->vip_card:'未知'; ?></td>
	</tr>
	<tr><td>用户来源</td><td>状态</td><td>信用等级</td><td>账户余额</td></tr>
	<tr style="background:#33FFCC;">
		<td><?php echo $model->channel?CustomerMain::$channel_dict[$model->channel]:'未知'; ?></td>
		<td><?php echo $model->status?CustomerMain::$status_dict[$model->status]:'未知'; ?></td>
		<td><?php echo $model->credit?$model->activity:'无'; ?></td>
		<td><?php echo $model->amount?$model->activity:'无'; ?></td>
	</tr>
	<tr><td>活跃度</td><td>企业名称</td><td>发票抬头</td><td>发票备注</td></tr>
	<tr style="background:#33FFCC;">
		<td><?php echo $model->activity?$model->activity:'无'; ?></td>
		<td><?php echo $model->company?$model->company:'未填写'; ?></td>
		<td><?php echo $model->invoice_title?$model->invoice_title:'未知'; ?></td>
		<td><?php echo $model->invoice_remark?$model->invoice_remark:'未填写'; ?></td>
	</tr>
</table>