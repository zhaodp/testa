<?php
	$this->pageTitle = '余额转移';
?>
<h2 class=" offset2">余额转移</h2>
<br>
<div class="form-horizontal span6 offset2">
	<form action="/v2/index.php?r=account/balanceTransfer" method="POST">
		<label class="control-label">迁出账户:</label>
		<div class="controls">
			<input type="text" name="sourceAccount" placeholder="普通用户手机号">
		</div>
		<label class="control-label">迁入账户:</label>
		<div class="controls">
			<input type="text" name="targetAccount" placeholder="迁入vip手机号或者司机工号">
		</div>
		<label class="control-label">转移金额:</label>
		<div class="controls">
			<input type="text" name="check" placeholder="转移金额">
		</div>
		<div class="controls">
			<input type="submit"  value="确认转账">
		</div>
	</form>
</div>