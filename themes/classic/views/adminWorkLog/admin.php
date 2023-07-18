
<h1>管理 工作日志</h1>

<div class="search-form">
<?php $this->renderPartial('_search_admin',array(
	'model'=>$model,
)); ?>
</div>

<?php
    $gridId = 'reply_list_view';
    $this->widget('zii.widgets.CListView', array(
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'itemView' => '_view_admin',
        'ajaxUpdate' => FALSE,
        'pagerCssClass' => 'pagination text-center',
        'pager' => Yii::app()->params['formatGridPage'],
    ));
?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'log_reply',
    'options' => array(
        'title' => '回复',
        'autoOpen' => false,
        'width' => '600',
        'height' => '450',
        'buttons' => array('关闭' => 'js:function(){$("#log_reply").dialog("close");}')
    ),
));
?>
    <iframe id="log_reply_Iframe" src="" style="width:550px;height:330px;border:0px;margin:0px;display:none;"></iframe>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

    
<script>
    function ereload(){
        window.location.reload();
        $("#log_reply").dialog("close");
    }
</script>