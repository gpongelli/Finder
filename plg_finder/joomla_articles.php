<?php
/**
 * @version		$Id: joomla_articles.php 1051 2010-09-16 16:14:31Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderJoomla_Articles
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_joomla_articles');
$lang->load('plg_finder_joomla_articles.custom');

/**
 * Finder adapter for Joomla Articles.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderJoomla_Articles
 */
class plgFinderJoomla_Articles extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Joomla_Articles';

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
	public function onChangeJoomlaArticle($ids, $property, $value)
	{
		// Check if we are changing the article state.
		if ($property === 'state')
		{
			// The article published state is tied to the section and category
			// published states so we need to look up all published states
			// before we change anything.
			foreach ($ids as $id)
			{
				$sql = clone($this->_getStateQuery());
				$sql->where('a.id = '.(int)$id);

				// Get the published states.
				$this->_db->setQuery($sql);
				$item = $this->_db->loadObject();

				// Translate the state.
				$temp = $this->_translateState($value, $item->cat_state, $item->sec_state);

				// Update the item.
				$this->_change($id, $property, $temp);
			}
		}
		// Check if we are changing the article access level.
		elseif ($property === 'access')
		{
			// The article access state is tied to the section and category
			// access states so we need to look up all access states
			// before we change anything.
			foreach ($ids as $id)
			{
				$sql = clone($this->_getStateQuery());
				$sql->where('a.id = '.(int)$id);

				// Get the published states.
				$this->_db->setQuery($sql);
				$item = $this->_db->loadObject();

				// Translate the state.
				$temp = max($value, $item->cat_access, $item->sec_access);

				// Update the item.
				$this->_change($id, 'access', $temp);
			}
		}

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
		// Check if we are changing the category state.
		if ($property === 'published')
		{
			// The article published state is tied to the section and category
			// published states so we need to look up all published states
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
					$temp = $this->_translateState($item->state, $value, $item->sec_state);

					// Update the item.
					$this->_change($item->id, 'state', $temp);
				}
			}
		}
		// Check if we are changing the category access level.
		elseif ($property === 'access')
		{
			// The article access state is tied to the section and category
			// access states so we need to look up all access states
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
					$temp = max($item->access, $value, $item->sec_access);

					// Update the item.
					$this->_change($item->id, 'access', $temp);
				}
			}
		}

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
			// The article published state is tied to the section and category
			// published states so we need to look up all published states
			// before we change anything.
			foreach ($ids as $id)
			{
				$sql = new JDatabaseQuery();
				$sql = clone($this->_getStateQuery());
				$sql->where('s.id = '.(int)$id);

				// Get the published states.
				$this->_db->setQuery($sql);
				$items = $this->_db->loadObjectList();

				// Adjust the state for each item within the section.
				foreach ($items as $item)
				{
					// Translate the state.
					$temp = $this->_translateState($item->state, $item->cat_state, $value);

					// Update the item.
					$this->_change($item->id, 'state', $temp);
				}
			}
		}
		// Check if we are changing the section access level.
		elseif ($property === 'access')
		{
			// The article access state is tied to the section and category
			// access states so we need to look up all access states
			// before we change anything.
			foreach ($ids as $id)
			{
				$sql = clone($this->_getStateQuery());
				$sql->where('s.id = '.(int)$id);

				// Get the published states.
				$this->_db->setQuery($sql);
				$items = $this->_db->loadObjectList();

				// Adjust the state for each item within the category.
				foreach ($items as $item)
				{
					// Translate the state.
					$temp = max($item->access, $item->cat_access, $value);

					// Update the item.
					$this->_change($item->id, 'access', $temp);
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
	public function onDeleteJoomlaArticle($ids)
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
	public function onSaveJoomlaArticle($id)
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
	 * Method to update the link information for items that have been trashed.
	 * We want to keep the item in the index for now but prevent it from being
	 * displayed in search results.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onTrashJoomlaArticle($ids)
	{
		// Update the items.
		return $this->_change($ids, 'state', -2);
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
		$item->metadata	= new JParameter($item->metadata);

		// Trigger the onPrepareContent event.
		$item->summary	= FinderIndexerHelper::prepareContent($item->summary, $item->params);
		$item->body		= FinderIndexerHelper::prepareContent($item->body, $item->params);

		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->id);
		$item->route	= ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid);
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->_getItemMenuTitle($item->url);

		// Adjust the title if necessary.
		if (!empty($title) && $this->params->get('use_menu_title', true)) {
			$item->title = $title;
		}

		// Add the meta-author.
		$item->metaauthor = $item->metadata->get('author');

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'created_by_alias');

		// Translate the state. Articles should only be published if the section and category are published.
		$item->state = $this->_translateState($item->state, $item->cat_state, $item->sec_state);

		// Set the language.
		$item->language	= $item->params->get('language', FinderIndexerHelper::getDefaultLanguage());

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Article');

		// Add the author taxonomy data.
		if (!empty($item->author) || !empty($item->created_by_alias)) {
			$item->addTaxonomy('Author', !empty($item->created_by_alias) ? $item->created_by_alias : $item->author);
		}

		// Add the category taxonomy data.
		if (!empty($item->category)) {
			$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);
		} else {
			$item->addTaxonomy('Category', JText::_('Uncategorized'));
		}

		// Add the section taxonomy data.
		if (!empty($item->section)) {
			$item->addTaxonomy('Section', $item->section, $item->sec_state, $item->sec_access);
		} else {
			$item->addTaxonomy('Section', JText::_('Uncategorized'));
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
		$sql->select('a.id, a.title, a.alias, a.introtext AS summary, a.fulltext AS body');
		$sql->select('a.state, a.sectionid, a.catid, a.created AS start_date, a.created_by');
		$sql->select('a.created_by_alias, a.modified, a.modified_by, a.attribs AS params');
		$sql->select('a.metakey, a.metadesc, a.metadata, a.access, a.version, a.ordering');
		$sql->select('a.publish_up AS publish_start_date, a.publish_down AS publish_end_date');
		$sql->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');
		$sql->select('s.title AS section, s.published AS sec_state, s.access AS sec_access');
		$sql->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$sql->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug');
		$sql->select('u.name AS author');
		$sql->from('#__content AS a');
		$sql->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$sql->join('LEFT', '#__sections AS s ON s.id = a.sectionid');
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
		return 'index.php?option=com_content&view=article&id='.$id;
	}

	/**
	 * Method to translate the native content states into states that the
	 * indexer can use.
	 *
	 * @param	integer		The article state.
	 * @param	integer		The category state.
	 * @param	integer		The section state.
	 * @return	integer		The translated indexer state.
	 */
	private function _translateState($article, $category, $section)
	{
		// If category and section are present, factor in their states as well.
		if ($category !== null && $section !== null) {
			if ($category == 0 || $section == 0) {
				$article = 0;
			}
		}

		// Translate the state.
		switch ($article)
		{
			// Unpublished or trashed.
			case 0:
			case -2:
				return 0;

			// Published or archived.
			default:
			case 1:
			case -1:
				return 1;
		}
	}

	/**
	 * Method to get a SQL query to load the published and access states for
	 * an article, category, and section.
	 *
	 * @return	object		A JDatabaseQuery object.
	 */
	private function _getStateQuery()
	{
		$sql = new JDatabaseQuery();
		$sql->select('a.id');
		$sql->select('a.state, c.published AS cat_state, s.published AS sec_state');
		$sql->select('a.access, c.access AS cat_access, s.access AS sec_access');
		$sql->from('#__content AS a');
		$sql->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$sql->join('LEFT', '#__sections AS s ON s.id = a.sectionid');

		return $sql;
	}
}