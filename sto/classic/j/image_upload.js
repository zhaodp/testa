/**
 * Created with JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 14-2-17
 * Time: 下午5:23
 * To change this template use File | Settings | File Templates.
 */

/**
 * 参数列表
 *  1、 图片上传URL
 *  2、 图片上传目录
 *  3、 回调函数
 *  这个JS还会涉及一个跨域的问题，应该设置一下domain。这里就不做处理了，以后有需求再改吧。
 */
(function($){
    $.imageUpload = function(input, options, callback){
        var input = $(input);
        var image_upload_url = '/v2/index.php?r=image/imgupload';
        var base_path = options.base_path;
        var box_html = createUploadBox();
        var box = jQuery(box_html);

        //图片上传按钮点击时触发
        input.click(function(){
            initUploadBox(base_path, callback);
        });


        function createUploadBox() {
            var box = '<div style="display: none;" id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'+
                '<div class="modal-header">'+
                    '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>'+
                    '<h3 id="myModalLabel">图片上传</h3>'+
                '</div>'+
                '<div class="modal-body">'+
                '<form target="yframe" method="post" enctype="multipart/form-data" >'+
                    '<input type="file" name="upload" size="38">'+
                    '<input type="submit" value="上传" class="btn" />'+
                '</form>'+
                '</div>'+
                '<div class="modal-footer">'+
                    '<button data-dismiss="modal" class="btn">关闭</button>'+
                '</div>'+
            '</div>';
            return box;
        }

        function initUploadBox(base_path, call_back_fun) {
            if (jQuery('#yframe').length == 0) {
                var iframe = '<iframe style="display:none; border:none;" src="" name="yframe" id="yframe"></iframe>';
                jQuery('body').append(iframe);
            }
            //一些乱七八糟的参数，不知道为啥设置这些还必须GET。问葫芦娃吧！
            var parameter = {
                "base_path" : base_path,
                "CKEditor" : "KnowledgeData_content",
                "CKEditorFuncNum" : "1",
                "langCode" : "zh-cn",
                "call_back_self" : "1",
                "call_back_fun" : call_back_fun,
                "type" : "img"
            };

            jQuery.each(parameter, function(i,v){
                image_upload_url += '&'+i+'='+v;
            });
            box.find('form').attr('action', image_upload_url);
            box.find('[name="base_path"]').val(base_path);
            box.find('[name="call_back_fun"]').val(call_back_fun);
            box.modal('show');
        }

    }

    $.fn.imageUpload = function(options, callback){
        this.each(function(){
            new $.imageUpload(this,options, callback);
        });
        return this;
    };
})(jQuery);
