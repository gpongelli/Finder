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
	 * @var		array		Container for index information.
	 */
	protected $_index;

	/**
	 * @var		integer		The number of visible items in the list.
	 */
	protected $_list_count;

	/**
	 * @var		integer		The total number of items.
	 */
	protected $_list_total;

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	public function getState($property = null)
	{
		// If the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$application	= &JFactory::getApplication('administrator');
			$context		= 'com_finder.index.';

			$this->setState('adapter.id', JRequest::getInt('adapter_id'));

			// Load the filter state.
			$this->setState('filter.search', $application->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
			$this->setState('filter.state', $application->getUserStateFromRequest($context.'filter.state', 'filter_state', '*', 'string'));
			$this->setState('filter.type', $application->getUserStateFromRequest($context.'filter.type', 'filter_type', 0, 'int'));

			// Load the list state.
			$this->setState('list.start', $application->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
			$this->setState('list.limit', $application->getUserStateFromRequest($context.'list.limit', 'limit', $application->getCfg('list_limit', 25), 'int'));
			$this->setState('list.ordering', $application->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'l.link_id', 'cmd'));
			$this->setState('list.direction', $application->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

			// Load the check parameters.
			if ($this->state->get('filter.state') === '*') {
				$this->setState('check.state', false);
			} else {
				$this->setState('check.state', true);
			}

			// Load the parameters.
			$this->setState('params', JComponentHelper::getParams('com_finder'));

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to get a list of indexed URLs.
	 *
	 * @return	mixed	False on failure, array on success.
	 * @since	1.0
	 */
	public function getData()
	{
		$false = false;

		if (!empty($this->_index)) {
			return $this->_index;
		}

		// Load the index data.
		$return	= $this->_getList($this->_getIndexQuery(), $this->getState('list.start'), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$this->_index = $return;

		return $return;
	}

	/**
	 * Method to get the number of relevant links.
	 *
	 * @access	public
	 * @return	mixed	False on failure, integer on success.
	 * @since	1.0
	 */
	public function getCount()
	{
		$false = false;

		if (!empty($this->_list_count)) {
			return $this->_list_count;
		}

		$sql = clone($this->_getIndexQuery());
		$sql->clear('select')->clear('order');
		$sql->select('COUNT(l.link_id)');

		// Load the indexed data count.
		$this->_db->setQuery($sql);
		$return = $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$this->_list_count = (int)$return;

		return $this->_list_count;
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

		$sql = clone($this->_getIndexQuery());
		$sql->clear('select')->clear('order')->clear('where');
		$sql->select('COUNT(l.link_id)');

		// Load the label total data.
		$this->_db->setQuery($sql);
		$return = $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
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
	 * Method to publish/unpublish links in the index.
	 *
	 * @access	public
	 * @param	array	$link_ids	An array of link ids.
	 * @param	int		$state	An integer representing the state of the link
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	public function publish($link_ids, $state)
	{
		$db		= $this->getDbo();

		// Set the links states to unpublished.
		$query	= 'UPDATE #__jxfinder_links SET published = ' . (int) $state
				. ' WHERE link_id = '.implode(' OR link_id = ', $link_ids);

		$db->setQuery($query);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		return true;
	}

	protected function _getIndexQuery()
	{
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('l.link_id, l.title, l.type_id, l.url, l.indexdate, l.state, l.published');
		$query->select('l.publish_start_date, l.publish_end_date, l.start_date, l.end_date');
		$query->select('t.title AS t_title');
		$query->from('#__jxfinder_links AS l');
		$query->join('INNER', '#__jxfinder_types AS t ON t.id = l.type_id');

		// Check the type filter.
		if ($this->getState('filter.type') !== 0) {
			$query->where('l.type_id = '.(int)$this->getState('filter.type'));
		}

		// Check for state filter.
		if ($this->getState('check.state')) {
			$query->where('l.published = '.(int)$this->getState('filter.state'));
		}

		// Check the search phrase.
		if ($this->getState('filter.search') != '')
		{
			$search = $this->_db->getEscaped($this->getState('filter.search'));
			$query->where('l.title LIKE "%'.$this->_db->getEscaped($search).'%"' .
						' OR l.url LIKE "%'.$this->_db->getEscaped($search).'%"' .
				 		' OR l.indexdate LIKE "%'.$this->_db->getEscaped($search).'%"');
		}

		$query->order($this->_db->getEscaped($this->getState('list.ordering')).' '.$this->getState('list.direction'));

		return $query;
	}

	protected function _getUrlQuery($cid)
	{
		$query	= 'SELECT url FROM #__jxfinder_links'
				. ' WHERE link_id = '.implode(' OR link_id = ', $cid);

		return $query;
	}
}
