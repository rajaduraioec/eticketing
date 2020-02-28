<?php
/**
 * Reactor Framework
 *
 * Copyright (c) 2014 - 2017, Increatech Business Solution Pvt Ltd, India
 * 
 * New BSD License
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this 
 * list of conditions and the following disclaimer. Redistributions in binary 
 * form must reproduce the above copyright notice, this list of conditions and 
 * the following disclaimer in the documentation and/or other materials provided 
 * with the distribution. Neither the name of Reactor or INCREATECH BUSINESS 
 * SOLUTION PVT LTD, nor the names of its contributors may be used to endorse 
 * or promote products derived from this software without specific prior written 
 * permission. 
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED WARRANTIES, INCLUDING, 
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
 * FOR A PARTICULAR PURPOSE ARE NONINFRINGEMENT. IN NO EVENT SHALL THE COPYRIGHT 
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package	Reactor Framework
 * @author	Increatech Dev Team
 * @copyright	Copyright (c) 2013 - 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link	https://increatech.com
 * @since	Version 1.0.0
 * @filesource
 */
/*
 * --------------------------------------------------------------------
 * SET YOUR TIMEZONE
 * --------------------------------------------------------------------
 *
 * Find your timezone here
 * http://php.net/manual/en/timezones.php
 */
	$timezone = "Asia/Kolkata";
	if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
	define('TIMEZONE', $timezone);

/*
 *---------------------------------------------------------------
 * DEVELOPMENT STAGE
 *---------------------------------------------------------------
 *
 *    development/testing/production
 *
 */

define("DEVSTAGE", "development");
define('RAPPVERSION','1.0.14');

// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------
/*
 * ---------------------------------------------------------------
 *  Resolve the Fusion path for increased reliability
 * ---------------------------------------------------------------
 */

// Set the current directory correctly for CLI requests
if (defined('STDIN'))
{
        chdir(dirname(__FILE__));
}
$reactor_path='reactor';
if (($_temp = realpath($reactor_path)) !== FALSE)
{
        $reactor_path = $_temp.DIRECTORY_SEPARATOR;
}
else
{
        // Ensure there's a trailing slash
        $reactor_path = strtr(
                rtrim($reactor_path, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
        ).DIRECTORY_SEPARATOR;
}

// Is the fusion path correct?
if ( ! is_dir($reactor_path))
{
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your Reactor folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
        exit(3); // EXIT_CONFIG
}


define('REACTORPATH', $reactor_path);
define('SELFINFO', pathinfo(__FILE__, PATHINFO_BASENAME));
define('SELFCTRLPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once REACTORPATH.'fusion/bootstrap.php';
