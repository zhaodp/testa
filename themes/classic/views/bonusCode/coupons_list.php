<?php
/* @var $this BonusCodeController */
/* @var $dataProvider CActiveDataProvider */

/*$this->breadcrumbs=array(
	'Bonus Codes',
);

$this->menu=array(
	array('label'=>'Create BonusCode', 'url'=>array('create')),
	array('label'=>'Manage BonusCode', 'url'=>array('admin')),
);*/
?>

<h1>Bonus Codes</h1>

<div class="row">

    <form method="get" action="/v2/index.php?r=BonusCode/actionGetCouponsList" id="yw0">
	<div style="display:none"><input type="hidden" name="r" value="BonusCode/GetCouponsList"></div>
    
    <div class="row span3">
        <label for="AdminActions_name">名称</label>       
	 <input type="text" id="AdminActions_name" name="admin_username" value="<?php echo !empty($search_user)?$search_user:''; ?>"  maxlength="20" size="20">    </div>
<?php $this->renderPartial('_search_2', array(
        //'model' => $model,
        'search_time' => $search_time,
		'user'=>$search_user,
    )); ?>
    <!--div class="row span2">
        <label>&nbsp;</label>
        <input type="submit" value="search" name="action" class="btn">    </div-->

    </form>
</div>

<table class="table">
<thead>
<tr>
<th >排行</th><th >客服</th><th >总计发放优惠券数量(<?php echo !empty($sum)?$sum:0; ?>)</th><th >已使用优惠券数量(<?php echo !empty($sum_use)?$sum_use:0; ?>)</th><th>转化率</th>
</tr>
</thead>

<tbody>
<?php 
if(!empty($dataList)){
	foreach($dataList as $key=>$value){
	?>
<tr>
<td><?php echo $key+1; ?></td><td><a href="?r=bonusCode/GetCouponsDetail&user=<?php echo $value['create_by']; ?>&coupons[start_time]=<?php echo !empty($search_time['start_time'])?$search_time['start_time']:''; ?>&coupons[end_time]=<?php echo !empty($search_time['end_time'])?$search_time['end_time']:''; ?>"><?php echo $value['create_by']; ?></a></td><td><?php echo $value['totalnum']; ?></td><td><?php echo $value['usenum']; ?></td>
<td><?php echo floor($value['usenum']*1000/$value['totalnum'])/10;echo '%'; ?></td>
</tr>
<?php 
	} 
} 
?>
</tbody>
</table>
<div id="pager" class="pagination text-right">    
    <?php    
    $this->widget('CLinkPager',array(    

        'header'=>'',    

        'firstPageLabel' => '首页',    

        'lastPageLabel' => '末页',    

        'prevPageLabel' => '上一页',    

        'nextPageLabel' => '下一页',    

        'pages' => $pages,    

        'maxButtonCount'=>13    

        )
    );  
    ?>    

    </div>    

