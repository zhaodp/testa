/**
 * Created with JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 14-1-8
 * Time: 下午2:44
 * To change this template use File | Settings | File Templates.
 */

(function($){

    $.citypicker = function(input,options, callback){
        var input = $(input);
        var input_position = input.position();
        var input_left = input_position.left;
        var input_top = input_position.top;

        if (options.type == 'box') {
            var main_box = createBox();
        } else {
            var main_box = createModal();
        }

        jQuery('body').append(main_box);

        input.focus(function(){
            showBox(main_box);
        }).click(function(){
                showBox(main_box);
            }).blur(function(){
                //hideBox(main_box);
            });

        jQuery('#dropdown').click(function(){
            //jQuery('#yw0').click(function(){})
            input.focus();
        });

        function showBox(main_box) {
            if (options.type=='box') {
                main_box.show();
            } else {
                main_box.modal('show');
            }
        }

        function hideBox(main_box) {
            if (options.type=='box') {
                main_box.hide();
            } else {
                main_box.modal('hide');
            }
        }

        function createBox() {
            var main_box_html = createMainBox();
            var nav_tab_html = createNavTabs();
            var tab_content_html = createTabContent();

            var main_box = jQuery(main_box_html);
            var nav_tab = jQuery(nav_tab_html);
            var tab_content = jQuery(tab_content_html);

            //选完城市后执行的方法
            tab_content.find('.city').click(function(){
                var city_id = jQuery(this).attr('city_id');
                var city_name = jQuery(this).attr('city_name');
                input.val(city_name);
                var input_hidden = jQuery('[name="'+options.name+'"]');
                if (input_hidden.length>0) {
                    input_hidden.val(city_id);
                }
                if (typeof(callback) == 'function'){
                    callback(city_id, city_name);
                }
                main_box.hide(500);
            });

            main_box.find('.close').click(function(){
                main_box.hide(500);
            });

            nav_tab.eq(0).addClass('active');
            tab_content.eq(0).addClass('active');

            main_box.find('#myTab').append(nav_tab);
            main_box.find('#myTabContent').append(tab_content);

            var main_box_left = main_box.width()/2 + input_left;
            var main_box_top = input_top+input.height()+10;

            main_box.css({
                'top' : main_box_top+'px',
                'left' : main_box_left+'px',
                'z-index': '1',
                'width' : '420px'
            });
            return main_box;
        }

        //创建城市弹框
        function createMainBox() {
            var html = '<div class="modal" style="display: none;"><button class="close" type="button" style="padding-right: 10px; padding-top: 5px;">×</button><ul class="nav nav-tabs" id="myTab" ></ul><div class="tab-content" id="myTabContent"></div></div>';
            return html;
        }

        function createModal() {
            var main_modal_html = createMainModal();
            var nav_tab_html = createNavTabs();
            var tab_content_html = createTabContent();

            var main_box = jQuery(main_modal_html);
            var nav_tab = jQuery(nav_tab_html);
            var tab_content = jQuery(tab_content_html);

            nav_tab.eq(0).addClass('active');
            tab_content.eq(0).addClass('active');

            main_box.find('#myTab').append(nav_tab);
            main_box.find('#myTabContent').append(tab_content);

            //选完城市后执行的方法
            tab_content.find('.city').click(function(){
                var city_id = jQuery(this).attr('city_id');
                var city_name = jQuery(this).attr('city_name');
                input.val(city_name);
                var input_hidden = jQuery('[name="'+options.name+'"]');
                if (input_hidden.length>0) {
                    input_hidden.val(city_id);
                }
                if (typeof(callback) == 'function'){
                    callback(city_id, city_name);
                }
                main_box.modal('hide');
            });

            return main_box;
        }

        function createMainModal() {
            var modal = '<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal hide fade" id="myModal" style="display: none;">'+
                '<div class="modal-header" style="padding-bottom: 0px; padding-top: 0px;">'+
                    '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>'+
                    '<h4 id="myModalLabel">城市选择</h4>'+
                '</div>'+
                '<div class="modal-body">'+
                    '<ul class="nav nav-tabs" id="myTab" ></ul>'+
                    '<div class="tab-content" id="myTabContent"></div>'+
                '</div>'+
                '<div class="modal-footer" style="padding-top: 4px; padding-bottom: 4px;">'+
                    '<button data-dismiss="modal" class="btn">关闭</button>'+
                '</div>'+
            '</div>';
            return modal;
        }

        function createNavTabs() {
            var html = '';
            if (options.cityList) {
                jQuery.each(options.cityList, function(k, v) {
                    html += '<li class=""><a data-toggle="tab" href="#'+k+'">'+k+'</a></li>';
                })
            }
            return html;
        }

        function createTabContent() {
            var html = '';
            if (options.cityList) {

                jQuery.each(options.cityList, function(k, v) {
                    html += '<div id="'+k+'" class="tab-pane"><table>';
                    var n = 1;
                    var length = v.length;
                    jQuery.each(v, function(i,c){
                        if (n%6 == 1) {
                            html += '<tr>';
                        }
                        html += '<td style="width:60px; padding-bottom: 10px;" class="text-center"><a class="city" href="javascript:void(0)" city_id="'+i+'" city_name="'+c+'">'+c+'</a></td>';
                        if (n%6 == 0 || n==length) {
                            html += '</tr>';
                        }
                        n++;
                    });
                    html += '</table></div>';
                });
            }
            return html;
        }
    }

    $.fn.citypicker = function(options, callback){
        this.each(function(){
            new $.citypicker(this,options, callback);
        });
        return this;
    };
})(jQuery);

