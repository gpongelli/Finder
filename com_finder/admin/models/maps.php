<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Maps model for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderModelMaps extends JModelList
{
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
				'id', 'a.id',
				'title', 'a.title',
			);
		}

		parent::__construct($config);
	}

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
			if ($this->state->get('filter.state') === '*') {
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
	function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

			// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		// If emtpy or an error, just return.
		if (empty($items)) {
			return array();
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
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
		$query->from($db->quoteName('#__jxfinder_taxonomy').' AS a');

		// Self-join to get children.
		$query->select('COUNT(b.id) AS num_children');
		$query->join('LEFT', $db->quoteName('#__jxfinder_taxonomy').' AS b ON b.parent_id=a.id');

		// Join to get the map links
		$query->select('COUNT(c.node_id) AS num_nodes');
		$query->join('LEFT', $db->quoteName('#__jxfinder_taxonomy_map').' AS c ON c.node_id=a.id');

		$query->group('a.id');

		// If the model is set to check item state, add to the query.
		if ($this->getState('check.state')) {
			$query->where($db->quoteName('a.state').' = '.(int)$this->getState('filter.state'));
		}

		// Filter the maps over the branch if set.
		$branch_id = $this->getState('filter.branch');
		if (!empty($branch_id)) {
			$query->where($db->quoteName('a.parent_id').' = '.(int)$branch_id);
		}

		// Filter the maps over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where($db->quoteName('a.title').' LIKE '.$db->quote('%'.$search.'%'));
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering').' '.$db->getEscaped($this->getState('list.direction'))));

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
	function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.branch');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.  Calling getState in this method will result in recursion.
	 *
	 * @param   string	$ordering	An optional ordering field.
	 * @param   string	$direction	An optional direction.
	 *
	 * @since	1.7
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// List state information.
		parent::populateState('a.id', 'asc');
	}
}
