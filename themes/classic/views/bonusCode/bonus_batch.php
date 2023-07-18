<?php

/**
 * 手动绑定优惠券
 * @date 2012-06-06
 */
$this->pageTitle = '优惠券批量绑定';
?>

<h1>优惠券批量绑定</h1>
<div class="row span12">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'order-form',
        'enableAjaxValidation' => false,
    ));
    ?>
    <table>
        <tr>
            <td><label class="span12">优惠码：</label></td>
            <td><?php echo $form->textField($model, 'bonus_sn', array('size' => 70, 'maxlength' => 60)); ?></td>
        </tr>
        <tr>
            <td><label class="span12">客户电话：</label></td>
            <td><?php echo $form->textArea($model, 'customer_phone', array('rows' => '20')); ?></td>
        </tr>
    </table>
    <?php
    echo CHtml::submitButton('保存', array('class' => 'span3 btn btn-success'));
    $this->endWidget();
    ?>
</div>