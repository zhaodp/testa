<?php
/* @var $this GetuiClientController */
/* @var $dataProvider CActiveDataProvider */
$this->pageTitle = '注册成功司机列表';
?>

<h1><?php echo $this->pageTitle;?></h1>

<div class="search-form" >
    <div class="span12">
        <?php
            $city = Dict::items('city');
            $form=$this->beginWidget('CActiveForm', array(
				'action'=>Yii::app()->createUrl($this->route),
				'method'=>'get',
			));
			echo '城市：';
			echo "<select name='city_id' id='register_city_id' style='width:80px;'>";
			foreach ($city as $city_id=>$city_name)
			{
				echo "<option value='".$city_id."' >".$city_name."</option>";
			}
			echo "</select>&nbsp;&nbsp;";
			echo "&nbsp;&nbsp;";
			echo '司机工号：';
			echo "<input type='text' name='driver_id' id='register_driver_user' style='width:80px;'>";
			echo CHtml::submitButton('Search');
			$this->endWidget();
        ?>
    </div>

</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'register-driver-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'司机姓名',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->driver_id'),
		 array(
			'name'=>'城市',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this,'getCity')),
		 array(
			'name'=>'client_id',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' =>  '$data->client_id'),

		 array(
			'name'=>'udid',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->udid'),
		array(
			'name'=>'注册时间',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->created'),
     ),
));

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('register-driver-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
