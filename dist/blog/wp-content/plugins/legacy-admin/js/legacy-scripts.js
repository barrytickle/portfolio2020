/**
 * @Package: WordPress Plugin
 * @Subpackage: Legacy - White Label WordPress Admin Theme
 * @Since: Legacy 1.0
 * @WordPress Version: 4.0 or above
 * This file is part of Legacy - White Label WordPress Admin Theme Plugin.
 */


jQuery(function($) {

    'use strict';

    var LEGACY_SETTINGS = window.LEGACY_SETTINGS || {};


    /******************************
     Menu resizer
     *****************************/
    LEGACY_SETTINGS.menuResizer = function() {
        var menuWidth = $("#adminmenuwrap").width();
        if ($("#adminmenuwrap").is(":hidden")) {
            $("body").addClass("menu-hidden");
            $("body").removeClass("menu-expanded");
            $("body").removeClass("menu-collapsed");
        }
        else if (menuWidth > 46) {
            $("body").addClass("menu-expanded");
            $("body").removeClass("menu-hidden");
            $("body").removeClass("menu-collapsed");
        } else {
            $("body").addClass("menu-collapsed");
            $("body").removeClass("menu-expanded");
            $("body").removeClass("menu-hidden");
        }

        LEGACY_SETTINGS.menuConnectionLine();

    };

    LEGACY_SETTINGS.menuClickResize = function() {
        $('#collapse-menu, #wp-admin-bar-menu-toggle').click(function(e) {
            var menuWidth = $("#adminmenuwrap").width();
            if ($("#adminmenuwrap").is(":hidden")) {
                $("body").addClass("menu-hidden");
                $("body").removeClass("menu-expanded");
                $("body").removeClass("menu-collapsed");
            }
            else if (menuWidth > 46) {
                $("body").addClass("menu-expanded");
                $("body").removeClass("menu-hidden");
                $("body").removeClass("menu-collapsed");
            } else {
                $("body").addClass("menu-collapsed");
                $("body").removeClass("menu-expanded");
                $("body").removeClass("menu-hidden");
            }
        });
    };

    LEGACY_SETTINGS.logoURL = function() {

        $("#adminmenuwrap").prepend("<div class='logo-overlay'></div>");

        $('#adminmenuwrap .logo-overlay').click(function(e) {
            var logourl = $("#legacy-logourl").attr("data-value");
            if (logourl != "") {
                window.location = logourl;
            }
        });
    };

    LEGACY_SETTINGS.iconPanel = function(e) {

        $('.legacyicon').click(function(e) {
            e.stopPropagation();
            var panel = $(this).parent().find(".legacyiconpanel");
            var iconstr = $(".legacyicons").html();
            panel.html("");
            panel.append(iconstr);
            panel.show();
        });


    };




    LEGACY_SETTINGS.menuToggle = function() {

        $('.legacytoggle').click(function(e) {

            var id = $(this).parent().attr("data-id");

            if ($(this).hasClass("plus")) {
                $(this).removeClass("plus dashicons-plus").addClass("minus dashicons-minus");
                //$(this).html("-");
                $(this).parent().parent().find(".legacymenupanel").removeClass("closed").addClass("opened");
            } else if ($(this).hasClass("minus")) {
                $(this).removeClass("minus dashicons-minus").addClass("plus dashicons-plus");
                //$(this).html("+");
                $(this).parent().parent().find(".legacymenupanel").removeClass("opened").addClass("closed");
            }

        });


        $('.legacysubtoggle').click(function(e) {

            var id = $(this).parent().attr("data-id");

            if ($(this).hasClass("plus")) {
                $(this).removeClass("plus dashicons-plus").addClass("minus dashicons-minus");
                //$(this).html("-");
                $(this).parent().parent().find(".legacysubmenupanel").removeClass("closed").addClass("opened");
            } else if ($(this).hasClass("minus")) {
                $(this).removeClass("minus dashicons-minus").addClass("plus dashicons-plus");
                //$(this).html("+");
                $(this).parent().parent().find(".legacysubmenupanel").removeClass("opened").addClass("closed");
            }

        });


    };

    LEGACY_SETTINGS.saveMenu = function() {

        $('#legacy-savemenu').click(function(e) {

            var neworder = "";
            var newsuborder = "";
            var menurename = "";
            var submenurename = "";
            var menudisable = "";
            var submenudisable = "";

            $(".legacymenu").each(function() {
                var id = $(this).attr("data-id");
                var menuid = $(this).attr("data-menu-id");
                neworder += menuid + "|";
                if ($(this).hasClass("disabled")) {
                    menudisable += menuid + "|";
                }
            });

            $(".legacysubmenu").each(function() {
                var id = $(this).attr("data-id");
                var parentpage = $(this).attr("data-parent-page");
                newsuborder += parentpage + ":" + id + "|";
                if ($(this).hasClass("disabled")) {
                    submenudisable += parentpage + ":" + id + "|";
                }
            });

            $(".legacy-menurename").each(function() {
                var id = $(this).attr("data-id");
                var sid = $(this).attr("data-menu-id");
                var val = $(this).attr("value");
                var icon = $(this).parent().parent().find(".legacy-menuicon").attr("value");
                //console.log(icon);
                menurename += id + ":" + sid + "@!@%@" + val + "[$!&!$]" + icon + "|#$%*|";
            });


            $(".legacy-submenurename").each(function() {
                var id = $(this).attr("data-id");
                var parent = $(this).attr("data-parent-id");
                var parentpage = $(this).attr("data-parent-page");
                var val = $(this).attr("value");
                submenurename += parentpage + "[($&)]" + parent + ":" + id + "@!@%@" + val + "|#$%*|";
            });


            //console.log(neworder);
            //console.log(menurename);

            var action = 'legacy_savemenu';
            var data = {
                neworder: neworder,
                newsuborder: newsuborder,
                menurename: menurename,
                submenurename: submenurename,
                menudisable: menudisable,
                submenudisable: submenudisable,
                action: action,
                legacy_nonce: legacy_vars.legacy_nonce
            };

            $.post(ajaxurl, data, function(response) {
                location.reload();
                //console.log(response);
            });

            return false;

        });

    };


    LEGACY_SETTINGS.resetMenu = function() {

        $('#legacy-resetmenu').click(function(e) {

            var action = 'legacy_resetmenu';
            var data = {
                action: action,
                legacy_nonce: legacy_vars.legacy_nonce
            };

            $.post(ajaxurl, data, function(response) {
                location.reload();
                //console.log(response);
            });

            return false;

        });

    };






    LEGACY_SETTINGS.menuDisplay = function() {

        $('.legacydisplay, .legacysubdisplay').click(function(e) {

            //var id = $(this).parent().attr("data-id");

            if ($(this).hasClass("disable")) {
                $(this).removeClass("disable").addClass("enable");
                //$(this).html("show");
                $(this).parent().parent().removeClass("enabled").addClass("disabled");
            } else if ($(this).hasClass("enable")) {
                $(this).removeClass("enable").addClass("disable");
                //$(this).html("hide");
                $(this).parent().parent().removeClass("disabled").addClass("enabled");
            }

        });


        $("#wp-admin-bar-menu-toggle").click(function(e) {
            LEGACY_SETTINGS.menuConnectionLine();
        });

    };



    LEGACY_SETTINGS.menuConnectionLine = function() {



        var mainmenu = ($("#adminmenu").height() - $("li#collapse-menu").height()) / 2;
        //$("#adminmenu:before, #adminmenu:after").css('height', +mainmenu + 'px');
        $('<style>#adminmenu:before, #adminmenu:after {height: ' + mainmenu + 'px;} #adminmenu:after {top: ' + mainmenu + 'px;}</style>').appendTo('head');
        //console.log(mainmenu);
        $("li.wp-has-submenu").each(function() {
            var id = $(this).attr("id");

            if ($("body").hasClass("folded") || $("body").hasClass("menu-collapsed")) {
                var subheight = ($(this).find(".wp-submenu").height() - 49) / 2;
            } else {
                var subheight = ($(this).find(".wp-submenu").height()) / 2;
            }

            var str = "";
            str += "li#" + id + " .wp-submenu:before, li#" + id + " .wp-submenu:after { height: " + subheight + "px !important;} ";
            str += "li#" + id + " .wp-submenu:after { top: " + subheight + "px !important;} ";
            str += ".folded li#" + id + " .wp-submenu:before, .folded li#" + id + " .wp-submenu:after { height: " + subheight + "px !important;} ";
            str += ".folded li#" + id + " .wp-submenu:after { top: " + (subheight + 49) + "px !important;} ";

            $('<style>' + str + '</style>').appendTo('head');
        });

    };



    LEGACY_SETTINGS.alternateSave = function() {
        $(".legacy_save_new").on('click', function(e) {
            legacy_ajaxsavestep(1);
            console.log("alternate save it");
            //e.preventDefault();
            return false;
        });

    };



    function legacy_ajaxsavestep(stepid) {

        //$("#uport_submitstep" + stepid).addClass("saving");

        //var id = $("#uport_id").val();
        //var settingids = $("#uport_settingids_" + stepid + "").val();        //alert(settingids);
        //var str = setting_values(settingids);
        var opt1 = $("#legacy_demo-dynamic-css-type .redux-image-select-selected input").val();
        var str = $("#legacy_demo-primary-color").find(".wp-color-result").attr("style") + ";" +$("#login-input-bg-opacity").val() +";"+ opt1;

        //alert(str); exit;
        var action = 'legacy_alternate_save';
        var data = {
            values: str,
            action: action,
            legacy_nonce: legacy_vars.legacy_nonce
        };

        $.post(ajaxurl, data, function(response) {
            //alert(response);
            $('.alternate_save_response').html(response);
            
        });

        return false;
    }






    /******************************
     initialize respective scripts 
     *****************************/
    $(document).ready(function() {
        LEGACY_SETTINGS.menuResizer();
        LEGACY_SETTINGS.menuClickResize();
        LEGACY_SETTINGS.logoURL();
        LEGACY_SETTINGS.menuToggle();
        LEGACY_SETTINGS.saveMenu();
        LEGACY_SETTINGS.menuDisplay();
        LEGACY_SETTINGS.iconPanel();
        LEGACY_SETTINGS.resetMenu();
        LEGACY_SETTINGS.menuConnectionLine();
        //LEGACY_SETTINGS.alternateSave();

    });

    $(window).resize(function() {
        LEGACY_SETTINGS.menuResizer();
        LEGACY_SETTINGS.menuClickResize();
    });

    $(window).load(function() {
        LEGACY_SETTINGS.menuResizer();
        LEGACY_SETTINGS.menuClickResize();
    });

});


jQuery(function($) {
    if ($.isFunction($.fn.sortable)) {
        $("#legacy-enabled, #legacy-disabled").sortable({
            connectWith: ".legacy-connectedSortable",
            handle: ".legacymenu-wrap",
            cancel: ".legacytoggle",
            placeholder: "ui-state-highlight",
        }).disableSelection();
    }
});


jQuery(function($) {
    if ($.isFunction($.fn.sortable)) {
        $(".legacysubmenu-wrap").sortable({
            placeholder: "ui-state-highlight",
        }).disableSelection();
    }
});


jQuery(function($) {
    $(document).ready(function() {
        $(document).on('click', ".pickicon", function() {
            var clss = $(this).attr("data-class");
            var prnt = $(this).parent().parent();
            //console.log(clss);
            prnt.find("input").attr("value", clss);
            prnt.find("input").val(clss);
            var main = prnt.find(".legacymenuicon");
            main.removeClass(main.attr("data-class")).addClass(clss);
            main.attr("data-class", clss);
            return false;
        });

        $(document).on('click', "body", function() {
            $(".legacyiconpanel").hide();
            //return false;
        });




    });
});
