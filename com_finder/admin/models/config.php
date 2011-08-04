<?php
/**
 * @version		$Id: config.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Configuration model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderModelConfig extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	protected
	 * @var		boolean
	 */
	var $__state_set		= null;

	/**
	 * Container for adapter information.
	 *
	 * @access	private
	 * @var		array
	 */
	var $_adapters			= null;

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
			$application	= &JFactory::getApplication('administrator');
			$context		= 'com_finder.config.';

			// Load the list state.
			$this->setState('list.ordering', $application->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'a.title', 'cmd'));
			$this->setState('list.direction', $application->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

			// Load the parameters.
			$this->setState('params', JComponentHelper::getParams('com_finder'));

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	function save()
	{
		// Initialize variables.
		$table			= &JTable::getInstance('component');
		$params 		= JRequest::getVar('params', array(), 'post', 'array');
		$row			= array();
		$row['option']	= 'com_finder';
		$row['params']	= $params;

		// Load the component data for com_finder.
		if (!$table->loadByOption('com_finder')) {
			$this->setError($table->getError());
			return false;
		}

		// Bind the new values
		$table->bind($row);

		// Check the row.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the row.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		// Get the adapters that had configuration parameters.
		$adapters = JRequest::getVar('adapters', array(), 'post', 'array');

		// Save the parameters for each adapter.
		foreach($adapters as $adapter)
		{
			// Get the params array from the post.
			$aparams	= JRequest::getVar($adapter.'_params', array(), 'post', 'array');
			$ini		= '';

			// Convert the params array to an INI string.
			foreach ($aparams as $k => $v) {
				$ini .= $k.'='.$v."\n";
			}

			$query	= 'UPDATE #__jxfinder_adapters'
					. ' SET params = '.$this->_db->Quote($ini)
					. ' WHERE alias = '.$this->_db->Quote($adapter);

			$this->_db->setQuery($query);

			// Save the adapter parameters.
			if (!$this->_db->query()) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}
}