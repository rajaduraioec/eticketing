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
 * Postgre Forge Class
 *
 * @package		Reactor
 * @subpackage	Drivers
 * @category	Database
 * @author		Increatech Dev Team
 */
class R_DB_postgre_forge extends R_DB_forge {

	/**
	 * UNSIGNED support
	 *
	 * @var	array
	 */
	protected $_unsigned		= array(
		'INT2'		=> 'INTEGER',
		'SMALLINT'	=> 'INTEGER',
		'INT'		=> 'BIGINT',
		'INT4'		=> 'BIGINT',
		'INTEGER'	=> 'BIGINT',
		'INT8'		=> 'NUMERIC',
		'BIGINT'	=> 'NUMERIC',
		'REAL'		=> 'DOUBLE PRECISION',
		'FLOAT'		=> 'DOUBLE PRECISION'
	);

	/**
	 * NULL value representation in CREATE/ALTER TABLE statements
	 *
	 * @var	string
	 */
	protected $_null = 'NULL';

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	object	&$db	Database object
	 * @return	void
	 */
	public function __construct(&$db)
	{
		parent::__construct($db);

		if (version_compare($this->db->version(), '9.0', '>'))
		{
			$this->create_table_if = 'CREATE TABLE IF NOT EXISTS';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE
	 *
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	protected function _alter_table($alter_type, $table, $field)
 	{
		if (in_array($alter_type, array('DROP', 'ADD'), TRUE))
		{
			return parent::_alter_table($alter_type, $table, $field);
		}

		$sql = 'ALTER TABLE '.$this->db->escape_identifiers($table);
		$sqls = array();
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			if ($field[$i]['_literal'] !== FALSE)
			{
				return FALSE;
			}

			if (version_compare($this->db->version(), '8', '>=') && isset($field[$i]['type']))
			{
				$sqls[] = $sql.' ALTER COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.' TYPE '.$field[$i]['type'].$field[$i]['length'];
			}

			if ( ! empty($field[$i]['default']))
			{
				$sqls[] = $sql.' ALTER COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.' SET DEFAULT '.$field[$i]['default'];
			}

			if (isset($field[$i]['null']))
			{
				$sqls[] = $sql.' ALTER COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.($field[$i]['null'] === TRUE ? ' DROP NOT NULL' : ' SET NOT NULL');
			}

			if ( ! empty($field[$i]['new_name']))
			{
				$sqls[] = $sql.' RENAME COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.' TO '.$this->db->escape_identifiers($field[$i]['new_name']);
			}

			if ( ! empty($field[$i]['comment']))
			{
				$sqls[] = 'COMMENT ON COLUMN '
					.$this->db->escape_identifiers($table).'.'.$this->db->escape_identifiers($field[$i]['name'])
					.' IS '.$field[$i]['comment'];
			}
		}

		return $sqls;
 	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute TYPE
	 *
	 * Performs a data type mapping between different databases.
	 *
	 * @param	array	&$attributes
	 * @return	void
	 */
	protected function _attr_type(&$attributes)
	{
		// Reset field lengths for data types that don't support it
		if (isset($attributes['CONSTRAINT']) && stripos($attributes['TYPE'], 'int') !== FALSE)
		{
			$attributes['CONSTRAINT'] = NULL;
		}

		switch (strtoupper($attributes['TYPE']))
		{
			case 'TINYINT':
				$attributes['TYPE'] = 'SMALLINT';
				$attributes['UNSIGNED'] = FALSE;
				return;
			case 'MEDIUMINT':
				$attributes['TYPE'] = 'INTEGER';
				$attributes['UNSIGNED'] = FALSE;
				return;
			default: return;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute AUTO_INCREMENT
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_auto_increment(&$attributes, &$field)
	{
		if ( ! empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE)
		{
			$field['type'] = ($field['type'] === 'NUMERIC')
				? 'BIGSERIAL'
				: 'SERIAL';
		}
	}

}
