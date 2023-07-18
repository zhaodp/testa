<?php
/**
 * Created by PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-28
 * Time: 下午2:25
 */

?>
<?php
$this->pageTitle = Yii::app()->name . ' - 客户投诉管理';
?>
<h1>客户投诉监控（任务人）</h1>
<?php $this->renderPartial('_monitor_operator_nav'); ?>
<div class="search-form thumbnail">
    <div class="caption">
        <?php $this->renderPartial('_search_monitor_operator', array(
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

            'type'=>'process_operator'

        )); ?>
    </div>
</div><!-- search-form -->
<div id="complain-grid" class="grid-view">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>投诉任务组</th>
            <th>投诉任务人</th>
            <th>投诉数量</th>
            <th>未及时响应数</th>
            <th>未及时跟进数</th>
            <th>未及时结案数</th>
            <th>累计未响应数</th>
            <th>累计未跟进数</th>
            <th>累计未结案数</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($list as $k=>$v) {
            ?>
            <tr>
                <td><?php echo $v['group']; ?></td>
                <td><?php echo $v['user']; ?></td>
                <td><?php echo $v['complainNum']; ?></td>
                <td><?php echo $v['delayedRnum']; ?></td>
                <td><?php echo $v['delayedFnum']; ?></td>
                <td><?php echo $v['delayedCnum']; ?></td>
                <td><?php echo $v['unRnum']; ?></td>
                <td><?php echo $v['unFnum']; ?></td>
                <td><?php echo $v['unCnum']; ?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>