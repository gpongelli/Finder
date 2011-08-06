<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

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
	 * The total number of extensions.
	 *
	 * @access	private
	 * @var		integer
	 */
	var $_filter_extensions		= null;

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
				'name', 'p.name',
				'enabled', 'p.enabled',
				'folder', 'p.folder',
				'extension_id', 'p.extension_id'
			);
		}

		parent::__construct($config);
	}

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
		$query->select('p.*');
		$query->from($db->quoteName('#__extensions').' AS p');
		$query->where($db->quoteName('p.type').' = '.$db->quote('plugin'));
		$query->where($db->quoteName('p.folder').' = '.$db->quote('finder'));

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', $db->quoteName('#__users').' AS uc ON uc.id=p.checked_out');

		// Check for a search filter.
		if ($this->getState('filter.search')) {
			$query->where('( '.$db->quoteName('p.name').' LIKE \'%'.$this->_db->getEscaped($this->getState('filter.search')).'%\' )');
		}

		// If the model is set to check item state, add to the query.
		if ($this->getState('filter.state')) {
			$query->where($db->quoteName('p.enabled').' = '.(int)$this->getState('filter.state'));
		}

		// Handle the list ordering.
		$ordering	= $this->getState('list.ordering');
		$direction	= $this->getState('list.direction');
		if (!empty($ordering)) {
			$query->order($db->getEscaped($ordering).' '.$db->getEscaped($direction));
		}

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
		$query->select('count(p.extension_id)');
		$query->from($db->quoteName('#__extensions').' AS p');
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
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 */
	protected function populateState()
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
		parent::populateState('p.name', 'asc');
	}
}
