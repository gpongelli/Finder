<?php
/**
 * @version		$Id: filters.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jx('jx.database.databasequery');

/**
 * Filters model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderModelFilters extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	private
	 * @var		boolean
	 */
	var $__state_set		= false;

	/**
	 * Array of filter data objects.
	 *
	 * @access	private
	 * @var		array
	 */
	var $_filter_data		= array();

	/**
	 * The number of visible filters.
	 *
	 * @access	private
	 * @var		integer
	 */
	var $_filter_count		= null;

	/**
	 * The total number of filters.
	 *
	 * @access	private
	 * @var		integer
	 */
	var $_filter_total		= null;

	/**
	 * The JPagination object.
	 *
	 * @access	private
	 * @var		object
	 */
	var	$_pagination		= null;

	/**
	 * The filters list data query.
	 *
	 * @access	private
	 * @var		string
	 */
	var $_list_query		= null;

	/**
	 * The filters list total query.
	 *
	 * @access	private
	 * @var		string
	 */
	var $_total_query		= null;

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
		// If the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$app		= &JFactory::getApplication();
			$params		= &JComponentHelper::getParams('com_finder');
			$context	= 'com_finder.filters.';

			// Load the filter state.
			$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
			$this->setState('filter.state', $app->getUserStateFromRequest($context.'filter.state', 'filter_state', '*', 'string'));

			// Load the list state.
			$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
			$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
			$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'a.title', 'cmd'));
			$this->setState('list.direction', $app->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

			// Handle 0 limit with > 0 start offset.
			if ($this->_state->get('list.limit') === 0) {
				$this->_state->set('list.start', 0);
			}

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
	 * Method to delete filters from the database.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	integer	$cid	An array of	numeric ids of the rows.
	 * @return	boolean	True on success/false on failure.
	 */
	function delete($cid)
	{
		// Add a table include path
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

		// Get a filters row instance
		$row = JTable::getInstance('Filter', 'FinderTable');

		for($i = 0, $c = count($cid); $i < $c; $i++)
		{
			// Load the row.
			$return = $row->load($cid[$i]);

			// Check for an error.
			if ($return == false) {
				$this->setError($row->getError());
				return false;
			}

			// Delete the row.
			$return = $row->delete();

			// Check for an error.
			if ($return == false) {
				$this->setError($row->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get the filters data set.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	array	An array of filter objects.
	 */
	function &getFilters()
	{
		$false = false;

		if (!empty($this->_filter_data)) {
			return $this->_filter_data;
		}

		// Load the filters data.
		$return	= $this->_getList($this->_getListQuery(), $this->getState('list.start'), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$this->_filter_data = $return;

		return $return;
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
		if (!empty($this->_pagination)) {
			return $this->_pagination;
		}

		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getCount(), $this->getState('list.start'), $this->getState('list.limit'));

		return $this->_pagination;
	}

	/**
	 * Method to get the total number of filters.
	 *
	 * @access	public
	 * @return	mixed	False on failure, integer on success.
	 * @since	1.0
	 */
	function getTotal()
	{
		if (!empty($this->_filter_total)) {
			return $this->_filter_total;
		}

		// Load the filter total data.
		$this->_db->setQuery($this->_getTotalQuery());
		$return = $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->_filter_total = (int)$return;

		return $this->_filter_total;
	}

	/**
	 * Method to get the relevant number of filters.
	 *
	 * @access	public
	 * @return	mixed	False on failure, integer on success.
	 * @since	1.0
	 */
	function getCount()
	{
		if (!empty($this->_filter_count)) {
			return $this->_filter_count;
		}

		// Load the filter count data.
		$return = $this->_getListCount($this->_getListQuery());

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->_filter_count = (int)$return;

		return $this->_filter_count;
	}

	function setStates($cid, $state = 0)
	{
		$user = &JFactory::getUser();

		// Add a table include path.
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

		// Get a filters row instance.
		$row = JTable::getInstance('Filter', 'FinderTable');

		// Update the state for each row
		for ($i=0; $i < count($cid); $i++)
		{
			// Load the row.
			$row->load($cid[$i]);

			// Make sure the filter isn't checked out by someone else.
			if ($row->checked_out != 0 && $row->checked_out != $user->id)
			{
				$this->setError(JText::sprintf('FINDER_FILTER_CHECKED_OUT', $cid[$i]));
				return false;
			}

			// Check the current ordering.
			if ($row->state != $state)
			{
				// Set the new ordering.
				$row->state = $state;

				// Save the row.
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to generate an SQL query.
	 *
	 * @access	private
	 * @return	string	An SQL query
	 * @since	1.0
	 */
	function _getListQuery()
	{
		if (empty($this->_list_query))
		{
			// Get the clauses fragments
			$ordering	= $this->getState('list.ordering');
			$direction	= $this->getState('list.direction');
			$search		= $this->getState('filter.search');
			$state		= $this->getState('filter.state');

			$query = new JDatabaseQuery();
			$query->select('a.*');
			$query->from('#__jxfinder_filters AS a');
			$query->order($ordering.' '.$direction);

			// Check for a search filter.
			if ($search) {
				$query->where('( a.title LIKE \'%'.$this->_db->getEscaped($search).'%\' )');
			}

			// If the model is set to check item state, add to the query.
			if ($this->getState('check.state')) {
				$query->where('a.state = '.(int)$this->getState('filter.state'));
			}

			$this->_list_query = $query->toString();
		}

		return $this->_list_query;
	}

	function _getTotalQuery()
	{
		if (empty($this->_total_query))
		{
			// Assemble the query.
			$query = new JDatabaseQuery();
			$query->select('count(a.filter_id)');
			$query->from('#__jxfinder_filters AS a');
			$this->_total_query = $query->toString();
		}

		return $this->_total_query;
	}
}