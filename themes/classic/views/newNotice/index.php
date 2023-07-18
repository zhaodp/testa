<?php if(isset($driver)){ ?>
    <h2>排行榜
        <small>（按收入排行）</small>
    </h2>
    <div class="btn-group">
        <?php
        echo CHtml::link('昨日排行', '', array('onclick' => 'driver_rank(0)', 'id' => 'driver_rank_daily', 'class' => "search-button btn-primary btn"));
        echo CHtml::link('上月排行', '', array('onclick' => 'driver_rank(1)', 'id' => 'driver_rank_month', 'class' => "btn"));
        ?>
    </div>
    <div id="rank_driver_news"></div>
<?php }?>

<?php $this->pageTitle = Yii::app()->name . ' -近期公告'; ?>
<h1>近期公告</h1>
<div class="search-form">
    <?php
    if(!isset($driver)){
        $this->renderPartial('_search', array(
            'model' => $model,
            'city_id'=>$city_id,
            'title'=>$title,
            'type'=>$type,
            'category'=>$category,
            'is_pass'=>$is_pass,
            'index_ispass'=>1,
        ));
    }else{
        $this->renderPartial('_search', array(
            'model' => $model,
            'driver'=>$driver,
            'title'=>$title,
            'type'=>$type,
            'category'=>$category,
        ));
    }
    ?>
</div><!-- search-form -->

<?php
//CGridView
if(!isset($driver)){
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'newNotice-index-grid',
        'dataProvider' => $dataProvider,
        'ajaxUpdate' => false,
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '公告分类',
                'headerHtmlOptions' => array(
                    'style' => 'width:60px',
                    'nowrap' => 'nowrap'
                ),
                'value' => 'NewNotice::$WebCategorys[$data["category"]]'
            ),
            array(
                'name' => '主题',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                    'style' => 'width:45%',
                    'nowrap' => 'nowrap'
                ),
                'value' => array($this,'Seetitle')
            ),
            array(
                'name' => '有效期截止',
                'headerHtmlOptions' => array(
                    'style' => 'width:90px',
                    'nowrap' => 'nowrap'
                ),
                'value' => 'substr($data["deadline"],0,10)'
            ),
            array(
                'name' => '创建时间',
                'headerHtmlOptions' => array(
                    'style' => 'width:90px',
                    'nowrap' => 'nowrap'
                ),
                'value' => 'substr($data["create_time"],0,10)'
            ),
            array(
                'name' => '城市',
                'headerHtmlOptions' => array(
                    'style' => 'width:160px',
                    'nowrap' => 'nowrap'
                ),
                'value' => array($this, 'getCity_ids')
            ),
        )
    ));
}else{
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'template-grid',
        'dataProvider' => $dataProvider,
        'ajaxUpdate' => false,
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '公告分类',
                'headerHtmlOptions' => array(
                    'style' => 'width:40px',
                    'nowrap' => 'nowrap'
                ),
                'value' => 'NewNotice::$WebCategorys[$data["category"]]'
            ),
            array(
                'name' => '标题',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                    'style' => 'width:120px',
                    'nowrap' => 'nowrap'
                ),
                'value' => array($this,'Seetitle')
            ),
            array(
                'name' => '创建时间',
                'headerHtmlOptions' => array(
                    'style' => 'width:95px',
                    'nowrap' => 'nowrap'
                ),
                'value' => '$data["create_time"]'
            ),
        )
    ));
}

?>
<script language="JavaScript" type="text/javascript">
    $(function(){
        $('.newNoticeDriver_read_id').css('color','#000');
        $('.newNoticeDriver_unread_id').css('color','blue').css('font-weight','blod');
        $('.click_a_button').click(function(){
            if(this.title=='点击关闭'){
                this.title='';
                this.innerHTML='在线试听语音公告';
            }else{
                this.innerHTML=pv_q(this.id,100,30);
                this.title='点击关闭';
            }
        });
        $('.newNoticeDriver_unread_id').click(function(){
            this.style.color='#000';
            this.style.fontWeight='';
            this.title='此公告已读';
        });

        <?php if(isset($driver)){ ?>
        //ajax请求司机排行
        $.get(<?php echo "'".Yii::app()->createUrl('/newNotice/rank')."'";?>, { catogray: 0,},
            function(data){
                $('#rank_driver_news').html(data);
            });
        <?php }?>
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
    <?php if(isset($driver)){ ?>
        function driver_rank(type){
            $(".btn-group a").removeClass("btn-primary");
            if(type==0){
                $('#driver_rank_daily').addClass("btn-primary");
            }else if(type==1){
                $('#driver_rank_month').addClass("btn-primary");
            }
            //ajax请求司机排行
            $.get(<?php echo "'".Yii::app()->createUrl('/newNotice/rank')."'";?>, { catogray: type,},
                function(data){
                    $('#rank_driver_news').html(data);
                });
        }
    <?php }?>

</script>

<?php $this->renderPartial('public'); ?>
