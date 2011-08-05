<?php
/**
 * @version		$Id: adapter.php 984 2010-06-22 00:55:25Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

// Register dependent classes.
JLoader::register('FinderIndexer', dirname(__FILE__).DS.'indexer.php');
JLoader::register('FinderIndexerHelper', dirname(__FILE__).DS.'helper.php');
JLoader::register('FinderIndexerQueue', dirname(__FILE__).DS.'queue.php');
JLoader::register('FinderIndexerResult', dirname(__FILE__).DS.'result.php');
JLoader::register('FinderIndexerTaxonomy', dirname(__FILE__).DS.'taxonomy.php');

/**
 * Prototype adapter class for the Finder indexer package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
abstract class FinderIndexerAdapter extends JPlugin
{
	/**
	 * The context is somewhat arbitrary but it must be unique or there will be
	 * conflicts when managing plugin/indexer state. A good best practice is to
	 * use the plugin name suffix as the context. For example, if the plugin is
	 * named 'plgFinderJoomla_Articles', the context could be 'Joomla_Articles'.
	 *
	 * @var		string		The plugin identifier.
	 */
	protected $_context;

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout;

	/**
	 * @var		string		The mime type of the content the adapter indexes.
	 */
	protected $_mime;

	/**
	 * @var		string		The type of content the adapter indexes.
	 */
	protected $_type_title;

	/**
	 * @var		integer		The type id of the content.
	 */
	protected $_type_id;

	/**
	 * @var		object		The database object.
	 */
	protected $_db;

	/**
	 * Method to instantiate the indexer adapter.
	 *
	 * @param	object		The object to observe.
	 * @param	array		An array that holds the plugin configuration.
	 * @return	void
	 */
	public function __construct(&$subject, $config)
	{
		// Get the database object.
		$this->_db = JFactory::getDBO();

		// Call the parent constructor.
		parent::__construct($subject, $config);

		// Get the type id.
		$this->_type_id = $this->_getTypeId();

		// Add the content type if it doesn't exist and is set.
		if (empty($this->_type_id) && !empty($this->_type_title)) {
			$this->_type_id = FinderIndexerHelper::addContentType($this->_type_title, $this->_mime);
		}

		// Check for a layout override.
		if ($this->params->get('layout')) {
			$this->_layout = $this->params->get('layout');
		}
	}

	/**
	 * Method to get the adapter state and push it into the indexer.
	 *
	 * @return	boolean		True on success.
	 * @throws	Exception on error.
	 */
	public function onStartIndex()
	{
		// Get the indexer state.
		$iState	= FinderIndexer::getState();

		// Get the number of content items.
		$total	= (int)$this->_getContentCount();

		// Add the content count to the total number of items.
		$iState->totalItems += $total;

		// Populate the indexer state information for the adapter.
		$iState->pluginState[$this->_context]['total']	= $total;
		$iState->pluginState[$this->_context]['offset']	= 0;

		// Set the indexer state.
		FinderIndexer::setState($iState);
	}

	public function onStartUpdate()
	{
		// Get the indexer state.
		$iState	= FinderIndexer::getState();

		// Get the indexer queue.
		$queue	= FinderIndexerQueue::get($this->_context);

		// Get the number of content items to update.
		$total	= count($queue);

		// Add the count to the total number of items.
		$iState->totalItems += $total;

		// Populate the indexer state information for the adapter.
		$iState->pluginState[$this->_context]['total']	= $total;
		$iState->pluginState[$this->_context]['offset']	= 0;

		// Set the indexer state.
		FinderIndexer::setState($iState);
	}

	/**
	 * Method to prepare for the indexer to be run. This method will often
	 * be used to include dependencies and things of that nature.
	 *
	 * @return	boolean		True on success.
	 * @throws	Exception on error.
	 */
	public function onBeforeIndex()
	{
		// Get the indexer and adapter state.
		$iState	= FinderIndexer::getState();
		$aState	= $iState->pluginState[$this->_context];

		// Check the progress of the indexer and the adapter.
		if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total']) {
			return true;
		}

		// Run the setup method.
		return $this->_setup();
	}

	/**
	 * Method to index a batch of content items. This method can be called by
	 * the indexer many times throughout the indexing process depending on how
	 * much content is available for indexing. It is important to track the
	 * progress correctly so we can display it to the user.
	 *
	 * @return	boolean		True on success.
	 * @throws	Exception on error.
	 */
	public function onBuildIndex()
	{
		// Get the indexer and adapter state.
		$iState	= FinderIndexer::getState();
		$aState	= $iState->pluginState[$this->_context];

		// Check the progress of the indexer and the adapter.
		if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total']) {
			return true;
		}

		// Get the batch offset and size.
		$offset	= (int)$aState['offset'];
		$limit	= (int)($iState->batchSize - $iState->batchOffset);

		// Get the content items to index.
		$items = $this->_getItems($offset, $limit);

		// Iterate through the items and index them.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			// Index the item.
			$this->_index($items[$i]);

			// Adjust the offsets.
			$offset++;
			$iState->batchOffset++;
			$iState->totalItems--;
		}

		// Update the indexer state.
		$aState['offset'] = $offset;
		$iState->pluginState[$this->_context] = $aState;
		FinderIndexer::setState($iState);

		return true;
	}

	public function onBuildUpdate()
	{
		// Get the indexer and adapter state.
		$iState	= FinderIndexer::getState();
		$aState	= $iState->pluginState[$this->_context];

		// Check the progress of the indexer and the adapter.
		if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total']) {
			return true;
		}

		// Get the batch offset and size.
		$offset	= (int)$aState['offset'];

		// Get the indexer queue.
		$queue	= FinderIndexerQueue::get($this->_context);

		/*
		 * We need to start building an SQL query that we will use to fetch
		 * the modified records. This will serve as the foundation and will be
		 * augmented to fetch the actual data by the _getListQuery()
		 */
		if (array_key_exists(0, $queue) === true)
		{
			// Get the timestamp of the first item in the queue.
			$first	= array_shift(array_values($queue));
			$time	= $first['timestamp'];

			// Get the query to load the items by time.
			$sql = $this->_getUpdateQueryByTime($time);
		}
		else
		{
			// Create an array of ids to fetch.
			$ids = array_keys($queue);
			JArrayHelper::toInteger($ids);

			// Get the query to load the items by id.
			$sql = $this->_getUpdateQueryByIds($ids);
		}

		// Get the content items to index.
		$items = $this->_getItems(0, count($queue), $sql);

		// Check if any items were returned.
		if (count($items))
		{
			// Iterate through the items and index them.
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				// Index the item.
				$this->_index($items[$i]);

				// Adjust the offsets.
				$offset++;
				$iState->batchOffset++;
				$iState->totalItems--;
			}
		}
		else
		{
			// Flush the queue for this context.
			FinderIndexerQueue::remove($this->_context);

			// Update indexer state to prevent endless polling.
			$offset += count($queue);
			$iState->batchOffset += count($queue);
			$iState->totalItems -= count($queue);
		}


		// Update the indexer state.
		$aState['offset'] = $offset;
		$iState->pluginState[$this->_context] = $aState;
		FinderIndexer::setState($iState);

		return true;
	}

	/**
	 * Method to change the value of a content item's property in the links
	 * table. This is used to synchronize published and access states that
	 * are changed when not editing an item directly.
	 *
	 * @param	mixed		An array of item ids or a single integer id.
	 * @param	string		The property that is being changed.
	 * @param	integer		The new value of that property.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	protected function _change($ids, $property, $value)
	{
		// Check for a property we know how to handle.
		if ($property !== 'state' && $property !== 'access') {
			return true;
		}

		// Check the ids.
		if (empty($ids)) {
			return true;
		}

		// Convert to array.
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		// Get the url for the content id.
		$items = array();
		foreach ($ids as $id) {
			$items[] = $this->_db->quote($this->_getUrl($id));
		}

		// Update the content items.
		$this->_db->setQuery(
			'UPDATE `#__jxfinder_links`' .
			' SET '.$this->_db->nameQuote($property).' = '.(int)$value .
			' WHERE `url` IN ('.implode(',', $items).')'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		return true;
	}

	/**
	 * Method to index an item.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	abstract protected function _index(FinderIndexerResult $item);

	/**
	 * Method to remove an item from the index.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	protected function _remove($ids)
	{
		// Check the ids.
		if (empty($ids)) {
			return true;
		}

		// Get the url for the content id.
		foreach ($ids as $id) {
			$items[] = $this->_db->quote($this->_getUrl($id));
		}

		// Get the link ids for the content items.
		$this->_db->setQuery(
			'SELECT `link_id` FROM `#__jxfinder_links`' .
			' WHERE `url` IN ('.implode(',', $items).')'
		);
		$items = $this->_db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Check the items.
		if (empty($items)) {
			return true;
		}

		// Remove the items.
		foreach ($items as $item) {
			FinderIndexer::remove($item);
		}

		return true;
	}

	/**
	 * Method to setup the adapter before indexing.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @throws	Exception on database error.
	 */
	abstract protected function _setup();

	/**
	 * Method to get the number of content items available to index.
	 *
	 * @return	integer		The number of content items available to index.
	 * @throws	Exception on database error.
	 */
	protected function _getContentCount()
	{
		$return = 0;

		// Get the list query.
		$sql = $this->_getListQuery();

		// Check if the query is valid.
		if (empty($sql)) {
			return $return;
		}

		// Tweak the SQL query to make the total lookup faster.
		if ($sql instanceof JDatabaseQuery) {
			$sql = clone($sql);
			$sql->clear('select');
			$sql->select('COUNT(*)');
			$sql->clear('order');
		}

		// Get the total number of content items to index.
		$this->_db->setQuery($sql);
		$return = (int)$this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		return $return;
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
		$sql->where('a.id = '.(int)$id);

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
	 * Method to get a list of content items to index.
	 *
	 * @param	integer		The list offset.
	 * @param	integer		The list limit.
	 * @return	array		An array of FinderIndexerResult objects.
	 * @throws	Exception on database error.
	 */
	protected function _getItems($offset, $limit, $sql = null)
	{
		$items = array();

		// Get the content items to index.
		$this->_db->setQuery($this->_getListQuery($sql), $offset, $limit);
		$rows = $this->_db->loadAssocList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Convert the items to result objects.
		foreach ($rows as $row)
		{
			// Convert the item to a result object.
			$item = JArrayHelper::toObject($row, 'FinderIndexerResult');

			// Set the item type.
			$item->type_id	= $this->_type_id;

			// Set the mime type.
			$item->mime		= $this->_mime;

			// Set the item layout.
			$item->layout	= $this->_layout;

			// Add the item to the stack.
			$items[] = $item;
		}

		return $items;
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

		return $sql;
	}

	/**
	 * Method to get the query clause for getting items to update by time.
	 *
	 * @param	string		The modified timestamp.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getUpdateQueryByTime($time)
	{
		// Build an SQL query based on the modified time.
		$sql = new JDatabaseQuery();
		$sql->where('a.modified >= '.$this->_db->quote($time));

		return $sql;
	}

	/**
	 * Method to get the query clause for getting items to update by id.
	 *
	 * @param	array		The ids to load.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getUpdateQueryByIds($ids)
	{
		// Build an SQL query based on the item ids.
		$sql = new JDatabaseQuery();
		$sql->where('a.id IN('.implode(',', $ids).')');

		return $sql;
	}

	/**
	 * Method to get the type id for the adapter content.
	 *
	 * @return	integer		The numeric type id for the content.
	 * @throws	Exception on database error.
	 */
	protected function _getTypeId()
	{
		// Get the type id from the database.
		$this->_db->setQuery(
			'SELECT `id` FROM `#__jxfinder_types`' .
			' WHERE `title` = '.$this->_db->quote($this->_type_title)
		);
		$result = (int)$this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		return $result;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	integer		The id of the item.
	 * @return	string		The URL of the item.
	 */
	abstract protected function _getURL($id);

	/**
	 * Method to get the page title of any menu item that is linked to the
	 * content item, if it exists and is set.
	 *
	 * @param	string		The url of the item.
	 * @return	mixed		The title on success, null if not found.
	 * @throws	Exception on database error.
	 */
	protected function _getItemMenuTitle($url)
	{
		$return = null;

		// Build a query to get the menu params.
		$sql = new JDatabaseQuery();
		$sql->select('params');
		$sql->from('#__menu');
		$sql->where('link = '.$this->_db->quote($url));
		$sql->where('published = 1');
		$sql->where('access <= '.(int)JFactory::getUser()->get('aid'));

		// Get the menu params from the database.
		$this->_db->setQuery($sql);
		$params = $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Check the results.
		if (empty($params)) {
			return $return;
		}

		// Instantiate the params.
		$params = new JParameter($params);

		// Get the page title if it is set.
		if ($params->get('page_title') && $params->get('show_page_title', true)) {
			$return = $params->get('page_title');
		}

		return $return;
	}
}