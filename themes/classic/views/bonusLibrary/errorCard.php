<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bonus-library-form',
        'enableAjaxValidation' => false,
    )); ?>

    <div>
        <table>
            <tr>
                <td>确认将以下卡标为坏卡？
                    <input type="hidden" id="sn_id" name="channel_id" value="<?php echo $bonus_sn; ?>">
                </td>
            </tr>
            <?php
            foreach ($arr_bonus_sn as $sn) {
                ?>
                <tr>
                    <td>
                        <?php echo $sn; ?>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <div style="width:27%; float: left;">
        <button type="button" onclick="errorCard()" style="width:76px">确定</button>
    </div>

</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
    function errorCard() {
        var bonus_sn = $('#sn_id').val();

        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/bonusLibrary/errorCardCorm');?>',
            'data': {
                'bonus_sn': bonus_sn
            },
            'type': 'post',
            'dataType': 'json',
            'success': function (data) {
                if (data == '0') {
                    alert('操作成功!');
                    window.parent.$('#view_bonus_dialog_error').dialog('close');
                    window.parent.$('.search-form form').submit();
                } else {
                    alert('操作失败,请稍后再试!');
                }
            }
        });
    }
</script>
