
/* =============
 * @author	Increatech Dev Team
 * @copyright	Copyright (c) 2013 - 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link	https://increatech.com
============= */
jQuery(document).ajaxStart(function() { Pace.restart(); });jQuery(document).ready(function () {livestatus();laststatus();setInterval(function(){livestatus();}, 120000);setInterval(function(){ laststatus();}, 600000);});function livestatus(){jQuery.ajax({url : baseurl+'/ajax_livestatus' ,type: "POST",dataType: "JSON",success: function(response){if(response.status){if(typeof response.content!== 'undefined'){jQuery("#livedata").html(response.content);}if(typeof response.timestamp!== 'undefined'){jQuery("#lvrefreshtime").html(response.timestamp);}}}});return;}function laststatus(){jQuery.ajax({url : baseurl+'/ajax_laststatus' ,type: "POST",dataType: "JSON",success: function(response){if(response.status){if(typeof response.content!== 'undefined'){jQuery("#lastdata").html(response.content);}if(typeof response.timestamp!== 'undefined'){jQuery("#larefreshtime").html(response.timestamp);}}}});return;}