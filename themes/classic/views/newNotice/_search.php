<div class="well span12">
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'nocite-search',
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'get',
));
?>
    <div class="row-fluid">
        <div class="span2">
            <?php echo CHtml::label('标题', 'title'); ?>
            <?php echo CHtml::textField('title', $title, array('class' => 'span12')); ?>
        </div>
        <div class="span2">
            <?php echo CHtml::label('公告类型', 'type'); ?>
            <?php
                echo CHtml::dropDownList('type', $type, array(''=>'全部')+NewNotice::$types, array('class' => 'span8'));
            ?>
        </div>
        <?php if(!isset($driver)){ ?>
        <div class="span2">
            <?php echo CHtml::label('城市', 'city_id'); ?>
            <?php echo CHtml::DropDownList('city_id', $city_id, Dict::items('city'), array('class' => 'span8')); ?>
        </div>
        <?php if(!isset($index_ispass)) {?>
        <div class="span2">
            <?php echo CHtml::label('发布状态', 'is_pass'); ?>
            <?php echo CHtml::DropDownList('is_pass', $is_pass, array(''=>'全部')+NewNotice::$passes, array('class' => 'span8')); ?>
        </div>
        <?php }?>
        <?php } ?>
        <div class="span2">
            <?php echo CHtml::label('公告分类', 'category'); ?>
            <?php echo CHtml::dropDownList('category', $category, array(''=>'　全部')+NewNotice::$WebCategorys, array('class' => 'span8')); ?>
        </div>
        <div class="span2">
            <label for="status">　</label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn btn-primary')); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <label for="status">　</label>
            <h4 style="color:red">试听音频需要安装QuickTime
       　<a href="http://www.apple.com/quicktime/download/index.html"  class="btn btn-primary"  target="_blank">去下载</a>
                <a href="<?php echo Yii::app()->createUrl('newNotice/add') ?>" class="btn btn-primary"   target="_blank">添加新公告</a>
                <a href="<?php echo Yii::app()->createUrl('newNoticePost/index') ?>" class="btn btn-primary"   target="_blank">长文章管理</a>
            </h4>
        </div>
    </div>
<?php $this->endWidget(); ?>
    
</div>