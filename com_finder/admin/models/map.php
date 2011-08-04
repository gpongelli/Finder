<?php
/**
 * @version		$Id: map.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jx('jx.database.databasequery');

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
	 * Flag to indicate model state initialization.
	 *
	 * @access	private
	 * @var		boolean
	 */
	var $__state_set	= false;

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
		// Remove all rows from the taxonomy table.
		$query	= 'DELETE FROM #__jxfinder_taxonomy WHERE parent_id > 1';

		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Remove all rows from the taxonomy map table.
		$query	= 'DELETE FROM #__jxfinder_taxonomy_map WHERE 1';

		$this->_db->setQuery($query);
		$this->_db->query();

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
		// Iterate the maps to delete each one.
		foreach ($map_ids as $map_id)
		{
			// Remove all relevant rows from the taxonomy map table.
			$query	= 'DELETE FROM #__jxfinder_taxonomy_map WHERE node_id = '.(int)$map_id;

			$this->_db->setQuery($query);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Remove the row from the taxonomy table.
			$query	= 'DELETE FROM #__jxfinder_taxonomy WHERE id = '.(int)$map_id.' AND parent_id > 1';

			$this->_db->setQuery($query);
			$this->_db->query();

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
	function publish($map_ids)
	{
		// Set the maps' states to publish.
		$query	= 'UPDATE #__jxfinder_taxonomy SET state = 1'
				. ' WHERE id = '.implode(' OR id = ', $map_ids);

		$this->_db->setQuery($query);
		$this->_db->query();

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
		// Set the maps' states to unpublish.
		$query	= 'UPDATE #__jxfinder_taxonomy SET state = -1'
				. ' WHERE id = '.implode(' OR id = ', $map_ids);

		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}
