<?php
$this->pageTitle = '用户屏蔽原因';
?>
<h4>用户屏蔽原因</h4>
<style type="text/css">
.big_box {
    width: 100%;
    height: auto;
    margin-top: 5px;
    clear: both;
}

.box_group {
    width: 100%;
    height: 23px;
    border-bottom: 1px solid #CCCCCC
}

.box_group_title {
    width: 100px;
    height: 20px;
    text-align: center;
    padding-top: 3px;
    overflow: hidden;
    background: #DFF9E4
}

.mods_box {
    width: 100%;
    height: auto;
    border-left: 1px solid #CCCCCC;
    border-bottom: 1px solid #CCCCCC;
    border-right: 1px solid #CCCCCC;
}

.mods_box_title {
    width: 200px;
    height: 40px;
    border-bottom: 1px solid #CCCCCC;
    float: left;
    text-align: center;
    padding-top: 3px;
    margin: 3px;
}

body {
    font-size: 12px;
}
</style>
<?php
if (! empty ( $user_remark )) {

        if (isset ( $user_remark ['remarks'] ) && ! empty ( $user_remark ['remarks'] )) {
            echo '<center>' . $user_remark ['remarks'] .'</center>';
        } else {
            echo '<center>暂无屏蔽原因</center>';
        }

        echo '</div>';
} else {

    echo '<center><h3>暂无屏蔽原因</h3></center>';
}
?>

