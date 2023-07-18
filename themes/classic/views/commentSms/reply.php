<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'comments-reply-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="control-group">
		<label class="control-label">司机：</label>
		<div class="controls">
			<input class='' name="driver_name" readonly value='<?php echo $comments['driver_name'];?>'></input>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">评价等级：</label>
		<div class="controls">
			<?php echo $comments['select'];?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">评价内容：</label>
		<div class="controls">
			<div class="alert"><?php echo $comments['comments']; ?></div>
            <?php echo CHtml::checkBox('CommentsReply[reset_comment]'); ?> &nbsp;重置内容
		</div>
	</div>

	<input size="20" maxlength="30" name="CommentsReply[comment_id]" id="CommentsReply_comment_id" type="hidden" value="<?php echo $model->comment_id; ?>" />	

	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
            <!-- 转为投诉  by曾志海 -->
            <input type="checkbox" value="1" name="to_complaint"> 转为投诉
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<?php echo CHtml::submitButton($model->isNewRecord ? '保存' : 'Save',array('class'=>'btn btn-success')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

<?php 
$criteria = new CDbCriteria();
$criteria->condition = 'comment_id=:comment_id';
$criteria->params = array (
	':comment_id'=>$model->comment_id
);
$criteria->order = 'created desc';
$dataProvider = new CActiveDataProvider('CommentsReply', array (
	'pagination'=>array (
		'pageSize'=>15
	), 
	'criteria'=>$criteria
));

$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'comments-reply-grid', 
	'dataProvider'=>$dataProvider, 
	'template'=>'{items}',
	'itemsCssClass'=>'table table-striped',
	'columns'=>array (
		array (
			'name'=>'description', 
			'headerHtmlOptions'=>array (
				'width'=>'240px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data->description'
		),
		array (
			'name'=>'operator', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data->operator'
		),
		array (
			'name'=>'操作时间', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d H:i", $data->created)'
		), 
	),
));
?>
</div>

<div class="row">
<?php

$criteria = new CDbCriteria();
$criteria->condition = 'driver_id=:driver_id';
$criteria->params = array (
	':driver_id'=>$comments['driver_id']
);
$data = CommentSms::model()->findAll($criteria);
$arrCommentsId = array();
if (!empty($data))
foreach ($data as $row)
{
	array_push($arrCommentsId, $row->id);
}

if (!empty($arrCommentsId))
{
	$criteria = new CDbCriteria();
	$criteria->addInCondition('comment_id', $arrCommentsId);
	$criteria->order = 'created desc';
	$dataProvider = new CActiveDataProvider('CommentsReply', array (
		'pagination'=>array (
			'pageSize'=>50
		), 
		'criteria'=>$criteria
	));	
	
?>
<div class="row"><h3>该司机全部处理意见历史</h3></div>
<?php 

$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'comments-reply-history-grid', 
	'dataProvider'=>$dataProvider, 
	'template'=>'{items}',
	'itemsCssClass'=>'table table-striped',
	'columns'=>array (
		array (
			'name'=>'description', 
			'headerHtmlOptions'=>array (
				'width'=>'240px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data->description'
		),
		array (
			'name'=>'operator', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data->operator'
		),
		array (
			'name'=>'操作时间', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d H:i", $data->created)'
		), 
	),
));
}
?>
</div>

