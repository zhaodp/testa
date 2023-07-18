<?php
/* @var $this MenuController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Menus',
);

$this->menu=array(
	array('label'=>'Create Menu', 'url'=>array('create')),
	array('label'=>'Manage Menu', 'url'=>array('admin')),
);
?>
<h1>菜单列表</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'menu-day-list-grid',
    'dataProvider'=>$model->search(),
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        array(
            'name'=>'菜单名称',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["name"]'),
        array(
            'name'=>'是否展示',
            'headerHtmlOptions'=>array(
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value' => $data["is_show"]?'是':'否'),
    ),
)); ?>
