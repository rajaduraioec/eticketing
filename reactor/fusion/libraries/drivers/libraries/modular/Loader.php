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


class MX_Loader extends R_Loader
{
	protected $_module;

	public $_r_plugins = array();
	public $_r_cached_vars = array();

	/** Initialize the loader variables **/
	public function initialize($controller = NULL)
	{
		/* set the module name */
		$this->_module = R::$APP->router->fetch_module();

		if ($controller instanceof MX_Controller)
		{
			/* reference to the module controller */
			$this->controller = $controller;

			/* references to r loader variables */
			foreach (get_class_vars('R_Loader') as $var => $val)
			{
				if ($var != '_r_ob_level')
				{
					$this->$var =& R::$APP->load->$var;
				}
			}
		}
		else
		{
			parent::initialize();

			/* autoload module items */
			$this->_autoloader(array());
		}

		/* add this module path to the loader variables */
		$this->_add_module_paths($this->_module);
	}

	/** Add a module path loader variables **/
	public function _add_module_paths($module = '')
	{
		if (empty($module)) return;

		foreach (Modules::$locations as $location => $offset)
		{
			/* only add a module path if it exists */
			if (is_dir($module_path = $location.$module.'/') && ! in_array($module_path, $this->_r_model_paths))
			{
				array_unshift($this->_r_model_paths, $module_path);
			}
		}
	}

	/** Load a module config file **/
	public function config($file, $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		return R::$APP->config->load($file, $use_sections, $fail_gracefully, $this->_module);
	}

	/** Load the database drivers **/
	public function database($params = '', $return = FALSE, $query_builder = NULL)
	{
		if ($return === FALSE && $query_builder === NULL &&
			isset(R::$APP->db) && is_object(R::$APP->db) && ! empty(R::$APP->db->conn_id))
		{
			return FALSE;
		}

		require_once BASEPATH.'database/DB'.EXT;

		if ($return === TRUE) return DB($params, $query_builder);

		R::$APP->db = DB($params, $query_builder);

		return $this;
	}

	/** Load a module helper **/
	public function helper($helper = array())
	{
		if (is_array($helper)) return $this->helpers($helper);

		if (isset($this->_r_helpers[$helper]))	return;

		list($path, $_helper) = Modules::find($helper.'_helper', $this->_module, 'helpers/');

		if ($path === FALSE) return parent::helper($helper);

		Modules::load_file($_helper, $path);
		$this->_r_helpers[$_helper] = TRUE;
		return $this;
	}

	/** Load an array of helpers **/
	public function helpers($helpers = array())
	{
		foreach ($helpers as $_helper) $this->helper($_helper);
		return $this;
	}

	/** Load a module language file **/
	public function language($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		R::$APP->lang->load($langfile, $idiom, $return, $add_suffix, $alt_path, $this->_module);
		return $this;
	}

	public function languages($languages)
	{
		foreach($languages as $_language) $this->language($_language);
		return $this;
	}

	/** Load a module library **/
	public function library($library, $params = NULL, $object_name = NULL)
	{
		if (is_array($library)) return $this->libraries($library);

		$class = strtolower(basename($library));

		if (isset($this->_r_classes[$class]) && $_alias = $this->_r_classes[$class])
			return $this;

		($_alias = strtolower($object_name)) OR $_alias = $class;

		list($path, $_library) = Modules::find($library, $this->_module, 'libraries/');

		/* load library config file as params */
		if ($params == NULL)
		{
			list($path2, $file) = Modules::find($_alias, $this->_module, 'config/');
			($path2) && $params = Modules::load_file($file, $path2, 'config');
		}

		if ($path === FALSE)
		{
			$this->_r_load_library($library, $params, $object_name);
		}
		else
		{
			Modules::load_file($_library, $path);

			$library = ucfirst($_library);
			R::$APP->$_alias = new $library($params);

			$this->_r_classes[$class] = $_alias;
		}
		return $this;
    }

	/** Load an array of libraries **/
	public function libraries($libraries)
	{
		foreach ($libraries as $library => $alias) 
		{
			(is_int($library)) ? $this->library($alias) : $this->library($library, NULL, $alias);
		}
		return $this;
	}

	/** Load a module model **/
	public function model($model, $object_name = NULL, $connect = FALSE)
	{
		if (is_array($model)) return $this->models($model);

		($_alias = $object_name) OR $_alias = basename($model);

		if (in_array($_alias, $this->_r_models, TRUE))
			return $this;

		/* check module */
		list($path, $_model) = Modules::find(strtolower($model), $this->_module, 'models/');

		if ($path == FALSE)
		{
			/* check application & packages */
			parent::model($model, $object_name, $connect);
		}
		else
		{
			class_exists('R_Model', FALSE) OR load_class('Model', 'core');

			if ($connect !== FALSE && ! class_exists('R_DB', FALSE))
			{
				if ($connect === TRUE) $connect = '';
				$this->database($connect, FALSE, TRUE);
			}

			Modules::load_file($_model, $path);

			$model = ucfirst($_model);
			R::$APP->$_alias = new $model();

			$this->_r_models[] = $_alias;
		}
		return $this;
	}

	/** Load an array of models **/
	public function models($models)
	{
		foreach ($models as $model => $alias) 
		{
			(is_int($model)) ? $this->model($alias) : $this->model($model, $alias);
		}
		return $this;
	}

	/** Load a module controller **/
	public function module($module, $params = NULL)
	{
		if (is_array($module)) return $this->modules($module);

		$_alias = strtolower(basename($module));
		R::$APP->$_alias = Modules::load(array($module => $params));
		return $this;
	}

