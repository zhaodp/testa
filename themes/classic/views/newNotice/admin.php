<?php $this->pageTitle = Yii::app()->name . ' -新公告管理'; ?>
<h1>新公告管理</h1>

<div class="search-form">
    <?php $this->renderPartial('_search', array(
        'model' => $model,
        'city_id'=>$city_id,
        'title'=>$title,
        'type'=>$type,
        'category'=>$category,
        'is_pass'=>$is_pass,
    )); ?>
</div><!-- search-form -->

<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'netNotice-grid',
    'ajaxUpdate' => false,
    'dataProvider' => $dataProvider,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name' => '标题类型',
            'headerHtmlOptions' => array(
                'style' => 'width:60px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'NewNotice::$WebCategorys[$data["category"]]'
        ),
        array(
            'name' => '标题',
            'type' => 'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:35%',
                'nowrap' => 'nowrap'
            ),
            'value' => array($this,'getTitleLong')
        ),
        array(
            'name' => '创建时间',
            'headerHtmlOptions' => array(
                'style' => 'width:70px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'substr($data["create_time"],0,10)'
        ),
        array(
            'name' => '发布时间',
            'headerHtmlOptions' => array(
                'style' => 'width:70px;color:red;',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["is_pass"]==NewNotice::PASS_FAI?"":substr($data["booking_push_datetime"],0,10)'
        ),
        array(
            'name' => '截止时间',
            'headerHtmlOptions' => array(
                'style' => 'width:70px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'substr($data["deadline"],0,10)'
        ),
        array(
            'name' => '城市',
            'headerHtmlOptions' => array(
                'style' => 'width:160px',
                'nowrap' => 'nowrap'
            ),
            'value' => array($this, 'getCity_ids')
        ),
        array(
            'name' => '语音',
            'type' => 'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:90px;',
                'nowrap' => 'nowrap',
            ),
            'value' =>array($this,'getAudioVoice')
        ),
        array(
            'name' => '发布状态',
            'type' => 'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:80px',
                'nowrap' => 'nowrap'
            ),
            'value' => array($this,'getState')
        ),
         array(
            'name' => '优先级',
            'type' => 'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:80px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["priority"]==0?"必读":"普通"'
        ),
        array(
            'name' => '发布人',
            'headerHtmlOptions' => array(
                'style' => 'width:50px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["opt_user"]'
        ),
        array(
            'name' => '操作',
            'type'=>'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:130px',
                'nowrap' => 'nowrap'
            ),
            'value' => array($this,'getOprates')
        ),
    )
));

?>
<script type="text/javascript">
    $(function () {
        $('.url_del_newNotice').click(function () {
            return confirm('你真的要删除此数据吗？') ? true : false;
        });
        $('.url_punish_newNotice').click(function () {
            if(confirm('你确定要发布吗？') ? true : false){
                $('.url_punish_newNotice').attr('disabled',true);
                return true;
            }else{
                return false;
            }
        });
    });
</script>

<script language="JavaScript" type="text/javascript">
    $(function(){
        $('.click_a_button').click(function(){
            this.innerHTML=pv_q(this.title,100,30);
        });
    });
    function pv_q(u, w, h){
        var pv='';
        pv += '<object width="'+w+'" height="'+h+'" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab">';
        pv += '<param name="src" value="'+u+'">';
        pv += '<param name="controller" value="true">';
        pv += '<param name="type" value="video/quicktime">';
        pv += '<param name="autoplay" value="false">';
        pv += '<param name="target" value="myself">';
        pv += '<param name="bgcolor" value="black">';
        pv += '<param name="pluginspage" value="http://www.apple.com/quicktime/download/index.html">';
        pv += '<embed src="'+u+'" width="'+w+'" height="'+h+'" controller="true" align="middle" bgcolor="black" target="myself" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/index.html"></embed>';
        pv += '</object>';
        return pv;
    }
</script>
<?php $this->renderPartial('public'); ?>
