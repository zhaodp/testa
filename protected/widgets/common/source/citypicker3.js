(function ($) {

    $.citypicker3 = function (input, options, callback) {
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

        input.focus(function () {
            showBox(main_box);
        }).click(function () {
            showBox(main_box);
        }).blur(function () {
            //hideBox(main_box);
        });

        jQuery('#dropdown').click(function () {
            //jQuery('#yw0').click(function(){})
            input.focus();
        });

        function showBox(main_box) {
            if (options.type == 'box') {
                main_box.show();
            } else {
                main_box.modal('show');
            }
        }

        function hideBox(main_box) {
            if (options.type == 'box') {
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
            tab_content.find('.city').click(function () {
                var city_id = jQuery(this).attr('city_id');
                var city_name = jQuery(this).attr('city_name');
                input.val(city_name);
                var input_hidden = jQuery('[name="' + options.name + '"]');
                if (input_hidden.length > 0) {
                    input_hidden.val(city_id);
                }
                if (typeof(callback) == 'function') {
                    callback(city_id, city_name);
                }
                main_box.hide(500);
            });

            main_box.find('.close').click(function () {
                main_box.hide(500);
            });

            nav_tab.eq(0).addClass('active');
            tab_content.eq(0).addClass('active');

            main_box.find('#myTab').append(nav_tab);
            main_box.find('#myTabContent').append(tab_content);

            var main_box_left = main_box.width() / 2 + input_left;
            var main_box_top = input_top + input.height() + 10;

            main_box.css({
                'top': main_box_top + 'px',
                'left': main_box_left + 'px',
                'z-index': '1',
                'width': '420px'
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
            tab_content.find('.city').click(function () {
                selectCity(this);
            });

            return main_box;
        }

        function selectCity(city) {
            var city_id = jQuery(city).attr('city_id');
            var city_name = jQuery(city).attr('city_name');
            input.val(city_name);
            var input_hidden = jQuery('[name="' + options.name + '"]');
            if (input_hidden.length > 0) {
                input_hidden.val(city_id);
                try{
                    input_hidden.change();
                } catch(error){
                    console.log(error)
                }


            }
            if (typeof(callback) == 'function') {
                callback(city_id, city_name);
            }
            main_box.modal('hide');
        }

        function createMainModal() {
            var modal = '<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal hide fade" id="myModal" style="display: none;">' +
                '<div class="modal-header" style="padding-bottom: 0px; padding-top: 0px;">' +
                '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>' +
                '<h4 id="myModalLabel">城市选择</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                '<span style="display: block;">搜索城市：<input type="text" value="" id="city_search"></span>' +
                '<ul class="nav nav-tabs" id="myTab" ></ul>' +
                '<div class="tab-content" id="myTabContent"></div>' +
                '</div>' +
                '<div class="modal-footer" style="padding-top: 4px; padding-bottom: 4px;">' +
                '<button data-dismiss="modal" class="btn">关闭</button>' +
                '</div>' +
                '</div>';
            return modal;
        }

        function createNavTabs() {
            var html = '';
            if (options.cityList) {
                jQuery.each(options.cityList, function (k, v) {
                    html += '<li><a data-toggle="tab" href="#' + k + '">' + k + '</a></li>';
                });
            }
            return html;
        }

        function createTabContent() {
            var html = '';
            if (options.cityList) {
                jQuery.each(options.cityList, function (k, v) {
                    html += '<div id="' + k + '" class="tab-pane"><table>';
                    jQuery.each(v, function (p, q) {
                        html += createTr(p, q);
                    });
                    html += '</table></div>';
                });
            }

            return html;
        }

        function createTr(key, data) {
            var str_tr = '';
            var length = data.length;
            str_tr += '<tr>';
            str_tr += '<td>' + key + '</td>';
            str_tr += '<td style="padding: 10px;line-height: 24px;">';
            var n = 1;
            jQuery.each(data, function (i, c) {
                var br = '';
                if (n != 0 && n % 6 == 0) {
                    br += '<br>';
                }
                str_tr += '<div style="width: 60px;display: inline;margin: 10px;"><a class="city" style="margin-right: 4px;" href="javascript:void(0)" city_id="' + i + '" city_name="' + c.city_name + '">' + c.city_name + '</a></div>' + br;
                n++;
            });
            str_tr += '</td>';
            str_tr += '</tr>';
            return str_tr;
        }

        function filterStr(str) {
            var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？%+_]");
            var specialStr = "";
            for (var i = 0; i < str.length; i++) {
                specialStr += str.substr(i, 1).replace(pattern, '');
            }
            return specialStr;
        }

        function filterCityInSearchTab(input) {
            var key_word = $(input).val();
            var res = searchData(key_word);
            putSearchResultToHtml(res);
        }

        function getSearchCityListEnglish(char) {
            var firstChar = char.substr(0, 1).toUpperCase();
            char = char.toLowerCase();
            var res = new Object();
            jQuery.each(options.cityList, function (k, v) {
                jQuery.each(v, function (k2, v2) {
                    if (k2 == firstChar) {
                        jQuery.each(v2, function (k3, v3) {
                            if (v3.pin_yin.indexOf(char) != -1) {
                                res[k3] = v3;
                            }
                        });
                    }
                });
            });
            return res;
        }

        function getSearchCityListChinese(char) {
            var res = new Object();
            jQuery.each(options.cityList, function (k, v) {
                jQuery.each(v, function (k2, v2) {
                    jQuery.each(v2, function (k3, v3) {
                        if (v3.city_name.indexOf(char) != -1) {
                            res[k3] = v3;
                        }
                    });
                });
            });
            return res;
        }

        function putSearchResultToHtml(data) {
            var html = '';
            var k = '';
            html += '<table>';
            html += createTr(k, data);
            html += '</table>';
            $('#搜索').html(html);
        }

        function searchData(strSearch) {
            if(strSearch.length==0){
                return '';
            }
            strSearch = filterStr(strSearch);
            var p = /[a-z]/i;
            var zimu_search = p.test(strSearch);
            if (zimu_search) {//字母搜索
                return getSearchCityListEnglish(strSearch);

            } else {//汉字搜索
                return getSearchCityListChinese(strSearch);

            }
        }

        function toggleSearch() {
            $('ul#myTab li').each(function () {
                var href = $(this).find('a').attr('href');
                if (href == '#搜索') {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
            $('#myTabContent .tab-pane').each(function () {
                var id = $(this).attr('id');
                if (id == '搜索') {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
            $('#搜索').find('.city').click(function () {
                selectCity(this);
            });

        }

        $('#city_search').keyup(function () {
            filterCityInSearchTab(this);
            toggleSearch();
        });

    };

    $.fn.citypicker3 = function (options, callback) {
        this.each(function () {
            new $.citypicker3(this, options, callback);
        });
        return this;
    };
})(jQuery);

