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
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object   $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 * @since   11.1
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
		return $user->authorise('core.delete', $this->option);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object   $record	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 * @since   11.1
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		return $user->authorise('core.edit.state', $this->option);
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

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 * @since   11.1
	 */
	function publish(&$pks, $value = 1)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		$table		= JTable::getInstance('Extension', 'JTable');
		$pks		= (array) $pks;

		// Include the content plugins for the change of state event.
		JPluginHelper::importPlugin('content');

		// Access checks.
		foreach ($pks as $i => $pk) {
			$table->reset();

			if ($table->load($pk)) {
				if (!$this->canEditState($table)) {
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		$context = $this->option.'.'.$this->name;

		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
