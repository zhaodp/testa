<?php $this->pageTitle = Yii::app()->name . ' -音频发布日志管理'; ?>
<h2>音频发布日志管理</h2>
<h2 style="color:red">试听音频需要安装QuickTime　<a href="http://www.apple.com/quicktime/download/index.html" target="_blank">去下载</a></h2>
<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'template-grid',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name' => 'id',
            'headerHtmlOptions' => array(
                'style' => 'width:40px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["id"]'
        ),
        array(
            'name' => 'city_id',
            'headerHtmlOptions' => array(
                'style' => 'width:50px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'Dict::item("city",$data["city_id"])'
        ),
        array(
            'name' => 'audio_url',
            'type' => 'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:140px',
                'nowrap' => 'nowrap'
            ),
            'value' => array($this,'AudioUrl')
        ),
        array(
            'name' => '音频长度',
            'headerHtmlOptions' => array(
                'style' => 'width:50px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["audio_second"]',
        ),
        array(
            'name' => 'created',
            'headerHtmlOptions' => array(
                'style' => 'width:110px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["created"]'
        ),
        array(
            'name' => '操作人',
            'headerHtmlOptions' => array(
                'style' => 'width:110px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'AdminUserNew::model()->getName($data["opt_user_id"])'
        ),
    )
));

?>

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