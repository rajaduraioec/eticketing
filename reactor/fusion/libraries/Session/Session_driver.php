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
 * Reactor Session Driver Class
 *
 * @package	Reactor
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Increatech Dev Team
 */
abstract class R_Session_driver implements SessionHandlerInterface {

	protected $_config;

	/**
	 * Data fingerprint
	 *
	 * @var	bool
	 */
	protected $_fingerprint;

	/**
	 * Lock placeholder
	 *
	 * @var	mixed
	 */
	protected $_lock = FALSE;

	/**
	 * Read session ID
	 *
	 * Used to detect session_regenerate_id() calls because PHP only calls
	 * write() after regenerating the ID.
	 *
	 * @var	string
	 */
	protected $_session_id;

	/**
	 * Success and failure return values
	 *
	 * Necessary due to a bug in all PHP 5 versions where return values
	 * from userspace handlers are not handled properly. PHP 7 fixes the
	 * bug, so we need to return different values depending on the version.
	 *
	 * @see	https://wiki.php.net/rfc/session.user.return-value
	 * @var	mixed
	 */
	protected $_success, $_failure;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		$this->_config =& $params;

		if (is_php('7'))
		{
			$this->_success = TRUE;
			$this->_failure = FALSE;
		}
		else
		{
			$this->_success = 0;
			$this->_failure = -1;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Cookie destroy
	 *
	 * Internal method to force removal of a cookie by the client
	 * when session_destroy() is called.
	 *
	 * @return	bool
	 */
	protected function _cookie_destroy()
	{
		return setcookie(
			$this->_config['cookie_name'],
			NULL,
			1,
			$this->_config['cookie_path'],
			$this->_config['cookie_domain'],
			$this->_config['cookie_secure'],
			TRUE
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock
	 *
	 * A dummy method allowing drivers with no locking functionality
	 * (databases other than PostgreSQL and MySQL) to act as if they
	 * do acquire a lock.
	 *
	 * @param	string	$session_id
	 * @return	bool
	 */
	protected function _get_lock($session_id)
	{
		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock
	 *
	 * @return	bool
	 */
	protected function _release_lock()
	{
		if ($this->_lock)
		{
			$this->_lock = FALSE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Fail
	 *
	 * Drivers other than the 'files' one don't (need to) use the
	 * session.save_path INI setting, but that leads to confusing
	 * error messages emitted by PHP when open() or write() fail,
	 * as the message contains session.save_path ...
	 * To work around the problem, the drivers will call this method
	 * so that the INI is set just in time for the error message to
	 * be properly generated.
	 *
	 * @return	mixed
	 */
	protected function _fail()
	{
		ini_set('session.save_path', config_item('sess_save_path'));
		return $this->_failure;
	}
}
