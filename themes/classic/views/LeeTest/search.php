<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver Recommands'=>array('index'),
	'Manage',
);

?>
<h1>爱代驾司机</h1>
<div id="pager">  
<form action="<?php echo $this->CreateUrl('LeeTest/Search')?>" method="post">
<div class="span3">
<label>手　机:</label><?php echo CHtml::textField('phone'); ?>
</div>
<div class="span3">
<label>身份证:<label><?php echo CHtml::textField('id_card')?>
</div>

<div class="row-fluid"><br />
<?php echo CHtml::submitButton('Search', array('class'=>'btn span2'))?>
</div>
</form>
<table class="table">
	<thead>
	<tr>
		<th><b>名字</b></th>
		<th><b>电话</b></th>
		<th><b>图片</b></th>
		<th><b>籍贯</b></th>
		<th><b>身份证</b></th>
		<th><b>名字存在<b></th>
		<th><b>身份证存在</b></th>
	</tr>
	</thead>
<?php foreach($model as $k => $v) {?>

	<tr>
		<td><?php echo $v['name'];?></td>
		<td><?php echo $v['phone']?></td>
		<td><img src="<?php echo $v['pic']?>"></td>
		<td><?php echo $v['jiguan']?></td>
		<td><?php echo $v['idcode']?></td>
		<td><?php echo $name_status[$k];?></td>
		<td><?php echo $id_status[$k]?></td>
	</tr>

<?php } ?>
</table>
<div class="pagination text-center">
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
</div>    