
/* =============
 * @author	Increatech Dev Team
 * @copyright	Copyright (c) 2013 - 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link	https://increatech.com
============= */

function modalinit(){jQuery(function ($) {$('.simple-ajax-modal').magnificPopup({type: 'ajax',modal: true});});return;}function validateme(url,formid){var formvalid;jQuery(function ($) {$('#'+formid).validate();$("#modal-submit").prop("disabled",true);formvalid=$('#'+formid).valid();});if(formvalid){ajaxmodalpost(url);}else{jQuery(function ($) { $("#modal-submit").prop("disabled",false);});}}function ajaxmodalpost(url){jQuery.ajax({url : url ,type: "POST",data: jQuery('#modal-form').serialize(),dataType: "JSON",success: function(response){if(response.status){if(typeof response.content!= 'undefined'){jQuery("div.modal-ajaxcontent-block").replaceWith(response.content);}if(response.tablereload){if(typeof datable!== 'undefined'){reload_table();}}}else{if(typeof response.content!= 'undefined'){jQuery("div.modal-ajaxcontent-block").replaceWith(response.content);}}}});}