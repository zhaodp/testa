<div class="well span12">

    <FORM method="post" action="">
		<div class="row-fluid">
		<div class="span3">
			<label>订单号码：</label> 
			<input type="input" name="order_id" value="<?php echo isset($_POST['order_id']) ? $_POST['order_id'] : '' ;?>" class="span12" size=20>
		</div>

		<div class="span2">
            <label for="">　</label>
            <input class="btn btn-primary" type="submit" name="yt0" value="搜索">
		</div>
		</div>
    </FORM>



</div>
<table class="table table-condensed">
<thead>
<tr>
<th >order_id</th>
<th >created</th>
<th >driver_id</th>
<th >state</th>
<th >description</th>
</thead>

<tbody>
<?php //desc
if ($order)
{

		//desc
	foreach ($order as $v)
	{
		?>
		<tr>
		<td><?php print_r($v['order_id']); ?></td>
		<td><?php print_r($v['created']); ?></td>
		<td><?php print_r($v['driver_id']); ?></td>
		<td><?php print_r($v['state']); ?></td>
		<td><?php print_r($v['description']); ?></td>
		</tr>
		<?php
	}


} else {?>
<tr><td colspan="12" class="empty"><span class="empty">没有找到数据.</span></td></tr>
<?php }?>
</tbody>
</table>
