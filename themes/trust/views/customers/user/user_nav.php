<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-13
 * Time: 下午8:53
 * auther mengtianxue
 */
?>
<h1>用户管理</h1>
<ul class="nav nav-tabs">
    <?php $action = $this->getAction()->getId(); ?>
    <?php
    $current_tab='';
    switch($action){
        case 'user_admin':
            $current_tab.='<li '.'class="active"  >';
            $current_tab.=CHtml::link('用户列表',Yii::app()->createUrl('/customers/user_admin'));
            $current_tab.= '</li>';
            echo $current_tab;
            break;
        case 'user_trans':
            $current_tab.='<li '.'class="active">';
            $current_tab.=CHtml::link('用户交易流水',Yii::app()->createUrl('/customers/user_trans'));
            $current_tab.= '</li>';
            echo $current_tab;
            break;
        default:
            break;

    }

    ?>



    <?php
    if ($action == 'user_info') {
        ?>
        <li class="active">
            <a href="javascript:void(0);">代驾服务</a>
        </li>
    <?php
    }
    ?>

    <?php
    if ($action == 'user_history') {
        ?>
        <li class="active">
            <a href="javascript:void(0);">充值历史</a>
        </li>
    <?php
    }
    ?>

</ul>