/*
 Theme Name: homey
 Description: homey
 Author: Favethemes
 Version: 1.0
 */
(function($) {
    "use strict";

    /* ------------------------------------------------------------------------ */
    /*  GLOBAL VARIABLES
    /* ------------------------------------------------------------------------ */
    var current_month = 1;
    var stickySidebarTop = 300;
    var adminBarHeight = 20;
    var searchCalClick = 0;
    var fromTimestamp, toTimestamp = 0; // init start and end timestamps
    var timestamp;

    var homey_is_dashboard = HOMEY_ajax_vars.homey_is_dashboard;
    var homey_calendar_months = HOMEY_ajax_vars.homey_calendar_months;
    var homey_date_format = HOMEY_ajax_vars.homey_date_format;
    var search_position = HOMEY_ajax_vars.search_position;
    var geo_country_limit = HOMEY_ajax_vars.geo_country_limit;
    var geocomplete_country = HOMEY_ajax_vars.geocomplete_country;
    var replytocom = HOMEY_ajax_vars.replytocom;
    var compare_url = HOMEY_ajax_vars.compare_url;
    var add_compare = HOMEY_ajax_vars.add_compare;
    var remove_compare = HOMEY_ajax_vars.remove_compare;
    var compare_limit = HOMEY_ajax_vars.compare_limit;
    var homey_is_transparent = HOMEY_ajax_vars.homey_is_transparent;
    var is_tansparent = HOMEY_ajax_vars.homey_tansparent;
    var is_top_header = HOMEY_ajax_vars.homey_is_top_header;
    var simple_logo = HOMEY_ajax_vars.simple_logo;
    var mobile_logo = HOMEY_ajax_vars.mobile_logo;
    var retina_logo = HOMEY_ajax_vars.retina_logo;
    var custom_logo_splash = HOMEY_ajax_vars.custom_logo_splash;
    var retina_logo_splash = HOMEY_ajax_vars.retina_logo_splash;
    var retina_logo_mobile = HOMEY_ajax_vars.retina_logo_mobile;
    var custom_logo_mobile_splash = HOMEY_ajax_vars.custom_logo_mobile_splash;
    var retina_logo_mobile_splash = HOMEY_ajax_vars.retina_logo_mobile_splash;
    var current_month2 = 2;
    var $win = $(window);
    var body_width = $('body').innerWidth();
    var header_area = $('.nav-area');
    var header_nav = $('.header-nav');
    var main_content_area = $('.main-content-area');
    var section_body = $('#section-body');
    var homey_main_search = $('#homey-main-search');
    var homey_main_search_height = homey_main_search.innerHeight();
    var homey_nav_sticky = $('#homey_nav_sticky');
    var homey_nav_sticky_height = homey_nav_sticky.innerHeight();
    var dashboard_header = $('.dashboard-page-title');
    var footer_area = $('.footer-area');
    var user_dashboard_left = $('.user-dashboard-left');
    var top_banner_wrap  = $('.top-banner-wrap ');
    var top_banner_wrap_height  = top_banner_wrap.innerHeight();

    var header_area_height = header_area.innerHeight();
    var header_area_outer_height = header_area.outerHeight();
    var dashboard_header_height = dashboard_header.innerHeight();
    var footer_area_height = footer_area.innerHeight();
    var search_area_height = $('.header-search').innerHeight();
    var listing_nav_area_height = $('.listing-nav').innerHeight();
    var focusedInput = null;

    var homey_is_rtl = HOMEY_ajax_vars.homey_is_rtl;

    if( homey_is_rtl == 'yes' ) {
        homey_is_rtl = true;
    } else {
        homey_is_rtl = false;
    }

    if(listing_nav_area_height == undefined) {
        listing_nav_area_height = 0;
    }

    var make_search_sticky_position = header_area_height;
    var searchStickyPlus = 20;
    if(search_position == 'under_banner') {
        make_search_sticky_position = header_area_height + top_banner_wrap_height;
        searchStickyPlus = 0;
    }

    var homey_is_mobile = false;
    if (/Android|webOS|iPhone|iPad|iPod|tablet|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        homey_is_mobile = true;
    }
    console.log(homey_is_mobile);

    var homey_is_android = false;
    if (/Android|Opera Mini/i.test(navigator.userAgent)) {
        homey_is_android = true;
    }
    console.log(homey_is_android);



    var only_nav_sticky = homey_nav_sticky.data('sticky');
    var only_search_sticky = homey_main_search.data('sticky');
    if(only_nav_sticky === 1) {
        stickySidebarTop = homey_nav_sticky_height;
    }
    if(only_search_sticky === 1) {
        stickySidebarTop = homey_main_search_height;
    }

    if(only_nav_sticky === 1 && only_search_sticky === 1) {
        stickySidebarTop = homey_nav_sticky_height + homey_main_search_height;
    }

    var componentForm = {
        establishment: 'long_name',
        street_number: 'short_name',
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'long_name',
        postal_code: 'short_name',
        postal_code_prefix : 'short_name',
        neighborhood: 'long_name',
        sublocality_level_1: 'long_name'
    };

    $('.mobile-main-nav').on('click', function(){
        $('#user-nav').removeClass('in');
    });

    $('.user-mobile-nav').on('click', function(){
        $('#mobile-nav').removeClass('in');
    });

    /* ------------------------------------------------------------------------ */
    /*  Match Height
    /* ------------------------------------------------------------------------ */
    if($('.homey-matchHeight-needed .homey-matchHeight').length){
        $('.homey-matchHeight').matchHeight({ remove: true });
        $('.homey-matchHeight').matchHeight();
    }

    /* ------------------------------------------------------------------------ */
    /*  parseInt Radix 10
    /* ------------------------------------------------------------------------ */
    function parseInt10(val) {
        return parseInt(val, 10);
    }

    if($('.comments-form').length > 0) {
        $('.comments-form .comment-respond').removeAttr('id', 'respond');

        if(replytocom !='') {
            $('html, body').animate({
                scrollTop: $("#comments-form").offset().top - 320
            }, 1000);
        }
    }

    /* ------------------------------------------------------------------------ */
    /*  BOOTSTRAP POPOVER
    /* ------------------------------------------------------------------------ */
    var popover_ele = $('[data-toggle="popover"]');
    popover_ele.popover({
        trigger: "hover",
        html: true
    });

    /* ------------------------------------------------------------------------ */
    /*  BOOTSTRAP TOOLTIP
    /* ------------------------------------------------------------------------ */
    var data_tooltip = $('[data-toggle="tooltip"]');
    data_tooltip.tooltip();

    /* ------------------------------------------------------------------------ */
    /*  ELEMENT HIDE ON DOCUMENT HIDE
    /* ------------------------------------------------------------------------ */
    function click_doc_hide(ele) {
        $(document).mouseup(function(e) {
            if (!$(ele).is(e.target) // if the target of the click isn't the container...
                &&
                $(ele).has(e.target).length === 0 // ... nor a descendant of the container
            ) {
                $(ele).fadeOut();
            }
        });
    }

    /* ------------------------------------------------------------------------ */
    /*  BOOTSTRAP SELECT PICKER
    /* ------------------------------------------------------------------------ */
    var select_picker = $('.selectpicker');
    if (select_picker.length > 0) {
        select_picker.selectpicker({
            dropupAuto: false
        });
    }

    /* ------------------------------------------------------------------------ */
    /*  CHECK USER AGENTS
    /* ------------------------------------------------------------------------ */
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);

    /* ------------------------------------------------------------------------ */
    /*  NAVIGATION
    /* ------------------------------------------------------------------------ */
    $('.navi ul li').each(function() {
        $(this).has('ul').not('.homey-megamenu li').addClass('has-child')
    });

    $(".navi ul .has-child").on({
        mouseenter: function() {
            $(this).addClass('active');
        },
        mouseleave: function() {
            $(this).removeClass('active');
        }
    });

    function homy_megamenu() {
        if ($(window).width() > 991) {
            var container = $('.container');
            var header = $('.header-type-1,.header-type-1');

            var containWidth = container.innerWidth();
            var windowWidth = $win.width();
            var containOffset = container.offset();

            if ($('.navi ul li').hasClass('homey-megamenu')) {

                $('.navi ul .homey-megamenu').each(function() {
                    $("> .sub-menu", this).wrap("<div class='homey-megamenu-wrap'></div>");
                    var thisOffset = $(this).offset();
                    if (header.children('.container').length > 0) {
                        $("> .homey-megamenu-wrap", this).css({
                            width: containWidth,
                            left: -(thisOffset.left - containOffset.left)
                        });
                    } else {
                        $("> .homey-megamenu-wrap", this).css({

                            width: windowWidth,
                            left: -thisOffset.left
                        });

                    }
                });

            }
        }
    }
    homy_megamenu();
    $win.on('resize', function() {
        homy_megamenu();
    });
    $win.bind('load', function() {
        homy_megamenu();
    });

    /* ------------------------------------------------------------------------ */
    /*  ACCOUNT DROPDOWN
    /* ------------------------------------------------------------------------ */
    function accountDropdown() {

        $(".account-loggedin").on({
            mouseenter: function() {
                $(this).addClass('active');
            },
            mouseleave: function() {
                $(this).removeClass('active');
            }
        });

    }
    accountDropdown();

    /* ------------------------------------------------------------------------ */
    /*  MOBILE MENU
    /* ------------------------------------------------------------------------ */
    function mobileMenu(menu_html, menu_place) {
        var siteMenu = $(menu_html).html();
        $(menu_place).html(siteMenu);

        $(menu_place + ' ul li').each(function() {
            $(this).has('ul').addClass('has-child');
        });

        $(menu_place + ' ul .has-child').append('<span class="expand-me"></span>');

        $(menu_place + ' .expand-me').on('click', function() {
            var parent = $(this).parent('li');
            if (parent.hasClass('active') == true) {
                parent.removeClass('active');
                parent.children('ul').slideUp();
            } else {
                parent.addClass('active');
                parent.children('ul').slideDown();
            }
        });
    }
    mobileMenu('.main-nav', '.main-nav-dropdown');

    $('.nav-trigger').on('click', function() {
        if ($(this).hasClass('mobile-open')) {
            $(this).removeClass('mobile-open');
        } else {
            $(this).addClass('mobile-open');
        }
    });

    /* ------------------------------------------------------------------------ */
    /*  START USER DASHBOARD PANEL AND SIDEBAR
    /* ------------------------------------------------------------------------ */
    // media query event handler
    if (matchMedia) {
        var mq = window.matchMedia("(max-width: 991px)");
        mq.addListener(WidthChange);
        WidthChange(mq);
    }
    // media query change
    function WidthChange(mq) {
        if (mq.matches) {

            $('.dashboard-page-title').css({
                "top": 60
            });
            $('.user-dashboard-right').css({
                "padding-top": 131
            });
        } else {
            $('.dashboard-sidebar').css({
                "top": header_area_height + dashboard_header_height + 34
            });

            $('.user-dashboard-left').css({
                "top": header_area_height
            });
            $('.user-dashboard-right').css({
                "padding-top": header_area_height + dashboard_header_height + 4
            });
            $('.dashboard-page-title').css({
                "top": header_area_height
            });
        }
    }



    /* ------------------------------------------------------------------------ */
    /*  START PROPERTY VIEW
    /* ------------------------------------------------------------------------ */

    function sticky_block() {
        if ($win.width() > 991) {
            var stickySidebar = $('.dashboard-view-block');
            var scroll_area = $(".dashboard-content-area");

            if (stickySidebar.length > 0) {
                var stickyHeight = stickySidebar.height(),
                    sidebarTop = stickySidebar.offset().top;
                sidebarTop = (sidebarTop - dashboadr_header_height) - header_area_height - 30;
            }
            // on scroll move the sidebar
            scroll_area.scroll(function() {
                if ($win.width() > 991) {

                    if (stickySidebar.length > 0) {

                        var scrollTop = scroll_area.scrollTop();

                        if (sidebarTop < scrollTop) {
                            stickySidebar.css('top', scrollTop - sidebarTop);

                            // stop the sticky sidebar at the footer to avoid overlapping
                            var sidebarBottom = stickySidebar.offset().top + stickyHeight,
                                stickyStop = sticky_content.offset().top + sticky_content.height();
                            if (stickyStop < sidebarBottom) {
                                var stopPosition = sticky_content.height() - stickyHeight;
                                stickySidebar.css('top', stopPosition);
                            }
                        } else {
                            stickySidebar.css('top', '0');
                        }
                    }
                } else {
                    return false;
                }
            });

            $win.resize(function() {
                if (stickySidebar.length > 0) {
                    stickyHeight = stickySidebar.height();
                }
            });
        } else {
            return false;
        }

    }
    sticky_block();
    $win.on('resize', function() {
        sticky_block();
    });

    /* ------------------------------------------------------------------------ */
    /*  STICKY HEADER
    /* ------------------------------------------------------------------------ */
    if (window.devicePixelRatio == 2) {

        if(is_tansparent) {
            if(retina_logo_splash != '') {
                custom_logo_splash = retina_logo_splash;
            }
            if(retina_logo != '') {
                simple_logo = retina_logo;
            }

            if(retina_logo_mobile != '') {
                mobile_logo = retina_logo_mobile;
            }

            if(retina_logo_mobile_splash != '') {
                custom_logo_mobile_splash = retina_logo_mobile_splash;
            }
        }
    }

    function homey_sticky_nav() {
        $(window).scroll(function() {
            var scroll = $(window).scrollTop();
            var admin_nav = $('#wpadminbar').height();
            var thisHeight = header_nav.outerHeight();

            if(only_nav_sticky === 0) {
                return;
            }

            if(is_tansparent) {
                $('.homey_logo img').attr('src', simple_logo);
                $('.mobile-logo img').attr('src', mobile_logo );
            }

            if( admin_nav == 'null' ) { admin_nav = 0; }

            if (scroll > header_area_height ) {
                header_nav.addClass('sticky-nav-area');
                header_nav.css('top', admin_nav);
                if(is_tansparent){
                    header_area.removeClass('transparent-header');

                }

                if (scroll >= header_area_height + 20 ) {
                    header_nav.addClass('homey-in-view');

                    if(is_top_header || !homey_is_transparent) {
                        section_body.css('padding-top',thisHeight);
                    }
                }

            } else {
                header_nav.removeClass('sticky-nav-area');
                header_nav.removeAttr("style");
                if(is_tansparent){
                    header_area.addClass('transparent-header');
                    setTransparentHeaderMarginBottom();
                    $('.homey_logo img').attr('src', custom_logo_splash);
                    $('.mobile-logo  img').attr('src', custom_logo_mobile_splash );
                }

                if (scroll <= header_area_height + 20 ) {
                    header_nav.removeClass('homey-in-view');
                }
                if(is_top_header || !homey_is_transparent) {
                    section_body.css('padding-top',0);
                }
            }
        });
    }

    function homey_sticky_search() {
        $(window).scroll(function() {
            var scroll = $(window).scrollTop();
            var admin_nav = $('#wpadminbar').height();

            var thisHeight = $('.main-search').outerHeight();

            if(only_search_sticky === 0) {
                return;
            }

            if( admin_nav == 'null' ) { admin_nav = 0; }

            if (scroll  >= make_search_sticky_position ) {
                homey_main_search.addClass('sticky-search-area');
                homey_main_search.css('top', admin_nav);
                if (scroll >= make_search_sticky_position + searchStickyPlus ) {
                    homey_main_search.addClass('homey-in-view');

                    if(is_top_header || !homey_is_transparent) {
                        section_body.css('padding-top',thisHeight);
                    }
                }
            } else {
                homey_main_search.removeClass('sticky-search-area');
                homey_main_search.removeAttr("style");
                if (scroll <= make_search_sticky_position + 20 ) {
                    homey_main_search.removeClass('homey-in-view');
                }
                if(is_top_header || !homey_is_transparent) {
                    section_body.css('padding-top',0);
                }
            }
        });
    }

    function homey_sticky_nav_search() {
        $(window).scroll(function() {
            var scroll = $(window).scrollTop();
            var thisHeight = header_nav.outerHeight();
            var admin_nav = $('#wpadminbar').height();

            if( admin_nav == 'null' ) { admin_nav = 0; }

            if (scroll >= header_area_height ) {
                header_area.addClass('sticky-nav-area');
                header_area.css('top', admin_nav);
                if (scroll >= header_area_height + 20 ) {
                    header_area.addClass('homey-in-view');
                    if(is_top_header || !homey_is_transparent) {
                        section_body.css('padding-top',thisHeight);
                    }
                }
            } else {
                header_area.removeClass('sticky-nav-area');
                header_area.removeAttr("style");
                if (scroll <= header_area_height + 20 ) {
                    header_area.removeClass('homey-in-view');
                }
                if(is_top_header || !homey_is_transparent) {
                    section_body.css('padding-top',0);
                }
            }
        });
    }

    if(!homey_is_mobile && homey_is_dashboard != 1) {
        if(only_nav_sticky === 1 && only_search_sticky === 1) {
            homey_sticky_nav_search();

        } else if(only_nav_sticky === 1) {
            homey_sticky_nav();

        } else if(only_search_sticky === 1) {
            homey_sticky_search();
        }
    }

    /* ------------------------------------------------------------------------ */
    /*  sticky search bar
    /* ------------------------------------------------------------------------ */
    $(document).ready(function(){
        adminBarHeight = $('#wpadminbar').innerHeight();
        if(adminBarHeight != null) {
            stickySidebarTop = stickySidebarTop + adminBarHeight;
        }


        if($(window).width()<992){
            $('.homey-sticky-map').theiaStickySidebar({
                additionalMarginTop: stickySidebarTop,
                updateSidebarHeight: false
            });
        }
        else{
            $('.homey-sticky-map').theiaStickySidebar({
                additionalMarginTop: stickySidebarTop + 30,
                updateSidebarHeight: false
            });
        }

        $('.homey_sticky').theiaStickySidebar({
            additionalMarginTop: stickySidebarTop + listing_nav_area_height,
            minWidth: 768,
            updateSidebarHeight: false,
        });
    });

    /* ------------------------------------------------------------------------ */
    /*  listing nav bar page scroll
    /* ------------------------------------------------------------------------ */
    $(document).scroll(function() {
        var y = $(this).scrollTop();

        var homey_listing_nav = $('.listing-nav');

        homey_listing_nav.css('top', stickySidebarTop);
        if (y > 200) {
            $('.listing-nav').fadeIn(250);
        } else {
            $('.listing-nav').fadeOut(0);
        }
    });

    // Select all links with hashes
    $('a[href*="#"]')
        // Remove links that don't actually link to anything
        .not('[href="#"]')
        .not('[href="#0"]')
        .on('click', function(event) {
            // On-page links
            if (
                location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
                &&
                location.hostname == this.hostname
            ) {
                // Figure out element to scroll to
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                // Does a scroll target exist?
                if (target.length) {
                    // Only prevent default if animation is actually gonna happen
                    event.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - (stickySidebarTop + listing_nav_area_height),
                    }, 1000, 'easeOutExpo', function() {
                    });
                }
            }
        });

    /* ------------------------------------------------------------------------ */
    /*  MAP VIEW TABER
    /* ------------------------------------------------------------------------ */
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var this_e = e;
        this_e.target // newly activated tab
        this_e.relatedTarget // previous active tab
    });

    /* ------------------------------------------------------------------------ */
    /*  HOMY TABERS
    /* ------------------------------------------------------------------------ */
    function homey_tabers(ele_tab, ele_tab_content, ele_delay) {
        var tab = $(ele_tab);
        var tab_content = $(ele_tab_content);

        tab.on('click', function() {
            var this_tab = $(this);

            if (this_tab.hasClass('active') == false) {
                tab.removeClass('active');
                this_tab.addClass('active');
                tab_content.removeClass('active in');
                tab_content.eq(this_tab.index()).addClass('active').delay(ele_delay).queue(function(next) {
                    tab_content.eq(this_tab.index()).addClass('in');
                    next();
                });
            }
        });
    }


    /* ------------------------------------------------------------------------ */
    /* carousel - property page gallery module
    /* ------------------------------------------------------------------------ */
    $(document).ready(function() {
        $(".past-day").on("click", function(e){
            e.preventDefault();
            if($("#hourly_check_inn").length > 0){
                $("#hourly_check_inn").val("");
            }

            if($("input.check_in_date").length > 0){
                $("input.check_in_date").val("");
            }

            if($("input.check_out_date").length > 0){
                $("input.check_out_date").val("");
            }
            return false;
        });
        $.fancybox.defaults.loop = true;

        $(".fanboxTopGalleryVar-item").on('click', function(e) {
            e.preventDefault();
            var fancy_image_index = $(this).data("fancyImageIndex");
            $.fancybox.open( $('.fanboxTopGalleryVar')).jumpTo( fancy_image_index );
        });

        $(".fanboxGallery-item").on('click', function(e) {
            e.preventDefault();
            var fancy_image_index = $(this).data("fancyImageIndex");
            $.fancybox.open( $('.fanboxGallery')).jumpTo( fancy_image_index );
        });

        $(".fanboxTopGallery-item").on('click', function(e) {
            e.preventDefault();
            var fancy_image_index = $(this).data("fancyImageIndex");
            $.fancybox.open( $('.fanboxTopGallery')).jumpTo( fancy_image_index );
        });

        $(".fanboxTopGalleryFullWidth-item").on('click', function(e) {
            e.preventDefault();
            var fancy_image_index = $(this).data("fancyImageIndex");
            $.fancybox.open( $('.fanboxTopGalleryFullWidth')).jumpTo( fancy_image_index );
        });
    });

    $(document).ready(function(){
        // if(is_tansparent) {
        //     if($('.homey_logo img').attr('src') != simple_logo){
        //         $('.homey_logo img').attr('src', simple_logo);
        //         $('.mobile-logo img').attr('src', mobile_logo );
        //     }
        // }

        $('.listing-slider').slick({
            rtl: homey_is_rtl,
            lazyLoad: 'ondemand',
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: false,
            asNavFor: '.listing-slider-nav',
        });
        $('.listing-slider-nav').slick({
            rtl: homey_is_rtl,
            lazyLoad: 'ondemand',
            slidesToShow: 6,
            slidesToScroll: 1,
            asNavFor: '.listing-slider',
            dots: false,
            focusOnSelect: true,
            variableWidth: true,
            arrows: false,
        });
    });

    $(document).ready(function(){
        $('.listing-slider-variable-width').slick({
            rtl: homey_is_rtl,
            lazyLoad: 'ondemand',
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            centerMode: true,
            variableWidth: true,
            arrows: true,
            adaptiveHeight: true,
        });
    });

    $(document).ready(function(){
        $('.header-slider').slick({
            rtl: homey_is_rtl,
            lazyLoad: 'ondemand',
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            arrows: true,
            adaptiveHeight: true,
        });
    });


    /* ------------------------------------------------------------------------ */
    /*  HOMEY EXTENDED MEGA MENU
    /* ------------------------------------------------------------------------ */
    var extended_menu_btn = $('.extended-menu-btn');
    var extended_menu = $('.header-extended-menu');

    extended_menu_btn.on('click', function() {

        if ($(this).hasClass('active') == true) {
            $(this).parents('.header-wrap').find('.header-extended-menu').slideUp().removeClass('menu-open');
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
            $(this).parents('.header-wrap').find('.header-extended-menu').slideDown().addClass('menu-open');
        }
    });


    /* ------------------------------------------------------------------------ */
    /* Dropdown Search Menu
    /* ------------------------------------------------------------------------ */
    var search_filter_btn = $('.search-filter-btn');
    var search_filter_btn_i = $('.search-filter-btn-i');
    var search_filter = $('.search-filter');
    var is_bars_clicked = false;

    search_filter_btn.on('click', function() {
        if(is_bars_clicked == false){
            if ($(this).hasClass('active') == true) {
                $(this).parents('form').find('.search-filter').removeClass('search-filter-open');
                $(this).removeClass('active');
            } else {
                $(this).addClass('active');
                $(this).parents('form').find('.search-filter').addClass('search-filter-open');
            }
        }
    });

    search_filter_btn_i.on('click', function() {
        is_bars_clicked = true;
        if ($(search_filter_btn).hasClass('active') == true) {
            $(this).parents('form').find('.search-filter').removeClass('search-filter-open');
            $(search_filter_btn).removeClass('active');
        } else {
            $(search_filter_btn).addClass('active');
            $(this).parents('form').find('.search-filter').addClass('search-filter-open');
        }
    });
    // To limit from two clicks to one click only
    setInterval(function(){
        is_bars_clicked = false;
    }, 1000);

    /* ------------------------------------------------------------------------ */
    /* Search Reset
    /* ------------------------------------------------------------------------ */
    var search_reset_btn = $('.search-reset-btn');

    search_reset_btn.on('click', function(e) {
        e.preventDefault();

        var filters = $(this).parents('.search-filter-wrap');
        $(this).closest('form').find("input[type=text], textarea").val("");
        $(this).parents('.half-map-wrap').find("input[type=text], textarea").val("");

        $('.search-destination').find("input[type=hidden]").val("");

        filters.find('select').removeAttr('selected');
        filters.find('select').selectpicker('val', '');
        filters.find('select').selectpicker('refresh');

        filters.find('input[type=checkbox]').removeAttr('checked');
    });

    /* ------------------------------------------------------------------------ */
    /* Dropdown Search Menu Mobile
    /* ------------------------------------------------------------------------ */
    var search_filter_mobile_btn = $('.search-filter-mobile-btn');
    var search_filter_mobile = $('.search-filter');

    search_filter_mobile_btn.on('touchstart', function() {

        if ($(this).hasClass('active') == true) {
            $(this).parents('.search-button').find('.search-filter').removeClass('search-filter-open');
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
            $(this).parents('.search-button').find('.search-filter').addClass('search-filter-open');
        }
    });

    /* ------------------------------------------------------------------------ */
    /*  SEARCH AUTO COMPLETE
    /* ------------------------------------------------------------------------ */
    function auto_complete() {
        var search_input = $(".input-search");
        var auto_complete_box = $(".search-auto-complete");

        search_input.on('keyup', function() {
            var this_input = $(this);
            var value = this_input.val();
            var closest_search = this_input.closest("form").find(auto_complete_box);

            if (value.length > 3) {
                if (auto_complete_box.is(":hidden")) {
                    closest_search.fadeIn(0);
                }
            } else {

                closest_search.fadeOut(0);

            }
        });

        $(document).mouseup(function(e) {
            var input_plus_auto_complete = $('.input-search,.search-auto-complete');
            if (!input_plus_auto_complete.is(e.target) // if the target of the click isn't the container...
                &&
                input_plus_auto_complete.has(e.target).length === 0 // ... nor a descendant of the container
            ) {
                auto_complete_box.fadeOut(0);
            }
        });
    }
    auto_complete();

    /* ------------------------------------------------------------------------ */
    /*  half map elements size
    /* ------------------------------------------------------------------------ */
    function setSectionHeight() {
        var window_height = $(window).innerHeight();
        var sections_height =  window_height - header_area_height;

        if ($(window).width() >= 767){
            $('.half-map-left-wrap, .half-map-right-wrap').css('height', sections_height);
        } else {
            $('.map-on-right .half-map-right-wrap').css('height', sections_height);
            $('.map-on-right .half-map-left-wrap').css('height', 'auto');
            $('.map-on-left .half-map-right-wrap').css('height', sections_height);
            $('.map-on-left .half-map-left-wrap').css('height', 'auto');
        }
    }
    setSectionHeight();
    $win.on('resize', function() {
        setSectionHeight();
    });

    /* ------------------------------------------------------------------------ */
    /* transparent header
    /* ------------------------------------------------------------------------ */
    function setTransparentHeaderMarginBottom() {
        var desktop_transparent_header_height = $('.transparent-header .header-nav.hidden-sm').innerHeight();
        var mobile_transparent_header_height = $('.transparent-header .header-nav.hidden-md').innerHeight();

        if ($(window).width() >= 991){
            $('.transparent-header .header-nav.hidden-sm').css('margin-bottom', -desktop_transparent_header_height);
        } else {
            $('.transparent-header .header-nav.hidden-md').css('margin-bottom', -mobile_transparent_header_height);
        }
    }
    setTransparentHeaderMarginBottom();
    $win.on('resize', function() {
        setTransparentHeaderMarginBottom();
    });

    /* ------------------------------------------------------------------------ */
    /* fullscreen banner
    /* ------------------------------------------------------------------------ */
    function fullscreenBanner() {
        var window_height = $(window).innerHeight();
        var desktop_header_height = $('.header-nav.hidden-sm').innerHeight();
        var mobile_header_height = $('.header-nav.hidden-md').innerHeight();

        if ($(window).width() >= 767){
            //parallax  and vide banner
            $('.top-banner-wrap-fullscreen').css('height', window_height - desktop_header_height);
            $('.transparent-header + .top-banner-wrap-fullscreen').css('height', window_height);
            // property slider banner
            $('.top-banner-wrap-fullscreen .slick-list, .top-banner-wrap-fullscreen .header-slider-item').css('height', window_height - desktop_header_height);
            $('.transparent-header + .top-banner-wrap-fullscreen .slick-list, .transparent-header + .top-banner-wrap-fullscreen .header-slider-item').css('height', window_height);

        } else {
            //parallax  and vide banner
            $('.top-banner-wrap-fullscreen').css('height', window_height - mobile_header_height);
            $('.transparent-header + .top-banner-wrap-fullscreen').css('height', window_height);
            // property slider banner
            $('.top-banner-wrap-fullscreen .slick-list, .top-banner-wrap-fullscreen .header-slider-item').css('height', window_height - mobile_header_height);
            $('.transparent-header + .top-banner-wrap-fullscreen .slick-list, .transparent-header + .top-banner-wrap-fullscreen .header-slider-item').css('height', window_height);
        }
    }
    fullscreenBanner();
    $win.on('resize', function() {
        fullscreenBanner();
    });

    /* ------------------------------------------------------------------------ */
    /*  parallax
    /* ------------------------------------------------------------------------ */
    $(document).ready(function () {
        $('.parallax').parallaxBackground({
            parallaxBgPosition: "center center",
            parallaxBgRepeat: "no-repeat",
            parallaxBgSize: "cover",
            parallaxSpeed: "0.25",
        });
    });

    /* ------------------------------------------------------------------------ */
    /* mobile booking form overlay
    /* ------------------------------------------------------------------------ */
    $(document).ready(function() {
        $( "#trigger-overlay-booking-form, .overlay-booking-module-close" ).on('click', function(){
            $("#overlay-booking-module").toggleClass( "open" );
        });
    });


    /* ------------------------------------------------------------------------ */
    /* mobile search form overlay
    /* ------------------------------------------------------------------------ */
    $(document).ready(function() {
        $( ".main-search .search-banner-mobile, .search-banner-mobile, .half-map-search .search-banner-mobile, .overlay-search-module-close" ).on('click', function(){
            $("#overlay-search-advanced-module").toggleClass( "open" );
        });
    });

    /* ------------------------------------------------------------------------ */
    /* side menu
    /* ------------------------------------------------------------------------ */
    $(document).ready(function() {
        $('.side-nav-trigger').on('click', function() {
            $(this).toggleClass('active');
            $('.side-nav-active').toggleClass('side-nav-active-push-toright' );
            $('#side-nav-panel').toggleClass('side-nav-panel-open');
        });
    });

    /* ------------------------------------------------------------------------ */
    /* compare Listings
    /* ------------------------------------------------------------------------ */
    $(document).ready(function() {
        $('.compare-property-label').on('click', function() {
            $(this).toggleClass('active');
            $('.compare-property-active').addClass('compare-property-active-push-toleft' );
            $('#compare-property-panel').addClass('compare-property-panel-open');
        });
        $('.close-compare-panel').on('click', function() {
            $(this).toggleClass('active');
            $('.compare-property-active').removeClass('compare-property-active-push-toleft' );
            $('#compare-property-panel').removeClass('compare-property-panel-open');
        });
        var listings_compare = homeyGetCookie('homey_compare_listings');
        var limit_item_compare = 4;
        add_to_compare(compare_url, add_compare, remove_compare, compare_limit, listings_compare, limit_item_compare );
        remove_from_compare(listings_compare, add_compare, remove_compare);
    });


    /* ------------------------------------------------------------------------ */
    /* Code by Waqas
    /* ------------------------------------------------------------------------ */
    if($('#commentform').length > 0) {
        $('#commentform #submit').addClass('btn btn-primary');
    }

    if($('ul.comments-list').length > 0) {
        $('ul.comments-list ul').addClass('list-unstyled');
    }


    /* ------------------------------------------------------------------------ */
    /*  Date picker
     /* ------------------------------------------------------------------------ */
    if($('.input_date').length > 0) {
        $( ".input_date" ).datepicker();
    }
    if($('.search-date').length > 0) {
        $( ".search-date" ).datepicker();
    }

    /*-----------------------------------------------------------------------------------*/
    /* Calendar Next/Prev
    /*-----------------------------------------------------------------------------------*/
    function calendar_next_prev(main_div, acdiv, is_next, singleMonth) {

        $('.'+acdiv).on('click', function (e) {
            e.preventDefault();

            var next_prev_m = homey_calendar_months;
            var next_prev_m1 = homey_calendar_months-1;
            var next_prev_m2 = homey_calendar_months-2;

            if(singleMonth) {

                if(is_next) {
                    if (current_month < next_prev_m1) {
                        current_month = current_month + 1;
                    } else {
                        current_month = next_prev_m;
                    }
                } else {
                    if (current_month > 1) {
                        current_month = current_month - 1;
                    } else {
                        current_month = 1;
                    }
                }

                $('.'+main_div).hide();
                $('.'+main_div).each(function () {
                    var month   =   parseInt($(this).attr('data-month'), 10);
                    if (month === current_month) {
                        $(this).fadeIn();
                    }
                });

            } else {

                if(is_next) {
                    if (current_month2 < next_prev_m2) {
                        current_month2 = current_month2 + 1;
                    } else {
                        current_month2 = next_prev_m1;
                    }
                } else {
                    if (current_month2 > 3) {
                        current_month2 = current_month2 - 1;
                    } else {
                        current_month2 = 2;
                    }
                }

                if(is_next) {
                    $('.'+main_div).hide();
                    $('.'+main_div).each(function () {
                        var month   =   parseInt($(this).attr('data-month'), 10);
                        if (month === current_month2 || month === current_month2+1) {
                            $(this).fadeIn();
                        }
                    });
                } else {
                    $('.'+main_div).hide();
                    $('.'+main_div).each(function () {
                        var month   =   parseInt($(this).attr('data-month'), 10);
                        if (month === current_month2 || month === current_month2-1) {
                            $(this).fadeIn();
                        }
                    });
                }
            }


            if(singleMonth) {
                if(current_month == next_prev_m) {
                    $(this).addClass('disabled');
                } else {
                    $('.homey-next-month, .listing-cal-next, .search-cal-next').removeClass('disabled');
                }

                if(current_month == 1) {
                    $(this).addClass('disabled');
                } else {
                    $('.homey-prev-month, .listing-cal-prev, .search-cal-prev').removeClass('disabled');
                }
            } else {

                if(current_month2 == next_prev_m1) {
                    $(this).addClass('disabled');
                } else {
                    $('.homey-next-month, .listing-cal-next, .search-cal-next').removeClass('disabled');
                }

                if(current_month2 == 2) {
                    $(this).addClass('disabled');
                } else {
                    $('.homey-prev-month, .listing-cal-prev, .search-cal-prev').removeClass('disabled');
                }
            }



        });
    }

    calendar_next_prev('homey_month_wrap', 'homey-next-month', true, true);
    calendar_next_prev('homey_month_wrap', 'homey-prev-month', false, true);

    calendar_next_prev('single-listing-calendar-wrap', 'listing-cal-next', true, false);
    calendar_next_prev('single-listing-calendar-wrap', 'listing-cal-prev', false, false);

    calendar_next_prev('single-listing-hourly-calendar-wrap', 'listing-cal-next', true, true);
    calendar_next_prev('single-listing-hourly-calendar-wrap', 'listing-cal-prev', false, true);

    calendar_next_prev('main-search-calendar-wrap', 'search-cal-next', true, false);
    calendar_next_prev('main-search-calendar-wrap', 'search-cal-prev', false, false);

    calendar_next_prev('main-search-hourly-calendar-wrap', 'search-cal-next', true, true);
    calendar_next_prev('main-search-hourly-calendar-wrap', 'search-cal-prev', false, true);


    /* ------------------------------------------------------------------------ */
    /* search for banners
    /* ------------------------------------------------------------------------ */
    $(document).ready(function() {
        $(".search-banner input").on('focus', function() {
            $(this).prev("label").css("display", "block");
            $(this).addClass("on-focus");
        });

        $(".search-destination input").on('focus', function() {
            $('.search-destination .clear-input-btn').css("display", "block");
        });

        $('.clear-input-btn').on('click', function(e) {
            e.preventDefault();
            $('.search-destination label, .search-destination .clear-input-btn').css("display", "none");
            $('.search-calendar-main').removeClass("depart_active").addClass('arrive_active');
            focusedInput = 'arrive';
            $('.main-search-calendar-wrap ul li').removeClass('in-between to-day from-day selected');
            searchCalClick = 0;
            timestamp = 0;
            $('.search-destination input').removeClass("on-focus");
            $('.search-destination input').val('');
        });

        $(".search-destination input").on('focus', function() {
            $('.search-calendar').css("display", "none");
            $('.search-guests-wrap').css("display", "none");
        });

        $(".search-guests input").on('focus', function() {
            $('.single-listing-booking-calendar-js, .search-calendar-main, .search-hourly-calendar-main').css("display", "none");
        });

        $('.search-hours-range-js').on('click', function(){
            $('.search-calendar').css("display", "none");
            $('.search-guests-wrap').css("display", "none");
        });

        $('.btn-clear-calendar').on('click', function() {
            $('.search-date-range input').val('');
            $('.search-date-range label').css("display", "none");
            $('.search-date-range input').removeClass("on-focus");
        });

        $('.guest-apply-btn .btn').on('click', function() {
            $('.search-guests-wrap').css("display", "none");
        });

        /* ---------------------------------------------
        *  By Waqas
        * ----------------------------------------------*/
        $(".main-search-date-range-js input").on('focus', function() {
            $('.search-calendar-main').css("display", "block");
            $('.search-calendar-main').addClass("homey_show_calendar");
            $('.search-calendar-main').addClass("arrive_active");
            $('.search-guests-wrap-js').css("display", "none");
            focusedInput = $(this).attr('name');
            $('.search-calendar-main').removeClass('arrive_active depart_active').addClass(focusedInput+'_active');

            $('.search-hourly-calendar-main').css("display", "block");

        });

        var countTo = 0;
        var clearToVar;
        $("div").on('click', function() {
            countTo++;
            if(countTo == 1){
                var classNames = $(this).parent().attr('class');
                var mapSearchClassIndex = 0;
                if(typeof classNames != "undefined"){
                    mapSearchClassIndex = classNames.indexOf('map-search');
                }
                if (
                    !$("input[name='guest']").is(':focus') &&
                    !$("input[name='guests']").is(':focus') &&
                    mapSearchClassIndex == -1
                    && classNames.indexOf('search-guests') == -1
                    && classNames.indexOf('pets-calculator') == -1
                ) {
                    $('.search-guests-wrap-js').css("display", "none");
                    $('.single-form-guests-js').css("display", "none");
                }
            }

            clearToVar = setInterval(function(){
                countToReset();
            }, 1000);
        });
        function countToReset(){
            countTo = 0;
            clearInterval(clearToVar);
        }

        $(".search-guests-js input").on('focus', function() {
            $(this).prev("label").css("display", "block");
            $(this).addClass("on-focus");
            $('.search-guests-wrap-js').css("display", "block");
        });

        // half map search range
        $(".halfmap-search-date-range-js input").on('focus', function() {
            $('.search-calendar-main').css("display", "block");
            $('.search-calendar-main').addClass("arrive_active");
            $('.search-guests-wrap-js').css("display", "none");
            focusedInput = $(this).attr('name');
            $('.search-calendar-main').removeClass('arrive_active depart_active').addClass(focusedInput+'_active');

            $('.search-hourly-calendar-main').css("display", "block");

        });

        if( ! homey_is_mobile ) {
            var box = document.querySelector('.search-date-range input');
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.search-date-range input') && !e.target.closest('.search-calendar')) {
                    $('.search-calendar-main').css("display", "none");
                    //below line was commented by zahid, because on iPhone was not working
                    //$('.single-listing-booking-calendar-js').css("display", "none");
                }
            });
        }
    });

    /* ------------------------------------------------------------------------ */
    /*  availability calendar cells height
    /* ------------------------------------------------------------------------ */
    function setCalendarCellHeight() {
        var clearHeightSetter = setInterval(function() {
            var calendarCellWidth = $('.availability-section .search-calendar li').innerWidth();
            if(calendarCellWidth > 0){
                $('.availability-section .search-calendar li').css('height', calendarCellWidth);
                $('.availability-section .search-calendar li').css('line-height', calendarCellWidth + 'px' );
            }
            clearInterval(clearHeightSetter);
        }, 600);
    }

    setCalendarCellHeight();
    $win.on('resize', function() {
        setCalendarCellHeight();
    });

    /*-----------------------------------------------------------------------------------*/
    /* Search Calendar
     /*-----------------------------------------------------------------------------------*/

    /*function homey_timeStamp(str) {
        return new Date(str.replace(/^(\d{2}\-)(\d{2}\-)(\d{4})$/,
            '$2$1$3')).getTime();
    };*/

    function homey_timeStamp(str) {
        var myDate=str.split("-");
        var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
        return new Date(newDate).getTime();
    };

    $('.main-search-calendar-wrap ul li').on('click', function () {
        var $this = $(this);

        // do nothing is date is disabled
        if($this.hasClass('day-disabled')){
            return false;
        }

        searchCalClick += 1;
        var vl = $this.data('formatted-date');
        timestamp = $this.data('timestamp');

        // if modify days after selecting once
        if (focusedInput == 'depart' && timestamp > fromTimestamp) {
            $('.main-search-calendar-wrap ul').find('li.to-day').removeClass('selected')
                .siblings().removeClass('to-day in-between');

            searchCalClick = 2;
        }

        if (searchCalClick == 1) {
            fromTimestamp = timestamp;

            //day nodes
            $('.main-search-calendar-wrap ul li').removeClass('to-day from-day selected in-between');
            $this.addClass('from-day selected');
            // move caret
            $('.search-calendar').removeClass('arrive_active').addClass('depart_active');

            // set value and trigger focus event manully
            $('input[name="arrive"]').val(vl).triggerHandler('focus');
            $('input[name="depart"]').val('').triggerHandler('focus');

        } else if (searchCalClick == 2) {
            toTimestamp = timestamp;
            //day end node
            $this.addClass('to-day selected');

            $('.search-calendar').removeClass('depart_active').addClass('arrive_active');

            var arrive_val = $('input[name="arrive"]').val();
            arrive_val = homey_timeStamp(arrive_val);
            var depart_val = homey_timeStamp(vl);

            if (arrive_val >= depart_val) {
                fromTimestamp = timestamp;
                toTimestamp = 0;

                //day nodes
                $('.main-search-calendar-wrap ul li').removeClass('to-day from-day selected in-between');
                $this.addClass('from-day selected');

                // move caret
                $('.search-calendar').removeClass('arrive_active').addClass('depart_active');
                $('input[name="arrive"]').val(vl);
                searchCalClick = 1;
            } else {
                setInBetween(fromTimestamp, toTimestamp);
                $('input[name="depart"]').val(vl);
                $('.search-calendar').hide();
                $('.search-guests-wrap-js').css("display", "block");
                $('input[name="guest"]').triggerHandler('focus');
            }
        }
        if (searchCalClick == 2) {
            searchCalClick = 0;
        }

    });

    $('.main-search-calendar-wrap ul li').on('hover', function () {

        var ts = $(this).data('timestamp');
        if (searchCalClick == 1) {
            setInBetween(fromTimestamp, ts);
        }
    });
    /*
    * method to send in-between days
    * */
    function setInBetween(fromTime, toTime) {
        $('.main-search-calendar-wrap ul li').removeClass('in-between')
            .filter(function () {
                var currentTs = $(this).data('timestamp');
                return currentTs > fromTime && currentTs < toTime;
            }).addClass('in-between');
    }


    //Hourly search calendar pick date
    $('.main-search-hourly-calendar-wrap ul li').on('click', function () {
        var $this = $(this);

        // do nothing is date is disabled
        if($this.hasClass('day-disabled')){
            return false;
        }
        var vl = $this.data('formatted-date');

        // set value and trigger focus event manully
        $('input[name="arrive"]').val(vl);
        $('.search-calendar').hide();

    });

    // Guests
    var search_guests_and_pets = function() {
        $('.search_adult_plus').on('click', function(e) {
            e.preventDefault();
            var guests = parseInt($('#guests').val()) || 0;
            var adult_guest = parseInt($('.search_adult_guest').val());
            var child_guest = parseInt($('.search_child_guest').val());

            adult_guest++;
            $('.search_homey_adult').text(adult_guest);
            $('.search_adult_guest').val(adult_guest);

            var total_guests = adult_guest + child_guest;

            $('input[name="guest"]').val(total_guests);
        });

        $('.search_adult_minus').on('click', function(e) {
            e.preventDefault();
            var guests = parseInt($('#guests').val()) || 0;
            var adult_guest = parseInt($('.search_adult_guest').val());
            var child_guest = parseInt($('.search_child_guest').val());

            if (adult_guest == 0) return;
            adult_guest--;
            $('.search_homey_adult').text(adult_guest);
            $('.search_adult_guest').val(adult_guest);

            var total_guests = adult_guest + child_guest;
            $('input[name="guest"]').val(total_guests);

            $('.search_adult_plus').removeAttr("disabled");
            $('.search_child_plus').removeAttr("disabled");
        });

        $('.search_child_plus').on('click', function(e) {
            e.preventDefault();
            var guests = parseInt($('#guests').val());
            var child_guest = parseInt($('.search_child_guest').val());
            var adult_guest = parseInt($('.search_adult_guest').val());

            child_guest++;
            $('.search_homey_child').text(child_guest);
            $('.search_child_guest').val(child_guest);

            var total_guests = child_guest + adult_guest;

            $('input[name="guest"]').val(total_guests);

        });

        $('.search_child_minus').on('click', function(e) {
            e.preventDefault();
            var guests = parseInt($('#guests').val());
            var child_guest = parseInt($('.search_child_guest').val());
            var adult_guest = parseInt($('.search_adult_guest').val());

            if (child_guest == 0) return;
            child_guest--;
            $('.search_homey_child').text(child_guest);
            $('.search_child_guest').val(child_guest);

            var total_guests = child_guest + adult_guest;

            $('input[name="guest"]').val(total_guests);

            $('.search_adult_plus').removeAttr("disabled");
            $('.search_child_plus').removeAttr("disabled");

        });
    }
    search_guests_and_pets();

    /*-----------------------------------------------------------------------------------*/
    /* Listings SORTING
    /*-----------------------------------------------------------------------------------*/
    function insertParam(key, value) {
        key = encodeURI(key);
        value = encodeURI(value);

        // get querystring , remove (?) and covernt into array
        var qrp = "";
        console.log(' amazing'+  value);
        var variable_url = '';

        // if(value != 'x_price'){
            qrp = document.location.search.substr(1).split('&');
        //     alert('inn');
        // }else{
        //     var qrp_remove_qry = document.location.search.substr(document.location.search.indexOf('?'));
        //     variable_url = document.location.search.replace("sortby="+value, "");
        //     alert(variable_url);
        //     window.location.href = variable_url;
        //     alert(' outt ');
        //     return false;
        // }

        // get qrp array length
        var i = qrp.length;
        var j;
        while (i--) {
            //covert query strings into array for check key and value
            j = qrp[i].split('=');

            // if find key and value then join
            if (j[0] == key) {
                j[1] = value;
                qrp[i] = j.join('=');
                break;
            }
        }

        if (i < 0) {
            qrp[qrp.length] = [key, value].join('=');
        }
        // reload the page
        document.location.search = qrp.join('&');

    }

    $('#sort_listings').on('change', function() {
        var key = 'sortby';
        var value = $(this).val();
        insertParam( key, value );
    });

    function homey_UTC_addDays(date, days) {
        //homeyDate => js date then add one day
        //js date => homneyDate

        var result = new Date(date);

        var now_utc = new Date(result.getUTCFullYear(), result.getUTCMonth(), result.getUTCDate(),  result.getUTCHours(), result.getUTCMinutes(), result.getUTCSeconds());
        var new_day=parseFloat(result.getUTCDate())+1 + parseFloat(days);
        now_utc.setDate(new_day);
        return now_utc;
    }

    function homey_period_checkin_checkout(start_date, end_date) {
        var today, prev_date,selected_date,selected_min_days,who_is;
        today = new Date();

        var check_in_date = $('#'+start_date);


        check_in_date.datepicker({
            dateFormat : homey_date_format,
            minDate: today,
        });

        check_in_date.change(function () {

            prev_date = jQuery('#'+start_date).val();// we have to manipulate date format for javascript
            /*prev_date = new Date(jQuery('#'+start_date).val());

            selected_min_days   =  1;

            if (selected_min_days>0){
                prev_date =homey_UTC_addDays( jQuery('#'+start_date).val(),selected_min_days-1 );
            }else{
                prev_date =homey_UTC_addDays( jQuery('#'+start_date).val(),0 );
            }*/

            jQuery("#"+end_date).val('');
            jQuery("#"+end_date).removeAttr('disabled');
            jQuery("#"+end_date).datepicker("destroy");
            jQuery("#"+end_date).datepicker({
                dateFormat : homey_date_format,
                minDate: prev_date,
            });

        });


    }
    homey_period_checkin_checkout('period_start_date', 'period_end_date');
    homey_period_checkin_checkout('cus_start_date', 'cus_end_date');



})(jQuery); // End Document ready

