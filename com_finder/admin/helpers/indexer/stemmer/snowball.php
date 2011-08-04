<?php
/**
 * @version		$Id: snowball.php 922 2010-03-11 20:17:33Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * Snowball stemmer class for the Finder indexer package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderIndexerStemmerSnowball extends FinderIndexerStemmer
{
	/**
	 * Method to stem a token and return the root.
	 *
	 * @param	string		The token to stem.
	 * @param	string		The language of the token.
	 * @return	string		The root token.
	 */
	public function stem($token, $lang)
	{
		// Stem the token if it is not in the cache.
		if (!isset($this->cache[$lang][$token]))
		{
			// Get the stem function from the language string.
			switch ($lang)
			{
				// Danish stemmer.
				case 'da':
					$function = 'stem_danish';
					break;

				// German stemmer.
				case 'de':
					$function = 'stem_german';
					break;

				// English stemmer.
				default:
				case 'en':
					$function = 'stem_english';
					break;

				// Spanish stemmer.
				case 'es':
					$function = 'stem_spanish';
					break;

				// Finnish stemmer.
				case 'fi':
					$function = 'stem_finnish';
					break;

				// French stemmer.
				case 'fr':
					$function = 'stem_french';
					break;

				// Hungarian stemmer.
				case 'hu':
					$function = 'stem_hungarian';
					break;

				// Italian stemmer.
				case 'it':
					$function = 'stem_italian';
					break;

				// Norwegian stemmer.
				case 'nb':
					$function = 'stem_norwegian';
					break;

				// Dutch stemmer.
				case 'nl':
					$function = 'stem_dutch';
					break;

				// Portuguese stemmer.
				case 'pt':
					$function = 'stem_portuguese';
					break;

				// Romanian stemmer.
				case 'ro':
					$function = 'stem_romanian';
					break;

				// Russian stemmer.
				case 'ru':
					$function = 'stem_russian_unicode';
					break;

				// Swedish stemmer.
				case 'sv':
					$function = 'stem_swedish';
					break;

				// Turkish stemmer.
				case 'tr':
					$function = 'stem_turkish_unicode';
					break;
			}

			// Stem the word if the stemmer method exists.
			$this->cache[$lang][$token] = function_exists($function) ? $function($token) : $token;
		}

		return $this->cache[$lang][$token];
	}
}