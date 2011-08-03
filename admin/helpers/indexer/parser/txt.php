<?php
/**
 * @version		$Id: txt.php 922 2010-03-11 20:17:33Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * Text Parser class for the Finder indexer package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderIndexerParserTxt extends FinderIndexerParser
{
	/**
	 * Method to process Text input and extract the plain text.
	 *
	 * @param	string		The input to process.
	 * @return	string		The plain text input.
	 */
	protected function process($input)
	{
		return $input;
	}
}