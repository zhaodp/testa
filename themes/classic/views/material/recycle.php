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
    <strong>当前物料状态</strong>
    <div style="padding: 19px;" class="alert alert-success">

        <?php
        if(!empty($material2driver)){

            //print_r($material2driver);die;
            echo '<table id="mater_info" style="text-align:center;"><tr><th>物料名</th><th>数量</th><th>领取时间</th><th>是否礼包</th><th>折旧费用(元/月)</th><th>未归还赔偿</th><th>状态</th>';
            echo '<th class="checkb"></td>';
            echo '</tr>';
            $s = '';
            foreach($material2driver as $v){

                if(isset($mater_info[$v->m_id]) && !empty($mater_info[$v->m_id])){
                    $quantity = $v->quantity;
                    $s = '<tr><td>'.$mater_info[$v->m_id]['name']
                        .'</td><td>'.$v->quantity
                        .'</td><td>'.$v->create_time
                        .'</td><td>'.Material2Driver::getIsGiftStatus($v->is_gift_bag)
                        .'</td><td>'.$mater_info[$v->m_id]['depreciation']
                        .'</td><td><span id="price_'.$v->id.'" style="display:none;">'.$mater_info[$v->m_id]['price']
                        .'</span>
                        <input type="hidden" name="mater_price" id="materi_' . $v->id . '" value="' . $mater_info[$v->m_id]['price'] . '">
                         <input type="hidden" name="mater_create_time" id="create_time_' . $v->id . '" value="' . $v->create_time . '">
                         <input type="hidden" name="mater_is_gift_bag" id="is_gift_bag' . $v->id . '" value="' . $v->is_gift_bag . '">
                         <input type="hidden" name="mater_type_id" id="type_id' . $v->id . '" value="' . $v->type_id . '">
                         <input type="hidden"  id="depreciation' . $v->id . '" value="' . $mater_info[$v->m_id]['depreciation'] . '">
                         <input type="hidden" name="mater_status" id="status' . $v->id . '" value="' . $v->status . '">

                         </td><td>'.Material2Driver::getstatus($v->status).'</td>';
                        if($v->status != Material2Driver::STATUS_RECYCLE){
                            $s.= '<td class="checkb"><input type="checkbox" name="driver_material['.$v->id.']" value="'.$v->id.'" checked="checked">归还</td>';
                        }else $s.='<td></td>';

                    $s.= '</tr>';
                }
                echo $s;

            }
            echo '</table>';
        }
        ?>
    </div>

    <script>
        var clickeds = false;
        var phone_disposit = <?php echo $material_money['cellphone_deposit'];?>;
        var price_zhuangbei = 0;
        var zhuangbei_disposit = <?php echo $material_money['equipment_deposit'];?>
//        $('#checkall').click(function(){
//            //alert('aaaa');return false;
//            $('input[name^="driver_material"]').attr("checked",this.checked);
//        });
        $('input[name^="driver_material"]').click(function (){
            var checked = this.checked;
            var id = this.value;
            if($('#status'+ id).val() != 3 || $('#is_gift_bag' + id).val() == 1) {
                if(checked == false){
                    $('#status'+ id).val() != 3  && $('#price_' + id).show();
                    calculate(id,'-');
                }else{
                    $('#status'+ id).val() != 3  && $('#price_' + id).hide();
                    calculate(id,'+');
                }
                summoney();
            }

        });

