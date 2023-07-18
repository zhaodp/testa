<style>
.areaR{
	float:left;
	margin-left:20px;
	width:700px;
}
#message{
	min-height:300px;
}
</style>
<div class="container">
	<div class="row">
		<div class="span3 bs-docs-sidebar">
			<ul class="nav nav-list bs-docs-sidenav affix">
			<?php 
			$i=0;
			$message = '';
			$title = '';
			foreach($model as $item){
				if($i==0){
					$message = $item->content;
					$title = $item->title;
				}
				$i++;
				echo '<li id='.$item->id.'><a href="javascript:show('.$item->id.')" title='.$item->title.'><i class="icon-chevron-right"></i> '.$item->title.'</a></li>';	
			}
			?>
			
			</ul>
		</div>
		<div class="areaR">
	<div style="height:67px;"></div>
	<section class="agreement" id="agreement">
		<div class="page-header">
			<h2><?php echo $title;?></h2>
		</div>
		<div style="margin-bottom:20px;" id="message"><?php echo $message;?></div>
	</section>
	
	
		
	</div>
</div>
<script>
$(function(){
	$(".span3").find("li").eq(0).addClass("active");
	//将收费标准放在第二位;
	var end_notice = '<li id=41>'+$("#41").html()+'</li>';
	$("#41").replaceWith('');
	$(".span3").find("li").eq(0).after(end_notice);
});
function show(select_id){
	//$(".span9 div").fadeOut(100,function(){
	//	$("$"+select_id).fadeIn("slow");
	//});
	$(".span3").find("li").removeClass("active");
	$("#"+select_id).addClass("active");
	title = $("#"+select_id).children("a").attr('title');
	$.ajax({
		  url: '<?php echo Yii::app()->createUrl('/recruitment/getnotice');?>',
		  data: 'id='+select_id,
		  cache: false,
		  beforeSend: function(){ 
			  $("#message").html("<center><img src=<?php echo SP_URL_IMG;?>loading.gif id='loadingimg' /></center>");
			},
		  success: function(html){
		   	$("#message").html(html);
		   	$("h2").html(title);
		  }
		});
}
</script>