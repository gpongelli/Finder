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
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_FINDER';

	/**
	 * Model context string.
	 *
	 * @var		string	The context of the model
	 */
	protected $_context		= 'com_finder.map';

	/**
	 * Returns a JTable object, always creating it.
	 *
	 * @param	string	$type	The table type to instantiate
	 * @param	string	$prefix	A prefix for the table class name. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Map', $prefix = 'FinderTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
