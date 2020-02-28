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

/*
| -------------------------------------------------------------------
| Foreign Characters
| -------------------------------------------------------------------
| This file contains an array of foreign characters for transliteration
| conversion used by the Text helper
|
*/
$foreign_characters = array(
	'/ä|æ|ǽ/' => 'ae',
	'/ö|œ/' => 'oe',
	'/ü/' => 'ue',
	'/Ä/' => 'Ae',
	'/Ü/' => 'Ue',
	'/Ö/' => 'Oe',
	'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
	'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
	'/Б/' => 'B',
	'/б/' => 'b',
	'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
	'/ç|ć|ĉ|ċ|č/' => 'c',
	'/Д/' => 'D',
	'/д/' => 'd',
	'/Ð|Ď|Đ|Δ/' => 'Dj',
	'/ð|ď|đ|δ/' => 'dj',
	'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
	'/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
	'/Ф/' => 'F',
	'/ф/' => 'f',
	'/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
	'/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
	'/Ĥ|Ħ/' => 'H',
	'/ĥ|ħ/' => 'h',
	'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
	'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
	'/Ĵ/' => 'J',
	'/ĵ/' => 'j',
	'/Ķ|Κ|К/' => 'K',
	'/ķ|κ|к/' => 'k',
	'/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
	'/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
	'/М/' => 'M',
	'/м/' => 'm',
	'/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
	'/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
	'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
	'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
	'/П/' => 'P',
	'/п/' => 'p',
	'/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
	'/ŕ|ŗ|ř|ρ|р/' => 'r',
	'/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
	'/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
	'/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
	'/ț|ţ|ť|ŧ|т/' => 't',
	'/Þ|þ/' => 'th',
	'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
	'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
	'/Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
	'/ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
	'/В/' => 'V',
	'/в/' => 'v',
	'/Ŵ/' => 'W',
	'/ŵ/' => 'w',
	'/Ź|Ż|Ž|Ζ|З/' => 'Z',
	'/ź|ż|ž|ζ|з/' => 'z',
	'/Æ|Ǽ/' => 'AE',
	'/ß/' => 'ss',
	'/Ĳ/' => 'IJ',
	'/ĳ/' => 'ij',
	'/Œ/' => 'OE',
	'/ƒ/' => 'f',
	'/ξ/' => 'ks',
	'/π/' => 'p',
	'/β/' => 'v',
	'/μ/' => 'm',
	'/ψ/' => 'ps',
	'/Ё/' => 'Yo',
	'/ё/' => 'yo',
	'/Є/' => 'Ye',
	'/є/' => 'ye',
	'/Ї/' => 'Yi',
	'/Ж/' => 'Zh',
	'/ж/' => 'zh',
	'/Х/' => 'Kh',
	'/х/' => 'kh',
	'/Ц/' => 'Ts',
	'/ц/' => 'ts',
	'/Ч/' => 'Ch',
	'/ч/' => 'ch',
	'/Ш/' => 'Sh',
	'/ш/' => 'sh',
	'/Щ/' => 'Shch',
	'/щ/' => 'shch',
	'/Ъ|ъ|Ь|ь/' => '',
	'/Ю/' => 'Yu',
	'/ю/' => 'yu',
	'/Я/' => 'Ya',
	'/я/' => 'ya'
);
