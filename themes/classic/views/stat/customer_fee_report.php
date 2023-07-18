<style>
<!--
.mini-layout {
	height: 680px;
	margin-bottom: 20px;
	padding: 9px;
	border: 1px solid #DDDDDD;
	border-radius: 6px 6px 6px 6px;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.075);
	background-color: #ffffff;
}
.mini-layout-h {
	margin-bottom: 20px;
	padding: 9px;
	border: 1px solid #DDDDDD;
	border-radius: 6px 6px 6px 6px;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.075);
	background-color: #ffffff;
}
.mini-layout-h:after{
	content:"."; 
	display:block; 
	height:0; 
	clear:both;
	visibility:hidden;
}
-->
</style>
<h1>用户付费统计数据</h1>
<section>
<div class="span12">
<?php 
$array_title = array(
					'total_count'=>'用户使用次数', 
					'top20_all'=>'消费总次数前20', 
					'last_month_top20_all'=>'上月消费次数前20',
					'top20_callcenter'=>'呼叫中心消费次数前20',
					'last_month_top20_callcenter'=>'上月呼叫中心次数消费前20',
					'top20_application'=>'客户端消费次数前20',
					'last_month_top20_application'=>'上月客户端消费次数前20',);
foreach($dataProvider as $key=>$data){
	?>
    <div class="span4">
      <h3><?php echo $array_title[$key]?></h3>
      <?php if ($key == 'total_count') {?>
      <div class="mini-layout-h">
      <?php } else { ?>
      <div class="mini-layout">
      <?php } ?>
		<ul class="nav nav-list">
			<?php if ($key == 'total_count') {?>
			<li><label class="span5">用户数</label><label class='span7'>用户总使用次数</label></li>
			<?php } else {?>
			<li><label class='span7' style="width: 30%">次数</label><label class='span7' style="width: 30%">花费</label><label class="span5" style="width: 30%">用户电话</label></li>
			<?php } ?>
			<?php foreach ($data as $val) {?>
				<?php if ($key == 'total_count') { ?>
				<li><label class="span5"><?php echo $val['customer_count'];?></label><label class='span7'><?php echo $val['use_count']; ?></label></li>
				<?php } else {?>
				<li><label class='span7' style="width: 30%"><?php echo $val['call_times'];?></label><label class='span7' style="width: 30%"><?php echo $val['fee'];?></label><label class="span5" style="width: 30%"><?php echo $val['telephone'];?></label></li>
				<?php }?>
			<?php }?>
		</ul>
      </div>
    </div>
<?php }?>
  </div>
</section>