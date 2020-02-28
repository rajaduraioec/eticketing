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
 * Database Cache Class
 *
 * @category	Database
 * @author		Increatech Dev Team
 */
class R_DB_Cache {

	/**
	 * R Singleton
	 *
	 * @var	object
	 */
	public $R;

	/**
	 * Database object
	 *
	 * Allows passing of DB object so that multiple database connections
	 * and returned DB objects can be supported.
	 *
	 * @var	object
	 */
	public $db;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param	object	&$db
	 * @return	void
	 */
	public function __construct(&$db)
	{
		// Assign the main R object to $this->R and load the file helper since we use it a lot
		$this->R =& get_instance();
		$this->db =& $db;
		$this->R->load->helper('file');

		$this->check_path();
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache Directory Path
	 *
	 * @param	string	$path	Path to the cache directory
	 * @return	bool
	 */
	public function check_path($path = '')
	{
		if ($path === '')
		{
			if ($this->db->cachedir === '')
			{
				return $this->db->cache_off();
			}

			$path = $this->db->cachedir;
		}

		// Add a trailing slash to the path if needed
		$path = realpath($path)
			? rtrim(realpath($path), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
			: rtrim($path, '/').'/';

		if ( ! is_dir($path))
		{
			log_message('debug', 'DB cache path error: '.$path);

			// If the path is wrong we'll turn off caching
			return $this->db->cache_off();
		}

		if ( ! is_really_writable($path))
		{
			log_message('debug', 'DB cache dir not writable: '.$path);

			// If the path is not really writable we'll turn off caching
			return $this->db->cache_off();
		}

		$this->db->cachedir = $path;
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieve a cached query
	 *
	 * The URI being requested will become the name of the cache sub-folder.
	 * An MD5 hash of the SQL statement will become the cache file name.
	 *
	 * @param	string	$sql
	 * @return	string
	 */
	public function read($sql)
	{
		$segment_one = ($this->R->uri->segment(1) == FALSE) ? 'default' : $this->R->uri->segment(1);
		$segment_two = ($this->R->uri->segment(2) == FALSE) ? 'index' : $this->R->uri->segment(2);
		$filepath = $this->db->cachedir.$segment_one.'+'.$segment_two.'/'.md5($sql);

		if ( ! is_file($filepath) OR FALSE === ($cachedata = file_get_contents($filepath)))
		{
			return FALSE;
		}

		return unserialize($cachedata);
	}

	// --------------------------------------------------------------------

	/**
	 * Write a query to a cache file
	 *
	 * @param	string	$sql
	 * @param	object	$object
	 * @return	bool
	 */
	public function write($sql, $object)
	{
		$segment_one = ($this->R->uri->segment(1) == FALSE) ? 'default' : $this->R->uri->segment(1);
		$segment_two = ($this->R->uri->segment(2) == FALSE) ? 'index' : $this->R->uri->segment(2);
		$dir_path = $this->db->cachedir.$segment_one.'+'.$segment_two.'/';
		$filename = md5($sql);

		if ( ! is_dir($dir_path) && ! @mkdir($dir_path, 0750))
		{
			return FALSE;
		}

		if (write_file($dir_path.$filename, serialize($object)) === FALSE)
		{
			return FALSE;
		}

		chmod($dir_path.$filename, 0640);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete cache files within a particular directory
	 *
	 * @param	string	$segment_one
	 * @param	string	$segment_two
	 * @return	void
	 */
	public function delete($segment_one = '', $segment_two = '')
	{
		if ($segment_one === '')
		{
			$segment_one  = ($this->R->uri->segment(1) == FALSE) ? 'default' : $this->R->uri->segment(1);
		}

		if ($segment_two === '')
		{
			$segment_two = ($this->R->uri->segment(2) == FALSE) ? 'index' : $this->R->uri->segment(2);
		}

		$dir_path = $this->db->cachedir.$segment_one.'+'.$segment_two.'/';
		delete_files($dir_path, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete all existing cache files
	 *
	 * @return	void
	 */
	public function delete_all()
	{
		delete_files($this->db->cachedir, TRUE, TRUE);
	}

}
