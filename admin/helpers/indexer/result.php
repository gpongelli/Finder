<?php
/**
 * @version		$Id: result.php 922 2010-03-11 20:17:33Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

JLoader::register('FinderIndexer', dirname(__FILE__).DS.'indexer.php');

/**
 * Result class for the Finder indexer package.
 *
 * This class uses magic __get() and __set() methods to prevent properties
 * being added that might confuse the system. All properties not explicitly
 * declared will be pushed into the _elements array and can be accessed
 * explicitly using the getElement() method.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderIndexerResult
{
	/**
	 * @var		array		An array of extra result properties.
	 */
	protected $_elements = array();

	/**
	 * This array tells the indexer which properties should be indexed and what
	 * weights to use for those properties.
	 *
	 * @var		array		The indexer processing instructions.
	 */
	protected $_instructions = array(
		FinderIndexer::TITLE_CONTEXT	=> array('title', 'subtitle', 'id'),
		FinderIndexer::TEXT_CONTEXT		=> array('summary', 'body'),
		FinderIndexer::META_CONTEXT		=> array('meta', 'list_price', 'sale_price'),
		FinderIndexer::PATH_CONTEXT		=> array('path', 'alias'),
		FinderIndexer::MISC_CONTEXT		=> array('comments'),
	);

	/**
	 * The indexer will use this data to create taxonomy mapping entries for
	 * the item so that it can be filtered by type, label, category, section,
	 * or whatever.
	 *
	 * @var		array		The taxonomy data for the node.
	 */
	protected $_taxonomy = array();

	/**
	 * @var		string		The content URL.
	 */
	public $url;

	/**
	 * @var		string		The content route.
	 */
	public $route;

	/**
	 * @var		string		The content title.
	 */
	public $title;

	/**
	 * @var		string		The content description.
	 */
	public $description;

	/**
	 * @var		integer		The size of the content data.
	 */
	public $size;

	/**
	 * @var		integer		The published state of the result.
	 */
	public $published;

	/**
	 * @var		integer		The content published state.
	 */
	public $state;

	/**
	 * @var		integer		The content access level.
	 */
	public $access;

	/**
	 * @var		string		The content language.
	 */
	public $language = 'en-GB';

	/**
	 * @var		string		The publishing start date.
	 */
	public $publish_start_date;

	/**
	 * @var		string		The publishing end date.
	 */
	public $publish_end_date;

	/**
	 * @var		string		The generic start date.
	 */
	public $start_date;

	/**
	 * @var		string		The generic end date.
	 */
	public $end_date;

	/**
	 * @var		mixed		The item list price.
	 */
	public $list_price;

	/**
	 * @var		mixed		The item sale price.
	 */
	public $sale_price;

	/**
	 * @var		integer		The content type id. This is set by the adapter.
	 */
	public $type_id;

	/**
	 * The magic set method is used to push aditional values into the elements
	 * array in order to preserve the cleanliness of the object.
	 *
	 * @param	string		The name of the element.
	 * @param	mixed		The value of the element.
	 * @return	void
	 */
	public function __set($name, $value)
	{
		$this->_elements[$name] = $value;
	}

	/**
	 * The magic get method is used to retrieve additional element values
	 * from the elements array.
	 *
	 * @param	string		The name of the element.
	 * @return	mixed		The value of the element if set, null otherwise.
	 */
	public function __get($name)
	{
		// Get the element value if set.
		if (array_key_exists($name, $this->_elements)) {
			return $this->_elements[$name];
		} else {
			return null;
		}
	}

	/**
	 * The magic isset method is used to check the state of additional element
	 * values in the elements array.
	 *
	 * @param	string		The name of the element.
	 * @return	boolean		True if set, false otherwise.
	 */
	public function __isset($name)
	{
		return isset($this->_elements[$name]);
	}

	/**
	 * The magic unset method is used to unset additional element values in the
	 * elements array.
	 *
	 * @param	string		The name of the element.
	 * @return	void
	 */
	public function __unset($name)
	{
		unset($this->_elements[$name]);
	}

	/**
	 * Method to retrieve additional element values from the elements array.
	 *
	 * @param	string		The name of the element.
	 * @return	mixed		The value of the element if set, null otherwise.
	 */
	public function getElement($name)
	{
		// Get the element value if set.
		if (array_key_exists($name, $this->_elements)) {
			return $this->_elements[$name];
		} else {
			return null;
		}
	}

	/**
	 * Method to set additional element values in the elements array.
	 *
	 * @param	string		The name of the element.
	 * @param	mixed		The value of the element.
	 * @return 	void
	 */
	public function setElement($name, $value)
	{
		$this->_elements[$name] = $value;
	}

	/**
	 * Method to get all processing instructions.
	 *
	 * @return	array		An array of processing instructions.
	 */
	public function getInstructions()
	{
		return $this->_instructions;
	}

	/**
	 * Method to add a processing instruction for an item property.
	 *
	 * @param	string		The group to associate the property with.
	 * @param	string		The property to process.
	 * @return	void
	 */
	public function addInstruction($group, $property)
	{
		// Check if the group exists. We can't add instructions for unknown groups.
		if (array_key_exists($group, $this->_instructions))
		{
			// Check if the property exists in the group.
			if (!in_array($property, $this->_instructions[$group]))
			{
				// Add the property to the group.
				$this->_instructions[$group][] = $property;
			}
		}
	}

	/**
	 * Method to remove a processing instruction for an item property.
	 *
	 * @param	string		The group to associate the property with.
	 * @param	string		The property to process.
	 * @return	void
	 */
	public function removeInstruction($group, $property)
	{
		// Check if the group exists. We can't remove instructions for unknown groups.
		if (array_key_exists($group, $this->_instructions))
		{
			// Search for the property in the group.
			$key = array_search($property, $this->_instructions[$group]);

			// If the property was found, remove it.
			if ($key !== false) {
				unset($this->_instructions[$group][$key]);
			}
		}
	}

	/**
	 * Method to get the taxonomy maps for an item.
	 *
	 * @param	string		The taxonomy branch to get.
	 * @return	array		An array of taxonomy maps.
	 */
	public function getTaxonomy($branch = null)
	{
		// Get the taxonomy branch if available.
		if ($branch !== null && isset($this->_taxonomy[$branch]))
		{
			// Filter the input.
			if (JX_FINDER_UNICODE) {
				$branch	= preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $branch);
			} else {
				$quotes = html_entity_decode('&#8216;&#8217;&#39;', ENT_QUOTES, 'UTF-8');
				$branch	= preg_replace('#[^\w\d'.$quotes.'+-.,]+#mi', ' ', $branch);
			}

			return $this->_taxonomy[$branch];
		}

		return $this->_taxonomy;
	}

	/**
	 * Method to add a taxonomy map for an item.
	 *
	 * @param	string		The title of the taxonomy branch to add the node to.
	 * @param	string		The title of the taxonomy node.
	 * @param	string		The published state of the taxonomy node.
	 * @param	string		The access level of the taxonomy node.
	 * @return	void
	 */
	public function addTaxonomy($branch, $title, $state = 1, $access = 0)
	{
		// Filter the input.
		if (JX_FINDER_UNICODE) {
			$branch	= preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $branch);
			$title	= preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $title);
		} else {
			$quotes = html_entity_decode('&#8216;&#8217;&#39;', ENT_QUOTES, 'UTF-8');
			$branch	= preg_replace('#[^\w\d'.$quotes.'+-.,]+#mi', ' ', $branch);
			$title	= preg_replace('#[^\w\d'.$quotes.'+-.,]+#mi', ' ', $title);
		}

		// Create the taxonomy node.
		$node = new JObject();
		$node->title	= $title;
		$node->state	= (int)$state;
		$node->access	= (int)$access;

		// Add the node to the taxonomy branch.
		$this->_taxonomy[$branch][$node->title] = $node;
	}
}