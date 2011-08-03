<?php
/**
 * @version		$Id: rtf.php 922 2010-03-11 20:17:33Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * RTF Parser class for the Finder indexer package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderIndexerParserRtf extends FinderIndexerParser
{
	/**
	 * Method to process RTF input and extract the plain text.
	 *
	 * @param	string		The input to process.
	 * @return	string		The plain text input.
	 */
	protected function process($input)
	{
		// Remove embeded pictures.
		$input = preg_replace('#{\\\pict[^}]*}#mis', '', $input);

		// Remove control characters.
		$input = str_replace(array('{', '}', "\\\n"), array(' ', ' ', "\n"), $input);
		$input = preg_replace ('#\\\([^;]+?);#mis', ' ', $input);
		$input = preg_replace ('#\\\[\'a-zA-Z0-9]+#mis', ' ', $input);

		return $input;
	}
}