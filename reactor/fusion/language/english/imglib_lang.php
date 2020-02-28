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

$lang['imglib_source_image_required'] = 'You must specify a source image in your preferences.';
$lang['imglib_gd_required'] = 'The GD image library is required for this feature.';
$lang['imglib_gd_required_for_props'] = 'Your server must support the GD image library in order to determine the image properties.';
$lang['imglib_unsupported_imagecreate'] = 'Your server does not support the GD function required to process this type of image.';
$lang['imglib_gif_not_supported'] = 'GIF images are often not supported due to licensing restrictions. You may have to use JPG or PNG images instead.';
$lang['imglib_jpg_not_supported'] = 'JPG images are not supported.';
$lang['imglib_png_not_supported'] = 'PNG images are not supported.';
$lang['imglib_jpg_or_png_required'] = 'The image resize protocol specified in your preferences only works with JPEG or PNG image types.';
$lang['imglib_copy_error'] = 'An error was encountered while attempting to replace the file. Please make sure your file directory is writable.';
$lang['imglib_rotate_unsupported'] = 'Image rotation does not appear to be supported by your server.';
$lang['imglib_libpath_invalid'] = 'The path to your image library is not correct. Please set the correct path in your image preferences.';
$lang['imglib_image_process_failed'] = 'Image processing failed. Please verify that your server supports the chosen protocol and that the path to your image library is correct.';
$lang['imglib_rotation_angle_required'] = 'An angle of rotation is required to rotate the image.';
$lang['imglib_invalid_path'] = 'The path to the image is not correct.';
$lang['imglib_invalid_image'] = 'The provided image is not valid.';
$lang['imglib_copy_failed'] = 'The image copy routine failed.';
$lang['imglib_missing_font'] = 'Unable to find a font to use.';
$lang['imglib_save_failed'] = 'Unable to save the image. Please make sure the image and file directory are writable.';
