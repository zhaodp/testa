<?php
/**
 * Created by PhpStorm.
 * User: xujiandong
 * Date: 2015/12/28
 * Time: 17:18
 */
$html = <<<EOD
<tr><td><input name="key" type="text" maxlength="32"></td><td><input name="value" type="text" maxlength="32"></td><td algin="center"><a href="javascript:0"><i class="icon-trash" style="vertical-align: bottom"></i></a></td></tr>
EOD;

Yii::app()->clientScript->registerScript('validate', "
    function trim(str){ //删除左右两端的空格
　　     return str.replace(/(^\s*)|(\s*$)/g, \"\");
　　 }

    $('i.icon-trash').click(function(){
        //删除key、value
        $(this).parents('tr').remove();
    });

    $('.icon-plus').click(function(){
        //增加新的key、value
         var values = $('input[name=\"value\"]');
        var keys = $('input[name=\"key\"]');
        if( keys.length != values.length ){
            alert('参数不匹配');
            return;
        }
        for(var i=0; i<keys.length; i++){
            var key = trim($(keys[i]).val());
            var value = trim($(values[i]).val());
            if( key=='' || value=='' ){
                alert('存在内容为空的输入框');
                return;
            }
        }
        $(this).parents('tr').before('".$html."');
        doc = window.parent.document.getElementById('cru-frame').contentDocument;
        $('i.icon-trash',doc).click(function(){
            //删除key、value
            $(this).parents('tr').remove();
        });
    });

    //预览
    $('#pre').click(function() {
        var inputs = $('input[name=\"value\"]');
        var tr = $('.table-bordered');
        if( inputs.length > 0 ){
            $(tr).removeClass('hidden');
        }else{
            $(tr).addClass('hidden');
        }
        $(tr).find('tr').html('');
        for(var i = 0; i < inputs.length; i++ ){
            tr.find('tr').append('<td>'+$(inputs[i]).val()+'</td>');
        }
    });

    $('#submit').click(function(){
        var params = {};
        var values = $('input[name=\"value\"]');
        var keys = $('input[name=\"key\"]');
        var totalAmount = $('#totalAmount').val();
        var flag = $.trim(totalAmount)==''?true:false;
        if( keys.length != values.length ){
            alert('参数不匹配');
            return;
        }
        for(var i=0; i<keys.length; i++){
            var key = trim($(keys[i]).val());
            var value = trim($(values[i]).val());
            if( key==totalAmount ){
                flag = true;
            }
            if( key=='' || value=='' ){
                alert('存在内容为空的输入框');
                return;
            }
            if( key in params ){
                alert('不能存在相同的key');
                return;
            }
            params[key] = value;
            if( trim($('#auditor').val())=='' ){
            alert('审核人不能为空');
            return;
            }
        }
        if( !flag ){
            alert('审核金额字段不在key列表内');
            return;
        }
        $.ajax({
            'type':'get',
            'url':'".Yii::app()->createUrl('/adminuserNew/actioneditaudit')."',
            'data':{'auditor':$('#auditor').val(),'action_id':$('#id').val(),'totalAmount':totalAmount,'params':JSON.stringify(params)},
            'success':function(data){
                var json = eval('('+data+')');
                if( json.code==0 ){
                    alert('保存成功');
                    window.parent.location.reload();
                }else{
                    alert(json.mes);
                }
            },
            'error':function(){
                alert('系统错误');
            }
        });
    });
    $('#delete').click(function(){
        if( confirm('确定删除？') ){
            $.ajax({
                'type':'get',
                'url':'".Yii::app()->createUrl('/adminuserNew/actiondeleteaudit')."',
                'data':{'action_id':$('#id').val()},
                'success':function(data){
                    var json = eval('('+data+')');
                    if( json.code==0 ){
                        alert('删除成功');
                        window.parent.location.reload();
                    }else{
                        alert('删除失败');
                    }
                },
                'error':function(){
                    alert('系统错误');
                }
            });
        }
    });
    ");
?>
<div class="form">
    <input type="text" style="display:none" id='id' value="<?php echo $id?>">
    <?php $form=$this->beginWidget('CActiveForm',array(
        'id'=>'audit-action-form',
        'enableAjaxValidation'=>false,
));?>
    <?php
        echo '<table class="table table-hover table-striped">
                  <tr>
                      <th style="text-align: center" >Key</th><th style="text-align: center" >Value</th><th style="text-align: center" ></th>
                  </tr>
                ';
        if( $model && isset($model['params']) ){
            $audits = isset($model['params']) ? json_decode($model['params']) : array();
            foreach( $audits as $key=>$value )
            {
                echo '<tr>
                          <td><input name="key" type="text" maxlength="32" value="'.$key.'"></td>
                          <td><input name="value" type="text" maxlength="32" value="'.$value.'"></td>
                          <td algin="center"><a href="javascript:0"><i class="icon-trash" style="vertical-align: bottom"></i></a></td>
                      </tr>';
            }
        }
        echo '<tr>
                  <td></td>
                  <td></td>
                  <td vertical-align="middle"><a href="javascript:0"><i class="icon-plus"></i></a></td>
              </tr>
              ';

        echo '</table>';
        echo '<div class="span3" style="width: 90%">
                  <a4>审核人：</a4><input id="auditor" type="text" maxlength="32" value="'.$auditorStr.'">
              </div>
              <div>
                  <a4>审核金额字段：</a4><input id="totalAmount" type="text" maxlength="32" value="'.$totalAmount.'" placeholder="非必填">
              </div>';
    ?>
       
        <button type="button" id="submit" class="btn" style="vertical-align:top" >保存</button>
        <button type="button" id="pre" class="btn" style="vertical-align:top" >预览</button>
        <button type="button" id="delete" class="btn btn-danger" style="vertical-align:top">删除配置</button>
    <hr class="bs-docs-separator">
    <table class="table table-bordered hidden" >
        <tr></tr>
    </table>
    <?php $this->endWidget(); ?>
</div>
