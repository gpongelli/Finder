<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Filters model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderModelFilters extends JModelList
{
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
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'filter_id', 'a.filter_id',
				'title', 'a.title',
				'state', 'a.state',
				'created_by_alias', 'a.created_by_alias',
				'created', 'a.created',
				'map_count', 'a.map_count'
			);
		}

		parent::__construct($config);
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

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery	$query	A JDatabaseQuery object
	 * @since	1.6
	 */
	function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select all fields from the table.
		$query->select('a.*');
		$query->from($db->quoteName('#__finder_filters').' AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', $db->quoteName('#__users').' AS uc ON uc.id=a.checked_out');

		// Join over the users for the author.
		$query->select('ua.name AS user_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS ua ON ua.id = a.created_by');

		// Check for a search filter.
		if ($this->getState('filter.search')) {
			$query->where('( '.$db->quoteName('a.title').' LIKE \'%'.$this->_db->getEscaped($this->getState('filter.search')).'%\' )');
		}

		// If the model is set to check item state, add to the query.
		if ($this->getState('filter.state')) {
			$query->where($db->quoteName('a.state').' = '.(int)$this->getState('filter.state'));
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering').' '.$db->getEscaped($this->getState('list.direction'))));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	$id	A prefix for the store id.
	 * @return	string	$id	A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	function getTotal()
	{
		// Assemble the query.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('count(a.filter_id)');
		$query->from($db->quoteName('#__finder_filters').' AS a');
		$db->setQuery($query);
		$return = $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		$this->_filter_total = (int)$return;

		return $this->_filter_total;
	}

	/**
	 * Method to auto-populate the model state.  Calling getState in this method will result in recursion.
	 *
	 * @param   string	$ordering	An optional ordering field.
	 * @param   string	$direction	An optional direction.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_finder');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}
}