//        $("#invoice").change(function(){
//            summoney();
//        });


        function calculate(id,type) {
            var type_zhuangbei_not_gift = false;
            var count = 0;

            if($('#is_gift_bag' + id).val() == 0){
                var phone_price_sub = 0;
                var type_id = $('#type_id' + id).val();
                var money_phone = tmp = phone_disposit; //初始装备押金内金额
                var tmp_phone_price_all = 0;
                if( type_id == 5 ) { //电话要算折旧费 单独计算
                    $('input[name^="driver_material"]').each(function(){
                        var checked_sub = this.checked; //遍历的当前checkbox 是否选中
                        var id_sub = this.value; //当前id
                        var type_idss = $('#type_id' + id_sub).val();
                        if(type_idss == 5 ) {
                            var create_time = $('#create_time_' + id_sub).val();
                            var depre  = $('#depreciation' + id_sub).val(); //折旧费
                            var fix = cal_cellphone_deposi(create_time, depre);
                            if(Number(fix) > Number($('#materi_'+ id_sub ).val()) ) {
                                fix = Number($('#materi_'+ id_sub ).val());
                            }

                            if(type == '-' && checked_sub == false) { //没选中状态
                                money_phone = money_phone -  Number($('#materi_'+ id_sub ).val()) + Number(fix) ;
                            }
                            else if(type == '+' && checked_sub == true) { //选中状态
                                phone_price_sub =   phone_price_sub + Number($('#materi_'+ id_sub ).val()) - fix;
                            }
                        }
                    });
                    if(type == '+'){
                        money_phone =  phone_price_sub;
                    }
                    if(money_phone < 0){
                        money_phone = 0;
                    }
                    $('#cellphone').val(money_phone);
                }else if(type_id ==6 || type_id == 7){
                    var id_name = '';
                    switch(type_id){
                        case '6':
                            id_name = 'card' ;
                            break;
                        case '7':
                            id_name = 'promise';
                            break;

                    }
                    if(type == '-') {
                        var nums = Number($('#' + id_name).val()) - Number($('#materi_' + id).val());
                    }
                    else if(type == '+') {
                        var nums = Number($('#' + id_name).val()) + Number($('#materi_' + id).val());
                    }

                    var final = nums >= 0 ? nums : 0;
                    $('#' + id_name).val(final);
                }
                else{
                    var nums = zhuangbei_calculate();
                    var final = nums >= 0 ? nums : 0;
                    $('#zhuangbei').val(final);
                }
            }
            else {
                var nums = zhuangbei_calculate();
                var final = nums >= 0 ? nums : 0;
                $('#zhuangbei').val(final);
            }
        }


        function zhuangbei_calculate(){
            var checked_is_packet_all = true; //初始默认红包都是选中状态
            var redpacket_empty = true; //属于红包的装备默认都未选中
            var tmp_redpacket_price_all = 0;
            var money = zhuangbei_disposit; //初始装备押金内金额

            $('input[name^="driver_material"]').each(function(){
                var checked_sub = this.checked; //遍历的当前checkbox 是否选中
                var id_sub = this.value; //当前id

                var type_id = $('#type_id' + id_sub).val();
                if(type_id != 5 && type_id != 6 && type_id != 7) {
                    if(checked_sub == false) { //没选中状态
                        if($('#is_gift_bag' + id_sub).val() == 0) {
                            money = money -  Number($('#materi_'+ id_sub ).val());
                        }
                        else {
                            tmp_redpacket_price_all = tmp_redpacket_price_all + Number($('#materi_'+id_sub).val());
                        }
                    }
                    else { //选中状态
                        if($('#is_gift_bag' + id_sub).val() == 1){ //
                            redpacket_empty = false;
                        }

                    }

                }
            });

            if(redpacket_empty == true){
                money = money - 200 ;
            }else {
                money = money - tmp_redpacket_price_all;
            }
            return money;
        }

        function summoney(){
            var zhuangbei = $('#zhuangbei').val();
            var card = $('#card').val();
            var cellphone = $('#cellphone').val();
            var promise = $('#promise').val();
            var invoice = $('#invoice').val();
            var total =  Number(zhuangbei) + Number(invoice)  + Number(cellphone) + Number(promise) + Number(card) ;

            $('#total').val(total );

        }

        function cal_cellphone_deposi(create_time,depre){
            var now_time = new Date().getTime();
            var tmp = create_time.replace(/-/g, "/");

            var format_create_time = Date.parse(tmp);
            var month = Math.ceil((now_time / 1000 - format_create_time / 1000) / (30 * 3600 * 24));
            var now_str = format_dates(new Date(),'yyyy-MM-dd');

            var old_str = format_dates(new Date(tmp) , 'yyyy-MM-dd');
            if(now_str == old_str){
                month = 0;
            }
            depre = depre || 0;
            return month * depre;
        }

        function format_dates(date, format) {
            format = format || 'yyyy-MM-dd hh:mm:ss';
            var o = {
                "M+": date.getMonth() + 1,
                "d+": date.getDate(),
                "h+": date.getHours(),
                "m+": date.getMinutes(),
                "s+": date.getSeconds(),
                "q+": Math.floor((date.getMonth() + 3) / 3),
                "S": date.getMilliseconds()
            };
            if (/(y+)/.test(format)) {
                format = format.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
            }
            for (var k in o) {
                if (new RegExp("(" + k + ")").test(format)) {
                    format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
                }
            }
            return format;
        }
    </script>


    <style>
        #mater_info{
            width:850px;
            border:solid #000000 1px;
            background: #ffffff;
        }
        #mater_info tr{
            border:solid #000000 1px;
        }
        #mater_info td{
            padding:5px;
            border:solid #000000 1px;
        }

        #mater_info th{
            font-size:16px;
            padding:5px;
            border:solid #000000 1px;
        }

        #mater_info .checkb{
            width:80px;
            text-align: center;
        }
    </style>



    <div style="padding: 19px;display:block;" class="alert alert-success" use="form" id="form_0">
        <div class="left_div">
            <strong>金额支出</strong>
            <div class="mateial_line"> <span class="type_name type_money">款项</span><span class="material_select">金额</span></div>
            <div class="mateial_line"> <span class="type_name type_money">装备押金：</span><span class="material_select" ><?php echo CHtml::textField('zhuangbei',$material_money['equipment_deposit'],array('readonly'=>'readonly','id'=>'zhuangbei','class'=>'text_shot'))?></span></div>
            <div class="mateial_line"> <span class="type_name type_money">手机租金：</span><span class="material_select"><?php echo CHtml::textField('cellphone',$material_money['cellphone_deposit'],array('readonly'=>'readonly','id'=>'cellphone','class'=>'text_shot'))?></span></div>
            <div class="mateial_line"> <span class="type_name type_money">手机卡：</span><span class="material_select"><?php echo CHtml::textField('card',$material_money['simcard_deposit'],array('readonly'=>'readonly','id'=>'card','class'=>'text_shot'))?></span></div>
            <div class="mateial_line"> <span class="type_name type_money">保证金：</span><span class="material_select"><?php echo CHtml::textField('promise',$material_money['cash_deposit'],array('readonly'=>'readonly','id'=>'promise','class'=>'text_shot'))?></span></div>
            <div class="mateial_line"> <span class="type_name type_money">发票：</span><span class="material_select"><?php echo CHtml::textField('invoice',$material_money['invoice'],array('id'=>'invoice','onchange'=>'summoney();','onkeyup'=>"this.value=this.value.replace(/[^0-9]/g,'')", 'onafterpaste' =>"this.value=this.value.replace(/[^0-9]/g,'')",'class'=>'text_shot'))?>
                    <span style="float:right;">注：发票需按照发票面额6%计算<br>金额后填写，作为收取的费用金额</span></span></div>
            <div class="mateial_line"> <span class="type_name type_money">汇总：</span><span class="material_select"><?php echo CHtml::textField('total',$material_money['total'],array('readonly'=>'readonly','id'=>'total','class'=>'text_shot'))?></span></div>
        </div>
    </div>
    <div class="buttons"><?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large','confirm'=>'您确定所填写数据无误并且提交保存么?')); ?></div>


<?php
    $this->renderPartial('user_foot');
} ?>
<style>
    .left_div{
        width:600px;
    }
    .mateial_line{
        background-color:white;
        padding:10px;
        display: block;
        width:500px;
        border-bottom:1px solid #000;
    }
    .type_name {
        width:100px;
        display:-moz-inline-box;
        display:inline-block;
        padding-right:20px; }
    .type_money{ padding-left:30px;}
    .material_select{width: 200px;}
    .text_shot{
        width:50px;
    }
</style>
