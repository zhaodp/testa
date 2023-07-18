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



    <div style="padding: 19px;" class="alert alert-success" use="form" id="form_0">

        <div class="left_div">
            <strong>司机申领</strong>
            <div class="mateial_line">
                <span class="type_name">类型</span>
                <span class="material_select material_title" >物料选择</span>
                <span class="quantity">数量</span>
                <span class="gift">是否礼包</span>
            </div>
            <?php
            $mater_typeinfo = Material::getTypeInfo(3);
            $str='';
            $i = 1;
            foreach($mater_typeinfo as $type_id => $name){
                if(isset($material[$type_id])){
                    $str .= '<div class="mateial_line">
                                     <span class="type_name">'.$name['name'].'：<input type="hidden" name="type_id['.$i.']" value="'.$type_id.'"></span>
                                     <span class="material_select"> '. CHtml::dropDownList('m_id['.$i.']','',$material[$type_id],array('style'=>'width:150px;')).'</span>
                                     <span class="quantity"><input type="text" name="quantity['.$i.']" id="quantity_'.$i.'" value="0" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,\'\')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,\'\')"></span>';
                    if($name['isredpacket']){
                        $str.='<span class="gift"><input type="checkbox" name="is_gift_bag['.$i.']" value = "1" ></span>';
                    }
                    $str.='</div>';
                    $i ++;
                }
            }
            echo $str;
            ?>
<!--            <div class="mateial_line">-->
<!--                <span class="type_name">T恤：<input type="hidden" name="type_id[1]" value="3"></span>-->
<!--                <span class="material_select">--><?php //if(isset($material[3])) echo CHtml::dropDownList('m_id[1]','',$material[3],array('style'=>'width:150px;'));?><!--</span>-->
<!--                <span class="quantity"><input type="text" name="quantity[1]" id="quantity_1" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span>-->
<!--                <span class="gift"><input type="checkbox" name="is_gift_bag[1]" value = "1"></span>-->
<!--            </div>-->
<!--            <div class="mateial_line">-->
<!--                <span class="type_name">马甲：<input type="hidden" name="type_id[2]" value="2"></span>-->
<!--                <span class="material_select">--><?php //if(isset($material[2])) echo CHtml::dropDownList('m_id[2]','',$material[2],array('style'=>'width:150px;'));?><!--</span>-->
<!--                <span class="quantity"><input type="text" name="quantity[2]" id="quantity_2" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span>-->
<!--                <span class="gift"><input type="checkbox" name="is_gift_bag[2]" value = "1"></span>-->
<!--            </div>-->
<!--            <div class="mateial_line">-->
<!--                <span class="type_name">工牌：<input type="hidden" name="type_id[3]" value="5"></span>-->
<!--                <span class="material_select">--><?php //if(isset($material[5])) echo CHtml::dropDownList('m_id[3]','',$material[5],array('style'=>'width:150px;'));?><!--</span>-->
<!--                <span class="quantity"><input type="text" name="quantity[3]" id="quantity_3" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span>-->
<!--                <span class="gift"><input type="checkbox" name="is_gift_bag[3]" value = "1"></span>-->
<!--            </div>-->
<!--            <div class="mateial_line">-->
<!--                <span class="type_name">支架：<input type="hidden" name="type_id[4]" value="4"></span>-->
<!--                <span class="material_select">--><?php //if(isset($material[4])) echo CHtml::dropDownList('m_id[4]','',$material[4],array('style'=>'width:150px;'));?><!--</span>-->
<!--                <span class="quantity"><input type="text" name="quantity[4]" id="quantity_4" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span>-->
<!--                <span class="gift"><input type="checkbox" name="is_gift_bag[4]" value = "1"></span>-->
<!--            </div>-->
<!--            <div class="mateial_line">-->
<!--                <span class="type_name">手机：<input type="hidden" name="type_id[5]" value="1"></span>-->
<!--                <span class="material_select">--><?php //if(isset($material[1])) echo CHtml::dropDownList('m_id[5]','',$material[1],array('style'=>'width:150px;'));?><!--</span>-->
<!--                <span class="quantity"><input type="text" name="quantity[5]" id="quantity_5" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span>-->
<!--            </div>-->
<!--            <div class="mateial_line">-->
<!--                <span class="type_name">手机卡：<input type="hidden" name="type_id[6]" value="6"></span>-->
<!--                <span class="material_select">--><?php //if(isset($material[6])) echo CHtml::dropDownList('m_id[6]','',$material[6],array('style'=>'width:150px;'));?><!--</span>-->
<!--                <span class="quantity"><input type="text" name="quantity[6]" id="quantity_6" value="" style="width:30px;" maxlength="1"  onkeyup="this.value=this.value.replace(/[^0-1]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-1]/g,'')"></span>-->
<!--            </div>-->
        </div>

    </div>
    <div class="buttons"><?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?></div>
    <?php
    $this->renderPartial('user_foot');
} ?>
<style>

    .mateial_line{
        background-color:white;
        padding:10px;
        display: block;
        width:460px;
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
    .gift{
        width:40px;
    }
    .quantity{ padding-left:40px; width:100px;padding-right:40px;}
</style>
<script type="text/javascript">
    $("document").ready(function () {
        $('#material').submit(function(){
            var mark_tag = $('#mark_tag').val();
            var mark = $('#mark').val();
            if(mark_tag != '礼包补领' && mark == ''){
                alert('请填写原因备注。');return false;
            }
            var notempty = false;
            $("input[name^='quantity']").each(function(){
                if($(this).val() != '' || $(this).val() != 0){
                    notempty = true;
                }
            });
            if(notempty == false){
                alert('请输入赠送物料数量。');
                return false;
            }


            var tmp = confirm('您确定所填写数据无误并且提交保存么?');
            if(tmp == false) return false;

        });
    });

    $('#mark_tag').change(function(){
        if($(this).val() == '礼包补领'){
            $("input[name^='is_gift_bag']").each(function(){
                $(this).removeAttr('readonly');
                $(this).removeAttr('onclick');
            });
        }else {
            $("input[name^='is_gift_bag']").each(function(){
                $(this).attr('readonly','readonly');
                $(this).attr('onclick','return false');
                $(this).removeAttr('checked');
            });

        }
    });
    </script>