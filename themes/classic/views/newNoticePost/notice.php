<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'newNotice-post-grid',
    'dataProvider' => $dataProvider,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name' => '标题',
            'headerHtmlOptions' => array(
                'style' => 'width:100px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["title"]'
        ),
        array(
            'name' => '创建时间',
            'headerHtmlOptions' => array(
                'style' => 'width:100px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["created"]'
        ),
        array(
            'name' => '创建者',
            'headerHtmlOptions' => array(
                'style' => 'width:40px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["opt_user_name"]'
        ),
        array(
            'name' => '选择',
            'type' => 'raw',
            'headerHtmlOptions' => array(
                'style' => 'width:40px',
                'nowrap' => 'nowrap'
            ),
            'value' => array($this,'getSelectNewNotice')
        ),
    )
));

?>

<span class="btn btn-info" title="" id="select_notice_post_id" aria-hidden="true" data-dismiss="modal" style="margin-left:60%;">确认选择</span>
　　<span class="btn btn-warning" id="select_notice_post_id_fail">取消选择</span>

<script type="text/javascript">
    $(function(){
        $('#select_notice_post_id').attr('title',$('#post_id_hidden').val());
        $('#select_notice_post_id_fail').attr('title',$('#post_id_hidden').val());

        $('#select_notice_post_id').click(function(){
            if(this.title!=''&&this.title!=0){
                $('#post_id_hidden').val(this.title);
                alert('成功选择长文章');
                var url=$('#get_long_post_id').attr('url');
                var temp=url.substr(0,url.indexOf('&post_id='));
                var lastUrl=temp+'&post_id='+$('#post_id_hidden').val();
                $('#get_long_post_id').attr('url',lastUrl);
                if($('#post_id_hidden').val()==0){
                    $('.long_is_display').css('display','none');
                    $('.long_is_display_title').html();
                }else{
                    $('.long_is_display').css('display','block');
                    var post_id_='#noticepost'+this.title;
                    $('.long_is_display_title').html('所选的长文章主题：'+$(post_id_).val());
                }
            }else{
                alert('未选中任何长文章');
            }
        });

        $('#select_notice_post_id_fail').click(function(){
            if(confirm('确定取消选择吗？')?true:false){
                var post_id_title='#noticepost'+this.title;
                if($(post_id_title).attr('checked')){
                    $('.check_select_new_notice_post_id').attr('checked',false);
                    $('#select_notice_post_id').attr('title','');
                    $('#select_notice_post_id_fail').attr('title','');
                    $('#post_id_hidden').val(0);
                    $('.long_is_display').css('display','none');
                    $('.long_is_display_title').html();

                    var url=$('#get_long_post_id').attr('url');
                    var temp=url.substr(0,url.indexOf('&post_id='));
                    var lastUrl=temp+'&post_id='+$('#post_id_hidden').val();
                    $('#get_long_post_id').attr('url',lastUrl);
                    alert('取消成功');
                }else{
                    alert('您还未选择，无需取消');
                }
            }
        });

    });
    function clickPostName(id){
        $('#select_notice_post_id').attr('title',id);
        $('#select_notice_post_id_fail').attr('title',id);
        $('.check_select_new_notice_post_id').attr('checked',false);
        var post_id_='#noticepost'+id;
        $(post_id_).attr('checked',true);
    }
</script>