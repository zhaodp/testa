<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-11
 * Time: 下午5:57
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '司机路考后台设置';
?>
<legend>司机路考后台设置</legend>
<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#home">设置</a></li>
        <li class=""><a href="<?php echo Yii::app()->createUrl('recruitment/roadExamList'); ?>">展现</a></li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <!--司机路考设置-->
        <div id="home" class="tab-pane active">
            <form class="form-inline">
                <div class="row-fluid">
                    <div class="span3">
                        <label>路考自动档考官司机编号</label>
                        <input type="text" placeholder="输入司机工号"  class="driver_id">
                        <input type="button" value="确认" func="automatic" class="btn btn-success">
                    </div>
                    <div class="span3">
                        <label>路考手动档考官司机编号</label>
                        <input type="text" placeholder="输入司机工号" class="driver_id">
                        <input type="button" value="确认" func="manuax" class="btn btn-success">
                    </div>
                </div>
                <hr>
                <div class="row-fluid">
                    <strong>自动档考官</strong>
                    <div id="auto">

                    </div>
                </div>
                <hr>
                <div class="row-fluid">
                    <strong>手动档考官</strong>
                    <div id="man">

                    </div>
                </div>
            </form>
        </div>
        <!--司机路考信息查询-->
        <div id="profile" class="tab-pane fade">

        </div>
    </div>
</div>

<!--弹框-->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">考官资料</h3>
    </div>
    <div class="modal-body" id="modal-body">
        <p>努力加载中.....</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>

<!--模板-->
<script type="text/html" id="tmpl">
    <div class="row-fluid">
        <div class="span4">
            <label>路考自动档考官司机工号</label>
            <input type="text" placeholder="输入司机工号" >
            <input type="button" value="确认" class="btn btn-success">
        </div>
        <div class="span4">
            <label>路考手动档考官司机工号</label>
            <input type="text" placeholder="输入司机工号">
            <input type="button" value="确认" class="btn btn-success">
        </div>
    </div>
</script>

<script type="text/javascript">
    var data = <?php echo json_encode($today_examiner);?>;
    var type_automatic = <?php echo DriverRoadExam::TYPE_AUTO;?>;
    var type_manuax = <?php echo DriverRoadExam::TYPE_MAN;?>;
    var city_id = <?php  echo $user_city_id; ?>;
    var exam_date = '<?php echo $exam_date;?>';

    jQuery(document).ready(function(){
        examinerList(data);
        //增加自动档考官
        jQuery('[func="automatic"]').click(function(){
            if (!city_id) {
                alert('只有分公司司管可以设置');
                return false;
            }
            var driver_id = jQuery(this).prev('input').val();
            var exam_type = type_automatic;
            saveByAjax(exam_type, driver_id);
        });

        //增加手动档考官
        jQuery('[func="manuax"]').click(function(){
            if (!city_id) {
                alert('只有分公司司管可以设置');
                return false;
            }
            var driver_id = jQuery(this).prev('input').val();
            var exam_type = type_manuax;
            saveByAjax(exam_type, driver_id);
        });

        //保存考官信息
        function saveByAjax(exam_type, driver_id) {
            var action = 'set_road_examiner';
            if (arguments.length>=3 && arguments[2]=='del') {
                var action = 'del_road_examiner';
            }
            var post_data = {
                'exam_type' : exam_type,
                'exam_date' : exam_date,
                'city_id' : city_id,
                'driver_id' : driver_id,
                'action' : action
            };
            jQuery.post(
                '<?php echo Yii::app()->createUrl('/recruitment/ajax');?>',
                post_data,
                function(d) {
                    if (d.status) {
                        if (d.msg) {
                            examinerList(d.msg);
                        }
                    } else {
                        alert(d.msg);
                    }
                },
                'json'
            );
        }

        function examinerList(data) {
            var length = 0;
            for(i in data) {
                length++;
            }
            if (length > 0) {
                jQuery('#man').html('');
                jQuery('#auto').html('');
                jQuery.each(data, function(i, d){
                    jQuery.each(d, function(j,v) {
                        var html = '<div class="alert alert-success" style="width:100px; float:left; ">'+
                                        '<button func="del" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<a func="info" class="btn btn-success" data-target="" data-toggle="modal">'+v+'</a>'+
                                   '</div>';
                        var url = '<?php echo Yii::app()->createUrl('driver/view', array('dialog'=>1, 'grid_id'=>'driver-grid'));?>'+'&id='+v;
                        var o = jQuery(html);
                        o.css('margin-right', '15px');
                        o.find("[func='info']").bind('click', function(){
                            $('#myModal').modal('toggle').css({'width':'750px','margin-left': function () {return -($(this).width() / 2);}});
                            $('#myModal').modal('show');
                            $('#modal-body').load(url);
                        });
                        o.find("[func='del']").bind('click', function(){
                            if (i == 'automatic') {
                                saveByAjax(type_automatic, v, 'del');
                            } else {
                                saveByAjax(type_manuax, v, 'del');
                            }
                            return false;
                        });
                        if (i == 'manuax') {
                            jQuery('#man').append(o);
                        }
                        if (i == 'automatic') {
                            jQuery('#auto').append(o);
                        }
                    })
                })
            }
        }
    });

</script>
