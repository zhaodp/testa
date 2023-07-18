<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-04-09
 * Time: 上午11:16
 * To change this template use File | Settings | File Templates.
 */

?>
<?php
$this->pageTitle = Yii::app()->name . ' - 客户投诉管理';
?>
<h1>客户投诉监控</h1>
<?php $this->renderPartial('_monitor_nav'); ?>
<div class="search-form thumbnail">
    <div class="caption">
        <?php $this->renderPartial('_search_monitor', array(
            's_time' => $start_time,
            'e_time' => $end_time,
            'category' => $category,
            'top_type' => $top_type,
            'second_type' => $second_type,
            'task_group' => $task_group,
            'task_operator' => $task_operator,
            'secondTypeList'=>$secondTypeList,

            'typeList'=>$typeList,
            'groupList'=>$groupList,
            'cateList'=>$cateList,
            'operatorList'=>$operatorList,

            'type'=>'stat_type'

        )); ?>
    </div>
</div><!-- search-form -->

<div id="complain-grid" class="grid-view">
<table class="table table-striped">
    <thead>
    <tr>
        <th>投诉大类</th>
        <th>投诉一级分类</th>
        <th>投诉二级分类</th>
        <th>投诉数量</th>
        <th>结案数量</th>
        <th>平均响应时间</th>
        <th>最快响应时间</th>
        <th>最慢响应时间</th>
        <th>平均结案时间</th>
        <th>最快结案时间</th>
        <th>最慢结案时间</th>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach ($list as $k=>$v) {
    ?>
        <tr>
            <td><?php echo $v['category']; ?></td>
            <td><?php echo $v['top_type']; ?></td>
            <td><?php echo $v['second_type']; ?></td>
            <td><?php echo $v['complainNum']; ?></td>
            <td><?php echo $v['closingNum']; ?></td>
            <td><?php echo $v['avgRtime']; ?></td>
            <td><?php echo $v['fastestRtime']; ?></td>
            <td><?php echo $v['lowestRtime']; ?></td>
            <td><?php echo $v['avgCtime']; ?></td>
            <td><?php echo $v['fastestCtime']; ?></td>
            <td><?php echo $v['lowestCtime']; ?></td>
        </tr>
    <?php }?>
    </tbody>
</table>
</div>
<?php
//var_dump($list);
//$this->renderPartial('_view_sp', array(
//    'model' => $model,
//));
?>


