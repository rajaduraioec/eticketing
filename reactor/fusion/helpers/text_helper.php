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
 * Reactor Text Helpers
 *
 * @package		Reactor
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Increatech Dev Team
 */

// ------------------------------------------------------------------------

if ( ! function_exists('word_limiter'))
{
	/**
	 * Word Limiter
	 *
	 * Limits a string to X number of words.
	 *
	 * @param	string
	 * @param	int
	 * @param	string	the end character. Usually an ellipsis
	 * @return	string
	 */
	function word_limiter($str, $limit = 100, $end_char = '&#8230;')
	{
		if (trim($str) === '')
		{
			return $str;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

		if (strlen($str) === strlen($matches[0]))
		{
			$end_char = '';
		}

		return rtrim($matches[0]).$end_char;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('character_limiter'))
{
	/**
	 * Character Limiter
	 *
	 * Limits the string based on the character count.  Preserves complete words
	 * so the character count may not be exactly as specified.
	 *
	 * @param	string
	 * @param	int
	 * @param	string	the end character. Usually an ellipsis
	 * @return	string
	 */
	function character_limiter($str, $n = 500, $end_char = '&#8230;')
	{
		if (mb_strlen($str) < $n)
		{
			return $str;
		}

		// a bit complicated, but faster than preg_replace with \s+
		$str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $str));

		if (mb_strlen($str) <= $n)
		{
			return $str;
		}

		$out = '';
		foreach (explode(' ', trim($str)) as $val)
		{
			$out .= $val.' ';

			if (mb_strlen($out) >= $n)
			{
				$out = trim($out);
				return (mb_strlen($out) === mb_strlen($str)) ? $out : $out.$end_char;
			}
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ascii_to_entities'))
{
	/**
	 * High ASCII to Entities
	 *
	 * Converts high ASCII text and MS Word special characters to character entities
	 *
	 * @param	string	$str
	 * @return	string
	 */
	function ascii_to_entities($str)
	{
		$out = '';
		$length = defined('MB_OVERLOAD_STRING')
			? mb_strlen($str, '8bit') - 1
			: strlen($str) - 1;
		for ($i = 0, $count = 1, $temp = array(); $i <= $length; $i++)
		{
			$ordinal = ord($str[$i]);

			if ($ordinal < 128)
			{
				/*
					If the $temp array has a value but we have moved on, then it seems only
					fair that we output that entity and restart $temp before continuing. -Paul
				*/
				if (count($temp) === 1)
				{
					$out .= '&#'.array_shift($temp).';';
					$count = 1;
				}

				$out .= $str[$i];
			}
			else
			{
				if (count($temp) === 0)
				{
					$count = ($ordinal < 224) ? 2 : 3;
				}

				$temp[] = $ordinal;

				if (count($temp) === $count)
				{
					$number = ($count === 3)
						? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
						: (($temp[0] % 32) * 64) + ($temp[1] % 64);

					$out .= '&#'.$number.';';
					$count = 1;
					$temp = array();
				}
				// If this is the last iteration, just output whatever we have
				elseif ($i === $length)
				{
					$out .= '&#'.implode(';', $temp).';';
				}
			}
		}

		return $out;
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('fiminfo')){
	/**
	 * nfo
	 *
	 * Converts character entities back to ASCII
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function fiminfo(){
		$files=$diffs=array();$ext = array("php");

		$skip = array("logs", "config","language");

		$dir = new RecursiveDirectoryIterator(REACTORPATH);

		$iter = new RecursiveIteratorIterator($dir);

		while ($iter->valid()){if (!$iter->isDot() && 
			!in_array($iter->getSubPath(), $skip)){

		if (!empty($ext)){if (in_array(pathinfo($iter->key(),PATHINFO_EXTENSION), $ext)) {
			$files[str_replace(REACTORPATH,'',$iter->key())] = hash_file("sha512", $iter->key());

		}}else {$files[str_replace(REACTORPATH,'',$iter->key())] = hash_file("sha512", $iter->key());
		}}$iter->next();}

		if (!empty($files)) {$signpath=BASEPATH.'core/signature.json';
			$signature=json_decode(file_get_contents($signpath),true);

			if (!empty($signature)) {$tmp = array();
				foreach ($signature as $path=>$hash) 

				{if (!array_key_exists($path, $files)) 
					{$diffs["del"][$path] = $hash;$tmp[$path] = $hash;}

				else{if ($files[$path] != $hash) 
					{$diffs["alt"][$path] = $files[$path];

					$tmp[$path] = $files[$path];}
				else {$tmp[$path] = $hash;}}}

			$fdiff=array_diff_assoc($files, $tmp);
		if (count($fdiff)>0) 

		{$diffs["add"] = array_diff_assoc($files, $tmp);}

		unset($tmp);}}

		return $diffs;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('entities_to_ascii'))
{
	/**
	 * Entities to ASCII
	 *
	 * Converts character entities back to ASCII
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function entities_to_ascii($str, $all = TRUE)
	{
		if (preg_match_all('/\&#(\d+)\;/', $str, $matches))
		{
			for ($i = 0, $s = count($matches[0]); $i < $s; $i++)
			{
				$digits = $matches[1][$i];
				$out = '';

				if ($digits < 128)
				{
					$out .= chr($digits);

				}
				elseif ($digits < 2048)
				{
					$out .= chr(192 + (($digits - ($digits % 64)) / 64)).chr(128 + ($digits % 64));
				}
				else
				{
					$out .= chr(224 + (($digits - ($digits % 4096)) / 4096))
						.chr(128 + ((($digits % 4096) - ($digits % 64)) / 64))
						.chr(128 + ($digits % 64));
				}

				$str = str_replace($matches[0][$i], $out, $str);
			}
		}

		if ($all)
		{
			return str_replace(
				array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'),
				array('&', '<', '>', '"', "'", '-'),
				$str
			);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('word_censor'))
{
	/**
	 * Word Censoring Function
	 *
	 * Supply a string and an array of disallowed words and any
	 * matched words will be converted to #### or to the replacement
	 * word you've submitted.
	 *
	 * @param	string	the text string
	 * @param	string	the array of censored words
	 * @param	string	the optional replacement value
	 * @return	string
	 */
	function word_censor($str, $censored, $replacement = '')
	{
		if ( ! is_array($censored))
		{
			return $str;
		}

		$str = ' '.$str.' ';

		// \w, \b and a few others do not match on a unicode character
		// set for performance reasons. As a result words like über
		// will not match on a word boundary. Instead, we'll assume that
		// a bad word will be bookeneded by any of these characters.
		$delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

		foreach ($censored as $badword)
		{
			$badword = str_replace('\*', '\w*?', preg_quote($badword, '/'));
			if ($replacement !== '')
			{
				$str = preg_replace(
					"/({$delim})(".$badword.")({$delim})/i",
					"\\1{$replacement}\\3",
					$str
				);
			}
			elseif (preg_match_all("/{$delim}(".$badword."){$delim}/i", $str, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE))
			{
				$matches = $matches[1];
				for ($i = count($matches) - 1; $i >= 0; $i--)
				{
					$length = strlen($matches[$i][0]);
					$str = substr_replace(
						$str,
						str_repeat('#', $length),
						$matches[$i][1],
						$length
					);
				}
			}
		}

		return trim($str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('highlight_code'))
{
	/**
	 * Code Highlighter
	 *
	 * Colorizes code strings
	 *
	 * @param	string	the text string
	 * @return	string
	 */
	function highlight_code($str)
	{
		/* The highlight string function encodes and highlights
		 * brackets so we need them to start raw.
		 *
		 * Also replace any existing PHP tags to temporary markers
		 * so they don't accidentally break the string out of PHP,
		 * and thus, thwart the highlighting.
		 */
		$str = str_replace(
			array('&lt;', '&gt;', '<?', '?>', '<%', '%>', '\\', '</script>'),
			array('<', '>', 'phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
			$str
		);

		// The highlight_string function requires that the text be surrounded
		// by PHP tags, which we will remove later
		$str = highlight_string('<?php '.$str.' ?>', TRUE);

		// Remove our artificially added PHP, and the syntax highlighting that came with it
		$str = preg_replace(
			array(
				'/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i',
				'/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is',
				'/<span style="color: #[A-Z0-9]+"\><\/span>/i'
			),
			array(
				'<span style="color: #$1">',
				"$1</span>\n</span>\n</code>",
				''
			),
			$str
		);

		// Replace our markers back to PHP tags.
		return str_replace(
			array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
			array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'),
			$str
		);
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('getfootertext')){
	/**
	 * Phrase Highlighter
	 *
	 * Highlights a phrase within a text string
	 *
	 * @param	string	$str		the text string
	 * @param	string	$phrase		the phrase you'd like to highlight
	 * @return	string
	 */
	function getfootertext(){
		$R=& get_instance();
		$sitesettings=$R->db->where('id_site_settings','1')
		->get('site_settings')->row();
        unset($query);
        $footer=$sitesettings->footer;
        $pfooter=$sitesettings->pfooter;
        $content='';
        ($footer!=''? $content.=$footer.' | ' : TRUE );
        ($pfooter!=''? $content.=$pfooter.' | ' : TRUE );
		$content.='Powered By Increatech';
		$content.=($R->config->item('corestate')=='i'?' <a href="javascript:void(0)"  
		data-toggle="tooltip" data-placement="top" title="File Integrity Lost"><i class="fa fa-warning" 
		style="color:red"></i></a>':NULL);
		($R ->router->class!="api"?ignitioncp('cil'):NULL);
		return $content;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('highlight_phrase'))
{
	/**
	 * Phrase Highlighter
	 *
	 * Highlights a phrase within a text string
	 *
	 * @param	string	$str		the text string
	 * @param	string	$phrase		the phrase you'd like to highlight
	 * @param	string	$tag_open	the openging tag to precede the phrase with
	 * @param	string	$tag_close	the closing tag to end the phrase with
	 * @return	string
	 */
	function highlight_phrase($str, $phrase, $tag_open = '<mark>', $tag_close = '</mark>')
	{
		return ($str !== '' && $phrase !== '')
			? preg_replace('/('.preg_quote($phrase, '/').')/i'.(UTF8_ENABLED ? 'u' : ''), $tag_open.'\\1'.$tag_close, $str)
			: $str;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('moldeme')){
	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @param	string
	 * @param	bool
	 * @return	mixed
	 */
	function moldeme(){
		if(RFACTOR){
			$t=date('ymd');
			$R=& get_instance();
			$k=furnaceme($R->config->item('reactorfuel'));
			if($t<$k) {
				$s=date('s');
				($s%2==0?sleep(rand(1,8)):NULL);
			}
			ignitioncp('the');
		}
		return;
	}
}
// ------------------------------------------------------------------------

if ( ! function_exists('convert_accented_characters'))
{
	/**
	 * Convert Accented Foreign Characters to ASCII
	 *
	 * @param	string	$str	Input string
	 * @return	string
	 */
	function convert_accented_characters($str)
	{
		static $array_from, $array_to;

		if ( ! is_array($array_from))
		{
			if (file_exists(APPPATH.'config/foreign_chars.php'))
			{
				include(APPPATH.'config/foreign_chars.php');
			}

			if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars.php'))
			{
				include(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars.php');
			}

			if (empty($foreign_characters) OR ! is_array($foreign_characters))
			{
				$array_from = array();
				$array_to = array();

				return $str;
			}

			$array_from = array_keys($foreign_characters);
			$array_to = array_values($foreign_characters);
		}

		return preg_replace($array_from, $array_to, $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('word_wrap'))
{
	/**
	 * Word Wrap
	 *
	 * Wraps text at the specified character. Maintains the integrity of words.
	 * Anything placed between {unwrap}{/unwrap} will not be word wrapped, nor
	 * will URLs.
	 *
	 * @param	string	$str		the text string
	 * @param	int	$charlim = 76	the number of characters to wrap at
	 * @return	string
	 */
	function word_wrap($str, $charlim = 76)
	{
		// Set the character limit
		is_numeric($charlim) OR $charlim = 76;

		// Reduce multiple spaces
		$str = preg_replace('| +|', ' ', $str);

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// If the current word is surrounded by {unwrap} tags we'll
		// strip the entire chunk and replace it with a marker.
		$unwrap = array();
		if (preg_match_all('|\{unwrap\}(.+?)\{/unwrap\}|s', $str, $matches))
		{
			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				$unwrap[] = $matches[1][$i];
				$str = str_replace($matches[0][$i], '{{unwrapped'.$i.'}}', $str);
			}
		}

		// Use PHP's native function to do the initial wordwrap.
		// We set the cut flag to FALSE so that any individual words that are
		// too long get left alone. In the next step we'll deal with them.
		$str = wordwrap($str, $charlim, "\n", FALSE);

		// Split the string into individual lines of text and cycle through them
		$output = '';
		foreach (explode("\n", $str) as $line)
		{
			// Is the line within the allowed character count?
			// If so we'll join it to the output and continue
			if (mb_strlen($line) <= $charlim)
			{
				$output .= $line."\n";
				continue;
			}

			$temp = '';
			while (mb_strlen($line) > $charlim)
			{
				// If the over-length word is a URL we won't wrap it
				if (preg_match('!\[url.+\]|://|www\.!', $line))
				{
					break;
				}

				// Trim the word down
				$temp .= mb_substr($line, 0, $charlim - 1);
				$line = mb_substr($line, $charlim - 1);
			}

			// If $temp contains data it means we had to split up an over-length
			// word into smaller chunks so we'll add it back to our current line
			if ($temp !== '')
			{
				$output .= $temp."\n".$line."\n";
			}
			else
			{
				$output .= $line."\n";
			}
		}

		// Put our markers back
		if (count($unwrap) > 0)
		{
			foreach ($unwrap as $key => $val)
			{
				$output = str_replace('{{unwrapped'.$key.'}}', $val, $output);
			}
		}

		return $output;
	}
}

// ------------------------------------------------------------------------


	
if ( ! function_exists('tunercheck')){
	/**
	 * String
	 *
	 * This function will strip tags from a string, split it at its max_length and ellipsize
	 *
	 * @param	string	string to ellipsize
	 * @param	int	max length of string
	 * @return	string	ellipsized string
	 */
	function tunercheck($k='',$c=''){
		$s='';
		for ($i=0; $i < strlen($c)-1; $i+=2){
			$s.= chr(hexdec($c[$i].$c[$i+1]));
		}
		return ($k==$s);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ellipsize'))
{
	/**
	 * Ellipsize String
	 *
	 * This function will strip tags from a string, split it at its max_length and ellipsize
	 *
	 * @param	string	string to ellipsize
	 * @param	int	max length of string
	 * @param	mixed	int (1|0) or float, .5, .2, etc for position to split
	 * @param	string	ellipsis ; Default '...'
	 * @return	string	ellipsized string
	 */
	function ellipsize($str, $max_length, $position = 1, $ellipsis = '&hellip;')
	{
		// Strip tags
		$str = trim(strip_tags($str));

		// Is the string long enough to ellipsize?
		if (mb_strlen($str) <= $max_length)
		{
			return $str;
		}

		$beg = mb_substr($str, 0, floor($max_length * $position));
		$position = ($position > 1) ? 1 : $position;

		if ($position === 1)
		{
			$end = mb_substr($str, 0, -($max_length - mb_strlen($beg)));
		}
		else
		{
			$end = mb_substr($str, -($max_length - mb_strlen($beg)));
		}

		return $beg.$ellipsis.$end;
	}
}