/* ------------------------------------------------------------------------ */
/*  Homey Cookie
/* ------------------------------------------------------------------------ */
function homeySetCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
};

function homeyGetCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
};

/*--------------------------------------------------------------------------
*   Add or remove favorites
* -------------------------------------------------------------------------*/
function homey_init_add_favorite(ajaxurl, userID, is_singular_listing) {
    jQuery(".add_fav").on('click', function (e) {
        e.preventDefault();
        var curnt = jQuery(this);
        var listID = jQuery(this).attr('data-listid');
        add_to_favorite( ajaxurl, listID, curnt, userID, is_singular_listing );
        return false;
    });
}

function homey_init_remove_favorite(ajaxurl, userID, is_singular_listing) {
    jQuery(".remove_fav").on('click', function () {
        var curnt = jQuery(this);
        var listID = jQuery(this).attr('data-listid');
        add_to_favorite( ajaxurl, listID, curnt, userID, is_singular_listing );
        var itemWrap = curnt.parents('tr').remove();
    });
}

function add_to_favorite( ajaxurl, listID, curnt, userID, is_singular_listing ) {
    if( parseInt( userID, 10 ) === 0 || userID == undefined) {
        jQuery('#modal-login').modal('show');
    } else {
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: {
                'action': 'homey_add_to_favorite',
                'listing_id': listID
            },
            beforeSend: function( ) {
                curnt.append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            },
            success: function( data ) {
                if( data.added ) {
                    curnt.children('i').remove();
                    curnt.text(data.response);
                    if(is_singular_listing == 'yes') {
                        curnt.prepend('<i class="fa fa-heart" aria-hidden="true"></i> ');
                    }
                } else {
                    curnt.children('i').remove();
                    curnt.text(data.response);
                    if(is_singular_listing == 'yes') {
                        curnt.prepend('<i class="fa fa-heart-o" aria-hidden="true"></i> ');
                    }
                }

            },
            complete: function(){

            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
            }
        });
    } // End else
}


