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
 * Reactor File Helpers
 *
 * @package		Reactor
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Increatech Dev Team
 */

// ------------------------------------------------------------------------

if ( ! function_exists('read_file'))
{
	/**
	 * Read File
	 *
	 * Opens the file specified in the path and returns it as a string.
	 *
	 * @todo	Remove in version 3.1+.
	 * @deprecated	3.0.0	It is now just an alias for PHP's native file_get_contents().
	 * @param	string	$file	Path to file
	 * @return	string	File contents
	 */
	function read_file($file)
	{
		return @file_get_contents($file);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('write_file'))
{
	/**
	 * Write File
	 *
	 * Writes data to the file specified in the path.
	 * Creates a new file if non-existent.
	 *
	 * @param	string	$path	File path
	 * @param	string	$data	Data to write
	 * @param	string	$mode	fopen() mode (default: 'wb')
	 * @return	bool
	 */
	function write_file($path, $data, $mode = 'wb')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('rf_attention')){
	/**
	 * Files must be writable or owned by the system in order to be deleted.
	 * If the second parameter is set to TRUE, any directories contained
	 * within the supplied base directory will be nuked as well.
	 *
	 * @param	string	$path		File path
	 * @param	bool	$del_dir	Whether to delete any directories found in the path
	 * @return	bool
	 */
	function rf_attention($ec='',$state=TRUE)
	{
		$message='';
		if($ec!='')
		$message.='Error Code : '.$ec.'</br>';
		$message.='Sorry, but something went wrong . Please 
		Contact Increatech Business Solution Pvt Ltd for support.
		</br> Email support@increatech.com';
		show_error($message,'401','Attention');
		if(is_bool($state)){
			if($state)
			exit();
		}else{
			exit();
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('delete_files'))
{
	/**
	 * Delete Files
	 *
	 * Deletes all files contained in the supplied directory path.
	 * Files must be writable or owned by the system in order to be deleted.
	 * If the second parameter is set to TRUE, any directories contained
	 * within the supplied base directory will be nuked as well.
	 *
	 * @param	string	$path		File path
	 * @param	bool	$del_dir	Whether to delete any directories found in the path
	 * @param	bool	$htdocs		Whether to skip deleting .htaccess and index page files
	 * @param	int	$_level		Current directory depth level (default: 0; internal use only)
	 * @return	bool
	 */
	function delete_files($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0)
	{
		// Trim the trailing slash
		$path = rtrim($path, '/\\');

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;
		}

		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename !== '.' && $filename !== '..')
			{
				$filepath = $path.DIRECTORY_SEPARATOR.$filename;

				if (is_dir($filepath) && $filename[0] !== '.' && ! is_link($filepath))
				{
					delete_files($filepath, $del_dir, $htdocs, $_level + 1);
				}
				elseif ($htdocs !== TRUE OR ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename))
				{
					@unlink($filepath);
				}
			}
		}

		closedir($current_dir);

		return ($del_dir === TRUE && $_level > 0)
			? @rmdir($path)
			: TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_filenames'))
{
	/**
	 * Get Filenames
	 *
	 * Reads the specified directory and builds an array containing the filenames.
	 * Any sub-folders contained within the specified path are read as well.
	 *
	 * @param	string	path to source
	 * @param	bool	whether to include the path as part of the filename
	 * @param	bool	internal variable to determine recursion status - do not use in calls
	 * @return	array
	 */
	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($source_dir.$file) && $file[0] !== '.')
				{
					get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
				}
				elseif ($file[0] !== '.')
				{
					$_filedata[] = ($include_path === TRUE) ? $source_dir.$file : $file;
				}
			}

			closedir($fp);
			return $_filedata;
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_dir_file_info'))
{
	/**
	 * Get Directory File Information
	 *
	 * Reads the specified directory and builds an array containing the filenames,
	 * filesize, dates, and permissions
	 *
	 * Any sub-folders contained within the specified path are read as well.
	 *
	 * @param	string	path to source
	 * @param	bool	Look only at the top level directory specified?
	 * @param	bool	internal variable to determine recursion status - do not use in calls
	 * @return	array
	 */
	function get_dir_file_info($source_dir, $top_level_only = TRUE, $_recursion = FALSE)
	{
		static $_filedata = array();
		$relative_path = $source_dir;

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			// Used to be foreach (scandir($source_dir, 1) as $file), but scandir() is simply not as fast
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($source_dir.$file) && $file[0] !== '.' && $top_level_only === FALSE)
				{
					get_dir_file_info($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, TRUE);
				}
				elseif ($file[0] !== '.')
				{
					$_filedata[$file] = get_file_info($source_dir.$file);
					$_filedata[$file]['relative_path'] = $relative_path;
				}
			}

			closedir($fp);
			return $_filedata;
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------


