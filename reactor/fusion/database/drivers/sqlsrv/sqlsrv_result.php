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
 * SQLSRV Result Class
 *
 * This class extends the parent result class: R_DB_result
 *
 * @category	Database
 * @author		Increatech Dev Team
 */
class R_DB_sqlsrv_result extends R_DB_result {

	/**
	 * Scrollable flag
	 *
	 * @var	mixed
	 */
	public $scrollable;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param	object	$driver_object
	 * @return	void
	 */
	public function __construct(&$driver_object)
	{
		parent::__construct($driver_object);

		$this->scrollable = $driver_object->scrollable;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		// sqlsrv_num_rows() doesn't work with the FORWARD and DYNAMIC cursors (FALSE is the same as FORWARD)
		if ( ! in_array($this->scrollable, array(FALSE, SQLSRV_CURSOR_FORWARD, SQLSRV_CURSOR_DYNAMIC), TRUE))
		{
			return parent::num_rows();
		}

		return is_int($this->num_rows)
			? $this->num_rows
			: $this->num_rows = sqlsrv_num_rows($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return @sqlsrv_num_fields($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Field Names
	 *
	 * Generates an array of column names
	 *
	 * @return	array
	 */
	public function list_fields()
	{
		$field_names = array();
		foreach (sqlsrv_field_metadata($this->result_id) as $offset => $field)
		{
			$field_names[] = $field['Name'];
		}

		return $field_names;
	}

	// --------------------------------------------------------------------

	/**
	 * Field data
	 *
	 * Generates an array of objects containing field meta-data
	 *
	 * @return	array
	 */
	public function field_data()
	{
		$retval = array();
		foreach (sqlsrv_field_metadata($this->result_id) as $i => $field)
		{
			$retval[$i]		= new stdClass();
			$retval[$i]->name	= $field['Name'];
			$retval[$i]->type	= $field['Type'];
			$retval[$i]->max_length	= $field['Size'];
		}

		return $retval;
	}

	// --------------------------------------------------------------------

	/**
	 * Free the result
	 *
	 * @return	void
	 */
	public function free_result()
	{
		if (is_resource($this->result_id))
		{
			sqlsrv_free_stmt($this->result_id);
			$this->result_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @return	array
	 */
	protected function _fetch_assoc()
	{
		return sqlsrv_fetch_array($this->result_id, SQLSRV_FETCH_ASSOC);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @param	string	$class_name
	 * @return	object
	 */
	protected function _fetch_object($class_name = 'stdClass')
	{
		return sqlsrv_fetch_object($this->result_id, $class_name);
	}

}
