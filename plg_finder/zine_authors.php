<?php
/**
 * @version		$Id: zine_authors.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Authors
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_zine_authors');
$lang->load('plg_finder_zine_authors.custom');

/**
 * Finder adapter for Zine Authors.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Authors
 */
class plgFinderZine_Authors extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Zine_Authors';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'author';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Author';

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
	public function onChangeZineAuthor($ids, $property, $value)
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
	public function onDeleteZineAuthor($ids)
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
	public function onSaveZineAuthor($id)
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
		$item->media = new JParameter($item->media);

		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->id);
		$item->itemid	= ZineHelperRoute::getItemid('author', $item->id);
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
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'username');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'position');

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Author');

		// Add the category taxonomy data.
		foreach ($this->_getCategories($item->id) as $category) {
			$item->addTaxonomy('Category', $category->title, $category->state);
		}

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
		$sql->select('a.id, a.fullname AS title, a.body AS summary, a.user_id, a.contact_id');
		$sql->select('a.published AS state, a.created_user_id, a.created_date, a.metakey');
		$sql->select('a.metadesc, a.georef_x, a.georef_y, a.georef_params, a.position, a.media');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->select('u.name AS username');
		$sql->from('#__jxzine_authors AS a');
		$sql->join('LEFT', '#__users AS u ON u.id = a.user_id');

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
		return 'index.php?option=com_zine&view=author&id='.$id;
	}

	/**
	 * Method to get the multi-mapped categories for the item.
	 *
	 * @param	integer		The id of the item.
	 * @return	array		An array of categories.
	 * @throws	Exception on database error.
	 */
	private function _getCategories($id)
	{
		$categories = array();

		// Get the author => categories.
		$sql = new JDatabaseQuery();
		$sql->select('c.title, c.published AS state');
		$sql->from('#__jxzine_categories AS c');
		$sql->join('INNER', '#__jxzine_author_category_map AS m ON m.right_id = c.id');
		$sql->where('m.left_id = '.(int)$id);

		// Load the results.
		$this->_db->setQuery($sql);
		$results = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Merge in the results.
		if (is_array($results)) {
			$categories = array_merge($categories, $results);
		}

		// Get the author => article => categories.
		$sql = new JDatabaseQuery();
		$sql->select('c.title, c.published AS state');
		$sql->from('#__jxzine_categories AS c');
		$sql->join('INNER', '#__jxzine_author_article_category_map AS m ON m.right_id = c.id');
		$sql->where('m.left_id = '.(int)$id);

		// Load the results.
		$this->_db->setQuery($sql);
		$results = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Merge in the results.
		if (is_array($results)) {
			$categories = array_merge($categories, $results);
		}

		return $categories;
	}
}