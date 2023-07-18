<?php
/* @var $this ZhaopinController */
$this->pageTitle = '在线考试  - e代驾';

?>
<div class="block">
	<div style="height:90px;"></div>
	<div class="page-header">
	<h2>在线考试</h2>
	</div>
<div class="row">
    <div class="span6">
        <h4>
            您报名的身份证号码是：<?php echo $id_card;?>
        </h4>
        <p>请阅读以下考试须知后，点击开始考试按钮进行考试。</p>
        <p>1、本次考试共有考题20道，须全部答对后才算通过。</p>
        <p>2、考试结束后，系统会告诉您考试结果。答错的题会告诉您正确答案。</p>
        <p>3、如果考试没有通过，请继续学习培训资料，准备下次考试。</p>
        <p>4、考试通过后，e代驾公司择时给您发面试通知短信，请注意查收，耐心等待。</p>
        <p><input type="button" id="begin" value="开始考试"/></p>
    </div>
</div>
<form id="frm" action="/exam" method="post">
    <input type="hidden" name="id_cards" id="id_cards" value='<?php echo $id_card;?>' />
</form>
<script>
    var map = {0:'A',1:'B',2:'C',3:'D'};
    var id_card = '<?php echo $id_card;?>';
    var exam = <?php echo json_encode($exam_array);?>;
    var list = <?php echo json_encode($list);?>;
    var exam_count = <?php echo count($list);?>;
    var flag = 0;
    var answer_list = new Array();
    jQuery('document').ready(function(){
        jQuery('#begin').click(function(){
            getExam(flag);
        });
    });
    function getExam(flag) {
        var id = list[flag];
        var data = exam[id];
        var html = '<div class="row">'+
            '<div class="span9">'+
            '<h4>'+parseInt(flag+1)+'、'+data.title+'</h4>'+
            '</div>'+
            '</div>';
        var input_type = data.type;
        var type = 'checkbox';
        if (input_type == 0) {
            type = 'radio';
        }
        jQuery.each(data.contents, function(i,v){
            html += '<input id="q_'+i+'" type="'+type+'" name="'+data.id+'[]" value="'+map[i]+'">'+v+"<br>";
        });
        html += '<input type="button" id="'+data.id+'" answer="'+data.type+'" onclick="submitAnswer(this)" value="确定"/>';
        var o = jQuery(html);
        jQuery('.span6').html('').append(o);
    }

    function submitAnswer(obj) {
        var o = jQuery(obj);
        var id = o.attr('id');
        var input_name = id+'[]';
        var input_type = o.attr('answer');
        var select_option = jQuery('[name="'+input_name+'"]:checked').length;
        if (select_option == 0) {
            alert('请选择答案');
            return false;
        }
        answer_list[id] = new Array();
        jQuery('[name="'+input_name+'"]:checked').each(function(i,v){
                //answer_list[id].push(jQuery(this).val());
                var name = jQuery(this).attr('name');
                var input = '<input type="hidden" name="'+name+'" value="'+jQuery(this).val()+'"/>';
                jQuery('#frm').append(jQuery(input));
            }
        );
        flag++;
        if (flag>=exam_count) {
            jQuery('#frm').submit();
        } else {
            getExam(flag);
        }
    }

</script>
