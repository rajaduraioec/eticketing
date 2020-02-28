
/* =============
 * @author	Increatech Dev Team
 * @copyright	Copyright (c) 2013 - 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link	https://increatech.com
============= */
jQuery(document).ajaxStart(function() { Pace.restart(); });function track(){var data={range: $('#buses').val(),depot: $('#depot').val()};jQuery.ajax({url : baseurl+'/ajax_response' ,type: "POST",data: data,dataType: "JSON",success: function(response){if(response.status){if(typeof response.content!== 'undefined'){jQuery("#reportdata").html(response.content);}}}});return;}