<?php
/* @var $this KnowledgeController */
/* @var $data Knowledge */
?>

<div class="row-fluid">

    <div
        style="font-size:18px; padding: 5px 0px 8px 0px;"><?php echo CHtml::link(CHtml::encode($data->title), 'javascript:void(0);', array('onclick' => "desc('$data->id')")); ?></div>
    <div style="font-size:14px; padding-bottom: 5px; color: rgb(0, 136, 204);">
        for&nbsp;&nbsp;<?php echo ($data->typeid == 0) ? '全部' : Dict::item('knowledge_type', $data->typeid); ?>
        &nbsp;&nbsp;<?php echo ($data->catid == 0) ? '全部' : Dict::item('knowledge_cat', $data->catid); ?>
        &nbsp;&nbsp;<?php echo ($data->is_case == 0) ? '' : '案例'; ?>
    </div>
    <p id="knowledge_desc_<?php echo $data->id; ?>">
        <b>摘要</b>：<?php echo empty($data->description) ? '暂无简介' : Helper::truncate_utf8_string($data->description, 70); ?>
        [ <a href="javascript:void(0);" onclick="desc('<?php echo $data->id; ?>')">详情</a> ]
    </p>

    <div id="knowledge_data_<?php echo $data->id; ?>" style="display: none;">
        <p>
            <?php
            $knowledgeData = KnowledgeData::model()->getKnowledgeData($data->id);
            if ($knowledgeData && !empty($knowledgeData['content'])) {
                echo "<b>详情</b>：" . nl2br($knowledgeData['content']);
            }
            ?>
        </p>
        <?php
        $i = 1;
        $case = KnowledgeCaseRel::model()->getCase($data->id);
        foreach ($case as $list) {
            $content = KnowledgeCase::model()->getContent($list['kc_id']);
            echo '<p class="text-success"><b>案例' . $i . "</b>：" . nl2br($content['content']) . "</p>";
            $i++;
        }
        ?>
    </div>

    <div>
        <span class="pull-right"><a href="javascript:void(0);" onclick = "solve('<?php echo $data->id;?>');" class="btn btn-success">有用</a></span>

        <p style='font-size: 12px;' class="muted">
            <b>知识录入时间</b>：<?php echo $data->created; ?>
            <br/>
            <b>最后操作人</b>：<?php echo $data->operator; ?>
        </p>
    </div>

</div>

<script type="text/javascript">
    function desc(id) {
        var knowledge_desc = 'knowledge_desc_' + id;
        var knowledge_data = 'knowledge_data_' + id;
        $("#" + knowledge_desc).hide();
        $("#" + knowledge_data).show();
    }

    function solve(id){
        var phone = $("#Knowledge_phone").val();
        $.ajax({
            type: 'post',
            url: '<?php echo Yii::app()->createUrl('/KnowledgeProblems/AjaxSolve');?>',
            data: 'phone=' + phone + '&id=' + id,
            success: function (e) {
                if(e == 1){
                    alert("已经解决");
                }else{
                    alert("问题已经解决，不能再操作");
                }
            }
        });

    }

</script>