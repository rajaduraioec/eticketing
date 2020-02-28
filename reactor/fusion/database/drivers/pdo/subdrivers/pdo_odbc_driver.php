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
 * PDO ODBC Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @package		Reactor
 * @subpackage	Drivers
 * @category	Database
 * @author		Increatech Dev Team
 */
class R_DB_pdo_odbc_driver extends R_DB_pdo_driver {

	/**
	 * Sub-driver
	 *
	 * @var	string
	 */
	public $subdriver = 'odbc';

	/**
	 * Database schema
	 *
	 * @var	string
	 */
	public $schema = 'public';

	// --------------------------------------------------------------------

	/**
	 * Identifier escape character
	 *
	 * Must be empty for ODBC.
	 *
	 * @var	string
	 */
	protected $_escape_char = '';

	/**
	 * ESCAPE statement string
	 *
	 * @var	string
	 */
	protected $_like_escape_str = " {escape '%s'} ";

	/**
	 * ORDER BY random keyword
	 *
	 * @var	array
	 */
	protected $_random_keyword = array('RND()', 'RND(%d)');

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Builds the DSN if not already set.
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		if (empty($this->dsn))
		{
			$this->dsn = 'odbc:';

			// Pre-defined DSN
			if (empty($this->hostname) && empty($this->HOSTNAME) && empty($this->port) && empty($this->PORT))
			{
				if (isset($this->DSN))
				{
					$this->dsn .= 'DSN='.$this->DSN;
				}
				elseif ( ! empty($this->database))
				{
					$this->dsn .= 'DSN='.$this->database;
				}

				return;
			}

			// If the DSN is not pre-configured - try to build an IBM DB2 connection string
			$this->dsn .= 'DRIVER='.(isset($this->DRIVER) ? '{'.$this->DRIVER.'}' : '{IBM DB2 ODBC DRIVER}').';';

			if (isset($this->DATABASE))
			{
				$this->dsn .= 'DATABASE='.$this->DATABASE.';';
			}
			elseif ( ! empty($this->database))
			{
				$this->dsn .= 'DATABASE='.$this->database.';';
			}

			if (isset($this->HOSTNAME))
			{
				$this->dsn .= 'HOSTNAME='.$this->HOSTNAME.';';
			}
			else
			{
				$this->dsn .= 'HOSTNAME='.(empty($this->hostname) ? '127.0.0.1;' : $this->hostname.';');
			}

			if (isset($this->PORT))
			{
				$this->dsn .= 'PORT='.$this->port.';';
			}
			elseif ( ! empty($this->port))
			{
				$this->dsn .= ';PORT='.$this->port.';';
			}

			$this->dsn .= 'PROTOCOL='.(isset($this->PROTOCOL) ? $this->PROTOCOL.';' : 'TCPIP;');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Platform-dependent string escape
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _escape_str($str)
	{
		$this->display_error('db_unsupported_feature');
	}

	// --------------------------------------------------------------------

	/**
	 * Determines if a query is a "write" type.
	 *
	 * @param	string	An SQL query string
	 * @return	bool
	 */
	public function is_write_type($sql)
	{
		if (preg_match('#^(INSERT|UPDATE).*RETURNING\s.+(\,\s?.+)*$#is', $sql))
		{
			return FALSE;
		}

		return parent::is_write_type($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool	$prefix_limit
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		$sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = '".$this->schema."'";

		if ($prefix_limit !== FALSE && $this->dbprefix !== '')
		{
			return $sql." AND table_name LIKE '".$this->escape_like_str($this->dbprefix)."%' "
				.sprintf($this->_like_escape_str, $this->_like_escape_chr);
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	$table
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		return 'SELECT column_name FROM information_schema.columns WHERE table_name = '.$this->escape($table);
	}
}
