<?php
$cs=Yii::app()->clientScript;
$this->pageTitle = '新增黑名单用户';
echo "<h1>".$this->pageTitle."</h1><br />";
?>
<table id=dis_container class="table table-striped table-bordered">
    <tr>
        <td>以下号码已在白名单中，无法加入黑名单</td>
    </tr>
    <tbody>
<?php
    foreach($in_whitelist as $item) {
        echo "<tr><td>$item</td></tr>";
    }
?>
    </tbody>
</table>