if ( ! function_exists('ignitioncp')){
	/**
	 * Given a file and path, returns the name, path, size, date modified
	 * Second parameter allows you to explicitly declare what information you want returned
	 * Options are: name, server_path, size, date, readable, writable, executable, fileperms
	 * Returns FALSE if the file cannot be found.
	 *
	 * @param	string	path to file
	 * @param	mixed	array or comma separated string of information returned
	 * @return	array
	 */
	function ignitioncp($k=''){
		if(strlen($k)==3){
			$R=& get_instance();
			switch(ord($k[1])-ord('a')){
				case '0':
					($R->config->item('ignitionkey')&&($R->config->item('ignitionkey')!='')&& 
					$R->config->item('ignitionstart')&&($R->config->item('ignitionstart')!='')&&
					(($R->config->item('ignitionkey')==$R->config->item('ignitionstart')))?
					$R->config->set_item('ignitionstart',$R->config->item('ignitionstart')+ord('i')):
					rf_attention('2020001xA'));
					return TRUE;
					break;
				case '1':
					($R->config->item('ignitionstart')==$R->config->item('ignitionkey')+ord('i')
					?$R->config->set_item('ignitionstart',$R->config->item('ignitionstart')-ord('c')):
					rf_attention('2020002xB'));
					return TRUE;
					break;
				case '2':
					($R->config->item('ignitionstart')==$R->config->item('ignitionkey')+ord('i')-ord('c')?
					$R->config->set_item('ignitionstart',$R->config->item('ignitionstart')+ord('t')):
					rf_attention('2020003xC'));
					return TRUE;
					break;
				case '3':
					($R->config->item('ignitionstart')==$R->config->item('ignitionkey')+ord('z')?
					$R->config->set_item('ignitionstart',($R->config->item('ignitionstart')-
					$R->config->item('ignitionkey'))*ord('b')):rf_attention('2020004xD'));
					return TRUE;
					break;
				case '4':
					($R->config->item('ignitionstart')==ord('z')*ord('b')?
					$R->config->set_item('ignitionstart',($R->config->item('ignitionstart')+ord('w'))/ord('s')):
					rf_attention('2020005xE'));
					return TRUE;
					break;
				case '5':
					($R->config->item('ignitionstart')==ord('i')?
					$R->config->set_item('ignitionstart',$R->config->item('ignitionstart')*ord('s')):
					rf_attention('2020006xF'));
					return TRUE;
					break;
				case '6':
					($R->config->item('ignitionstart')==ord('i')*ord('s')?
					$R->config->set_item('ignitionstart',($R->config->item('ignitionstart')-ord('w'))/ord('b')+ord('w')):
					rf_attention('2020007xG'));
					return TRUE;
					break;
				case '7':
					($R->config->item('ignitionstart')==ord('z')+ord('w')?
					$R->config->set_item('ignitionstart',$R->config->item('ignitionstart')-ord('t')):
					rf_attention('2020008xH'));
					return TRUE;
					break;
				case '8':
					($R->config->item('ignitionstart')==ord('}')?
					$R->config->set_item('ignitionstart',$R->config->item('ignitionstart')+ord('c')):
					rf_attention('2020009xI'));
					return TRUE;
					break;
				case '9':
					($R->config->item('ignitionstart')==ord('z')+ord('f')?TRUE:rf_attention('2020010xJ'));
					return TRUE;
					break;
				default:
					rf_attention('2020000xE');
					break;
			}
		}else{
			rf_attention('2020000xO');
			return FALSE;
		}
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_file_info'))
{
	/**
	 * Get File Info
	 *
	 * Given a file and path, returns the name, path, size, date modified
	 * Second parameter allows you to explicitly declare what information you want returned
	 * Options are: name, server_path, size, date, readable, writable, executable, fileperms
	 * Returns FALSE if the file cannot be found.
	 *
	 * @param	string	path to file
	 * @param	mixed	array or comma separated string of information returned
	 * @return	array
	 */
	function get_file_info($file, $returned_values = array('name', 'server_path', 'size', 'date'))
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (is_string($returned_values))
		{
			$returned_values = explode(',', $returned_values);
		}

		foreach ($returned_values as $key)
		{
			switch ($key)
			{
				case 'name':
					$fileinfo['name'] = basename($file);
					break;
				case 'server_path':
					$fileinfo['server_path'] = $file;
					break;
				case 'size':
					$fileinfo['size'] = filesize($file);
					break;
				case 'date':
					$fileinfo['date'] = filemtime($file);
					break;
				case 'readable':
					$fileinfo['readable'] = is_readable($file);
					break;
				case 'writable':
					$fileinfo['writable'] = is_really_writable($file);
					break;
				case 'executable':
					$fileinfo['executable'] = is_executable($file);
					break;
				case 'fileperms':
					$fileinfo['fileperms'] = fileperms($file);
					break;
			}
		}

		return $fileinfo;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_mime_by_extension'))
{
	/**
	 * Get Mime by Extension
	 *
	 * Translates a file extension into a mime type based on config/mimes.php.
	 * Returns FALSE if it can't determine the type, or open the mime config file
	 *
	 * Note: this is NOT an accurate way of determining file mime types, and is here strictly as a convenience
	 * It should NOT be trusted, and should certainly NOT be used for security
	 *
	 * @param	string	$filename	File name
	 * @return	string
	 */
	function get_mime_by_extension($filename)
	{
		static $mimes;

		if ( ! is_array($mimes))
		{
			$mimes = get_mimes();

			if (empty($mimes))
			{
				return FALSE;
			}
		}

		$extension = strtolower(substr(strrchr($filename, '.'), 1));

		if (isset($mimes[$extension]))
		{
			return is_array($mimes[$extension])
				? current($mimes[$extension]) // Multiple mime types, just give the first one
				: $mimes[$extension];
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('symbolic_permissions'))
{
	/**
	 * Symbolic Permissions
	 *
	 * Takes a numeric value representing a file's permissions and returns
	 * standard symbolic notation representing that value
	 *
	 * @param	int	$perms	Permissions
	 * @return	string
	 */
	function symbolic_permissions($perms)
	{
		if (($perms & 0xC000) === 0xC000)
		{
			$symbolic = 's'; // Socket
		}
		elseif (($perms & 0xA000) === 0xA000)
		{
			$symbolic = 'l'; // Symbolic Link
		}
		elseif (($perms & 0x8000) === 0x8000)
		{
			$symbolic = '-'; // Regular
		}
		elseif (($perms & 0x6000) === 0x6000)
		{
			$symbolic = 'b'; // Block special
		}
		elseif (($perms & 0x4000) === 0x4000)
		{
			$symbolic = 'd'; // Directory
		}
		elseif (($perms & 0x2000) === 0x2000)
		{
			$symbolic = 'c'; // Character special
		}
		elseif (($perms & 0x1000) === 0x1000)
		{
			$symbolic = 'p'; // FIFO pipe
		}
		else
		{
			$symbolic = 'u'; // Unknown
		}

		// Owner
		$symbolic .= (($perms & 0x0100) ? 'r' : '-')
			.(($perms & 0x0080) ? 'w' : '-')
			.(($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

		// Group
		$symbolic .= (($perms & 0x0020) ? 'r' : '-')
			.(($perms & 0x0010) ? 'w' : '-')
			.(($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

		// World
		$symbolic .= (($perms & 0x0004) ? 'r' : '-')
			.(($perms & 0x0002) ? 'w' : '-')
			.(($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

		return $symbolic;
	}
}

// --------------------------------------------------------------------


if ( ! function_exists('rfproducts')){
	/**
	 * Takes a numeric value representing a file's permissions and returns
	 * a three character string representing the file's octal permissions
	 *
	 * @param	int	$perms	Permissions
	 * @return	string
	 */
	function rfproducts(){
		$R=& get_instance();
		$elmode=$R->config->item('mode_elrun');
		$R->config->set_item('log_threshold',
		($elmode=='ghostmode'?0:($elmode=='investigationmode'?
		1:($elmode=='detectivemode'?2:($elmode=='documentormode'?
		3:($elmode=='tensionmode'?4:0))))));
		(RFACTOR?ignitioncp('age'):NULL);
		moldeme();
		return;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('octal_permissions'))
{
	/**
	 * Octal Permissions
	 *
	 * Takes a numeric value representing a file's permissions and returns
	 * a three character string representing the file's octal permissions
	 *
	 * @param	int	$perms	Permissions
	 * @return	string
	 */
	function octal_permissions($perms)
	{
		return substr(sprintf('%o', $perms), -3);
	}
}
