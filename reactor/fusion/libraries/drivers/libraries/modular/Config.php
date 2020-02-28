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
(defined('BASEPATH')) OR exit('No direct script access allowed');

class MX_Config extends R_Config 
{	
	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE, $_module = '') 
	{
		if (in_array($file, $this->is_loaded, TRUE)) return $this->item($file);

		$_module OR $_module = R::$APP->router->fetch_module();
		list($path, $file) = Modules::find($file, $_module, 'config/');
		
		if ($path === FALSE)
		{
			parent::load($file, $use_sections, $fail_gracefully);					
			return $this->item($file);
		}  
		
		if ($config = Modules::load_file($file, $path, 'config'))
		{
			/* reference to the config array */
			$current_config =& $this->config;

			if ($use_sections === TRUE)	
			{
				if (isset($current_config[$file])) 
				{
					$current_config[$file] = array_merge($current_config[$file], $config);
				} 
				else 
				{
					$current_config[$file] = $config;
				}
				
			} 
			else 
			{
				$current_config = array_merge($current_config, $config);
			}

			$this->is_loaded[] = $file;
			unset($config);
			return $this->item($file);
		}
	}
}