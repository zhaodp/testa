<?php $this->pageTitle = Yii::app()->name . ' -公告长文章管理'; ?>
<h1>长文章管理</h1>

<div class="search-form">
<div class="well span12">
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'post-search',
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'get',
));
?>
        <div class="span3">
            <?php echo CHtml::label('标题', 'title'); ?>
            <?php echo CHtml::textField('title', $title, array('class' => 'span12')); ?>
        </div>
        <div class="span3 buttons">
            <label for="status">　</label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn btn-primary span4')); ?>　　
            <a href="<?php echo Yii::app()->createUrl('newNoticePost/add')?>" class="btn btn-info">添加</a>
        </div>
<?php $this->endWidget(); ?>
</div>
</div>
<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'new-notice-post-grid',
    'ajaxUpdate' => false,
    'dataProvider' => $dataProvider,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name' => '标题',
            'headerHtmlOptions' => array(
                'style' => 'width:40%',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["title"]'
        ),
        array(
            'name' => '创建时间',
            'headerHtmlOptions' => array(
                'style' => 'width:100px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["created"]'
        ),
        array(
            'name' => '创建者',
            'headerHtmlOptions' => array(
                'style' => 'width:100px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["opt_user_name"]'
        ),
        array(
            'name' => '操作',
            'type'=>'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:150px',
                'nowrap' => 'nowrap'
            ),
            'value' => array($this,'getNewNoticePostOprate')
        ),
    )
));

?>
<script type="text/javascript">
    $(function () {
        $('.url_del_newNoticePost').click(function () {
            return confirm('你真的要删除此数据吗？') ? true : false;
        });
    });
</script>

<?php $this->renderPartial('public'); ?>