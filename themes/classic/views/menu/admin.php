<?php
/* @var $this MenuController */
/* @var $model Menu */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#menu-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>菜单管理</h1>

<?php
echo CHtml::link('新建主菜单',Yii::app()->createUrl('menu/create'));
?>


<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'menu-grid',
    'dataProvider'=>$dataProvider,
    'columns'=>array(
        array(
            'name'=>' ',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => array($this,'showStep'),
        ),
        array(
            'name'=>'菜单名称',
            'headerHtmlOptions'=>array (
                'width'=>'180px',
                'nowrap'=>'nowrap',
            ),
            'type'=>'raw',
            'value' => '($data["parentid"]>0)? "&nbsp;&nbsp;&nbsp;&nbsp;".$data["name"] :$data["name"] ',//'"&nbsp;".$data["name"]',
        ),

        array('name'=>'链接地址','value'=>array($this,'actionGetRoles')),
        array('name'=>'展示','value'=>'($data["is_show"])?"是":"否"'),
        array('name'=>'操作','value'=>array($this,'actionOptUrl')),

    ),
)); ?>


</div>