<ul class="nav nav-tabs" id="myTab">
    <?php
    $tab_array = array(
        1=>array(
            'url'=>Yii::app()->createUrl('material/index'),
            'name'=>'司机物料信息',
        ),
        2=>array(
            'url'=>Yii::app()->createUrl('material/stat'),
            'name'=>'物料盘存',
        ),
        3=>array(
            'url'=>Yii::app()->createUrl('material/moneyList'),
            'name'=>'资金对账',
        ),
        4=>array(
            'url'=>Yii::app()->createUrl('material/list'),
            'name'=>'通知申领',
        ),
        5=>array(
            'url'=>Yii::app()->createUrl('material/configAdmin'),
            'name'=>'物料管理',
        ),

    );
    $tab_str = '';
    foreach($tab_array as $key => $val){
        if($tab == $key){
            $tab_str .= '<li class="active"><a  data-toggle="tab" >'.$val['name'].'</a></li>';
        }else{
            $tab_str .= '<li class=""><a href="'.$val['url'].'" >'.$val['name'].'</a></li>';
        }
    }
    echo $tab_str;

    ?>

</ul>