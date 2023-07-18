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
</style>
<table class="table_see">
    <tr>
        <td colspan="2" class="td_text"><strong><?php echo $model->title;?></strong></td>
    </tr>
    <tr>
        <td colspan="2" class="td_text_content"><?php echo $model->content;?></td>
    </tr>
    <tr>
        <td colspan="2" class="right_td"><?php echo $model->created;?></td>
    </tr>
    <tr>
        <td colspan="2" class="right_td"><?php echo $model->opt_user_name;?></td>
    </tr>
</table>