	/** Load an array of controllers **/
	public function modules($modules)
	{
		foreach ($modules as $_module) $this->module($_module);
		return $this;
	}

	/** Load a module plugin **/
	public function plugin($plugin)
	{
		if (is_array($plugin)) return $this->plugins($plugin);

		if (isset($this->_r_plugins[$plugin]))
			return $this;

		list($path, $_plugin) = Modules::find($plugin.'_pi', $this->_module, 'plugins/');

		if ($path === FALSE && ! is_file($_plugin = APPPATH.'plugins/'.$_plugin.EXT))
		{
			show_error("Unable to locate the plugin file: {$_plugin}");
		}

		Modules::load_file($_plugin, $path);
		$this->_r_plugins[$plugin] = TRUE;
		return $this;
	}

	/** Load an array of plugins **/
	public function plugins($plugins)
	{
		foreach ($plugins as $_plugin) $this->plugin($_plugin);
		return $this;
	}

	/** Load a module view **/
	public function view($view, $vars = array(), $return = FALSE)
	{
		list($path, $_view) = Modules::find($view, $this->_module, 'views/');

		if ($path != FALSE)
		{
			$this->_r_view_paths = array($path => TRUE) + $this->_r_view_paths;
			$view = $_view;
		}

		return $this->_r_load(array('_r_view' => $view, '_r_vars' => $this->_r_prepare_view_vars($vars), '_r_return' => $return));
	}

	protected function &_r_get_component($component)
	{
		return R::$APP->$component;
	}

	public function __get($class)
	{
		return (isset($this->controller)) ? $this->controller->$class : R::$APP->$class;
	}

	public function _r_load($_r_data)
	{
		extract($_r_data);

		if (isset($_r_view))
		{
			$_r_path = '';

			/* add file extension if not provided */
			$_r_file = (pathinfo($_r_view, PATHINFO_EXTENSION)) ? $_r_view : $_r_view.EXT;

			foreach ($this->_r_view_paths as $path => $cascade)
			{
				if (file_exists($view = $path.$_r_file))
				{
					$_r_path = $view;
					break;
				}
				if ( ! $cascade) break;
			}
		}
		elseif (isset($_r_path))
		{

			$_r_file = basename($_r_path);
			if( ! file_exists($_r_path)) $_r_path = '';
		}

		if (empty($_r_path))
			show_error('Unable to load the requested file: '.$_r_file);

		if (isset($_r_vars))
			$this->_r_cached_vars = array_merge($this->_r_cached_vars, (array) $_r_vars);

		extract($this->_r_cached_vars);

		ob_start();

		if ((bool) @ini_get('short_open_tag') === FALSE && R::$APP->config->item('rewrite_short_tags') == TRUE)
		{
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_r_path))));
		}
		else
		{
			include($_r_path);
		}

		log_message('debug', 'File loaded: '.$_r_path);

		if ($_r_return == TRUE) return ob_get_clean();

		if (ob_get_level() > $this->_r_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			R::$APP->output->append_output(ob_get_clean());
		}
	}

	/** Autoload module items **/
	public function _autoloader($autoload)
	{
		$path = FALSE;

		if ($this->_module)
		{
			list($path, $file) = Modules::find('constants', $this->_module, 'config/');

			/* module constants file */
			if ($path != FALSE)
			{
				include_once $path.$file.EXT;
			}

			list($path, $file) = Modules::find('autoload', $this->_module, 'config/');

			/* module autoload file */
			if ($path != FALSE)
			{
				$autoload = array_merge(Modules::load_file($file, $path, 'autoload'), $autoload);
			}
		}

		/* nothing to do */
		if (count($autoload) == 0) return;

		/* autoload package paths */
		if (isset($autoload['packages']))
		{
			foreach ($autoload['packages'] as $package_path)
			{
				$this->add_package_path($package_path);
			}
		}

		/* autoload config */
		if (isset($autoload['config']))
		{
			foreach ($autoload['config'] as $config)
			{
				$this->config($config);
			}
		}

		/* autoload helpers, plugins, languages */
		foreach (array('helper', 'plugin', 'language') as $type)
		{
			if (isset($autoload[$type]))
			{
				foreach ($autoload[$type] as $item)
				{
					$this->$type($item);
				}
			}
		}
		
		// Autoload drivers
		if (isset($autoload['drivers']))
		{
		    foreach ($autoload['drivers'] as $item => $alias)
		    {
		        (is_int($item)) ? $this->driver($alias) : $this->driver($item, $alias);
		    }
		}

		/* autoload database & libraries */
		if (isset($autoload['libraries']))
		{
			if (in_array('database', $autoload['libraries']))
			{
				/* autoload database */
				if ( ! $db = R::$APP->config->item('database'))
				{
					$this->database();
					$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
				}
			}

			/* autoload libraries */
			foreach ($autoload['libraries'] as $library => $alias)
			{
				(is_int($library)) ? $this->library($alias) : $this->library($library, NULL, $alias);
			}
		}

		/* autoload models */
		if (isset($autoload['model']))
		{
			foreach ($autoload['model'] as $model => $alias)
			{
				(is_int($model)) ? $this->model($alias) : $this->model($model, $alias);
			}
		}

		/* autoload module controllers */
		if (isset($autoload['modules']))
		{
			foreach ($autoload['modules'] as $controller)
			{
				($controller != $this->_module) && $this->module($controller);
			}
		}
	}
}

/** load the R class for Modular Separation **/
(class_exists('R', FALSE)) OR require dirname(__FILE__).'/R.php';
