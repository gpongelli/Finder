<?php
/**
 * @version		$Id: zine_articles.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Articles
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_zine_articles');
$lang->load('plg_finder_zine_articles.custom');

/**
 * Finder adapter for Zine Articles.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderZine_Articles
 */
class plgFinderZine_Articles extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Zine_Articles';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'article';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Article';

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
	public function onChangeZineArticle($ids, $property, $value)
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
	public function onDeleteZineArticle($ids)
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
	public function onSaveZineArticle($id)
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
		$item->custom	= new JParameter($item->custom);
		$item->georef	= new JParameter($item->georef_params);

		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->id);
		$item->itemid	= ZineHelperRoute::getItemid('article', $item->id);
		$itemid			= $item->itemid ? '&Itemid='.$item->itemid : null;
		$item->route	= $this->_getURL($item->slug).$itemid;;
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Rename some properties.
		$item->state		= $item->published;
		$item->start_date	= $item->article_date;

		// Remove unnecessary properties.
		unset($item->published);
		unset($item->style_id);
		unset($item->georef_params);

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
		$item->addTaxonomy('Type', 'Article');

		// Add the publication taxonomy data.
		$item->addTaxonomy('Publication', $item->publication, $item->pub_state, $item->pub_access);

		// Add the issue taxonomy data.
		$item->addTaxonomy('Issue', $item->issue, $item->iss_state, $item->iss_access);

		// Add the section taxonomy data.
		$item->addTaxonomy('Section', $item->section, $item->sec_state);

		// Add the category taxonomy data.
		foreach ($this->_getCategories($item->id) as $category) {
			$item->addTaxonomy('Category', $category->title, $category->state);
		}

		// Add the author taxonomy data.
		$item->addTaxonomy('Author', $item->author, $item->aut_state);

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
		$sql->select('a.*');
		$sql->select('p.title AS publication, p.published AS pub_state, p.access AS pub_access');
		$sql->select('i.title AS issue, i.published AS iss_state, i.access AS iss_access, i.issue_volume');
		$sql->select('i.issue_number, i.issue_year, i.issue_month, i.issue_month_end, i.issue_month_name');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->select('u.fullname AS author, a.published AS aut_state');
		$sql->select('s.title AS section, s.published AS sec_state');
		$sql->from('#__jxzine_articles AS a');
		$sql->join('LEFT', '#__jxzine_publications AS p ON p.id = a.publication_id');
		$sql->join('LEFT', '#__jxzine_issues AS i ON i.id = a.issue_id');
		$sql->join('LEFT', '#__jxzine_categories AS s ON s.id = a.section_id');
		$sql->join('LEFT', '#__jxzine_authors AS u ON u.id = a.author_id');

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
		return 'index.php?option=com_zine&view=article&id='.$id;
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
		// Get the article => categories.
		$sql = new JDatabaseQuery();
		$sql->select('c.title, c.published AS state');
		$sql->from('#__jxzine_categories AS c');
		$sql->join('INNER', '#__jxzine_article_category_map AS m ON m.right_id = c.id');
		$sql->where('m.left_id = '.(int)$id);

		// Load the results.
		$this->_db->setQuery($sql);
		$categories = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		return $categories;
	}
}