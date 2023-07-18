<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 14-2-21
 * Time: 下午1:35
 * To change this template use File | Settings | File Templates.
 */
$baseScriptUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview';
$cs=Yii::app()->clientScript;
$cs->registerScriptFile($baseScriptUrl.'/jquery.yiigridview.js');
//$cs->registerScriptFile(SP_URL_STO.'www/js/swfobject.js');

$cs=Yii::app()->clientScript;
$fancybox_js_path = SP_URL_STO.'www/js/fancybox';

//<!-- Add jQuery library -->
Yii::app()->clientScript->registerCoreScript('jquery');

//<!-- Add mousewheel plugin (this is optional) -->
$cs->registerScriptFile($fancybox_js_path.'/lib/jquery.mousewheel.pack.js?v=3.1.3');

//<!-- Add fancyBox main JS and CSS files -->
$cs->registerScriptFile($fancybox_js_path.'/source/jquery.fancybox.pack.js?v=2.1.5');
$cs->registerCssFile($fancybox_js_path.'/source/jquery.fancybox.css?v=2.1.5');

//<!-- Add Button helper (this is optional) -->
$cs->registerCssFile($fancybox_js_path.'/source/helpers/jquery.fancybox-buttons.css?v=1.0.5');
$cs->registerScriptFile($fancybox_js_path.'/source/helpers/jquery.fancybox-buttons.js?v=1.0.5');

//<!-- Add Thumbnail helper (this is optional) -->
$cs->registerCssFile($fancybox_js_path.'/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7');
$cs->registerScriptFile($fancybox_js_path.'/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7');

//<!-- Add Media helper (this is optional) -->
$cs->registerScriptFile($fancybox_js_path.'/source/helpers/jquery.fancybox-media.js?v=1.0.6');
?>
<div class="container">
    <div class="row-fluid">
        <div class="span2"><strong style="font-size: 24px;">审核列表</strong></div>
        <div class="span3 offset1">
        </div>
    </div>
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'driver-grid',
        'dataProvider'=>$dataProvider,
        'ajaxUpdate' => 'true',
        'columns'=>array(
            array(
                'name'=>'城市',
                'value'=>'Dict::item("city", $data->city_id);'
            ),

            array(
                'name'=>'月份',
                'value'=>'$data->use_date'
            ),

            array(
                'name'=>'商家名称',
                'value'=>'$data->name'
            ),

            array(
                'name'=>'上传时间',
                'value'=>'$data->created',
            ),

            array(
                'name' => '状态',
                'value' => 'CompanyBusinessInfo::$status_dict[$data->status]'
            ),

            array(
                'header' => '操作',
                'class'=>'CButtonColumn',
                'htmlOptions' => array(
                    'width' => '180px',
                ),
                'template'=>'{archives}',
                'buttons'=>array(
                    'archives'=>array(
                        'label'=>'查看审核',
                        'url' => 'Yii::app()->createUrl("companyKpi/businessView", array("id"=>$data->id, "dialog"=> true, "code"=>md5($data->id.$data->status."edj")))',
                        'options' => array('func'=>'review', 'class'=>'btn'),
                    ),
                ),
            )
        )
    ));
    ?>
</div>

<script type="text/html" id="modal_module">
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">审核</h3>
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        <button class="btn btn-primary" func="pass">通过审核</button>
    </div>
</div>
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){

        var modal_css = {
            height : window.screen.availHeight,
            width : window.screen.availWidth,
            top : 0,
            margin : 0,
            left : 0
        };


        jQuery('[func="review"]').live('click', function(){
            var f = function() {jQuery('#driver-grid').yiiGridView('update');};
            var url = jQuery(this).attr('href');
            var Request = new Object();
            Request = GetRequest(url);
            var id = Request['id'];
            jQuery.get(
                url,
                function(d) {
                    var model_html = jQuery('#modal_module').html();
                    var module = jQuery(model_html);
                    module.attr('id', 'mid_'+id);
                    module.find('.modal-body').html(d);
                    module.css(modal_css);
                    module.find('[func="pass"]').click(function(){
                        review(id, function(){
                            module.modal('hide');
                            $('#driver-grid').yiiGridView('update');
                        })
                    });
                    module.modal('show');

                }
            );
            return false;
        });

    });



    function GetRequest(url) {
        //var url = location.search; //获取url中"?"符后的字串
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
        }
        return theRequest;
    }


    function review(id, call_back_func) {
        if (confirm('审核通过?')) {
            var url = '<?php echo Yii::app()->createUrl("companyKpi/businessAjax");?>';
            var status = <?php echo CompanyBusinessInfo::STATUS_REVIEW;?>;
            var id = id;
            jQuery.get(
                url,
                {
                    'id' : id,
                    'status' : status,
                    'act' : 'change_status'
                },
                function(d) {
                    if (d.status) {
                        if (typeof(call_back_func) == 'function') {
                            call_back_func();
                        }
                    }
                },
                'json'
            );
        }

    }

</script>
