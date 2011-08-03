<?php
/**
 * @version		$Id: modellist.php 501 2010-09-01 19:51:00Z robs $
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.model');
jx('jx.database.databasequery');

/**
 * Model class for handling lists of items.  This class has the ability to autopopulate
 * its state from the request.
 *
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @since		2.0
 */
class JModelList extends JModel
{
	/**
	 * Indicates if the internal state has been set.
	 *
	 * @var		bool
	 * @since	2.0
	 */
	protected $__state_set = false;

	/**
	 * Internal memory based cache array of data.
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected $_cache = array();

	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the _getStoreId() method and caching data structures.
	 *
	 * @var		string
	 * @since	2.0
	 */
	protected $_context = null;

	/**
	 * Constructor.
	 *
	 * @param	array	The instance configuration array.
	 * @return	void
	 * @since	2.0
	 */
	function __construct($config = array())
	{
		// Call the parent model constructor.
		parent::__construct($config);

		// Set the internal state flag if the model should not autopopulate the state from the request.
		if (!empty($config['ignore_request'])) {
			$this->__state_set = true;
		}
	}

	/**
	 * Method to get the model state or state property value.
	 *
	 * @param	string	Optional property name.
	 * @param   mixed	Optional default value.
	 * @return	mixed	The property where specified, the state object where omitted.
	 * @since	2.0
	 */
	public function getState($property = null, $default = null)
	{
		if (!$this->__state_set)
		{
			// Private method to autopopulate the model state.
			$this->_populateState();

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		return $property === null ? $this->_state : $this->_state->get($property, $default);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	2.0
	 */
	public function &getItems()
	{
		// Get a storage key.
		$store = $this->_getStoreId($this->_context);

		// Try to load the data from internal storage.
		if (!empty($this->_cache[$store])) {
			return $this->_cache[$store];
		}

		// Load the list items.
		$query	= $this->_getListQuery();
		$items	= $this->_getList((string) $query, $this->getState('list.start'), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->_cache[$store] = $items;

		return $this->_cache[$store];
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return	object	A JPagination object for the data set.
	 * @since	2.0
	 */
	public function &getPagination()
	{
		// Get a storage key.
		$store = $this->_getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (!empty($this->_cache[$store])) {
			return $this->_cache[$store];
		}

		// Create the pagination object.
		jimport('joomla.html.pagination');
		$page = new JPagination($this->getTotal(), (int) $this->getState('list.start'), (int) $this->getState('list.limit'));

		// Add the object to the internal cache.
		$this->_cache[$store] = $page;

		return $this->_cache[$store];
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return	integer	The total number of items available in the data set.
	 * @since	2.0
	 */
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->_getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (!empty($this->_cache[$store])) {
			return $this->_cache[$store];
		}

		// Load the total.
		$query = $this->_getListQuery();
		$total = (int) $this->_getListCount((string) $query);

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the total to the internal cache.
		$this->_cache[$store] = $total;

		return $this->_cache[$store];
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return	object	A JDatabaseQuery object to retrieve the data set.
	 * @since	2.0
	 */
	protected function _getListQuery()
	{
		$query = new JDatabaseQuery;

		return $query;
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	An identifier string to generate the store id.
	 * @return	string	A store id.
	 * @since	2.0
	 */
	protected function _getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');

		return md5($this->_context.':'.$id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 * @since	2.0
	 */
	protected function _populateState($ordering = null, $direction = null)
	{
		// If the context is set, assume that stateful lists are used.
		if ($this->_context)
		{
			$app = JFactory::getApplication();

			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
			$this->setState('list.limit', $limit);

			$limitstart = $app->getUserStateFromRequest($this->_context.'.limitstart', 'limitstart', 0);
			$this->setState('list.start', $limitstart);

			$orderCol = $app->getUserStateFromRequest($this->_context.'.ordercol', 'filter_order', $ordering);
			$this->setState('list.ordering', $orderCol);

			$orderDirn = $app->getUserStateFromRequest($this->_context.'.orderdirn', 'filter_order_Dir', $direction);
			$this->setState('list.direction', $orderDirn);
		}
		else
		{
			$this->setState('list.start', 0);
			$this->_state->set('list.limit', 0);
		}
	}

	/**
	 * Method to retrieve data from cache.
	 *
	 * @param	string		The cache store id.
	 * @param	boolean		Flag to enable the use of external cache.
	 * @return	mixed		The cached data if found, null otherwise.
	 */
	protected function _retrieve($id, $persistent = true)
	{
		$data = null;

		// Use the internal cache if possible.
		if (isset($this->_cache[$id])) {
			return $this->_cache[$id];
		}

		// Use the external cache if data is persistent.
		if ($persistent) {
			$data = JFactory::getCache($this->_context, 'output')->get($id);
			$data = $data ? unserialize($data) : null;
		}

		// Store the data in internal cache.
		if ($data) {
			$this->_cache[$id] = $data;
		}

		return $data;
	}

	/**
	 * Method to store data in cache.
	 *
	 * @param	string		The cache store id.
	 * @param	mixed		The data to cache.
	 * @param	boolean		Flag to enable the use of external cache.
	 * @return	boolean		True on success, false on failure.
	 */
	protected function _store($id, $data, $persistent = true)
	{
		// Store the data in internal cache.
		$this->_cache[$id] = $data;

		// Store the data in external cache if data is persistent.
		if ($persistent) {
			return JFactory::getCache($this->_context, 'output')->store(serialize($data), $id);
		}

		return true;
	}
}