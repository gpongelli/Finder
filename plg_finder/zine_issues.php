<?php
/**
 * @version		$Id: zine_issues.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Issues
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_zine_issues');
$lang->load('plg_finder_zine_issues.custom');

/**
 * Finder adapter for Zine Issues.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Issues
 */
class plgFinderZine_Issues extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Zine_Issues';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'issue';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Issue';

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
	public function onChangeZineIssue($ids, $property, $value)
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
	public function onDeleteZineIssue($ids)
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
	public function onSaveZineIssue($id)
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
		$item->itemid	= ZineHelperRoute::getItemid('issue', $item->id);
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
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'issue_volume');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'issue_number');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'issue_year');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'issue_month');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'issue_month_end');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'issue_month_name');

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Issue');

		// Add the issue taxonomy data.
		$item->addTaxonomy('Issue', $item->title, $item->state, $item->access);

		// Add the publication taxonomy data.
		$item->addTaxonomy('Publication', $item->publication, $item->pub_state, $item->pub_access);

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
		$sql->select('a.id, a.publication_id, a.title, a.alias, a.subtitle, a.body AS summary');
		$sql->select('a.published AS state, a.ordering, a.access, a.params, a.media, a.issue_volume');
		$sql->select('a.issue_number, a.issue_year, a.issue_month, a.issue_month_end, a.issue_month_name');
		$sql->select('a.created_user_id, a.created_date, a.modified_user_id, a.modified_date');
		$sql->select('a.issue_date, a.start_date, a.end_date, a.metakey, a.metadesc');
		$sql->select('p.title AS publication, p.published AS pub_state, p.access AS pub_access');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->from('#__jxzine_issues AS a');
		$sql->join('LEFT', '#__jxzine_publications AS p ON p.id = a.publication_id');

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
		return 'index.php?option=com_zine&view=issue&id='.$id;
	}
}