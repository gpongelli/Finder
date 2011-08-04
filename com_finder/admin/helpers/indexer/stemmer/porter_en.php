<?php
/**
 * @version		$Id: porter_en.php 922 2010-03-11 20:17:33Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @copyright	Copyright (C) 2005 Richard Heyes (http://www.phpguru.org/). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * Porter English stemmer class for the Finder indexer package.
 *
 * This class was adapted from one written by Richard Heyes.
 * See copyright and link information above.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderIndexerStemmerPorter_En extends FinderIndexerStemmer
{
	/**
	 * Regex for matching a consonant.
	 *
	 * @var		string
	 */
	private static $regex_consonant = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';

	/**
	 * Regex for matching a vowel.
	 *
	 * @var		string
	 */
	private static $regex_vowel = '(?:[aeiou]|(?<![aeiou])y)';

	/**
	 * Method to stem a token and return the root.
	 *
	 * @param	string		The token to stem.
	 * @param	string		The language of the token.
	 * @return	string		The root token.
	 */
	public function stem($token, $lang)
	{
		// Check if the token is long enough to merit stemming.
		if (strlen($token) <= 2) {
			return $token;
		}

		// Check if the language is English.
		if ($lang !== 'en') {
			return $token;
		}

		// Stem the token if it is not in the cache.
		if (!isset($this->cache[$lang][$token]))
		{
			// Stem the token.
			$result = $token;
			$result = self::step1ab($result);
			$result = self::step1c($result);
			$result = self::step2($result);
			$result = self::step3($result);
			$result = self::step4($result);
			$result = self::step5($result);

			// Add the token to the cache.
			$this->cache[$lang][$token] = $result;
		}

		return $this->cache[$lang][$token];
	}

	/**
	 * Step 1
	 *
	 * @param	string		The token to stem.
	 */
	private static function step1ab($word)
	{
	    // Part a
	    if (substr($word, -1) == 's') {

	           self::replace($word, 'sses', 'ss')
	        OR self::replace($word, 'ies', 'i')
	        OR self::replace($word, 'ss', 'ss')
	        OR self::replace($word, 's', '');
	    }

	    // Part b
	    if (substr($word, -2, 1) != 'e' OR !self::replace($word, 'eed', 'ee', 0)) { // First rule
	        $v = self::$regex_vowel;

	        // ing and ed
	        if (   preg_match("#$v+#", substr($word, 0, -3)) && self::replace($word, 'ing', '')
	            OR preg_match("#$v+#", substr($word, 0, -2)) && self::replace($word, 'ed', '')) { // Note use of && and OR, for precedence reasons

	            // If one of above two test successful
	            if (    !self::replace($word, 'at', 'ate')
	                AND !self::replace($word, 'bl', 'ble')
	                AND !self::replace($word, 'iz', 'ize')) {

	                // Double consonant ending
	                if (    self::doubleConsonant($word)
	                    AND substr($word, -2) != 'll'
	                    AND substr($word, -2) != 'ss'
	                    AND substr($word, -2) != 'zz') {

	                    $word = substr($word, 0, -1);

	                } else if (self::m($word) == 1 AND self::cvc($word)) {
	                    $word .= 'e';
	                }
	            }
	        }
	    }

	    return $word;
	}


	/**
	 * Step 1c
	 *
	 * @param	string		The token to stem.
	 */
	private static function step1c($word)
	{
	    $v = self::$regex_vowel;

	    if (substr($word, -1) == 'y' && preg_match("#$v+#", substr($word, 0, -1))) {
	        self::replace($word, 'y', 'i');
	    }

	    return $word;
	}


	/**
	 * Step 2
	 *
	 * @param	string		The token to stem.
	 */
	private static function step2($word)
	{
	    switch (substr($word, -2, 1)) {
	        case 'a':
	               self::replace($word, 'ational', 'ate', 0)
	            OR self::replace($word, 'tional', 'tion', 0);
	            break;

	        case 'c':
	               self::replace($word, 'enci', 'ence', 0)
	            OR self::replace($word, 'anci', 'ance', 0);
	            break;

	        case 'e':
	            self::replace($word, 'izer', 'ize', 0);
	            break;

	        case 'g':
	            self::replace($word, 'logi', 'log', 0);
	            break;

	        case 'l':
	               self::replace($word, 'entli', 'ent', 0)
	            OR self::replace($word, 'ousli', 'ous', 0)
	            OR self::replace($word, 'alli', 'al', 0)
	            OR self::replace($word, 'bli', 'ble', 0)
	            OR self::replace($word, 'eli', 'e', 0);
	            break;

	        case 'o':
	               self::replace($word, 'ization', 'ize', 0)
	            OR self::replace($word, 'ation', 'ate', 0)
	            OR self::replace($word, 'ator', 'ate', 0);
	            break;

	        case 's':
	               self::replace($word, 'iveness', 'ive', 0)
	            OR self::replace($word, 'fulness', 'ful', 0)
	            OR self::replace($word, 'ousness', 'ous', 0)
	            OR self::replace($word, 'alism', 'al', 0);
	            break;

	        case 't':
	               self::replace($word, 'biliti', 'ble', 0)
	            OR self::replace($word, 'aliti', 'al', 0)
	            OR self::replace($word, 'iviti', 'ive', 0);
	            break;
	    }

	    return $word;
	}


	/**
	 * Step 3
	 *
	 * @param	string		The token to stem.
	 */
	private static function step3($word)
	{
	    switch (substr($word, -2, 1)) {
	        case 'a':
	            self::replace($word, 'ical', 'ic', 0);
	            break;

	        case 's':
	            self::replace($word, 'ness', '', 0);
	            break;

	        case 't':
	               self::replace($word, 'icate', 'ic', 0)
	            OR self::replace($word, 'iciti', 'ic', 0);
	            break;

	        case 'u':
	            self::replace($word, 'ful', '', 0);
	            break;

	        case 'v':
	            self::replace($word, 'ative', '', 0);
	            break;

	        case 'z':
	            self::replace($word, 'alize', 'al', 0);
	            break;
	    }

	    return $word;
	}


	/**
	 * Step 4
	 *
	 * @param	string		The token to stem.
	 */
	private static function step4($word)
	{
	    switch (substr($word, -2, 1)) {
	        case 'a':
	            self::replace($word, 'al', '', 1);
	            break;

	        case 'c':
	               self::replace($word, 'ance', '', 1)
	            OR self::replace($word, 'ence', '', 1);
	            break;

	        case 'e':
	            self::replace($word, 'er', '', 1);
	            break;

	        case 'i':
	            self::replace($word, 'ic', '', 1);
	            break;

	        case 'l':
	               self::replace($word, 'able', '', 1)
	            OR self::replace($word, 'ible', '', 1);
	            break;

	        case 'n':
	               self::replace($word, 'ant', '', 1)
	            OR self::replace($word, 'ement', '', 1)
	            OR self::replace($word, 'ment', '', 1)
	            OR self::replace($word, 'ent', '', 1);
	            break;

	        case 'o':
	            if (substr($word, -4) == 'tion' OR substr($word, -4) == 'sion') {
	               self::replace($word, 'ion', '', 1);
	            } else {
	                self::replace($word, 'ou', '', 1);
	            }
	            break;

	        case 's':
	            self::replace($word, 'ism', '', 1);
	            break;

	        case 't':
	               self::replace($word, 'ate', '', 1)
	            OR self::replace($word, 'iti', '', 1);
	            break;

	        case 'u':
	            self::replace($word, 'ous', '', 1);
	            break;

	        case 'v':
	            self::replace($word, 'ive', '', 1);
	            break;

	        case 'z':
	            self::replace($word, 'ize', '', 1);
	            break;
	    }

	    return $word;
	}


	/**
	 * Step 5
	 *
	 * @param	string		The token to stem.
	 */
	private static function step5($word)
	{
	    // Part a
	    if (substr($word, -1) == 'e') {
	        if (self::m(substr($word, 0, -1)) > 1) {
	            self::replace($word, 'e', '');

	        } else if (self::m(substr($word, 0, -1)) == 1) {

	            if (!self::cvc(substr($word, 0, -1))) {
	                self::replace($word, 'e', '');
	            }
	        }
	    }

	    // Part b
	    if (self::m($word) > 1 AND self::doubleConsonant($word) AND substr($word, -1) == 'l') {
	        $word = substr($word, 0, -1);
	    }

	    return $word;
	}


	/**
	 * Replaces the first string with the second, at the end of the string. If third
	 * arg is given, then the preceding string must match that m count at least.
	 *
	 * @param	string		String to check
	 * @param	string		Ending to check for
	 * @param	string		Replacement string
	 * @param	integer		Optional minimum number of m() to meet
	 * @return	boolean		Whether the $check string was at the end
	 *						of the $str string. True does not necessarily mean
	 *						that it was replaced.
	 */
	private static function replace(&$str, $check, $repl, $m = null)
	{
	    $len = 0 - strlen($check);

	    if (substr($str, $len) == $check) {
	        $substr = substr($str, 0, $len);
	        if (is_null($m) OR self::m($substr) > $m) {
	            $str = $substr . $repl;
	        }

	        return true;
	    }

	    return false;
	}


	/**
	 * What, you mean it's not obvious from the name?
	 *
	 * m() measures the number of consonant sequences in $str. if c is
	 * a consonant sequence and v a vowel sequence, and <..> indicates arbitrary
	 * presence,
	 *
	 * <c><v>       gives 0
	 * <c>vc<v>     gives 1
	 * <c>vcvc<v>   gives 2
	 * <c>vcvcvc<v> gives 3
	 *
	 * @param	string		The string to return the m count for
	 * @return	integer		The m count
	 */
	private static function m($str)
	{
	    $c = self::$regex_consonant;
	    $v = self::$regex_vowel;

	    $str = preg_replace("#^$c+#", '', $str);
	    $str = preg_replace("#$v+$#", '', $str);

	    preg_match_all("#($v+$c+)#", $str, $matches);

	    return count($matches[1]);
	}


	/**
	 * Returns true/false as to whether the given string contains two
	 * of the same consonant next to each other at the end of the string.
	 *
	 * @param	string		String to check
	 * @return	boolean		Result
	 */
	private static function doubleConsonant($str)
	{
	    $c = self::$regex_consonant;

	    return preg_match("#$c{2}$#", $str, $matches) AND $matches[0]{0} == $matches[0]{1};
	}


	/**
	 * Checks for ending CVC sequence where second C is not W, X or Y
	 *
	 * @param	string		String to check
	 * @return	boolean		Result
	 */
	private static function cvc($str)
	{
	    $c = self::$regex_consonant;
	    $v = self::$regex_vowel;

	    return     preg_match("#($c$v$c)$#", $str, $matches)
	           AND strlen($matches[1]) == 3
	           AND $matches[1]{2} != 'w'
	           AND $matches[1]{2} != 'x'
	           AND $matches[1]{2} != 'y';
	}
}