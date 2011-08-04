<?php
/**
 * @version		$Id: stemmer.php 922 2010-03-11 20:17:33Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * Stemmer base class for the Finder indexer package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
abstract class FinderIndexerStemmer
{
	/**
	 * @var		array		An internal cache of stemmed tokens.
	 */
	public $cache = array();

	/**
	 * Method to get a stemmer, creating it if necessary.
	 *
	 * @param	string		The type of stemmer to load.
	 * @return	object		A FinderIndexerStemmer.
	 * @throws	Exception on invalid stemmer.
	 */
	public static function getInstance($adapter)
	{
		static $instances;

		// Only create one stemmer for each adapter.
		if (isset($instances[$adapter])) {
			return $instances[$adapter];
		}

		// Create an array of instances if necessary.
		if (!is_array($instances)) {
			$instances = array();
		}

		// Setup the adapter for the stemmer.
		$adapter	= JFilterInput::clean($adapter, 'cmd');
		$path		= dirname(__FILE__).DS.'stemmer'.DS.$adapter.'.php';
		$class		= 'FinderIndexerStemmer'.ucfirst($adapter);

		// Check if a stemmer exists for the adapter.
		if (file_exists($path)) {
			// Instantiate the stemmer.
			require_once $path;
			$instances[$adapter] = new $class;
		}
		else {
			// Throw invalid adapter exception.
			throw new Exception(JText::sprintf('FINDER_INDEXER_INVALID_STEMMER', $adapter));
		}

		return $instances[$adapter];
	}

	/**
	 * Method to stem a token and return the root.
	 *
	 * @param	string		The token to stem.
	 * @param	string		The language of the token.
	 * @return	string		The root token.
	 */
	abstract public function stem($token, $lang);
}