<?php
/* @var $this KnowledgeController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Knowledges',
);

?>

<?php
if($driver){
    echo "<h1>". $driver->name."(".$driver->user . ")" ."</h1>";
}else{
    echo "<h1>知识库查询</h1>";
}
?>
<div class="row-fluid">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
        'htmlOptions' => array('class' => 'form-inline'),
    )); ?>
        <input id="Knowledge_phone" type="hidden" name="phone" value="<?php echo $_GET['phone']; ?>">
        <input id="Knowledge_title" type="text" name="title" maxlength="100" size="60" value="<?php echo empty($_GET['title']) ? '' : $_GET['title']; ?>">
        <?php echo CHtml::submitButton('搜 索', array('class' => 'btn')); ?>
    <?php $this->endWidget(); ?>
</div>




<?php if(empty($_GET['title'])){ ?>
<div class="row-fluid">
<?php
    $i = 1;
    foreach($knowledge_list as $k => $v){
        if(!empty($v['list'])){
            echo "<div class = 'box span6'>";
            echo "<h4><a href = '#'>". $v['name']."</a></h4><ul>";

            foreach($v['list'] as $list){
                echo "<li><a href = '#'>".$list['title']."</a></li>";
            }

            echo "</ul></div>";
            if($i%2 == 0){
                echo '</div>
                <div class="row-fluid">';
            }
        }
        $i++;
    }
?>

</div>
<?php
}else{
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $dataProvider,
        'itemView' => '_view',
    ));
} ?>
