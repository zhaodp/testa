<strong>当前物料状态</strong>
<div style="padding: 19px;" class="alert alert-success">

    <?php
    if(!empty($material2driver)){

        //print_r($material2driver);die;
        echo '<table id="mater_info"><tr><th>物料名</th><th>数量</th><th>领取时间</th><th>是否礼包</th><th>单价</th><th>状态</th>';
        if(isset($showcheckbox) && $showcheckbox){
            echo '<th class="checkb"><input type="checkbox" name="checkeckall" id="checkall" value="">全选</td>';
        }
        echo '</tr>';
        $s = '';
        foreach($material2driver as $v){
            if(isset($mater_info[$v->m_id]) && !empty($mater_info[$v->m_id])){
                $quantity = $v->quantity;
                $s = '<tr><td>'.$mater_info[$v->m_id]['name']
                    .'</td><td>'.$v->quantity
                    .'</td><td>'.$v->create_time
                    .'</td><td>'.Material2Driver::getIsGiftStatus($v->is_gift_bag)
                    .'</td><td>'.$mater_info[$v->m_id]['price']
                    .'</td><td>'.Material2Driver::getstatus($v->status).'</td>';
                if(isset($showcheckbox) && $showcheckbox ){
                    $desc = isset($desc) ? $desc : '';
                    if($v->status != Material2Driver::STATUS_RECYCLE){
                        $s.= '<td class="checkb"><input type="hidden" name="mater_type_id" id="type_id_'.$v->id.'" value="'.$v->type_id.'">
                        <input type="hidden" id="have2change'.$v->id.'" value="">
                        <input type="hidden" id="isgift'.$v->id.'" value="'.$v->is_gift_bag.'">
                        <input type="checkbox" name="driver_material['.$v->id.']" value="'.$v->id.'" >'.$desc.'</td>';
                    }else $s.='<td></td>';
                }

                $s.= '</tr>';
            }
            echo $s;

        }
        echo '</table>';
    }
    ?>
</div>

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