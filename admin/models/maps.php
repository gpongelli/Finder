<?php
/**
 * @version		$Id: maps.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jx('jx.database.databasequery');

/**
 * Maps model for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderModelMaps extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	private
	 * @var		boolean
	 */
	var $__state_set	= false;

	/**
	 * An array of totals for the lists.
	 *
	 * @access	protected
	 * @var		array
	 */
	var $_totals		= array();

	/**
	 * Array of lists containing items.
	 *
	 * @access	protected
	 * @var		array
	 */
	var $_lists			= array();

	/**
	 * Overridden method to get model state variables.
	 *
	 * @access	public
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	function getState($property = null)
	{
		if (!$this->__state_set)
		{
			$app		= &JFactory::getApplication('administrator');
			$user		= &JFactory::getUser();
			$config		= &JFactory::getConfig();
			$params		= JComponentHelper::getParams('com_finder');
			$context	= 'com_finder.maps.';

			// Get the list filters.
			// Load the filter state.
			$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
			$this->setState('filter.state', $app->getUserStateFromRequest($context.'filter.state', 'filter_state', '*', 'string'));
			$this->setState('filter.branch', $app->getUserStateFromRequest($context.'filter.branch', 'filter_branch', 1, 'int'));

			// Load the list state.
			$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
			$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
			$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'a.ordering', 'cmd'));
			$this->setState('list.direction', $app->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

			// Load the user parameters.
			$this->setState('user',	$user);
			$this->setState('user.id', (int)$user->id);
			$this->setState('user.aid', (int)$user->get('aid'));

			// Load the check parameters.
			if ($this->_state->get('filter.state') === '*') {
				$this->setState('check.state', false);
			} else {
				$this->setState('check.state', true);
			}

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @access	public
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.0
	 */
	function &getItems()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId('finder.maps');

		// Try to load the value from internal storage.
		if (!empty ($this->_lists[$key])) {
			return $this->_lists[$key];
		}

		// Load the list.
		$query	= $this->_getListQuery();
		$rows	= $this->_getList($query->toString(), $this->getState('list.start'), $this->getState('list.limit'));

		// Add the rows to the internal storage.
		$this->_lists[$key] = $rows;

		return $this->_lists[$key];
	}

	/**
	 * Method to get a list pagination object.
	 *
	 * @access	public
	 * @return	object	A JPagination object.
	 * @since	1.0
	 */
	function &getPagination()
	{
		jimport('joomla.html.pagination');

		// Create the pagination object.
		$instance = new JPagination($this->getTotal(), (int)$this->getState('list.start'), (int)$this->getState('list.limit'));

		return $instance;
	}

	/**
	 * Method to get the total number of published items.
	 *
	 * @access	public
	 * @return	int		The number of published items.
	 * @since	1.0
	 */
	function getTotal()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId('finder.maps');

		// Try to load the value from internal storage.
		if (!empty ($this->_totals[$key])) {
			return $this->_totals[$key];
		}

		// Load the total.
		$query = $this->_getListQuery();
		$return = (int)$this->_getListCount($query->toString());

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Push the value into internal storage.
		$this->_totals[$key] = $return;

		return $this->_totals[$key];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @access	protected
	 * @return	string		An SQL query
	 * @since	1.0
	 */
	function _getListQuery()
	{
		$query = new JDatabaseQuery();

		// Select all fields from the table.
		$query->select('a.*');
		$query->from('`#__jxfinder_taxonomy` AS a');

		// Self-join to get children.
		$query->select('COUNT(b.id) AS num_children');
		$query->join('LEFT', '`#__jxfinder_taxonomy` AS b ON b.parent_id=a.id');

		// Join to get the map links
		$query->select('COUNT(c.node_id) AS num_nodes');
		$query->join('LEFT', '`#__jxfinder_taxonomy_map` AS c ON c.node_id=a.id');

		$query->group('a.id');

		// If the model is set to check item state, add to the query.
		if ($this->getState('check.state')) {
			$query->where('a.state = '.(int)$this->getState('filter.state'));
		}

		// Filter the maps over the branch if set.
		$branch_id = $this->getState('filter.branch');
		if (!empty($branch_id)) {
			$query->where('a.parent_id = '.(int)$branch_id);
		}

		// Filter the maps over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where('a.title LIKE '.$this->_db->Quote('%'.$search.'%'));
		}

		// Add the list ordering clause.
		$query->order($this->_db->getEscaped($this->getState('list.ordering').' '.$this->_db->getEscaped($this->getState('list.direction'))));

		//echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @access	protected
	 * @param	string		$id		A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.0
	 */
	function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.branch');

		return md5($id);
	}
}
