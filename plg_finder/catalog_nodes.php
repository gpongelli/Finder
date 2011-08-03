<?php
/**
 * @version		$Id: catalog_nodes.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderCatalog_Nodes
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_catalog_nodes');
$lang->load('plg_finder_catalog_nodes.custom');

/**
 * Finder adapter for Catalog Nodes.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderCatalog_Nodes
 */
class plgFinderCatalog_Nodes extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Catalog_Nodes';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'node';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Node';

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
	public function onChangeCatalogNode($ids, $property, $value)
	{
		// Perform any necessary value translations.
		if ($property === 'published') {
			$property = 'state';
		}

		// Update the items.
		return $this->_change($ids, $property, $value);
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onDeleteCatalogNode($ids)
	{
		// Remove the items.
		return $this->_remove($ids);
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
		$item->params			= new JParameter($item->params);
		$item->media			= new JParameter($item->media);
		$item->georef_params	= new JParameter($item->georef_params);

		// Trigger the onPrepareContent event.
		$item->summary	= FinderIndexerHelper::prepareContent($item->summary, $item->params);
		$item->body		= FinderIndexerHelper::prepareContent($item->body, $item->params);

		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->id);
		$item->route	= $this->_getURL($item->slug);
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->_getItemMenuTitle($item->url);

		// Adjust the title if necessary.
		if (!empty($title) && $this->params->get('use_menu_title', true)) {
			$item->title = $title;
		}

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'reference');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// If a class is defined for this item, use that as the item type.
		if (!empty($item->cls_title))
		{
			// Override the type title.
			$this->_type_title = $item->cls_title;

			// Check if the node type is already defined.
			$this->_type_id = $this->_getTypeId();

			// Add the type and get the new type id if necessary.
			if (empty($this->_type_id)) {
				$this->_type_id = FinderIndexerHelper::addContentType($this->_type_title, $this->_mime);
			}

			// Override the type id for the result item.
			$item->type_id = $this->_type_id;

			// Add the type taxonomy data.
			$item->addTaxonomy('Type', $item->cls_title);

			// Add the class taxonomy data.
			$item->addTaxonomy('Class', $item->cls_title, $item->cls_state);
		}
		// If no class is defined, use the base type of "Node".
		else
		{
			// Add the type taxonomy data.
			$item->addTaxonomy('Type', 'Node');
		}

		// Add the category taxonomy data.
		foreach ($this->_getCategories($item->id) as $category) {
			$item->addTaxonomy('Category', $category->title, $category->state);
		}

		// Add the attribute taxonomy data.
		foreach ($this->_getAttributes($item->id) as $attribute) {
			$item->addTaxonomy($attribute->branch, $attribute->title, $attribute->state);
		}

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return	boolean		True on success, false on failure.
	 */
	protected function _setup()
	{
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
		$sql->select('a.id, a.reference, a.title, a.alias, a.subtitle, a.body AS summary');
		$sql->select('a.body, a.node_date AS start_date, a.start_date AS publish_start_date');
		$sql->select('a.finish_date AS publish_end_date, a.params, a.media, a.metakey, a.metadesc');
		$sql->select('a.georef_x, a.georef_y, a.georef_params, a.price1 AS list_price, a.price2 AS sale_price');
		$sql->select('a.currency_id, a.available, a.available_date, a.published AS state, a.access');
		$sql->select('a.ordering, a.created_date, a.modified_date');
		$sql->select('c.title AS cls_title, c.published AS cls_state');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->select('u.name AS author');
		$sql->from('#__jxcatalog_nodes AS a');
		$sql->join('LEFT', '#__jxcatalog_classes AS c ON c.id = a.class_id');
		$sql->join('LEFT', '#__users AS u ON u.id = a.created_user_id');

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
		return 'index.php?option=com_catalog&view=node&id='.$id;
	}

	/**
	 * Method to get the attributes for the item.
	 *
	 * @param	integer		The id of the item.
	 * @return	array		An array of attributes.
	 * @throws	Exception on database error.
	 */
	private function _getAttributes($id)
	{
		// Get the node => attributes.
		$sql = new JDatabaseQuery();
		$sql->select('tt.title AS branch, t.title AS title, t.published as state');
		$sql->from('#__jxcatalog_attributes AS a');
		$sql->join('INNER', '#__jxcatalog_taxon_types AS tt ON tt.id = a.taxon_type_id');
		$sql->join('INNER', '#__jxcatalog_taxa AS t ON t.id = a.taxon_id');
		$sql->where('node_id = '.(int)$id);

		// Load the results.
		$this->_db->setQuery($sql->toString());
		$attributes	= $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}


		return $attributes;
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
		// Get the node => categories.
		$sql = new JDatabaseQuery();
		$sql->select('c.title, c.published AS state');
		$sql->from('#__jxcatalog_categories AS c');
		$sql->join('INNER', '#__jxcatalog_node_category_map AS m ON m.right_id = c.id');
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