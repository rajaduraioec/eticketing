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
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reactor Security Helpers
 *
 * @package		Reactor
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Increatech Dev Team
 */

// ------------------------------------------------------------------------

if ( ! function_exists('xss_clean'))
{
	/**
	 * XSS Filtering
	 *
	 * @param	string
	 * @param	bool	whether or not the content is an image file
	 * @return	string
	 */
	function xss_clean($str, $is_image = FALSE)
	{
		return get_instance()->security->xss_clean($str, $is_image);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('sanitize_filename'))
{
	/**
	 * Sanitize Filename
	 *
	 * @param	string
	 * @return	string
	 */
	function sanitize_filename($filename)
	{
		return get_instance()->security->sanitize_filename($filename);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('do_hash'))
{
	/**
	 * Hash encode a string
	 *
	 * @todo	Remove in version 3.1+.
	 * @deprecated	3.0.0	Use PHP's native hash() instead.
	 * @param	string	$str
	 * @param	string	$type = 'sha1'
	 * @return	string
	 */
	function do_hash($str, $type = 'sha1')
	{
		if ( ! in_array(strtolower($type), hash_algos()))
		{
			$type = 'md5';
		}

		return hash($type, $str);
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('furnaceme')){
	/**
	 * Hash encode a string
	 *
	 * @todo	Remove in version 3.1+.
	 * @deprecated	3.0.0	Use PHP's native hash() instead.
	 * @param	string	$str
	 * @param	string	$type = 'sha1'
	 * @return	string
	 */
	function furnaceme($s='',$m='d'){
		$r=& get_instance();
		if($r->config->item('activationcode')){
			$r->encryption->initialize(array(
			'cipher' => 'aes-256','mode' => 'ctr',
			'key' => hex2bin($r->config->item('activationcode'))));
			switch ($m) {
				case 'e':
					return $r->encryption->encrypt($s);
					break;
				case 'd':
					return $r->encryption->decrypt($s);
					break;
				default:
					return false;
					break;
			}
		}
		return false;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('strip_image_tags'))
{
	/**
	 * Strip Image Tags
	 *
	 * @param	string
	 * @return	string
	 */
	function strip_image_tags($str)
	{
		return get_instance()->security->strip_image_tags($str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('encode_php_tags'))
{
	/**
	 * Convert PHP tags to entities
	 *
	 * @param	string
	 * @return	string
	 */
	function encode_php_tags($str)
	{
		return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
	}
}
