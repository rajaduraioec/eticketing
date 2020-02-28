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
 * oci8 Result Class
 *
 * This class extends the parent result class: R_DB_result
 *
 * @category	Database
 * @author		Increatech Dev Team
 */
class R_DB_oci8_result extends R_DB_result {

	/**
	 * Statement ID
	 *
	 * @var	resource
	 */
	public $stmt_id;

	/**
	 * Cursor ID
	 *
	 * @var	resource
	 */
	public $curs_id;

	/**
	 * Limit used flag
	 *
	 * @var	bool
	 */
	public $limit_used;

	/**
	 * Commit mode flag
	 *
	 * @var	int
	 */
	public $commit_mode;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	object	&$driver_object
	 * @return	void
	 */
	public function __construct(&$driver_object)
	{
		parent::__construct($driver_object);

		$this->stmt_id = $driver_object->stmt_id;
		$this->curs_id = $driver_object->curs_id;
		$this->limit_used = $driver_object->limit_used;
		$this->commit_mode =& $driver_object->commit_mode;
		$driver_object->stmt_id = FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		$count = oci_num_fields($this->stmt_id);

		// if we used a limit we subtract it
		return ($this->limit_used) ? $count - 1 : $count;
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
		for ($c = 1, $fieldCount = $this->num_fields(); $c <= $fieldCount; $c++)
		{
			$field_names[] = oci_field_name($this->stmt_id, $c);
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
		for ($c = 1, $fieldCount = $this->num_fields(); $c <= $fieldCount; $c++)
		{
			$F		= new stdClass();
			$F->name	= oci_field_name($this->stmt_id, $c);
			$F->type	= oci_field_type($this->stmt_id, $c);
			$F->max_length	= oci_field_size($this->stmt_id, $c);

			$retval[] = $F;
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
			oci_free_statement($this->result_id);
			$this->result_id = FALSE;
		}

		if (is_resource($this->stmt_id))
		{
			oci_free_statement($this->stmt_id);
		}

		if (is_resource($this->curs_id))
		{
			oci_cancel($this->curs_id);
			$this->curs_id = NULL;
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
		$id = ($this->curs_id) ? $this->curs_id : $this->stmt_id;
		return oci_fetch_assoc($id);
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
		$row = ($this->curs_id)
			? oci_fetch_object($this->curs_id)
			: oci_fetch_object($this->stmt_id);

		if ($class_name === 'stdClass' OR ! $row)
		{
			return $row;
		}

		$class_name = new $class_name();
		foreach ($row as $key => $value)
		{
			$class_name->$key = $value;
		}

		return $class_name;
	}

}
