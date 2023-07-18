<?php $updateId = 'update_'.$data->id; ?>
<?php $subTitleId = 'sub_title_'.$data->id; ?>
<?php $demoId = 'demo_'.$data->id; ?>
<?php $fowordId = 'foword_'.$data->id; ?>
<?php $replys = AdminWorkLogReply::model()->getReplyByLogId($data->id); ?>
<div class="view" update_id="<?php echo $updateId; ?>" onmouseover='$(this).css("background-color","#F5F5F5");$("#" + $(this).attr("update_id")).show()' onmouseout='$(this).css("background-color","#fff");$("#" + $(this).attr("update_id")).hide()'>

<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="2">
                <a style="float:left;margin-right:20px;" data-toggle="collapse" data-target="#<?php echo $demoId; ?>" href="javascript:;"><i id="<?php echo $fowordId; ?>" class="icon-chevron-<?php echo $index == 0 ? 'down' : 'right' ?>"></i>
                <b>#<?php echo substr($data->work_date, 0, strpos($data->work_date, ' ')); ?></b></a>

                <div style="width:70%;height:18px;float:left;overflow:hidden;margin-left:20px;">
                    <div class="<?php echo $index == 0 ? 'hide' : ''; ?>" id="<?php echo $subTitleId; ?>">
                    <small style="margin-right:20px;color:red;"><?php echo $data->author; ?>(<?php echo $data->department; ?>)</small>
                    <small>
                        <?php echo Helper::truncate_utf8_string(CHtml::encode($data->work_log), 50); ?>
                    </small>
                    </div>
                </div>

                <?php echo CHtml::link('修改', array('adminWorkLog/update','id'=>$data->id), array('id'=>$updateId, 'style'=>'display:none;float:right;')); ?>
            </th>
        </tr>
    </thead>
    <tbody id="<?php echo $demoId; ?>" class="panel-collapse collapse <?php echo $index == 0 ? 'in' : 'hide' ?>">
        <tr>
            <td style="width:100px;" <?php echo !empty($replys) ? 'rowspan="2"' : ''; ?>>
                <small>姓名：<?php echo $data->author; ?></small><br>
                <small>城市：<?php echo Dict::item('city', $data->city); ?></small><br>
                <small>部门：<?php echo $data->department; ?></small><br>
                <?php if($data->category){ ?><small>分类：<?php echo $data->category; ?></small><br><?php } ?>
                <small>时间：<?php echo substr($data->create_time, 0, 10); ?></small><br>
            </td>
            <td height="100%">
                <div class="log">
                    <?php echo nl2br(CHtml::encode($data->work_log)); ?>
                    <br />
                </div>

            </td>
        </tr>
        <?php
            if(!empty($replys)){
        ?>
            <tr>
                <td style="background:#FCF8E3;">
                    <?php
                        $i = 0;
                        foreach($replys as $reply){
                    ?>
                            <div class="reply" <?php echo $i > 0 ? 'style="margin-top:15px;"' : '';$i++; ?>>
                                <small><b><?php echo $i ?># <?php echo CHtml::encode($reply->author); ?> </b>&nbsp;在&nbsp; <?php echo $reply->update_time; ?>&nbsp;回复：</small><br />
                                <small style="color:red;"><?php echo CHtml::encode($reply->content); ?></small>
                            </div>
                    <?php
                        }
                    ?>
                </td>
            </tr>
        <?php
            }
        ?>
    </tbody>
</table>
</div>
<script>
    $('#<?php echo $demoId; ?>').on('hidden.bs.collapse', function () {
        $('#<?php echo $demoId; ?>').toggle();
        $('#<?php echo $subTitleId; ?>').toggle();
        $('#<?php echo $fowordId; ?>').removeClass('icon-chevron-down');
        $('#<?php echo $fowordId; ?>').addClass('icon-chevron-right');
    })
    $('#<?php echo $demoId; ?>').on('show.bs.collapse', function () {
        $('#<?php echo $demoId; ?>').toggle();
        $('#<?php echo $subTitleId; ?>').toggle();
        $('#<?php echo $fowordId; ?>').removeClass('icon-chevron-right');
        $('#<?php echo $fowordId; ?>').addClass('icon-chevron-down');
    })
</script>
