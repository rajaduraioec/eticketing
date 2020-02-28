
/* =============
 * @author	Increatech Dev Team
 * @copyright	Copyright (c) 2013 - 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link	https://increatech.com
============= */

(function ($) {'use strict';function initNavbar() {$('.navbar-toggle').on('click', function (event) {$(this).toggleClass('open');$('#navigation').slideToggle(400);});$('.navigation-menu>li').slice(-2).addClass('last-elements');$('.navigation-menu li.has-submenu a[href="#"]').on('click', function (e) {if ($(window).width() < 992) {e.preventDefault();$(this).parent('li').toggleClass('open').find('.submenu:first').toggleClass('open');}});}function initScrollbar() {$('.slimscroll-noti').slimScroll({height: '230px',position: 'right',size: "5px",color: '#98a6ad',wheelStep: 10});}function initMenuItem() {$(".navigation-menu a").each(function () {if (this.href == window.location.href) {$(this).parent().addClass("active");$(this).parent().parent().parent().addClass("active");$(this).parent().parent().parent().parent().parent().addClass("active");}});}function init() {initNavbar();initScrollbar();initMenuItem();}init();})(jQuery);