<?php
/**
 * @version		$Id: zine_publications.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Publications
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_zine_publications');
$lang->load('plg_finder_zine_publications.custom');

/**
 * Finder adapter for Zine Publications.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Publications
 */
class plgFinderZine_Publications extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Zine_Publications';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'publication';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Publication';

	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param	array		An array of item ids.
	 * @param	string		The property that is being changed.
	 * @param	integer		The new value of that property.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onChangeZinePublication($ids, $property, $value)
	{
		// Perform any necessary value translations.
		if ($property === 'published') {
			$property = 'state';
		}
		// Update the items.
		$this->_change($ids, $property, $value);

		return true;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onDeleteZinePublication($ids)
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
	public function onSaveZinePublication($id)
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
		$item->media	= new JParameter($item->media);

		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->id);
		$item->itemid	= ZineHelperRoute::getItemid('publication', $item->id);
		$itemid			= $item->itemid ? '&Itemid='.$item->itemid : null;
		$item->route	= $this->_getURL($item->slug).$itemid;;
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Rename some properties.
		$item->state = $item->published;

		// Remove unnecessary properties.
		unset($item->published);

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Publication');

		// Add the publication taxonomy data.
		$item->addTaxonomy('Publication', $item->title, $item->state, $item->access);

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
		require_once JPATH_SITE.'/components/com_zine/helpers/route.php';

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
		$sql->select('a.id, a.title, a.alias, a.subtitle, a.body AS summary, a.published AS state');
		$sql->select('a.ordering, a.access, a.params, a.media, a.created_user_id, a.created_date');
		$sql->select('a.modified_user_id, a.modified_date, a.metakey, a.metadesc');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->from('#__jxzine_publications AS a');

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
		return 'index.php?option=com_zine&view=publication&id='.$id;
	}
}