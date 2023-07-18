<style type="text/css">
    table.table_see{
        width:100%;
        background: #eee;
    }
    table.table_see tr{
        padding-bottom:20px;
        line-height:180%;
    }
    table.table_see tr td.td_text{
        border:1px solid #eee0e5;
        text-indent: 10px;
        text-align: center;
    }
    table.table_see tr td.td_text_content{
        width:700px;
        text-indent:20px;
        margin:0 auto;
    }
    table.table_see tr td.right_td{
        text-align:right;
        padding-right:30px;
    }
    table.table_see tr td.td1{
        width:15%;
    }
</style>
<table class="table_see">
    <tr>
        <td colspan="2" class="td_text"><strong><?php echo $model->title;?></strong></td>
    </tr>
    <tr>
        <td class="td1"><strong>　公告类型：</strong></td>
        <td><?php echo NewNotice::$types[$model->type];?> </td>
    </tr>
    <tr>
        <td colspan="2" class="td_text_content"><?php echo ($model->content||$model->type==NewNotice::TEXT)?$model->content:'<span class="click_see_button btn btn-small btn-info" style="text-align:center;" id="'.$model->audio_url.'">在线试听语音公告 </span>';?></td>
    </tr>
    <tr>
        <td colspan="2" class="right_td"><?php echo $model->update_time;?></td>
    </tr>
    <tr>
        <td colspan="2" class="right_td"><?php echo $model->update_user;?></td>
    </tr>
</table>
<script language="JavaScript" type="text/javascript">
    $(function(){
        $('.click_see_button').click(function(){
            if(this.title=='点击关闭'){
                this.title='';
                this.innerHTML='在线试听语音公告';
            }else{
                this.innerHTML=pv_q(this.id,100,30);
                this.title='点击关闭';
            }
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