<?php
Yii::app()->clientScript->registerScript('search', "
	$('.search-button').click(function(){
	    $('.search-form').toggle();
	    return false;
	    });
	$('#search-form').submit(function(){
	    $('#support-ticket-grid').yiiGridView('update', {
data: $(this).serialize()
});
	    return false;
	    });
	");
?>

<?php
$this->pageTitle = '每月销单、拒单排名前10%代驾分城市配置';
echo "<h1>".$this->pageTitle."</h1><br />";
?>

<div class="search-form" style="display:block">
<div class="well span12">
<?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl("driver/scoreCity"),'method' => 'post',)); ?>
    <div class="span12">

        <div>
            <?php echo "适用地区" ?>
            <input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;<input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选
            <br><br>
            <?php
            $citys = Common::getScoreOpenCitys();
            foreach ($citys as $key=>$item){
                $checked = in_array($key,$checkedCitys);
                echo CHtml::checkBox("city[]",$checked,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
            }

            ?>
        </div>
    <div>
<a class="btn btn-info" href="javascript:;" id="btn">确认提交</a>
</div>

</div>
<?php $this->endWidget(); ?>

</div>
</div>
</div>

<script>

$('#btn').click(function(){
	if($("input[name='city[]']:checked").size()==0){
	alert("请先勾取城市！");
	return;
	}
	$('#yw0').submit();
	});

$('#che_all').click(function(){
	if($(this).attr("checked")){

	$("input:enabled[name='city[]']").each(function(){
	    $(this).attr("checked","true");
	    $('#unche_all').removeAttr("checked");
	    });
	}//else{
	//       $("input[name='city[]']").each(function(){
	//            $(this).removeAttr("checked");
	//   });
	//   }
	});

$('#unche_all').click(function(){ 
	if($(this).attr("checked")){
	$("input:enabled[name='city[]']").each(function(){
	    if($(this).attr("checked")){
	    $(this).removeAttr("checked");
	    $('#che_all').removeAttr("checked");
	    }else{
	    $(this).attr("checked","true")
	    }
	    }); 
	}//else{
	//     $("input[name='city[]']").each(function(){
	//          $(this).removeAttr("checked");
	// }); 
	// }
	});

$('.city_id').click(function(){
	if(this.checked==false){
	$('#che_all').attr('checked',false);
	}else if($(".city_id:checked").size()==$('.city_id').length){
	$('#che_all').attr('checked',true);
	}
	});


</script>
