<?php
    $this->pageTitle = 'mysql从库监控配置 - '.$this->pageTitle;
?>
<div class="well form span12">
    <?php
    echo CHtml::form();
    ?>
    <div class="span11 row">
        <h4>mysql从库监控配置</h4>
    </div>
    <div class="span11 row">
        选择数据库：
        <?php
        echo CHtml::checkBoxList('dbs', !empty($data['selectDb']) ? $data['selectDb'] : FALSE, $data['dbs'], array(
            'checkAll' => '全选',
            'labelOptions' => array('class' => 'checkbox inline', 'style' => 'padding-left:5px;'),
            'template' => '{input}{label}',
            'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;',
        ));
        ?>
    </div>
    <div class="span11 row">
        <?php
        echo CHtml::submitButton('保存', array('class' => 'btn btn-success', 'name' => 'submit'));
        ?>
    </div>
</div>
<?php
if (Yii::app()->user->hasFlash('saveSlaveDb')) {
    echo '<script>alert("' . Yii::app()->user->getFlash('saveSlaveDb') . '");</script>';
}
?>
