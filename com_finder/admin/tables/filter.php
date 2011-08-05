<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Filter table class for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderTableFilter extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__jxfinder_filters', 'filter_id', $db);
	}

	function check()
	{
		if (trim($this->alias) == '') {
			$this->alias = $this->title;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);

		if (trim(str_replace('-','',$this->alias)) == '') {
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		// Check the end date is not earlier than start up.
		if ($this->d2 > $this->_db->getNullDate() && $this->d2 < $this->d1) {
			// Swap the dates.
			$temp = $this->d1;
			$this->d1 = $this->d2;
			$this->d2 = $temp;
		}

		return true;
	}

	/**
	 * Overriden JTable::store to set modified data and user id.
	 *
	 * @param	boolean	$updateNulls	True to update fields even if they are null.
	 *
	 * @return	boolean	True on success.
	 * @since	1.7
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		if ($this->filter_id) {
			// Existing item
			$this->modified		= $date->toMySQL();
			$this->modified_by	= $user->get('id');
		} else {
			// New item. A filter's created field can be set by the user,
			// so we don't touch it if it is set.
			if (!intval($this->created)) {
				$this->created = $date->toMySQL();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}

		if (is_array($this->data)) {
			$this->map_count	= count($this->data);
			$this->data			= implode(',', $this->data);
		}
		else {
			$this->map_count	= 0;
			$this->data			= implode(',', array());
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Filter', 'FinderTable');
		if ($table->load(array('alias' => $this->alias)) && ($table->filter_id != $this->filter_id || $this->filter_id == 0)) {
			$this->setError(JText::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS'));
			return false;
		}
		return parent::store($updateNulls);
	}
}
