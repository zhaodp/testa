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
    $this->renderPartial('driver_material',array('material2driver'=>$material2driver,'mater_info'=>$mater_info,'showcheckbox'=>1,'desc'=>'归还'));


    ?>



    <div style="padding: 19px;" class="alert alert-success" use="form" id="form_0">

        <div class="left_div">
            <strong>司机申领</strong>

            <div class="mateial_line"> <span class="type_name">类型</span><span class="material_select material_title" >物料选择</span><span class="quantity">数量</span><span class="quantity">未归还赔偿</span></div>
            <div id="container_mater">

            </div>
                <div>注：<br> 1) 如果更换的物料原本是礼包，那么替换的物料也是礼包，如果不是礼包物料，那么更换后也不是礼包物料<br>2)请提示师傅更换后的物料如果未归还需要按未归还赔偿金额从押金中扣除。</div>
        </div>

    </div>
    <div class="buttons"><?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?></div>


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
        width:550px;
        border-bottom:1px solid #000;
    }
    .type_name {
        width:100px;
        display:-moz-inline-box;
        display:inline-block;
        padding-right:20px; }
    .material_select{width: 200px;}
    .material_title{
        width:150px;
        display:-moz-inline-box;
        display:inline-block;
    }
    .quantity{ padding-left:40px; width:100px;}
</style>
<script>
    <?php if(isset($price_arr) && is_array($price_arr)){
        foreach($price_arr as $id => $price){
            echo "var price_{$id}={$price};\n";
        }
    }?>
    var count_pub = 1;


    $('#material').submit(function(){
        var check = false;
        $("input[name^='driver_material']").each(function(){
            if(this.checked == true){
                check = true;
            }
        });
        if(!check){
            alert('内容不能为空，请注意。');return false;
        }
        var submit = confirm('您确定所填写数据无误并且提交保存么?');
        if(submit == false) return false;

    });


    $('input[name^="driver_material"]').click(function (){
        var checked = this.checked;
        var id = this.value;
        var type_id = $('#type_id_'+id).val();
        var gift_type = $('#isgift'+ id).val();
        //console.log(type_id);return false;
        if(checked == false){
            var relase_id = $('#have2change' + id).val();
            $('#linemater'+ relase_id).remove();
        }else{
            add_line(type_id,id,gift_type);
        }

    });
    $('#checkall').click(function(){
        //alert('aaaa');return false;
        $('input[name^="driver_material"]').attr("checked",this.checked);
        $('input[name^="driver_material"]').each(function(){
            var checked = this.checked;
            var id = this.value;
            var type_id = $('#type_id_'+id).val();
            var gift_type = $('#isgift'+ id).val();
            //console.log(type_id);return false;
            if(checked == false){
                var relase_id = $('#have2change' + id).val();
                $('#linemater'+ relase_id).remove();
            }else{
                add_line(type_id,id,gift_type);
            }
        });

    });

    function add_line(type_id,mid,gift_type){
        var count = $('#container_mater').children('div').length;
        if(count == 0) {
            count = count_pub;
        }
        else {
            count = count_pub;
        }
        count_pub ++;
        $.ajax({
            type: 'get',
            url: '<?php echo Yii::app()->createUrl('/material/getMaterHtml');?>',
            data:  {
                'type_id':type_id ,
                'count':count,
                'isgift':gift_type
            },
            dataType : 'html',
            success: function(data){
                //console.log(data);

                //var datas = data.replace(new RegExp(/(RRR)/g),count);
                $('#container_mater').append(data);
                $('#have2change'+ mid).val(count);

            }});
    }

    function m_change(id,obj){
        var wuliaoname = obj.value;
        var price = eval("price_"+wuliaoname);
        $('#priceee'+ id).html(price);
    }

    function getId(name_str){
        var pattern =new RegExp("\\[(.*?)\\]");
        var st = name_str.match(pattern);
        return st[1];
    }
</script>