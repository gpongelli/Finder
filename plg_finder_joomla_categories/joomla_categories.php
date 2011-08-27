<?php
/**
 * @version		$Id: joomla_categories.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderJoomla_Categories
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder adapter for Joomla Categories.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderJoomla_Categories
 */
class plgFinderJoomla_Categories extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Joomla_Categories';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'category';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Category';

	/**
	 * Constructor
	 *
	 * @param	object	$subject	The object to observe
	 * @param	array	$config		An array that holds the plugin configuration
	 *
	 * @return	void
	 * @since	1.8
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @param	integer		The id of the item.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onBeforeSaveJoomlaCategory($id)
	{
		JLog::add('plgFinderJoomla_Categories::onBeforeSaveJoomlaCategory', JLog::INFO);

		// Queue the item to be reindexed.
		FinderIndexerQueue::add($this->_context, $id, JFactory::getDate()->toMySQL());

		return true;
	}

	/**
	 * Method to update the item link information when the item category is
	 * changed. This is fired when the item category is published, unpublished,
	 * or an access level is changed.
	 *
	 * @param	array		An array of item ids.
	 * @param	string		The property that is being changed.
	 * @param	integer		The new value of that property.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onChangeJoomlaCategory($ids, $property, $value)
	{
		JLog::add('plgFinderJoomla_Categories::onChangeJoomlaCategory', JLog::INFO);

		// Check if we are changing the category state.
		if ($property === 'published')
		{
			// The article published state is tied to the category
			// published state so we need to look up all published states
			// before we change anything.
			foreach ($ids as $id)
			{
				$sql = clone($this->_getStateQuery());
				$sql->where('c.id = '.(int)$id);

				// Get the published states.
				$this->_db->setQuery($sql);
				$items = $this->_db->loadObjectList();

				// Adjust the state for each item within the category.
				foreach ($items as $item)
				{
					// Translate the state.
					$temp = $this->_translateState($value);

					// Update the item.
					$this->change($item->id, 'state', $temp);
				}
			}
		}
		// Check if we are changing the category access level.
		elseif ($property === 'access')
		{
			// The article access state is tied to the category
			// access state so we need to look up all access states
			// before we change anything.
			foreach ($ids as $id)
			{
				$sql = clone($this->_getStateQuery());
				$sql->where('c.id = '.(int)$id);

				// Get the published states.
				$this->_db->setQuery($sql);
				$items = $this->_db->loadObjectList();

				// Adjust the state for each item within the category.
				foreach ($items as $item)
				{
					// Translate the state.
					$temp = max($value);

					// Update the item.
					$this->change($item->id, 'access', $temp);
				}
			}
		}

		return true;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onDeleteJoomlaCategory($ids)
	{
		JLog::add('plgFinderJoomla_Categories::onDeleteJoomlaCategory', JLog::INFO);

		// Remove the items.
		return $this->remove($ids);
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function index(FinderIndexerResult $item)
	{
		JLog::add('plgFinderJoomla_Categories::index', JLog::INFO);

		// Initialize the item parameters.
		$registry = new JRegistry;
		$registry->loadString($item->params);
		$item->params	= $registry;

		// Trigger the onPrepareContent event.
		$item->summary	= FinderIndexerHelper::prepareContent($item->summary, $item->params);

		// Build the necessary route and path information.
		$item->url		= $this->getURL($item->id);
		$item->route	= ContentHelperRoute::getCategoryRoute($item->slug, $item->catid);
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->getItemMenuTitle($item->url);

		// Adjust the title if necessary.
		if (!empty($title) && $this->params->get('use_menu_title', true)) {
			$item->title = $title;
		}

		// Translate the state. Categories should only be published if the section is published.
		$item->state = $this->_translateState($item->state);

		// Set the language.
		$item->language	= $item->params->get('language', FinderIndexerHelper::getDefaultLanguage());

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Category');

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return	boolean		True on success.
	 */
	protected function setup()
	{
		JLog::add('plgFinderJoomla_Categories::setup', JLog::INFO);

		// Load dependent classes.
		require_once JPATH_SITE.'/components/com_content/helpers/route.php';

		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param	mixed		A JDatabaseQuery object or null.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function getListQuery($sql = null)
	{
		JLog::add('plgFinderJoomla_Categories::getListQuery', JLog::INFO);

		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
		$sql->select('a.id, a.title, a.alias, a.description AS summary');
		$sql->select('a.published AS state, a.access, a.params');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->from('#__categories AS a');
		$sql->where($db->quoteName('a.id').' > 1');

		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	mixed		The id of the item.
	 * @return	string		The URL of the item.
	 */
	protected function getURL($id)
	{
		JLog::add('plgFinderJoomla_Categories::getURL', JLog::INFO);

		return 'index.php?option=com_content&view=category&id='.$id;
	}

	/**
	 * Method to translate the native content states into states that the
	 * indexer can use.
	 *
	 * @param	integer		The category state.
	 * @return	integer		The translated indexer state.
	 */
	private function _translateState($category)
	{
		JLog::add('plgFinderJoomla_Categories::_translateState', JLog::INFO);

		// Translate the state.
		switch ($category)
		{
			// Unpublished.
			case 0:
				return 0;

			// Published.
			default:
			case 1:
				return 1;
		}
	}

	/**
	 * Method to get a SQL query to load the published and access states for
	 * a category and section.
	 *
	 * @return	object		A JDatabaseQuery object.
	 */
	private function _getStateQuery()
	{
		JLog::add('plgFinderJoomla_Categories::_getStateQuery', JLog::INFO);

		$sql = $this->_db->getQuery(true);
		$sql->select($db->quoteName('c.id'));
		$sql->select($db->quoteName('c.published').' AS cat_state');
		$sql->select($db->quoteName('c.access').' AS cat_access');
		$sql->from($db->quoteName('#__categories').' AS c');

		return $sql;
	}
}
