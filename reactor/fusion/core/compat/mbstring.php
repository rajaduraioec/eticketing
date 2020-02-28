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
defined('RAPPVERSION') OR exit('No direct script access allowed');

/**
 * PHP ext/mbstring compatibility package
 *
 * @package		Reactor
 * @subpackage	Reactor
 * @category	Compatibility
 * @link		http://php.net/mbstring
 */

// ------------------------------------------------------------------------

if (MB_ENABLED === TRUE)
{
	return;
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_strlen'))
{
	/**
	 * mb_strlen()
	 *
	 * WARNING: This function WILL fall-back to strlen()
	 * if iconv is not available!
	 *
	 * @link	http://php.net/mb_strlen
	 * @param	string	$str
	 * @param	string	$encoding
	 * @return	int
	 */
	function mb_strlen($str, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			return iconv_strlen($str, isset($encoding) ? $encoding : config_item('charset'));
		}

		log_message('debug', 'Compatibility (mbstring): iconv_strlen() is not available, falling back to strlen().');
		return strlen($str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_strpos'))
{
	/**
	 * mb_strpos()
	 *
	 * WARNING: This function WILL fall-back to strpos()
	 * if iconv is not available!
	 *
	 * @link	http://php.net/mb_strpos
	 * @param	string	$haystack
	 * @param	string	$needle
	 * @param	int	$offset
	 * @param	string	$encoding
	 * @return	mixed
	 */
	function mb_strpos($haystack, $needle, $offset = 0, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			return iconv_strpos($haystack, $needle, $offset, isset($encoding) ? $encoding : config_item('charset'));
		}

		log_message('debug', 'Compatibility (mbstring): iconv_strpos() is not available, falling back to strpos().');
		return strpos($haystack, $needle, $offset);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mb_substr'))
{
	/**
	 * mb_substr()
	 *
	 * WARNING: This function WILL fall-back to substr()
	 * if iconv is not available.
	 *
	 * @link	http://php.net/mb_substr
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int 	$length
	 * @param	string	$encoding
	 * @return	string
	 */
	function mb_substr($str, $start, $length = NULL, $encoding = NULL)
	{
		if (ICONV_ENABLED === TRUE)
		{
			isset($encoding) OR $encoding = config_item('charset');
			return iconv_substr(
				$str,
				$start,
				isset($length) ? $length : iconv_strlen($str, $encoding), // NULL doesn't work
				$encoding
			);
		}

		log_message('debug', 'Compatibility (mbstring): iconv_substr() is not available, falling back to substr().');
		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}
