<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-15
 * Time: 下午5:32
 * To change this template use File | Settings | File Templates.
 */

?>

<style>
    .tit {
        background-color: #D9EDF7;
    }

    .navbars .navbar-inner{
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
    <div class="navbar-inner" style="background-color: #FAFAFA; background-image: linear-gradient(to bottom, #FFFFFF, #F2F2F2); box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067);">
        <a class="brand" href="#">商品信息</a>
    </div>
</div>


<table class="table table-bordered">
    <tr>
        <th>商品类型</th>
        <th>商品名称</th>
        <th>商品规格</th>
        <th>商品数量</th>
        <th>商品单价</th>
    </tr>
    <?php
    $s = '';
    foreach($sku_data as $v){
        $s.= '<tr><td width="20%">'.$v['type'].'</td><td width="20%">'.$v['name'].'</td><td width="20%">'.$v['size'].'</td><td width="20%">'.$v['number'].'</td><td width="20%">'.$v['price'].'</td></tr>';
    }
    echo $s;
    ?>



</table>


<div class="navbar">
    <div class="navbar-inner" style="background-color: #FAFAFA; background-image: linear-gradient(to bottom, #FFFFFF, #F2F2F2); box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067);">
        <a class="brand" href="#">收货人信息</a>
    </div>
</div>


<table class="table table-bordered">
    <tr>
        <td class="tit">收货人姓名</td>
        <td><?php echo $data->receiver_name;?></td>
        <td class="tit">收货人电话</td>
        <td><?php echo $data->receiver_phone;?></td>
    </tr>
    <tr>
        <td class="tit" >收货人地址 </td>
        <td colspan="3"><?php echo $province_name.$city_name.$district_name.$data->receiver_addr;?></td>
    </tr>
</table>
