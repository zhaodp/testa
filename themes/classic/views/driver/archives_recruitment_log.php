<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-15
 * Time: 下午5:45
 * To change this template use File | Settings | File Templates.
 */
$this->layout = '//layouts/main_no_nav';
?>

<style>
    th {
        background-color: #D9EDF7;
    }

    .navbar .navbar-inner{
        background-color: #FAFAFA!important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067)!important;
        background-image: -moz-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#FFF), to(#F2F2F2));
        background-image: -webkit-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -o-linear-gradient(top, #FFF, #F2F2F2);
        background-image: linear-gradient(to bottom, #FFF, #F2F2F2);
    }

</style>

<div class="navbar">
    <div class="navbar-inner" >
        <a class="brand" href="#">司机追溯</a>
    </div>
</div>

<table class="table table-bordered">
    <?php
    if($data){
    ?>
    <tr>
        <th>时间</th>
        <th>记录</th>
    </tr>
    <?php
        foreach($data as $item){
            echo '<tr><td>'.date('Y-m-d H:i',$item['time']).'</td><td>'.$item['message'].'</td></tr>';
        }
    }else{ echo '<tr><td>暂无日志</td></tr>';}
    ?>
</table>

