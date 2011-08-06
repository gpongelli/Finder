<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Map model for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderModelMap extends JModel
{
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
			$context		= 'com_finder.map.';

			// Load the parameters.
			$this->setState('params', JComponentHelper::getParams('com_finder'));

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to purge all maps from the taxonomy.
	 *
	 * @access	public
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function purge()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->delete()->from($db->quoteName('#__jxfinder_taxonomy'))->where($db->quoteName('parent_id').' > 1');
		$db->setQuery($query);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$query->clear();
		$query->delete()->from($db->quoteName('#__jxfinder_taxonomy_map'))->where('1');
		$db->setQuery($query);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to delete maps from the taxonomy.
	 *
	 * @access	public
	 * @param	array	$map_ids	An array of map ids.
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function delete($map_ids)
	{
		$db		= $this->getDbo();

		// Iterate the maps to delete each one.
		foreach ($map_ids as $map_id)
		{
			$query	= $db->getQuery(true);

			// Remove all relevant rows from the taxonomy map table.
			$query->delete()->from($db->quoteName('#__jxfinder_taxonomy_map'))->where($db->quoteName('node_id').' > '.(int)$map_id);
			$db->setQuery($query);
			$db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			$query->clear();
			$query->delete()->from($db->quoteName('#__jxfinder_taxonomy'))->where($db->quoteName('id').' = '.(int)$map_id.' AND '.$db->quoteName('parent_id').' > 1');
			$db->setQuery($query);
			$db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to publish maps in the taxonomy.
	 *
	 * @access	public
	 * @param	array	$map_ids	An array of map ids.
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function publish($map_ids, $state)
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->update($db->quoteName('#__jxfinder_taxonomy'));
		$query->set($db->quoteName('state').' = ' . (int) $state);
		$query->where($db->quoteName('id').' = '.implode(' OR id = ', $map_ids));
		$db->setQuery($query);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to unpublish maps in the taxonomy.
	 *
	 * @access	public
	 * @param	array	$map_ids	An array of map ids.
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function unpublish($map_ids)
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->update($db->quoteName('#__jxfinder_taxonomy'));
		$query->set($db->quoteName('state').' = 0');
		$query->where($db->quoteName('id').' = '.implode(' OR id = ', $map_ids));
		$db->setQuery($query);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}
