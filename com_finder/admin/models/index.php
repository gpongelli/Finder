<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Index model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderModelIndex extends JModelList
{
	/**
	 * @var		integer		The total number of items.
	 */
	protected $_list_total;

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
				'state', 'l.state',
				'title', 'l.title',
				'type_id', 'l.type_id',
				'url', 'l.url',
				'indexdate', 'l.indexdate',
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
	 * Method to delete one or more records.
	 *
	 * @param   array    $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 * @since   11.1
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		$pks		= (array) $pks;
		$table		= $this->getTable();

		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) {

			if ($table->load($pk)) {

				if ($this->canDelete($table)) {

					$context = $this->option.'.'.$this->name;

					// Trigger the onContentBeforeDelete event.
					$result = $dispatcher->trigger($this->event_before_delete, array($context, $table));
					if (in_array(false, $result, true)) {
						$this->setError($table->getError());
						return false;
					}

					if (!$table->delete($pk)) {
						$this->setError($table->getError());
						return false;
					}

					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger($this->event_after_delete, array($context, $table));

				} else {

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error) {
						JError::raiseWarning(500, $error);
					}
					else {
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
					}
				}

			} else {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

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

		$query->select('l.*');
		$query->select('t.title AS t_title');
		$query->from($db->quoteName('#__jxfinder_links').' AS l');
		$query->join('INNER', $db->quoteName('#__jxfinder_types').' AS t ON t.id = l.type_id');

		// Check the type filter.
		if ($this->getState('filter.type')) {
			$query->where($db->quoteName('l.type_id').' = '.(int)$this->getState('filter.type'));
		}

		// Check for state filter.
		if ($this->getState('filter.state')) {
			$query->where($db->quoteName('l.state').' = '.(int)$this->getState('filter.state'));
		}

		// Check the search phrase.
		if ($this->getState('filter.search') != '')
		{
			$search = $this->_db->getEscaped($this->getState('filter.search'));
			$query->where($db->quoteName('l.title').' LIKE "%'.$this->_db->getEscaped($search).'%"' .
						' OR '.$db->quoteName('l.url').' LIKE "%'.$this->_db->getEscaped($search).'%"' .
				 		' OR '.$db->quoteName('l.indexdate').' LIKE "%'.$this->_db->getEscaped($search).'%"');
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
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Link', $prefix = 'FinderTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the total number of items.
	 *
	 * @access	public
	 * @return	mixed	False on failure, integer on success.
	 * @since	1.0
	 */
	public function getTotal()
	{
		if (!empty($this->_list_total)) {
			return $this->_list_total;
		}

		// Assemble the query.
		$db		= $this->getDbo();
		$query	= clone($this->getListQuery());
		$query->clear('select')->clear('order')->clear('where');
		$query->select('count(l.link_id)');
		$db->setQuery($query);
		$return = $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		$this->_list_total = (int)$return;

		return $this->_list_total;
	}

	/**
	 * Method to purge the index, deleting all links.
	 *
	 * @return	boolean		True on success, false on failure.
	 */
	public function purge()
	{
		$db		= $this->getDbo();

		// Truncate the links table.
		$db->setQuery('TRUNCATE TABLE #__jxfinder_links');
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Truncate the links terms tables.
		for ($i = 0; $i <= 15; $i++)
		{
			// Get the mapping table suffix.
			$suffix = dechex($i);

			$db->setQuery('TRUNCATE TABLE #__jxfinder_links_terms'.$suffix);
			$db->query();

			// Check for a database error.
			if ($db->getErrorNum()) {
				// Throw database error exception.
				throw new Exception($db->getErrorMsg(), 500);
			}
		}

		// Truncate the terms table.
		$db->setQuery('TRUNCATE TABLE #__jxfinder_terms');
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Truncate the taxonomy map table.
		$db->setQuery('TRUNCATE TABLE #__jxfinder_taxonomy_map');
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Delete all the taxonomy nodes except the root.
		$db->setQuery('DELETE FROM #__jxfinder_taxonomy WHERE id > 1');
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Truncate the tokens tables.
		$db->setQuery('TRUNCATE TABLE `#__jxfinder_tokens`');
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Truncate the tokens aggregate table.
		$db->setQuery('TRUNCATE TABLE `#__jxfinder_tokens_aggregate`');
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		return true;
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

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '', 'string');
		$this->setState('filter.type', $type);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_finder');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('l.title', 'asc');
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
		$table		= $this->getTable();
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
