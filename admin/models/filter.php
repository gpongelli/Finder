<?php
/**
 * @version		$Id: filter.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Filter model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderModelFilter extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	protected
	 * @var		boolean
	 */
	var $__state_set		= null;

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
			$params		= &JComponentHelper::getParams('com_finder');

			// Load the filter state.
			if (JRequest::getWord('layout') === 'edit') {
				$filter_id = (int)$app->getUserState('com_finder.edit.filter.id');
				$this->setState('filter.id', $filter_id);
			} else {
				$filter_id = (int)JRequest::getInt('filter_id');
				$this->setState('filter.id', $filter_id);
			}

			// Add the filter id to the context to preserve sanity.
			$context	= 'com_finder.filter.'.$filter_id.'.';

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		return parent::getState($property);
	}


	/**
	 * Method to checkin a row.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	integer	$id		The numeric id of a row
	 * @return	boolean	True on success/false on failure
	 */
	function checkin($filter_id = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$user_id	= (int) $user->get('id');
		$filter_id	= (int) $filter_id;

		if ($filter_id === 0) {
			$filter_id = $this->getState('filter.id');
		}

		if (!$filter_id) {
			return true;
		}

		// Get a FinderTableFilter instance.
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
		$filter = &JTable::getInstance('Filter', 'FinderTable');

		// Attempt to check-in the row.
		$return = $filter->checkin($user_id, $filter_id);

		// Check for a database error.
		if ($return === false) {
			$this->setError($filter->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check-out a filter for editing.
	 *
	 * @access	public
	 * @param	int		$filter_id	The numeric id of the filter to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.0
	 */
	function checkout($filter_id)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$user_id	= (int) $user->get('id');
		$filter_id	= (int) $filter_id;

		// Check for a new filter id.
		if ($filter_id === -1) {
			return true;
		}

		// Get a FinderTableFilter instance.
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
		$filter = &JTable::getInstance('Filter', 'FinderTable');

		// Attempt to check-out the row.
		$return = $filter->checkout($user_id, $filter_id);

		// Check for a database error.
		if ($return === false) {
			$this->setError($filter->getError());
			return false;
		}

		// Check if the row is checked-out by someone else.
		if ($return === null) {
			$this->setError(JText::_('FINDER_FILTER_CHECKED_OUT'));
			return false;
		}

		return true;
	}

	/**
	 * Method to get the form object.
	 *
	 * @access	public
	 * @return	mixed	JForm object on success, false on failure.
	 * @since	1.0
	 */
	function &getForm()
	{
		$false	= false;

		// Get the form.
		jx('jx.form.form');
		JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		$form = JForm::getInstance('filter', 'JForm', 'filter', array('array' => 'JForm'));

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		return $form;
	}

	function &getFilter()
	{
		$filter_id	= (int)$this->getState('filter.id');
		$false		= false;

		// Get a FinderTableFilter instance.
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
		$filter = &JTable::getInstance('Filter', 'FinderTable');

		// Attempt to load the row.
		$return = $filter->load($filter_id);

		// Check for a database error.
		if ($return === false && $filter->getError()) {
			$this->serError($filter->getError());
			return $false;
		}

		// Process the filter data.
		if (!empty($filter->data)) {
			$filter->data = explode(',', $filter->data);
		}
		else if (empty($filter->data)) {
			$filter->data = array();
		}

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		return $filter;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @access	public
	 * @param	array	The form data.
	 * @return	mixed	Array of filtered data if valid, false otherwise.
	 * @since	1.0
	 */
	function validate($data)
	{
		// Get the form.
		$form = &$this->getForm();

		// Check for an error.
		if ($form === false) {
			return false;
		}

		// Filter and validate the form data.
		$data	= $form->filter($data);
		$return	= $form->validate($data);

		// Check for an error.
		if (JError::isError($return)) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}

	function save($data)
	{
		$app		= &JFactory::getApplication();
		$offset		= $app->getCfg('offset');
		$filterId	= (int)$this->getState('filter.id');
		$isNew		= true;

		// Add a table include path
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

		// Get a filter row instance.
		$filter = JTable::getInstance('Filter', 'FinderTable');

		// Load the row if saving an existing item.
		if ($filterId > 0)
		{
			$filter->load($filterId);
			$isNew = false;
		}

		// Ensure user supplied dates are stored in GMT based on their timezone
		if (isset($data['params']) && is_array($data['params']))
		{
			if (isset($data['params']['d1']) && intval($data['params']['d1'])) {
				$date =& JFactory::getDate($data['params']['d1'], $offset);
				$data['params']['d1'] = $date->toMySQL();
			}
			if (isset($data['params']['d2']) && intval($data['params']['d2'])) {
				$date =& JFactory::getDate($data['params']['d2'], $offset);
				$data['params']['d2'] = $date->toMySQL();
			}
		}

		// Bind the data
		if (!$filter->bind($data))
		{
			$this->setError(JText::sprintf('FINDER_FILTER_BIND_FAILED', $filter->getError()));
			return false;
		}

		// Prepare the row for saving
		$filter = $this->_prepareSave($filter);

		// Check the data
		if (!$filter->check())
		{
			$this->setError($filter->getError());
			return false;
		}

		// Store the data
		if (!$filter->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $filter->filter_id;
	}

	function _prepareSave($filter)
	{
		jimport('joomla.filter.output');
		$date = &JFactory::getDate();

		$filter->title		= htmlspecialchars_decode($filter->title, ENT_QUOTES);
		$filter->alias		= JFilterOutput::stringURLSafe($filter->alias);

		if (empty($filter->alias)) {
			$filter->alias = JFilterOutput::stringURLSafe($filter->title);
		}

		if (!$filter->filter_id)
		{
			// Get the user object
			$user	= &JFactory::getUser();

			// Set the values
			$filter->created			= $date->toMySQL();
			$filter->created_by			= $user->get('id');
			$filter->created_by_alias	= $user->get('name');
		}
		else
		{
			// Get the user object
			$user	= &JFactory::getUser();

			// Set the values
			$filter->modified		= $date->toMySQL();
			$filter->modified_by	= $user->get('id');
		}

		if (is_array($filter->data)) {
			$filter->map_count		= count($filter->data);
			$filter->data			= implode(',', $filter->data);
		}
		else {
			$filter->map_count		= 0;
			$filter->data			= implode(',', array());
		}

		return $filter;
	}
}