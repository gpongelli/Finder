<?php
/**
 * @version		$Id: labels_labels.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderLabels_Labels
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_labels_labels');
$lang->load('plg_finder_labels_labels.custom');

/**
 * Finder adapter for Labels Labels.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderLabels_Labels
 */
class plgFinderLabels_Labels extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Labels_Labels';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'label';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Label';

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
	public function onChangeLabelsLabel($ids, $property, $value)
	{
		// Perform any necessary value translations.
		if ($property === 'state') {
			$temp = $this->_translateState($value);
		}

		// Update the items.
		return $this->_change($ids, $property, $temp);
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onDeleteLabelsLabel($ids)
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
	public function onSaveLabelsLabel($id)
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
	 * Method to load labels data for content items.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onPrepareFinderContent(FinderIndexerResult &$item)
	{
		static $installed;

		// Check if the extension is installed.
		if (is_null($installed))
		{
			$this->_db->setQuery('SHOW TABLES LIKE '.$this->_db->quote($this->_db->replacePrefix('#__jxlabels_labels')));
			$installed = (bool)$this->_db->loadResult();
		}

		// Return an empty array if not installed.
		if ($installed === false) {
			return true;
		}

		// Get the labels for the item.
		$sql = new JDatabaseQuery();
		$sql->select('DISTINCT l.title, l.alias, l.state, l.access');
		$sql->from('#__jxlabels_labels AS l');
		$sql->join('INNER', '#__jxlabels_maps AS m ON m.label_id = l.label_id');
		$sql->where('m.url = '.$this->_db->quote($item->url));

		// Load the results.
		$this->_db->setQuery($sql);
		$labels = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Check the results.
		if (empty($labels)) {
			return true;
		}

		// Load the route helper.
		require_once JPATH_SITE.DS.'components'.DS.'com_labels'.DS.'helpers'.DS.'route.php';

		// Prepare the labels and add the taxonomy data.
		foreach ($labels as $label)
		{
			// Get the label's url and route information.
			$label->itemid	= LabelsHelperRoute::getLabelRoute($label->alias);
			$label->url		= 'index.php?option=com_labels&view=label&label='.$label->alias;
			$label->route	= !empty($label->itemid) ? $label->url.'&Itemid='.$label->itemid : $label->url;

			// Add the labels taxonomy data.
			$item->addTaxonomy('Label', $label->title, $label->state, $label->access);
		}

		// Initialize the JX namespace if necessary.
		if (!($item->jx instanceof JObject)) {
			$item->jx = new JObject();
		}

		// Finally, add all the labels data to the item.
		$item->jx->labels = $labels;

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
		$item->itemid	= LabelsHelperRoute::getLabelRoute($item->alias);
		$item->itemid	= $item->itemid !== null ? '&Itemid='.$item->itemid : null;
		$item->route	= $this->_getURL($item->alias).$item->itemid;
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->_getItemMenuTitle($item->url);

		// Adjust the title if necessary.
		if (!empty($title) && $this->params->get('use_menu_title', true)) {
			$item->title = $title;
		}

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'created_by_alias');

		// Translate the state.
		$item->state = $this->_translateState($item->state);

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Label');

		// Add the author taxonomy data.
		if (!empty($item->author) || !empty($item->created_by_alias)) {
			$item->addTaxonomy('Author', !empty($item->created_by_alias) ? $item->created_by_alias : $item->author);
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
		require_once JPATH_SITE.'/components/com_labels/helpers/route.php';

		return true;
	}

	/**
	 * Method to get a content item to index.
	 *
	 * @param	integer		The id of the content item.
	 * @return	object		A FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function _getItem($id)
	{
		// Get the list query and add the extra WHERE clause.
		$sql = $this->_getListQuery();
		$sql->where('a.label_id = '.(int)$id);

		// Get the item to index.
		$this->_db->setQuery($sql);
		$row = $this->_db->loadAssoc();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Convert the item to a result object.
		$item = JArrayHelper::toObject($row, 'FinderIndexerResult');

		// Set the item type.
		$item->type_id	= $this->_type_id;

		// Set the item layout.
		$item->layout	= $this->_layout;

		return $item;
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
		$sql->select('a.label_id, a.label_id AS id, a.title, a.alias, a.body AS summary');
		$sql->select('a.metakey, a.metadesc, a.state, a.access, a.ordering');
		$sql->select('a.created AS start_date, a.created_by, a.created_by_alias');
		$sql->select('a.params, a.media');
		$sql->select('u.name AS author');
//		$sql->select('c.title AS category, c.state AS cat_state, c.access AS cat_access');
//		$sql->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.group_id, c.alias) ELSE c.group_id END as catslug');
		$sql->from('#__jxlabels_labels AS a');
//		$sql->join('LEFT', '#__jxlabels_groups AS c ON c.group_id = a.group_id');
		$sql->join('LEFT', '#__users AS u ON u.id = a.created_by');

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
		return 'index.php?option=com_labels&view=label&label='.$id;
	}

	/**
	 * Method to translate the native content states into states that the
	 * indexer can use.
	 *
	 * @param	integer		The native content state.
	 * @return	integer		The translated indexer state.
	 */
	protected function _translateState($state)
	{
		// Translate the state.
		switch ($state)
		{
			// Archived.
			case -1:
				return 1;

			// Unpublished.
			case 0:
				return 0;

			// Published.
			default:
			case 1:
				return 1;
		}
	}
}