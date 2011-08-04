<?php
/**
 * @version		$Id: adapters.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jx('jx.application.component.modellist');

/**
 * Adapters model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderModelAdapters extends JModelList
{
	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the _getStoreId() method and caching data structures.
	 *
	 * @var		string
	 */
	protected $_context = 'com_finder.adapters';

	/**
	 *
	 */
	public function setStates($cid, $state = 0)
	{
		$user = &JFactory::getUser();

		// Get a plugin row instance.
		$row = JTable::getInstance('Plugin', 'JTable');

		// Update the state for each row
		for ($i = 0; $i < count($cid); $i++)
		{
			// Load the row.
			$row->load($cid[$i]);

			// Make sure the filter isn't checked out by someone else.
			if ($row->checked_out != 0 && $row->checked_out != $user->id)
			{
				$this->setError(JText::sprintf('FINDER_ADAPTER_CHECKED_OUT', $cid[$i]));
				return false;
			}

			// Check the current state.
			if ($row->published != $state)
			{
				// Set the new state.
				$row->published = $state;

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
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return	object	A JDatabaseQuery object to retrieve the data set.
	 */
	protected function _getListQuery()
	{
		// Get the plugins in the finder folder.
		$sql = new JDatabaseQuery();
		$sql->select('p.*');
		$sql->from('#__plugins AS p');
		$sql->where('p.folder = "finder"');

		// Handle a published state filter.
		if ($this->getState('check.state')) {
			$sql->where('p.published = '.(int)$this->getState('filter.state'));
		}

		// Handle a search filter.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $this->_db->getEscaped($search, true);
			$sql->where('LOWER(p.name) LIKE "%'.$search.'%"');
		}

		// Handle the list ordering.
		$ordering	= $this->getState('list.ordering');
		$direction	= $this->getState('list.direction');
		$sql->order($this->_db->getEscaped($ordering).' '.$this->_db->getEscaped($direction));

		return $sql;
	}

	/**
	 * Method to get a store id based on model the configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	An identifier string to generate the store id.
	 * @return	string	A store id.
	 */
	protected function _getStoreId($id = '')
	{
		// Add the filter state.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');

		// The parent class adds the list state.
		return parent::_getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
		$app		= JFactory::getApplication();
		$params		= JComponentHelper::getParams('com_finder');
		$context	= 'com_finder.adapters.';

		// Load the filter state.
		$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
		$this->setState('filter.state', $app->getUserStateFromRequest($context.'filter.state', 'filter_state', '*', 'string'));

		// Load the list state.
		$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'p.name', 'cmd'));
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
	}
}