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

$lang['db_invalid_connection_str'] = 'Unable to determine the database settings based on the connection string you submitted.';
$lang['db_unable_to_connect'] = 'Unable to connect to your database server using the provided settings.';
$lang['db_unable_to_select'] = 'Unable to select the specified database: %s';
$lang['db_unable_to_create'] = 'Unable to create the specified database: %s';
$lang['db_invalid_query'] = 'The query you submitted is not valid.';
$lang['db_must_set_table'] = 'You must set the database table to be used with your query.';
$lang['db_must_use_set'] = 'You must use the "set" method to update an entry.';
$lang['db_must_use_index'] = 'You must specify an index to match on for batch updates.';
$lang['db_batch_missing_index'] = 'One or more rows submitted for batch updating is missing the specified index.';
$lang['db_must_use_where'] = 'Updates are not allowed unless they contain a "where" clause.';
$lang['db_del_must_use_where'] = 'Deletes are not allowed unless they contain a "where" or "like" clause.';
$lang['db_field_param_missing'] = 'To fetch fields requires the name of the table as a parameter.';
$lang['db_unsupported_function'] = 'This feature is not available for the database you are using.';
$lang['db_transaction_failure'] = 'Transaction failure: Rollback performed.';
$lang['db_unable_to_drop'] = 'Unable to drop the specified database.';
$lang['db_unsupported_feature'] = 'Unsupported feature of the database platform you are using.';
$lang['db_unsupported_compression'] = 'The file compression format you chose is not supported by your server.';
$lang['db_filepath_error'] = 'Unable to write data to the file path you have submitted.';
$lang['db_invalid_cache_path'] = 'The cache path you submitted is not valid or writable.';
$lang['db_table_name_required'] = 'A table name is required for that operation.';
$lang['db_column_name_required'] = 'A column name is required for that operation.';
$lang['db_column_definition_required'] = 'A column definition is required for that operation.';
$lang['db_unable_to_set_charset'] = 'Unable to set client connection character set: %s';
$lang['db_error_heading'] = 'A Database Error Occurred';