function add_to_compare(compare_url, add_compare, remove_compare, compare_limit, listings_compare, limit_item_compare) {
    jQuery('a.compare-btn').attr('href', compare_url + '?ids=' + homeyGetCookie('homey_compare_listings'));

    var listings_compare = homeyGetCookie('homey_compare_listings');

    if (listings_compare.length > 0) {
        jQuery('.compare-property-label').fadeIn(1000);
    }

    if(listings_compare && listings_compare.length){
        listings_compare = listings_compare.split(',');
        if(listings_compare.length){
            for(var i = 0 ; i < listings_compare.length; i++){
                jQuery( '.homey_compare[data-listing_id="'+listings_compare[i]+'"]' ).text(remove_compare);
            }
            jQuery('.compare-property-label').find('.compare-count').html(listings_compare.length);
        }
    }else{
        listings_compare = [];
    }


    jQuery( '.homey_compare' ).on('click', function(e) {
        e.preventDefault();

        var listings_compare = homeyGetCookie('homey_compare_listings');

        if(listings_compare && listings_compare.length) {
            listings_compare = listings_compare.split(',');
        } else {
            listings_compare = [];
        }

        var listing_id = jQuery( this ).data( 'listing_id' );
        var index = listings_compare.indexOf( listing_id.toString() );
        var image_div = jQuery(this).parents('.item-wrap');
        var thumb_url = image_div.find('.item-media-thumb img').attr('src');


        if( index == -1 ){
            if(listings_compare.length >= limit_item_compare){
                alert(compare_limit);
            }else{

                jQuery('.compare-wrap').append('<div class="compare-item remove-'+listing_id+'"><a href="" class="remove-compare remove-icon" data-listing_id="'+listing_id+'"><i class="fa fa-trash-o" aria-hidden="true"></i></a><img class="img-responsive" src="'+thumb_url+'" width="450" height="300" alt="Thumb"></div>');

                jQuery(this).text(remove_compare);
                listings_compare.push(listing_id.toString());
                homeySetCookie('homey_compare_listings', listings_compare.join(','), 30);
                jQuery('.compare-property-label').find('.compare-count').html(listings_compare.length);
                jQuery('a.compare-btn').attr('href', compare_url + '?ids=' + homeyGetCookie('homey_compare_listings'));
                jQuery('.compare-property-label').fadeIn(1000);
                jQuery(this).toggleClass('active');
                jQuery('.compare-property-active').addClass('compare-property-active-push-toleft' );
                jQuery('#compare-property-panel').addClass('compare-property-panel-open');

                remove_from_compare(listings_compare, add_compare, remove_compare);
            }
        }else{

            jQuery('div.remove-'+listing_id).remove();
            jQuery(this).text(add_compare);
            listings_compare.splice(index, 1);
            homeySetCookie('homey_compare_listings', listings_compare.join(','), 30);
            jQuery('.compare-property-label').find('.compare-count').html(listings_compare.length);
            jQuery('a.compare-btn').attr('href', compare_url + '?ids=' + homeyGetCookie('homey_compare_listings'));

            if (listings_compare.length > 0) {
                jQuery('.compare-property-label').fadeIn(1000);
                jQuery(this).toggleClass('active');
                jQuery('.compare-property-active').addClass('compare-property-active-push-toleft' );
                jQuery('#compare-property-panel').addClass('compare-property-panel-open');
            } else {
                jQuery('.compare-property-label').fadeOut(1000);
            }
        }
        return false;

    });
}


