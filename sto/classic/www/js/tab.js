// JavaScript Document
$(document).ready(function(){
	$(".current").css("opacity", "1.0");
    setInterval("slideSwitch()", 3000);			   
});
function slideSwitch(){
	 var $current = $("#slideshow2 li.current");
    var $next = $current.next().length ? $current.next() : $("#slideshow2 li:first");
    $current.fadeOut();
	
    $next.css({
        opacity: 0.0,display:'block'
    }).addClass("current").animate({
        opacity: 1.0
    }, 2000, function () {
        $current.removeClass("current prev");
    });
}
function city(){
	$(".ddl").slideToggle();
}
function city_sel(id,id_q){
	$("#city").html($("#city_list_"+id_q).html());
	city();
	$(".date_region dl").css({display:'none'});
	$(".date_region dl#time"+id).css({display:'block'});
	$(".date_region pre").css({display:'none'});
	$(".date_region pre#beizhu"+id).css({display:'block'});
}
function switched(id){
	$(".c_content_l dl").css({display:'none'});
	$(".c_content_l dl#hz"+id).css({display:'block'});
	$(".c_content_r a").removeClass('actives');
	$(".c_content_r a#fh_"+id).addClass("actives");
}