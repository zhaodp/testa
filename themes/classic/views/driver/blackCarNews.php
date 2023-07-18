<div id="table_blackNews">
    <style type="text/css">
        table.table_news tr th, table.table_news tr td {
            text-align: center;
            line-height: 180%;
            background: #eee;
            border: 1px solid #ccc;
        }
    </style>
    <h4>价格信息列表</h4>
    <table width="100%" class="table_news">
        <tr>
            <th width="20%">司机工号</th>
            <th width="80%">价格信息</th>
        </tr>
        <?php if (!empty($DriverNews)) { ?>
            <?php foreach ($DriverNews as $key => $value) { ?>
                <tr>
                    <td><?php echo $value['employee_id']; ?></td>
                    <td><?php echo $value['comments']; ?></td>
                </tr>
            <?php } ?>
            <?php if (!empty($pages)) { ?>
                <tr>
                    <td colspan="6">
                        <?php $this->widget('CLinkPager', array(
                            'pages' => $pages,
                        ));?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="6">没有任何信息</td>
            </tr>
        <?php } ?>
    </table>
</div>
<script type="text/javascript">
    $('#table_blackNews .yiiPager a').live('click', function () {
        $.ajax({
            url: $(this).attr('href'),
            success: function (html) {
                $('#table_blackNews').html(html);
            }
        });
        return false;//阻止a标签
    });
</script>