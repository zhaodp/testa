<?php
/* @var $this QuestionnaireController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Questionnaires',
);
?>

<h1>Questionnaires</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-exam-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array (
				'name'=>'id',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'$data->id'
		),
		array (
				'name'=>'name',
				'headerHtmlOptions'=>array (
						'width'=>'100px',
						'nowrap'=>'nowrap'
				),
				'value'=>'$data->name'
		),
		array (
					'name'=>'Phone',
					'headerHtmlOptions'=>array (
							'width'=>'100px',
							'nowrap'=>'nowrap'
					),
					'value'=>'$data->phone'
			),
	),
)); 
?>
