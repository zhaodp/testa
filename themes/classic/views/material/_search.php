<?php $this->renderPartial('tab',array('tab'=> 1));
?>
<div class="container">
    <div class="row-fluid">
        <form action="<?php echo Yii::app()->createUrl('material/index');?>" method="get">
            第一步：请输入司机工号，或报名号（报名号只输入后面数字部分）：
            <?php  echo CHtml::textField('id', $id,  array('style'=>'width:110px;','id'=>'search_id'));?>
            <br>
            第二步：点击选择以下相应操作。<br>
            <?php
            echo CHtml::link('签约发放', Yii::app()->createUrl('/material/index',array('type'=>'0')), array('class' => 'btn goclick','style'=>'padding:10px;margin:10px;'));
            echo CHtml::link('额外申领', Yii::app()->createUrl('/material/index',array('type'=>'2')), array('class' => 'btn goclick','style'=>'padding:10px;margin:10px;'));
            echo CHtml::link('解约回收', Yii::app()->createUrl('/material/index',array('type'=>'4')), array('class' => 'btn goclick','style'=>'padding:10px;margin:10px;'));
            echo CHtml::link('物料更换', Yii::app()->createUrl('/material/index',array('type'=>'1')), array('class' => 'btn goclick','style'=>'padding:10px;margin:10px;'));
            echo CHtml::link('补领/赠送', Yii::app()->createUrl('/material/index',array('type'=>'3')), array('class' => 'btn goclick','style'=>'padding:10px;margin:10px;'));
            echo CHtml::link('查询历史', Yii::app()->createUrl('/material/detail'), array('class' => 'btn goclick','style'=>'padding:10px;margin:10px;'));
            ?>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('.goclick').click(function(){

        var url = this.href;
        var id = $('#search_id').val();
        if(id){
            //var url_new = DeleteUrlParam(url,'id');
            //alert(url_new); return false;
            this.href = url + '&id='+id;
        }else {
            alert('请输入工号或报名号');
            return false;
        }
    });

    //删除Url参数值
//    function DeleteUrlParam(url,name) {
//        var reg = new RegExp("([&\?]?)" + name + "=[^&]+(&?)", "g")
//
//        var newUrl = url.replace(reg, function (a, b, c) {
//            if (c.length == 0) {
//                return '';
//            } else {
//                return b;
//            }
//        });
//
//        return newUrl;
//    }

</script>