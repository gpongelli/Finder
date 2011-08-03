<?php
/**
 * @version		$Id: joomla_sections.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderJoomla_Sections
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_joomla_sections');
$lang->load('plg_finder_joomla_sections.custom');

/**
 * Finder adapter for Joomla Sections.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderJoomla_Sections
 */
class plgFinderJoomla_Sections extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Joomla_Sections';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'section';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Section';

	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @param	integer		The id of the item.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onBeforeSaveJoomlaSection($id)
	{
		// Queue the item to be reindexed.
		FinderIndexerQueue::add($this->_context, $id, JFactory::getDate()->toMySQL());

		return true;
	}

	/**
	 * Method to update the item link information when the item section is
	 * changed. This is fired when the item section is published, unpublished,
	 * or an access level is changed.
	 *
	 * @param	array		An array of item ids.
	 * @param	string		The property that is being changed.
	 * @param	integer		The new value of that property.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onChangeJoomlaSection($ids, $property, $value)
	{
		// Check if we are changing the section state.
		if ($property === 'published')
		{
			// Update the item.
			$this->_change($ids, 'state', $value);
		}
		// Check if we are changing the section access level.
		elseif ($property === 'access')
		{
			// Update the item.
			$this->_change($ids, 'access', $value);
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
	public function onDeleteJoomlaSection($ids)
	{
		// Remove the items.
		return $this->_remove($ids);
	}

	/**
	 * Method to reindex the link information for an item that has been saved.
	 *
	 * @param	integer		The id of the item.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onSaveJoomlaSection($id)
	{
		// Run the setup method.
		$this->_setup();

		// Get the item.
		$item = $this->_getItem($id);

		// Index the item.
		$this->_index($item);

		return true;
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function _index(FinderIndexerResult $item)
	{
		// Initialize the item parameters.
		$item->params	= new JParameter($item->params);

		// Trigger the onPrepareContent event.
		$item->summary	= FinderIndexerHelper::prepareContent($item->summary, $item->params);

		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->id);
		$item->route	= ContentHelperRoute::getSectionRoute($item->id);
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->_getItemMenuTitle($item->url);

		// Adjust the title if necessary.
		if (!empty($title) && $this->params->get('use_menu_title', true)) {
			$item->title = $title;
		}

		// Set the language.
		$item->language	= $item->params->get('language', FinderIndexerHelper::getDefaultLanguage());

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Section');

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
	protected function _setup()
	{
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
	protected function _getListQuery($sql = null)
	{
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : new JDatabaseQuery();
		$sql->select('a.id, a.title, a.alias, a.description AS summary');
		$sql->select('a.published AS state, a.access, a.ordering, a.params');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->from('#__sections AS a');
		$sql->where('a.scope = "content"');

		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	mixed		The id of the item.
	 * @return	string		The URL of the item.
	 */
	protected function _getURL($id)
	{
		return 'index.php?option=com_content&view=section&id='.$id;
	}
}