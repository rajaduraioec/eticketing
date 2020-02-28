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
 * ODBC Result Class
 *
 * This class extends the parent result class: R_DB_result
 *
 * @package		Reactor
 * @subpackage	Drivers
 * @category	Database
 * @author		Increatech Dev Team
 */
class R_DB_odbc_result extends R_DB_result {

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		if (is_int($this->num_rows))
		{
			return $this->num_rows;
		}
		elseif (($this->num_rows = odbc_num_rows($this->result_id)) !== -1)
		{
			return $this->num_rows;
		}

		// Work-around for ODBC subdrivers that don't support num_rows()
		if (count($this->result_array) > 0)
		{
			return $this->num_rows = count($this->result_array);
		}
		elseif (count($this->result_object) > 0)
		{
			return $this->num_rows = count($this->result_object);
		}

		return $this->num_rows = count($this->result_array());
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return odbc_num_fields($this->result_id);
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
		$num_fields = $this->num_fields();

		if ($num_fields > 0)
		{
			for ($i = 1; $i <= $num_fields; $i++)
			{
				$field_names[] = odbc_field_name($this->result_id, $i);
			}
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
		for ($i = 0, $odbc_index = 1, $c = $this->num_fields(); $i < $c; $i++, $odbc_index++)
		{
			$retval[$i]			= new stdClass();
			$retval[$i]->name		= odbc_field_name($this->result_id, $odbc_index);
			$retval[$i]->type		= odbc_field_type($this->result_id, $odbc_index);
			$retval[$i]->max_length		= odbc_field_len($this->result_id, $odbc_index);
			$retval[$i]->primary_key	= 0;
			$retval[$i]->default		= '';
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
			odbc_free_result($this->result_id);
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
		return odbc_fetch_array($this->result_id);
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
		$row = odbc_fetch_object($this->result_id);

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

// --------------------------------------------------------------------

if ( ! function_exists('odbc_fetch_array'))
{
	/**
	 * ODBC Fetch array
	 *
	 * Emulates the native odbc_fetch_array() function when
	 * it is not available (odbc_fetch_array() requires unixODBC)
	 *
	 * @param	resource	&$result
	 * @param	int		$rownumber
	 * @return	array
	 */
	function odbc_fetch_array(&$result, $rownumber = 1)
	{
		$rs = array();
		if ( ! odbc_fetch_into($result, $rs, $rownumber))
		{
			return FALSE;
		}

		$rs_assoc = array();
		foreach ($rs as $k => $v)
		{
			$field_name = odbc_field_name($result, $k+1);
			$rs_assoc[$field_name] = $v;
		}

		return $rs_assoc;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('odbc_fetch_object'))
{
	/**
	 * ODBC Fetch object
	 *
	 * Emulates the native odbc_fetch_object() function when
	 * it is not available.
	 *
	 * @param	resource	&$result
	 * @param	int		$rownumber
	 * @return	object
	 */
	function odbc_fetch_object(&$result, $rownumber = 1)
	{
		$rs = array();
		if ( ! odbc_fetch_into($result, $rs, $rownumber))
		{
			return FALSE;
		}

		$rs_object = new stdClass();
		foreach ($rs as $k => $v)
		{
			$field_name = odbc_field_name($result, $k+1);
			$rs_object->$field_name = $v;
		}

		return $rs_object;
	}
}
