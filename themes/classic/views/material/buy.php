<?php
/**
 * Created by JetBrains PhpStorm.
 * User: duke
 * Date: 2014-11-03
 * Time: 下午12:23
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '司机物料管理';
$this->renderPartial('_search',array('id'=>$id));

 if(is_array($userinfo)){
    $this->renderPartial('user_head',array(
        'id'=>$id,
        'title'=>$title,
        'type'=>$type,
        'id_type'=>$id_type,
        'userinfo'=>$userinfo,
        'material'=>$material,
        'price_arr'=>$price_arr,
        'time'=>$time,

    ));
    ?>



                            <div style="padding: 19px;display:block;float:left;" class="alert alert-success" use="form" id="form_0">

                                <div class="left_div">
                                    <strong>司机申领</strong>
                                    <div class="mateial_line"> <span class="type_name">类型</span><span class="material_select material_title" >物料选择</span><span class="quantity">数量</span></div>
                                    <?php
                                    $mater_typeinfo = Material::getTypeInfo(2);
                                    $str='';
                                    $i = 1;
                                    foreach($mater_typeinfo as $type_id => $name){
                                        if(isset($material[$type_id])){
                                            $str .= '<div class="mateial_line"> <span class="type_name">'.$name['name'].'：<input type="hidden" name="type_id['.$i.']" value="'.$type_id.'"></span><span class="material_select"> '. CHtml::dropDownList('m_id['.$i.']','',$material[$type_id],array('style'=>'width:150px;')).'</span><span class="quantity"><input type="text" name="quantity['.$i.']" id="quantity_'.$i.'" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,\'\')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,\'\')"></span></div>';
                                            $i ++;
                                        }
                                    }
                                    echo $str;
                                    ?>
<!--                                    <div class="mateial_line"> <span class="type_name">T恤：<input type="hidden" name="type_id[1]" value="3"></span><span class="material_select">--><?php //if(isset($material[3])) echo CHtml::dropDownList('m_id[1]','',$material[3],array('style'=>'width:150px;'));?><!--</span><span class="quantity"><input type="text" name="quantity[1]" id="quantity_1" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span></div>-->
<!--                                    <div class="mateial_line"> <span class="type_name">马甲：<input type="hidden" name="type_id[2]" value="2"></span><span class="material_select">--><?php //if(isset($material[2])) echo CHtml::dropDownList('m_id[2]','',$material[2],array('style'=>'width:150px;'));?><!--</span><span class="quantity"><input type="text" name="quantity[2]" id="quantity_2" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span></div>-->
<!--                                    <div class="mateial_line"> <span class="type_name">工牌：<input type="hidden" name="type_id[3]" value="5"></span><span class="material_select">--><?php //if(isset($material[5])) echo CHtml::dropDownList('m_id[3]','',$material[5],array('style'=>'width:150px;'));?><!--</span><span class="quantity"><input type="text" name="quantity[3]" id="quantity_3" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span></div>-->
<!--                                    <div class="mateial_line"> <span class="type_name">支架：<input type="hidden" name="type_id[4]" value="4"></span><span class="material_select">--><?php //if(isset($material[4])) echo CHtml::dropDownList('m_id[4]','',$material[4],array('style'=>'width:150px;'));?><!--</span><span class="quantity"><input type="text" name="quantity[4]" id="quantity_4" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span></div>-->
<!--                                    <div class="mateial_line"> <span class="type_name">手机：<input type="hidden" name="type_id[5]" value="1"></span><span class="material_select">--><?php //if(isset($material[1])) echo CHtml::dropDownList('m_id[5]','',$material[1],array('style'=>'width:150px;'));?><!--</span><span class="quantity"><input type="text" name="quantity[5]" id="quantity_5" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span></div>-->
<!--                                    <div class="mateial_line"> <span class="type_name">手机卡：<input type="hidden" name="type_id[6]" value="6"></span><span class="material_select">--><?php //if(isset($material[6])) echo CHtml::dropDownList('m_id[6]','',$material[6],array('style'=>'width:150px;'));?><!--</span><span class="quantity"><input type="text" name="quantity[6]" id="quantity_6" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span></div>-->
                                </div>
                                <div class="right_div">
                                    <strong>金额收入</strong>
                                    <div class="mateial_line"> <span class="type_name type_money">款项</span><span class="material_select">金额</span></div>
                                    <div class="mateial_line"> <span class="type_name type_money">装备押金：</span><span class="material_select" ><?php echo CHtml::textField('zhuangbei','',array('readonly'=>'readonly','id'=>'zhuangbei','class'=>'text_shot'))?></span></div>
                                    <div class="mateial_line"> <span class="type_name type_money">手机租金：</span><span class="material_select"><?php echo CHtml::textField('cellphone','',array('readonly'=>'readonly','id'=>'cellphone','class'=>'text_shot'))?></span></div>
                                    <div class="mateial_line"> <span class="type_name type_money">手机卡：</span><span class="material_select"><?php echo CHtml::textField('card','',array('readonly'=>'readonly','id'=>'card','class'=>'text_shot'))?></span></div>
                                    <div class="mateial_line"> <span class="type_name type_money">发票：</span><span class="material_select"><?php echo CHtml::textField('invoice','',array('id'=>'invoice','class'=>'text_shot','onkeyup'=>"this.value=this.value.replace(/[^0-9]/g,'')", 'onafterpaste' =>"this.value=this.value.replace(/[^0-9]/g,'')"))?><span style="float:right;">注：发票需按照发票面额6%计算<br>金额后填写，作为收取的费用金额</span></span></div>
                                    <div class="mateial_line"> <span class="type_name type_money">汇总：</span><span class="material_select"><?php echo CHtml::textField('total','',array('readonly'=>'readonly','id'=>'total','class'=>'text_shot'))?></span></div>
                                </div>

                            </div>
                            <div class="buttons"><?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large','id'=>'submits')); ?></div>
     <?php
     $this->renderPartial('user_foot');
 } ?>
<style>
    .left_div{
        width:500px;
        float:left;

    }
    .right_div{
        width:530px;
        float:right;
    }
    .mateial_line{
        background-color:white;
        padding:10px;
        display: block;
        width:440px;
        border-bottom:1px solid #000;
    }
    .type_name {
        width:100px;
        display:-moz-inline-box;
        display:inline-block;
        padding-right:20px; }
    .type_money{ padding-left:30px;}
    .material_select{width: 200px;}
    .material_title{
        width:150px;
        display:-moz-inline-box;
        display:inline-block;
    }
    .quantity{ padding-left:40px; width:100px;}
    .text_shot{
        width:50px;
    }
</style>
<script type="text/javascript">
    <?php if(isset($price_arr) && is_array($price_arr)){
        foreach($price_arr as $id => $price){
            echo "var price_{$id}={$price};\n";
        }
    }?>
    $("document").ready(function () {
        $('#material').submit(function(){
            //alert('aaa');return false;
            var check = false;
            $("input[name^='quantity']").each(function(){
                var vals = Number($(this).val());
                if(vals != 0) {
                    check = true;
                }
            });

            var invoice_vals = $('#invoice').val() != 0 ? true : false;
            if(!check && !invoice_vals){
                alert('内容不能为空，请注意。');return false;
            }
            var tmp = confirm('您确定所填写数据无误并且提交保存么?');
            if(tmp == false) return false;

        });
    });


    $("#invoice").change(function(){
        summoney();
    });

    $("select[name^='m_id']").change(function(){
        var name = $(this).attr('name');
        var id = name.substring(5,name.length-1);
        var quantity_name =  'quantity_'+id;
        var quantity = $('#'+quantity_name).val();
        if(quantity != '' && quantity > 0){
            summoney();
        }
    });

    $("input[name^='quantity']").change(function(){
        summoney();
    });

    function summoney(){
        var cellphone =  0;
        var invoices = 0;
        var zhuangbei = 0;
        var card = 0;
        var baozhengjin = 0;
        var total = 0;

        $("select[name^='m_id']").each(function(){
            var name = $(this).attr('name');
            var id = name.substring(5,name.length-1);
            var type_name = 'type_id['+id+']';
            var type_id = $("input[name='"+type_name+"']").val();

            var quan_name = 'quantity[' + id + ']';
            var quantity = $("input[name='"+quan_name+"']").val();
            var wuliaoname = $(this).val();
            var price = eval("price_"+wuliaoname);

            if(quantity > 0){
                switch(type_id){
                    case '5':
                        cellphone = quantity * price;
                        break;
                    case '6':
                        card = quantity * price;
                        break;
                    case '7':
                        baozhengjin = quantity * price;
                        break;

                    default:
                        zhuangbei = zhuangbei + (quantity * price) ;
                }
                total = total + (quantity * price);
            }


        });
        invoices = $('input[name="invoice"]').val();
        total = Number(total) + Number(invoices);

        $('input[name="zhuangbei"]').val(zhuangbei);
        $('input[name="cellphone"]').val(cellphone);
        $('input[name="card"]').val(card);
        $('input[name="total"]').val(total);

    }
</script>