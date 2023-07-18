/**
 *
 * @authors 张文双 (zhangwenshuang@edaijia-in.cn)
 * @date    2015-06-25 10:15:59
 * @description 侧边栏浮动导航
 * @version 1.0
 */
var __FILE__ = $("script").last().attr("src");
var NavHost = __FILE__.replace('/sto/classic/menu/fnav.js', '');
;
(function() {
    //读取cookie
    function getCookie(f) {
        var e = document.cookie.split("; ");
        for (var g = 0; g < e.length; g++) {
            var h = e[g].split("=");
            if (h[0] == f) {
                return unescape(h[1]);
            }
        }
    }
    var exp = expLogo = "";
    var canopennav = getCookie("is-v2_open_nav");
    if(canopennav == "true"){
        exp = "edj-v2-fnav-box-exp";
        expLogo = "edj-v2logo-exp";
        $("body").css("paddingLeft", "200px");
    }
    $("body").append("<div id='edj-v2-hnav' class='edj-v2-hnav'><a class='edj-v2-logo "+expLogo+"' href='javascript:void(0)' id='nav_ctrl'></a><form id='edj_search_form' title='可输入订单号、手机号、司机工号' target='_blank' action='http://www.edaijia.cn/v2/index.php?r=system/search' method='get'><input type='hidden' value='system/search' name='r'><div><div class='icon_search' id='edj_v2_submit'></div><input id='edj-v2-search' name='q' placeholder='司机/订单/客户/用户ID'/></div></form></div><div id='edj-v2-fnav-box' class='edj-v2-fnav-box "+exp+"'><ul class='edj-v2-fnavs'></ul><div id='edj-v2-fnav-switch' class='edj-v2-fnav-switch'><div class='edj-v2-fnav-btn'></div></div></div>");
    var FNav = function(opts) {
        this.opts = opts || {};
        this.init();
    };
    FNav.prototype = {
        defaults: {
            rootLen: 0,
            cIndex: 0,
            path: "/",
            domain: "." + document.domain.split(".").slice(-2).join(".")
        }, //参数配置
        labelId: "",
        canOpenNav: false,
        isModle:"pc",
        logoLink:NavHost,
        init: function() {
            var that = this;
            window.defaultsCountIndex = 0;
            if(that.isMobile()){
                that.isModle = "mobile";
            }
            that.render();
            that.bindEvts();
        },
        render: function() {
            var that = this,
                listData = that.opts.NLData || [],
                listHtml = "";
            if (listData.length > 0) {
                listHtml = that.getNavHtml(listData);
                $("body").css({
                    "paddingTop": "50px"
                });
                $("#edj-v2-fnav-box > ul").html(listHtml);
                that.labelId = that.cookie.get("labelId");
                if (that.labelId && that.labelId.length > 0) {

                    that.canOpenNav = that.cookie.get("is-v2_open_nav");

                    var btn = $("#edj-v2-fnav-switch").find(".edj-v2-fnav-btn"),
                        treat = $("#edj-v2-fnav-box"),
                        cli = $(".edj-v2-fnavs > li div"),
                        ev2hna = $("#edj-v2-hnav");

                    treat.addClass("edj-v2-fnav-box-exp");
                    btn.addClass("edj-v2-fnav-active");
                    treat.find(".edj-v2-fnavs .edj-v2-tree-title").css("display", "inline-block");

                    cli.find("span.right-arr").show();

                    ev2hna.find(".edj-v2-logo").addClass("edj-v2logo-exp");

                    var atvli = $("#edj-v2-fnav-box li[data-level='" + that.labelId + "']"),
                        lblArr = that.labelId.split("."),
                        dep = lblArr.length,
                        curLbl = -1,
                        pli = null;
                    if (atvli.hasClass("tree-branch")) {
                        atvli.addClass("tree-branch-show");
                        atvli.children("div").children("span.right-arr").addClass("active-arr");
                        atvli.children("ul").show();
                    } else {
                        atvli.addClass("active-li");
                    }
                    for (var i = dep; i > 0; i--) {
                        curLbl = lblArr.slice(0, i).join(".");
                        pli = atvli.parents("li[data-level='" + curLbl + "']");
                        pli.addClass("tree-branch-show");
                        pli.children("div").children("span.right-arr").addClass("active-arr");
                        pli.children("ul").show();
                    }

                    if (that.canOpenNav == "true") {
                        $("body").css("paddingLeft", "200px");
                    } else {
                        treat.removeClass("edj-v2-fnav-box-exp");
                        btn.removeClass("edj-v2-fnav-active");
                        treat.find(".edj-v2-fnavs .edj-v2-tree-title").css("display", "none");

                        $(".edj-v2-fnavs > li.tree-branch-show").children("ul").hide();
                        cli.find("span.right-arr").removeClass("active-arr").hide();

                        ev2hna.find(".edj-v2-logo").removeClass("edj-v2logo-exp");
                        $("body").css("paddingLeft", "56px");
                    }

                } else {
                    $("body").css("paddingLeft", "56px");
                }
                if(that.isModle === "mobile"){
                    $("body").css("paddingLeft", 0);
                    $("#edj-v2-fnav-box").hide().removeClass("edj-v2-fnav-box-exp");
                    $("#nav_ctrl").removeClass("edj-v2logo-exp");
                }
            }
        },
        cookie: {
            set: function(l, j, opts) {
                opts = opts || {};
                var k = l + "=" + escape(j);
                var h = new Date(),
                    i = 0,
                    g = opts.g,
                    path = opts.path,
                    domain = opts.domain;
                if (g && g > 0) {
                    i = g * 3600 * 1000;
                    h.setTime(h.getTime() + i);
                    k += "; expires=" + h.toGMTString();
                } else {
                    k += "; expires=Session";
                }
                path ? k += ";path=" + path : "";
                domain ? k += ";domain=" + domain : "";
                document.cookie = k;
            },
            get: function(f) {
                var e = document.cookie.split("; ");
                for (var g = 0; g < e.length; g++) {
                    var h = e[g].split("=");
                    if (h[0] == f) {
                        return unescape(h[1]);
                    }
                }
            },
            del: function(c) {
                var d = new Date();
                d.setTime(d.getTime() - 10000);
                document.cookie = c + "=a; expires=" + d.toGMTString();
            }
        },
        isMobile:function(){
            var ua = navigator.userAgent.toLowerCase();
            if(/(iphone|ipod)/.test(ua) || /(android)/.test(ua)){
                return true;
            }
            return false;
        },
        getNavHtml: function(lstData) {
            var that = this,
                lstData = lstData || [],
                resHtml = "",
                item = {},
                isRootClass = that.defaults && that.defaults.cIndex == 0 ? " root-branch" : "";
            for (var i = 0, len = lstData.length; i < len; i++) {
                item = lstData[i];
                if (item.hasSub) {
                    if (item.className) {
                        resHtml += "<li class='tree-branch " + isRootClass + "' data-level='" + item.labelId + "'><div alt='" + item.label + "' title='" + item.label + "'><span class='edj-v2-ico-nav " + item.className + "'></span><span class='edj-v2-tree-title'>" + item.label + "</span><span class='right-arr'></span></div><ul>" + arguments.callee(item.navList) + "</ul></li>";
                    } else {
                        resHtml += "<li class='tree-branch " + isRootClass + "' data-level='" + item.labelId + "'><div alt='" + item.label + "' title='" + item.label + "'><span class='edj-v2-tree-title'>" + item.label + "</span><span class='right-arr'></span></div><ul>" + arguments.callee(item.navList) + "</ul></li>";
                    }
                } else {
                    if (item.is_target && item.is_target > 0) {
                        resHtml += "<li class='tree-leaf' alt='" + item.label + "' title='" + item.label + "' data-level='" + item.labelId + "'><a target='_blank' href='" + item.link + "'>" + item.label + "</a></li>"
                    } else {
                        resHtml += "<li class='tree-leaf' alt='" + item.label + "' title='" + item.label + "' data-level='" + item.labelId + "'><a href='" + item.link + "'>" + item.label + "</a></li>"
                    }
                }
            }
            that.defaults && that.defaults.cIndex++;
            return resHtml;
        },
        bindEvts: function() {
            var that = this;
            $("body").delegate("#edj-v2-search", "focus", function(){
                $(this).removeAttr("placeholder");
            }).delegate("#edj-v2-search","blur", function(){
                var self_tar = $(this);
                if($.trim(self_tar.val()).length <= 0){
                    self_tar.attr("placeholder","司机/订单/客户/用户ID");
                }
            }).delegate("#edj-v2-fnav-switch", "click", function() {
                var btn = $(this).find(".edj-v2-fnav-btn");
                var treat = $("#edj-v2-fnav-box");
                var cli = $(".edj-v2-fnavs > li div"),
                    atvli = $(".edj-v2-fnavs > li.tree-branch-show"),
                    ev2hna = $("#edj-v2-hnav");
                if (treat.hasClass("edj-v2-fnav-box-exp")) {
                    treat.removeClass("edj-v2-fnav-box-exp");
                    btn.removeClass("edj-v2-fnav-active");
                    treat.find(".edj-v2-fnavs .edj-v2-tree-title").css("display", "none");

                    atvli.children("ul").hide();
                    cli.find("span.right-arr").removeClass("active-arr").hide();

                    ev2hna.find(".edj-v2-logo").removeClass("edj-v2logo-exp");

                    $("body").css("paddingLeft", "56px");

                    that.cookie.set("is-v2_open_nav", false, {
                        path: that.defaults.path,
                        domain: that.defaults.domain
                    });

                } else {
                    treat.addClass("edj-v2-fnav-box-exp");
                    btn.addClass("edj-v2-fnav-active");
                    treat.find(".edj-v2-fnavs .edj-v2-tree-title").css("display", "inline-block");

                    cli.find("span.right-arr").show();
                    $(".tree-branch-show > div > span.right-arr").addClass("active-arr");
                    atvli.children("ul").show();

                    ev2hna.find(".edj-v2-logo").addClass("edj-v2logo-exp");

                    $("body").css("paddingLeft", "200px");

                    that.cookie.set("is-v2_open_nav", true, {
                        path: that.defaults.path,
                        domain: that.defaults.domain
                    });

                }
            }).delegate(".edj-v2-fnavs div", "click", function() {
                if ($(this).parent().find("li.active-li") <= 0) {
                    that.cookie.set("labelId", $(this).parent().attr("data-level"), {
                        path: that.defaults.path,
                        domain: that.defaults.domain
                    });
                }
                if ($("#edj-v2-fnav-box").hasClass("edj-v2-fnav-box-exp")) {
                    var cli = $(this),
                        pcli = cli.parent(),
                        pali = pcli.siblings();
                    if (pcli.hasClass("tree-branch")) { // Branch
                        // 树枝节点
                        $("#edj-v2-fnav-box li").removeClass("active-li");

                        if (pcli.hasClass("tree-branch-show")) {
                            cli.find("span.right-arr").removeClass("active-arr");
                            cli.siblings("ul").hide();
                            pcli.removeClass("tree-branch-show");
                        } else {
                            cli.siblings("ul").show();
                            cli.find("span.right-arr").addClass("active-arr");
                            pcli.addClass("tree-branch-show");

                            pali.find("ul").hide();
                            pali.removeClass("tree-branch-show");
                            pali.find("li").removeClass("tree-branch-show");
                            pali.find("span.right-arr").removeClass("active-arr");

                            var mySiblings = cli.parents(".root-branch").siblings();
                            mySiblings.removeClass("tree-branch-show");
                            mySiblings.find("ul").hide();
                            mySiblings.find("li").removeClass("tree-branch-show");
                            mySiblings.find("span.right-arr").removeClass("active-arr");
                        }
                    }
                } else {
                    var btn = $("#edj-v2-fnav-switch").find(".edj-v2-fnav-btn");
                    var treat = $("#edj-v2-fnav-box");
                    var curli = $(this),
                        pcurli = curli.parent(),
                        spcurli = pcurli.siblings(".tree-branch-show"),
                        cli = $(".edj-v2-fnavs > li div"),
                        pcli = cli.parent();
                    $("#edj-v2-fnav-box").addClass("edj-v2-fnav-box-exp");
                    treat.addClass("edj-v2-fnav-box-exp");
                    btn.addClass("edj-v2-fnav-active");
                    treat.find(".edj-v2-fnavs .edj-v2-tree-title").css("display", "inline-block");
                    cli.find("span.right-arr").show();
                    $("#edj-v2-hnav").find(".edj-v2-logo").addClass("edj-v2logo-exp");

                    spcurli.removeClass("tree-branch-show");
                    spcurli.find("ul").hide();
                    spcurli.find("li").removeClass("tree-branch-show");
                    spcurli.find("span.right-arr").removeClass("active-arr");

                    curli.siblings("ul").show();
                    pcurli.addClass("tree-branch-show");
                    curli.children("span.right-arr").addClass("active-arr");

                    if (cli.parent().hasClass("root-branch")) {
                        $("body").css("paddingLeft", "200px");
                    }

                    that.cookie.set("is-v2_open_nav", true, {
                        path: that.defaults.path,
                        domain: that.defaults.domain
                    });

                }
            }).delegate("#edj_v2_submit", "click", function() {
                if ($.trim($("#edj-v2-search").val()).length <= 0) {
                    alert("请输入搜索内容");
                    return false;
                }
                $("#edj_search_form").submit();
            }).delegate(".edj-v2-fnavs a", "click", function() {
                that.cookie.set("labelId", $(this).parent().attr("data-level"), {
                    path: that.defaults.path,
                    domain: that.defaults.domain
                });
                var cli = $(this),
                    pcli = cli.parent(),
                    pali = pcli.siblings();
                pali.removeClass("active-li");
                pcli.addClass("active-li");
                pali.find("ul").hide();
                pali.removeClass("tree-branch-show");
                pali.find("li").removeClass("tree-branch-show");
                pali.find("span.right-arr").removeClass("active-arr");
                if(that.isModle === "mobile"){
                    $("#nav_ctrl").trigger("click");
                }
            }).delegate("#nav_ctrl","click",function(){
                if(that.isModle === "mobile"){
                    $("#edj-v2-fnav-switch").trigger("click");
                    var _this = $(this),navs = _this.parent().next("#edj-v2-fnav-box");
                    if(!navs.hasClass("edj-v2-fnav-box-exp")){
                        navs.hide();
                        $("body").css("paddingLeft",0);
                    }else{
                        if(navs.is(":visible")){
                            navs.hide();
                            $("#edj-v2-fnav-switch").trigger("click");
                            $("body").css("paddingLeft",0);
                        }else{
                            navs.show();
                        }
                    }
                }else{
                    location.href = that.logoLink;
                }
            });
        }
    }
    $(function() {
        var url = NavHost + "/index.php?r=default/left";
        $.ajax({
            url: url,
            type: 'GET',
            data: {},
            crossDomain: true,
            dataType: 'jsonp',
            timeout: 5000,
            error:function(){
            },
            success: function(data) {
                if(data.menus){
                    new FNav({
                        NLData: data.menus || testData
                    });
                    $("#edj_search_form").attr("action", data.v2url + "/index.php?r=system/search");
                }else{
                    window.location = NavHost + "/index.php?r=site/logout";
                }
            }
        });
    });
}())