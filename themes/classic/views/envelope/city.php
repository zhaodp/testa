

<?php echo CHtml::link('创建红包', Yii::app()->createUrl('envelope/create'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
&nbsp;
<?php echo CHtml::link('进行中的红包', Yii::app()->createUrl('envelope/admin'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
&nbsp;
<?php echo CHtml::link('红包发放列表', Yii::app()->createUrl('envelope/extend'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
&nbsp;
<?php echo CHtml::link('红包发放统计', Yii::app()->createUrl('envelope/city'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
<h1>红包发放量</h1>
<?php

$num = count($data);
if ($num == 0) {
    echo "暂无数据!";
}

$row = ceil($num / 2);
$show = 0;
?>
<table border="1" cellpadding="1" cellspacing="1" width="98%" align="center">
    <?php
    for ($i = 0;
         $i < $row;
         $i++) {
        ?>
        <tr>
            <th colspan="4">
                <?php
                $city_id1 = $cityList[$show];
                $cityData1 = $data[$city_id1];
                echo Dict::item('city', $city_id1);
                $show++;
                ?>
            </th>
            <th colspan="4">
                <?php
                if ($show < $num) {
                    $city_id2 = $cityList[$show];
                    $cityData2 = $data[$city_id2];
                    echo Dict::item('city', $city_id2);
                    $show++;
                }
                ?>
            </th>
        </tr>
        <tr>
            <th width="12.5%">
                日期
            </th>
            <th width="12.5%">
                红包金额
            </th>
            <th width="12.5%">
                红包个数
            </th>
            <th width="12.5%">
                司机个数
            </th>

            <th width="12.5%">
                日期
            </th>
            <th width="12.5%">
                红包金额
            </th>
            <th width="12.5%">
                红包个数
            </th>
            <th width="12.5%">
                司机个数
            </th>
        </tr>

        <tr>
            <td colspan="4">
                <table width="100%"  border="1" cellpadding="1" cellspacing="1" >
                    <?php foreach ($cityData1 as $d) { ?>
                        <tr>
                            <td width="25%">
                                <?php echo $d->last_changed_date; ?>
                            </td>
                            <td width="25%">
                                <?php echo $d->amount; ?>
                            </td>
                            <td width="25%">
                                <?php echo $d->envelope_id; ?>
                            </td>
                            <td>
                                <?php echo $d->id; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>


            <td colspan="4">
                <?php
                if ($show < $num && !empty($cityData2)) {
                    ?>
                    <table width="100%"  border="1" cellpadding="1" cellspacing="1" >
                        <?php foreach ($cityData2 as $d) {
                            ?>
                            <tr>
                                <td width="25%">
                                    <?php echo $d->last_changed_date; ?>
                                </td>
                                <td width="25%">
                                    <?php echo $d->amount; ?>
                                </td>
                                <td width="25%">
                                    <?php echo $d->envelope_id; ?>
                                </td>
                                <td>
                                    <?php echo $d->id; ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>
