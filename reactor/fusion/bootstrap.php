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

switch (DEVSTAGE)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;
	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;
	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}
$fusion_path = REACTORPATH.'fusion';
if (($_temp = realpath($fusion_path)) !== FALSE)
{
        $fusion_path = $_temp.DIRECTORY_SEPARATOR;
}
else
{
        // Ensure there's a trailing slash
        $fusion_path = strtr(
                rtrim($fusion_path, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
        ).DIRECTORY_SEPARATOR;
}

// Is the fusion path correct?
if ( ! is_dir($fusion_path))
{
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your Fusion folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
        exit(3); // EXIT_CONFIG
}
define('BASEPATH', $fusion_path);

$flux_path = BASEPATH.'libraries/drivers';
// The path to the "flux" directory
if (is_dir($flux_path))
{
        if (($_temp = realpath($flux_path)) !== FALSE)
        {
                $flux_path = $_temp;
        }
        else
        {
                $flux_path = strtr(
                        rtrim($flux_path, '/\\'),
                        '/\\',
                        DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
                );
        }
}
elseif (is_dir(BASEPATH.$flux_path.DIRECTORY_SEPARATOR))
{
        $flux_path = BASEPATH.strtr(
                trim($flux_path, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
        );
}
else
{
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your Flux folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
        exit(3); // EXIT_CONFIG
}
define('MODEELVALUE','EMPTY');
define('APPPATH', $flux_path.DIRECTORY_SEPARATOR);
$turbine_path = APPPATH.'templates';
// The path to the "turbine" directory
if ( ! isset($turbine_path[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
{
        $turbine_path = APPPATH.'views';
}
elseif (is_dir($turbine_path))
{
        if (($_temp = realpath($turbine_path)) !== FALSE)
        {
                $turbine_path = $_temp;
        }
        else
        {
                $turbine_path = strtr(
                        rtrim($turbine_path, '/\\'),
                        '/\\',
                        DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
                );
        }
}
elseif (is_dir(APPPATH.$turbine_path.DIRECTORY_SEPARATOR))
{
        $turbine_path = APPPATH.strtr(
                trim($turbine_path, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
        );
}
else
{
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
        exit(3); // EXIT_CONFIG
}
define('SELF', SELFINFO);
define('FCPATH', SELFCTRLPATH);
define('SYSDIR', basename(BASEPATH));
define('VIEWPATH', $turbine_path.DIRECTORY_SEPARATOR);
define("ENVIRONMENT", DEVSTAGE);
require_once BASEPATH.'core/Reactor.php';