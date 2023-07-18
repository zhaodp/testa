<?php
/* @var $this BonusCodeController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Bonus Codes',
);

$this->menu=array(
	array('label'=>'Create BonusCode', 'url'=>array('create')),
	array('label'=>'Manage BonusCode', 'url'=>array('admin')),
);
?>

<h1>Bonus Codes</h1>

<div class="well search-form">
    <?php $this->renderPartial('_search_2', array(
        //'model' => $model,
        'search_time' => $search_time,
		'user'=>$user,
    )); ?>


</div>


<table class="table">
<thead>
<tr>
<th >时间</th><th >优惠券号</th><th >手机号</th><th >订单流水号</th>
</tr>
</thead>

<tbody>
<?php 
if(!empty($data)){
	foreach($data as $key=>$value){
	?>
<tr>
<td><?php echo date("Y-m-d H:i:s",$value['created']); ?></td><td><?php echo Common::parseBonus($value['bonus_sn']); ?></td><td><?php echo Common::parsePhone($value['customer_phone']); ?></td><td><?php echo $value['order_id']; ?></td>
</tr>
<?php 
	} 
} 
?>
</tbody>
  
</table>

<div id="pager"  class="pagination text-right">    
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
