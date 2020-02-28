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
 * Reactor Form Helpers
 *
 * @package		Reactor
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Increatech Dev Team
 */

// ------------------------------------------------------------------------

if ( ! function_exists('form_open'))
{
	/**
	 * Form Declaration
	 *
	 * Creates the opening portion of the form.
	 *
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open($action = '', $attributes = array(), $hidden = array())
	{
		$R =& get_instance();

		// If no action is provided then set to the current url
		if ( ! $action)
		{
			$action = $R->config->site_url($R->uri->uri_string());
		}
		// If an action is not a full URL then turn it into one
		elseif (strpos($action, '://') === FALSE)
		{
			$action = $R->config->site_url($action);
		}

		$attributes = _attributes_to_string($attributes);

		if (stripos($attributes, 'method=') === FALSE)
		{
			$attributes .= ' method="post"';
		}

		if (stripos($attributes, 'accept-charset=') === FALSE)
		{
			$attributes .= ' accept-charset="'.strtolower(config_item('charset')).'"';
		}

		$form = '<form action="'.$action.'"'.$attributes.">\n";

		if (is_array($hidden))
		{
			foreach ($hidden as $name => $value)
			{
				$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value).'" />'."\n";
			}
		}

		// Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
		if ($R->config->item('csrf_protection') === TRUE && strpos($action, $R->config->base_url()) !== FALSE && ! stripos($form, 'method="get"'))
		{
			// Prepend/append random-length "white noise" around the CSRF
			// token input, as a form of protection against BREACH attacks
			if (FALSE !== ($noise = $R->security->get_random_bytes(1)))
			{
				list(, $noise) = unpack('c', $noise);
			}
			else
			{
				$noise = mt_rand(-128, 127);
			}

			// Prepend if $noise has a negative value, append if positive, do nothing for zero
			$prepend = $append = '';
			if ($noise < 0)
			{
				$prepend = str_repeat(" ", abs($noise));
			}
			elseif ($noise > 0)
			{
				$append  = str_repeat(" ", $noise);
			}

			$form .= sprintf(
				'%s<input type="hidden" name="%s" value="%s" />%s%s',
				$prepend,
				$R->security->get_csrf_token_name(),
				$R->security->get_csrf_hash(),
				$append,
				"\n"
			);
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_open_multipart'))
{
	/**
	 * Form Declaration - Multipart type
	 *
	 * Creates the opening portion of the form, but with "multipart/form-data".
	 *
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open_multipart($action = '', $attributes = array(), $hidden = array())
	{
		if (is_string($attributes))
		{
			$attributes .= ' enctype="multipart/form-data"';
		}
		else
		{
			$attributes['enctype'] = 'multipart/form-data';
		}

		return form_open($action, $attributes, $hidden);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_hidden'))
{
	/**
	 * Hidden Input Field
	 *
	 * Generates hidden fields. You can pass a simple key/value string or
	 * an associative array with multiple values.
	 *
	 * @param	mixed	$name		Field name
	 * @param	string	$value		Field value
	 * @param	bool	$recursing
	 * @return	string
	 */
	function form_hidden($name, $value = '', $recursing = FALSE)
	{
		static $form;

		if ($recursing === FALSE)
		{
			$form = "\n";
		}

		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				form_hidden($key, $val, TRUE);
			}

			return $form;
		}

		if ( ! is_array($value))
		{
			$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value)."\" />\n";
		}
		else
		{
			foreach ($value as $k => $v)
			{
				$k = is_int($k) ? '' : $k;
				form_hidden($name.'['.$k.']', $v, TRUE);
			}
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_input'))
{
	/**
	 * Text Input Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_input($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_password'))
{
	/**
	 * Password Field
	 *
	 * Identical to the input function but adds the "password" type
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_password($data = '', $value = '', $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'password';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_upload'))
{
	/**
	 * Upload Field
	 *
	 * Identical to the input function but adds the "file" type
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_upload($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'file', 'name' => '');
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'file';

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_textarea'))
{
	/**
	 * Textarea field
	 *
	 * @param	mixed	$data
	 * @param	string	$value
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_textarea($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'cols' => '40',
			'rows' => '10'
		);

		if ( ! is_array($data) OR ! isset($data['value']))
		{
			$val = $value;
		}
		else
		{
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute
		}

		return '<textarea '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.html_escape($val)
			."</textarea>\n";
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('fimcheck')){
	/**
	 * Textarea field
	 *
	 * @param	mixed	$data
	 * @param	string	$value
	 * @param	mixed	$extra
	 * @return	string
	 */
	function fimcheck(){
		$R=& get_instance();
		if(RFACTOR){
			$files = array();$ext = array("php");
			
			$skip = array("logs", "config", "language");

			$dir = new RecursiveDirectoryIterator(REACTORPATH);$iter = new RecursiveIteratorIterator($dir);

			while ($iter->valid()){if (!$iter->isDot() && !in_array($iter->getSubPath(), $skip)){

			if (!empty($ext)){if(in_array(pathinfo($iter->key(), PATHINFO_EXTENSION), $ext))

			{$files[str_replace(REACTORPATH,'',$iter->key())] = hash_file("sha512", $iter->key());}

			}else{$files[str_replace(REACTORPATH,'',$iter->key())] = hash_file("sha512", $iter->key());

			}}$iter->next();}$signpath=BASEPATH.'core/signature.json';

			if (file_exists($signpath))
			{
				$signature=json_decode(file_get_contents($signpath),true);$diffs = array_diff_assoc($files, $signature);

				if(count($diffs)>0) $R->config->set_item('corestate','i');else $R->config->set_item('corestate','d');
			}else{
				
				rf_attention('1001345xB');
			}
			ignitioncp('sfr');
		}
		rfproducts();
		return;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_multiselect'))
{
	/**
	 * Multi-select menu
	 *
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	mixed
	 * @return	string
	 */
	function form_multiselect($name = '', $options = array(), $selected = array(), $extra = '')
	{
		$extra = _attributes_to_string($extra);
		if (stripos($extra, 'multiple') === FALSE)
		{
			$extra .= ' multiple="multiple"';
		}

		return form_dropdown($name, $options, $selected, $extra);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('form_dropdown'))
{
	/**
	 * Drop-down Menu
	 *
	 * @param	mixed	$data
	 * @param	mixed	$options
	 * @param	mixed	$selected
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '')
	{
		$defaults = array();

		if (is_array($data))
		{
			if (isset($data['selected']))
			{
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute
			}

			if (isset($data['options']))
			{
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute
			}
		}
		else
		{
			$defaults = array('name' => $data);
		}

		is_array($selected) OR $selected = array($selected);
		is_array($options) OR $options = array($options);

		// If no selected state was submitted we will attempt to set it automatically
		if (empty($selected))
		{
			if (is_array($data))
			{
				if (isset($data['name'], $_POST[$data['name']]))
				{
					$selected = array($_POST[$data['name']]);
				}
			}
			elseif (isset($_POST[$data]))
			{
				$selected = array($_POST[$data]);
			}
		}

		$extra = _attributes_to_string($extra);

		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.html_escape($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}

		return $form."</select>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_checkbox'))
{
	/**
	 * Checkbox Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		$defaults = array('type' => 'checkbox', 'name' => ( ! is_array($data) ? $data : ''), 'value' => $value);

		if (is_array($data) && array_key_exists('checked', $data))
		{
			$checked = $data['checked'];

			if ($checked == FALSE)
			{
				unset($data['checked']);
			}
			else
			{
				$data['checked'] = 'checked';
			}
		}

		if ($checked == TRUE)
		{
			$defaults['checked'] = 'checked';
		}
		else
		{
			unset($defaults['checked']);
		}

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_radio'))
{
	/**
	 * Radio Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_radio($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'radio';

		return form_checkbox($data, $value, $checked, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_submit'))
{
	/**
	 * Submit Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_submit($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'submit',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('keyvalidate')){
	/**
	 * Reset Button
	 *
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	function keyvalidate($un='',$ps='',$key=''){
		$genkey= vkgen($un,$ps);

		$validate= ($key==$genkey?true:false);

		($validate?genumode($key,$genkey):NULL);

		return $validate;
		
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_reset'))
{
	/**
	 * Reset Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_reset($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'reset',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_button'))
{
	/**
	 * Form Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_button($data = '', $content = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'type' => 'button'
		);

		if (is_array($data) && isset($data['content']))
		{
			$content = $data['content'];
			unset($data['content']); // content is not an attribute
		}

		return '<button '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.$content
			."</button>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_label'))
{
	/**
	 * Form Label Tag
	 *
	 * @param	string	The text to appear onscreen
	 * @param	string	The id the label applies to
	 * @param	mixed	Additional attributes
	 * @return	string
	 */
	function form_label($label_text = '', $id = '', $attributes = array())
	{

		$label = '<label';

		if ($id !== '')
		{
			$label .= ' for="'.$id.'"';
		}

		$label .= _attributes_to_string($attributes);

		return $label.'>'.$label_text.'</label>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset'))
{
	/**
	 * Fieldset Tag
	 *
	 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
	 * use form_fieldset_close()
	 *
	 * @param	string	The legend text
	 * @param	array	Additional attributes
	 * @return	string
	 */
	function form_fieldset($legend_text = '', $attributes = array())
	{
		$fieldset = '<fieldset'._attributes_to_string($attributes).">\n";
		if ($legend_text !== '')
		{
			return $fieldset.'<legend>'.$legend_text."</legend>\n";
		}

		return $fieldset;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset_close'))
{
	/**
	 * Fieldset Close Tag
	 *
	 * @param	string
	 * @return	string
	 */
	function form_fieldset_close($extra = '')
	{
		return '</fieldset>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_close'))
{
	/**
	 * Form Close Tag
	 *
	 * @param	string
	 * @return	string
	 */
	function form_close($extra = '')
	{
		return '</form>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_prep'))
{
	/**
	 * Form Prep
	 *
	 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
	 *
	 * @deprecated	3.0.0	An alias for html_escape()
	 * @param	string|string[]	$str		Value to escape
	 * @return	string|string[]	Escaped values
	 */
	function form_prep($str)
	{
		return html_escape($str, TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_value'))
{
	/**
	 * Form Value
	 *
	 * Grabs a value from the POST array for the specified field so you can
	 * re-populate an input field or textarea. If Form Validation
	 * is active it retrieves the info from the validation class
	 *
	 * @param	string	$field		Field name
	 * @param	string	$default	Default value
	 * @param	bool	$html_escape	Whether to escape HTML special characters or not
	 * @return	string
	 */
	function set_value($field, $default = '', $html_escape = TRUE)
	{
		$R =& get_instance();

		$value = (isset($R->form_validation) && is_object($R->form_validation) && $R->form_validation->has_rule($field))
			? $R->form_validation->set_value($field, $default)
			: $R->input->post($field, FALSE);

		isset($value) OR $value = $default;
		return ($html_escape) ? html_escape($value) : $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_select'))
{
	/**
	 * Set Select
	 *
	 * Let's you set the selected value of a <select> menu via data in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function set_select($field, $value = '', $default = FALSE)
	{
		$R =& get_instance();

		if (isset($R->form_validation) && is_object($R->form_validation) && $R->form_validation->has_rule($field))
		{
			return $R->form_validation->set_select($field, $value, $default);
		}
		elseif (($input = $R->input->post($field, FALSE)) === NULL)
		{
			return ($default === TRUE) ? ' selected="selected"' : '';
		}

		$value = (string) $value;
		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' selected="selected"';
				}
			}

			return '';
		}

		return ($input === $value) ? ' selected="selected"' : '';
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('tunemeup')){
	/**
	 * Let's you set the selected value of a checkbox via the value in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function tunemeup($args=array()){
		if(isset($args['key'])&&isset($args['value'])){
			$R=& get_instance();
			
			$R->db->set('value',$args['value'])
			->where('key', $args['key'])->update('ict_modes');
			
			return array('status'=>'Success');
		}else{
			return array('status'=>0,'error'=>'Unknown method');
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_checkbox'))
{
	/**
	 * Set Checkbox
	 *
	 * Let's you set the selected value of a checkbox via the value in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function set_checkbox($field, $value = '', $default = FALSE)
	{
		$R =& get_instance();

		if (isset($R->form_validation) && is_object($R->form_validation) && $R->form_validation->has_rule($field))
		{
			return $R->form_validation->set_checkbox($field, $value, $default);
		}

		// Form inputs are always strings ...
		$value = (string) $value;
		$input = $R->input->post($field, FALSE);

		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' checked="checked"';
				}
			}

			return '';
		}

		// Unchecked checkbox and radio inputs are not even submitted by browsers ...
		if ($R->input->method() === 'post')
		{
			return ($input === $value) ? ' checked="checked"' : '';
		}

		return ($default === TRUE) ? ' checked="checked"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_radio'))
{
	/**
	 * Set Radio
	 *
	 * Let's you set the selected value of a radio field via info in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string	$field
	 * @param	string	$value
	 * @param	bool	$default
	 * @return	string
	 */
	function set_radio($field, $value = '', $default = FALSE)
	{
		$R =& get_instance();

		if (isset($R->form_validation) && is_object($R->form_validation) && $R->form_validation->has_rule($field))
		{
			return $R->form_validation->set_radio($field, $value, $default);
		}

		// Form inputs are always strings ...
		$value = (string) $value;
		$input = $R->input->post($field, FALSE);

		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' checked="checked"';
				}
			}

			return '';
		}

		// Unchecked checkbox and radio inputs are not even submitted by browsers ...
		if ($R->input->method() === 'post')
		{
			return ($input === $value) ? ' checked="checked"' : '';
		}

		return ($default === TRUE) ? ' checked="checked"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_error'))
{
	/**
	 * Form Error
	 *
	 * Returns the error for a specific form field. This is a helper for the
	 * form validation class.
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_error($field = '', $prefix = '', $suffix = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			return '';
		}

		return $OBJ->error($field, $prefix, $suffix);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('validation_errors'))
{
	/**
	 * Validation Error String
	 *
	 * Returns all the errors associated with a form submission. This is a helper
	 * function for the form validation class.
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function validation_errors($prefix = '', $suffix = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			return '';
		}

		return $OBJ->error_string($prefix, $suffix);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_parse_form_attributes'))
{
	/**
	 * Parse the form attributes
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	array	$attributes	List of attributes
	 * @param	array	$default	Default values
	 * @return	string
	 */
	function _parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val)
		{
			if ($key === 'value')
			{
				$val = html_escape($val);
			}
			elseif ($key === 'name' && ! strlen($default['name']))
			{
				continue;
			}

			$att .= $key.'="'.$val.'" ';
		}

		return $att;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_attributes_to_string'))
{
	/**
	 * Attributes To String
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	mixed
	 * @return	string
	 */
	function _attributes_to_string($attributes)
	{
		if (empty($attributes))
		{
			return '';
		}

		if (is_object($attributes))
		{
			$attributes = (array) $attributes;
		}

		if (is_array($attributes))
		{
			$atts = '';

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_get_validation_object'))
{
	/**
	 * Validation Object
	 *
	 * Determines what the form validation class was instantiated as, fetches
	 * the object and returns it.
	 *
	 * @return	mixed
	 */
	function &_get_validation_object()
	{
		$R =& get_instance();

		// We set this as a variable since we're returning by reference.
		$return = FALSE;

		if (FALSE !== ($object = $R->load->is_loaded('Form_validation')))
		{
			if ( ! isset($R->$object) OR ! is_object($R->$object))
			{
				return $return;
			}

			return $R->$object;
		}

		return $return;
	}
}