function remove_from_compare(listings_compare, add_compare, remove_compare) {
    jQuery('.remove-compare').on('click', function(e){
        e.preventDefault();

        if(typeof listings_compare == 'object') {

            listings_compare = listings_compare.toString();
        }

        if(listings_compare && listings_compare.length){
            listings_compare = listings_compare.split(',');
            if(listings_compare.length){
                for(var i = 0 ; i < listings_compare.length; i++){
                    jQuery( '.homey_compare[data-listing_id="'+listings_compare[i]+'"]' ).text(remove_compare);
                }
                jQuery('.compare-property-label').find('.compare-count').html(listings_compare.length);
            }
        }else{
            listings_compare = [];
        }

        var listing_id = jQuery( this ).data( 'listing_id' );
        var index = listings_compare.indexOf( listing_id.toString() );
        listings_compare.splice(index, 1);
        homeySetCookie('homey_compare_listings', listings_compare.join(','), 30);
        jQuery('.compare-property-label').find('.compare-count').html(listings_compare.length);

        jQuery('.compare-'+listing_id).text(add_compare);
        jQuery(this).parents('.compare-item').remove();
    });
}

var clearVidPlayCounter = setInterval(function(){
    if(typeof jQuery('#video-background').data('vide') != "undefined"){
        jQuery('#video-background').data('vide').getVideoObject().play();
        console.log('just try to play on safari.');
        clearInterval(clearVidPlayCounter);
    }
}, 500);

jQuery('.delete_user_account').click(function(){
    jQuery("#delete_account_warning").css("display", "block");
    jQuery("#delete_account_warning").css("margin-left", "30%");
});

jQuery('#hide_delete_confirmation_wrap').click(function(){
    jQuery("#delete_account_warning").css("display", "none");
});

