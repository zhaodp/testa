<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
    var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
    $(this).text(title);
	return false;
});
$('.search-form form').submit(function(){
	$('#bonus-code-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

$click_confirm = <<<EOD
function(){
	if (confirm('确认将该优惠券失效？')) {
        return true ;
    } else {
        return false ;
    }
}
EOD;
?>

<h1>优惠券列表</h1>

<?php echo CHtml::link('展开搜索', '#', array('class' => 'btn search-button')); ?>
&nbsp;
<?php echo CHtml::link('创建优惠码', Yii::app()->createUrl('bonusCode/create'), array('class' => 'btn btn-success','target'=>'_blank')); ?>
&nbsp;
<?php echo CHtml::link('更新优惠码','javascript:;',array('func'=>'updatebouns','class'=>'btn btn-success')); ?>
<div class="search-form" style="display:none">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
    ));
    ?>
</div><!-- search-form -->

<?php
$gridId = 'bonus-code-grid';
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => $gridId,
    'dataProvider' => $model->search('id DESC'),
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
//	'filter'=>$model,
    'columns' => array(
        array(
            'name' => 'name',
            'htmlOptions' => array(
                'style' => 'width:15%;'
            ),
            'type' => 'raw',
            'value' => 'CHtml::link($data->name,array("bonusCode/view","id"=>$data->id),array("title" => "点击可以查看优惠劵详情","onclick"=>"
                 $(\'#cru-frame\').attr(\'src\',$(this).attr(\'href\'));
                 $(\'#cru-frame\').show();
                 $(\'#mydialog\').dialog(\'open\');
                 return false;
            "))."<br/>".CHtml::link("查看短信",array("bonusCode/admin","smsId"=>$data->id),array("id"=>"smsLink_$data->id","onclick"=>"
                $(\'#smsIframe\').attr(\'src\',$(\'#smsLink_$data->id\').attr(\'href\'));
                $(\'#smsIframe\').show();
                $(\'#smsDialog\').dialog(\'open\');
                return false;
            "))',
        ),
        array(
            'name' => 'channel',
            'type' => 'raw',
            'value' => 'Dict::item(\'bonus_channel\',$data->channel)',
        ),
        'money',
        array(
            'name' => 'issued',
            'htmlOptions' => array(
                'style' => 'width:7%;'
            ),
            'header' => '号码',
            'type' => 'raw',
            'value' => 'Dict::item(\'bonus_sn_type\',$data->sn_type)."<br>".$data->issued.($data->sn_type==1?"":"张")',
        ),
        array(
            'name' => 'sn_end',
            'header' => '日期限制',
            'type' => 'raw',
            'value' => '
                "开始：".substr($data->effective_date,0,10)."<br>".
                "绑定：".substr($data->binding_deadline,0,10)."<br>".
                "使用：".substr(BonusCode::model()->getUseInfoById($data->id),0,10)."<br>"
            ',
        ),
        array(
            'name' => 'channel_limited',
            'htmlOptions' => array(
                'style' => 'width:18%;'
            ),
            'header' => '类型限制',
            'type' => 'raw',
            'value' => '
                "城市：".implode(",",BonusCode::model()->getCityById($data->id))."<br>".
                "使用：".Dict::item(\'repeat_limited\',$data->repeat_limited)."<br>".
                "渠道：".Dict::item(\'channel_limited\',$data->channel_limited)."<br>".
                "用户：".Dict::item(\'user_limited\',$data->user_limited)."<br>"

            ',
        ),
        array(
            'name' => 'create_by',
            'header' => '申请人',
            'type' => 'raw',
            'value' => '$data->create_by."<br>".str_replace(" ","<br>",$data->created)',
        ),
        array(
            'name' => '使用统计',
            'htmlOptions' => array(
                'style' => 'width:10%;'
            ),
            'type' => 'raw',
            'value' => '"优惠码：".$data->issued.($data->sn_type==1?"":"张")."<br/>".
            "共绑定：".CustomerBonus::model()->getBindingByID($data)."张<br/>".
            "共使用：".CustomerBonus::model()->getUsedByID($data)."张<br/>".
            CHtml::link("查看绑定明细",Yii::app()->createUrl("bonusCode/bonus_admin",array("CustomerBonus[bonus_type_id]"=>$data->id)), array( "target" => "_blank"))'
        ),
        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => 'Dict::item(\'bonus_code_status\',$data->status)."<br/>".CHtml::link("操作记录",
                Yii::app()->createUrl("bonusCodeLog/operationLog",array("bonusId"=>$data->id)),
                array(
                "id"=>"statusLink_$data->id",
                "onclick"=>"
                    $(\'#statusIframe\').attr(\'src\',$(\'#statusLink_$data->id\').attr(\'href\')).show();
                    $(\'#statusDialog\').dialog(\'open\');
                    return false;
            "))',
        ),
        array(
            'header' => '操作',
            'htmlOptions' => array(
                'style' => 'width:85px;'
            ),
            'class' => 'CButtonColumn',
            'template' => '{update} {audit} {download} {delete} {select} {downshell}',
            'buttons' => array(
                'update' => array(
                    'label' => '修改',
                    'imageUrl' => false,
                    'options'=>array('target' => '_blank'),
                    'visible' => 'in_array($data->status, array(BonusCode::STATUS_AUDIT,BonusCode::STATUS_APPROVED,BonusCode::STATUS_NOT_AUDIT))',
                ),
                'delete' => array(
                    'label' => '<br/>删除',
                    'imageUrl' => false,
                    'visible' => 'in_array($data->status, array(BonusCode::STATUS_AUDIT,BonusCode::STATUS_NOT_AUDIT))',
                ),
                'download' => array(
                    'label' => '<br/>下载优惠券',
                    'visible' => 'in_array($data->status, array(BonusCode::STATUS_APPROVED))',
                    'url' => 'Yii::app()->createUrl("bonusLibrary/download", array("bonus_id"=>$data->id))',
                    'options' => array(
                        'target' => '_blank'
                    ),
                ),
                'select' => array(
                    'label' => '优惠码查看',
                    'visible' => 'in_array($data->status, array(BonusCode::STATUS_APPROVED))',
                    'options'=>array('target' => '_blank'),
                    'url' => 'Yii::app()->createUrl("bonusLibrary/admin", array("BonusLibrary[bonus_id]"=>$data->id))',
                ),
                'audit' => array(
                    'label' => '审核',
                    'visible' => '$data->status == BonusCode::STATUS_AUDIT',
                    'url' => 'Yii::app()->createUrl("bonusCode/audit",array("id"=>$data->id))',
                    'click' => 'function(){
                        $(\'#auditIframe\').attr(\'src\',$(this).attr(\'href\'));
                        $(\'#auditIframe\').show();
                        $("#auditDialog").dialog("open");
                        return false;
                    }'
                ),
                'downshell' => array(
                    'label' => '<br/>失效',
                    'visible' => 'in_array($data->status, array(BonusCode::STATUS_AUDIT,BonusCode::STATUS_APPROVED,BonusCode::STATUS_NOT_AUDIT))',
                    'url' => 'Yii::app()->createUrl("bonusCode/downshell",array("id"=>$data->id))',
                    'click' =>$click_confirm ,
                ),
            )
        ),
    ),
));

?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'优惠券详情',
        'autoOpen'=>false,
        'width'=>'900',
        'height'=>'500',
        'modal'=>true,
        'buttons'=>array(
            '关闭'=>'js:function(){$("#mydialog").dialog("close");} '
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'smsDialog',
    'options' => array(
        'title' => '短信内容',
        'autoOpen' => false,
        'width' => '600',
        'height' => '450',
        'buttons' => array('关闭' => 'js:function(){$("#smsDialog").dialog("close");}')
    ),
));
?>
    <iframe id="smsIframe" src="" style="width:550px;height:330px;border:0px;margin:0px;display:none;"></iframe>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'auditDialog',
    'options' => array(
        'title' => '审核',
        'autoOpen' => false,
        'width' => '600',
        'height' => '450',
        'buttons' => array('关闭' => 'js:function(){$("#auditDialog").dialog("close");}')
    ),
));
?>
    <iframe id="auditIframe" src="" style="width:550px;height:330px;border:0px;margin:0px;display:none;"></iframe>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'statusDialog',
    'options' => array(
        'title' => '操作记录',
        'autoOpen' => false,
        'width' => '600',
        'height' => '450',
        'buttons' => array('关闭' => 'js:function(){$("#statusDialog").dialog("close");}')
    ),
));
?>
    <iframe id="statusIframe" src="" style="width:550px;height:330px;border:0px;margin:0px;display:none;"></iframe>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<script>
    function ereload(){
        $("#auditDialog").dialog("close");
        $.fn.yiiGridView.update('<?php echo $gridId; ?>');
    }
    jQuery('[func="updatebouns"]').click(function(){
            if (!confirm('更新优惠券信息需要10秒左右时间,请耐心等待，确认要更新？')) {
                return false;
            }
            obj=jQuery(this);
            obj.html('更新中.....');
            $.ajax({
                'url':'<?php
                echo Yii::app()->createUrl('/bonusCode/bonusCodeImport');
                ?>',
                'data':{},
                'type':'get',
                'success':function(data){
                    if(data=='ok'){obj.html('更新优惠码');alert('更新成功!');}
                },
                'cache':false
            });
        });
</script>