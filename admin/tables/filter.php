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
 * Filter table class for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderTableFilter extends JTable
{
	var $filter_id			= null;

	var $title				= null;

	var $alias				= null;

	var $state				= null;

	var $created			= null;

	var $created_by			= null;

	var $created_by_alias	= null;

	var $modified			= null;

	var $modified_by		= null;

	var $checked_out		= null;

	var $checked_out_time	= null;

	var $map_count			= null;

	var $data				= null;

	var $params				= null;

	function __construct(&$db)
	{
		parent::__construct('#__jxfinder_filters', 'filter_id', $db);

		// Initialize variables
		$this->filter_id		= 0;
		$this->state			= 1;
		$this->checked_out		= 0;
		$this->checked_out_time	= 0;
		$this->data				= array();
	}

	function bind($values)
	{
		// Parameters
		if (is_array($values['params'])) {
			$registry = new JRegistry;
			$registry->loadArray($values['params']);
			$values['params'] = trim($registry->toString());
		}

		return parent::bind($values);
	}

	function check()
	{
		// Check that the label has a real title.
		if (empty($this->title)) {
			$this->setError(JText::_('FINDER_CHECK_TITLE_FAILED'));
			return false;
		}

		// Check that the label has a real alias.
		if (empty($this->alias)) {
			$this->setError(JText::_('FINDER_CHECK_ALIAS_FAILED'));
			return false;
		}

		// Check for a duplicate alias.
		$this->_db->setQuery('SELECT filter_id FROM #__jxfinder_filters WHERE alias = '.$this->_db->Quote($this->alias));
		$return = $this->_db->loadResult();

		if ($return && $return !== $this->filter_id)
		{
			// Get the aliases like this one.
			$query	= 'SELECT alias FROM #__jxfinder_filters'
					. ' WHERE alias LIKE "'.$this->_db->getEscaped($this->alias).'%"';
			$this->_db->setQuery($query);
			$aliases = $this->_db->loadResultArray();

			// Find an available alias.
			if (is_array($aliases) && count($aliases))
			{
				for ($i = 2, $n = count($aliases)+2; $i <= $n; $i++)
				{
					if (!in_array($this->alias.$i, $aliases)) {
						$this->alias .= $i;
						break;
					}
				}
			}
		}

		return true;
	}

	function checkin($user_id = null, $oid = null)
	{
		$k = $this->_tbl_key;

		if ($user_id === null) {
			$user_id = (int)$this->checked_out;
		}

		if ($oid !== null) {
			$this->$k = $oid;
		}

		if ($this->$k == null) {
			return false;
		}

		$this->$k = (int) $this->$k;

		// Prepare the query to check-in the row.
		$query	= 'UPDATE '.$this->_db->nameQuote($this->_tbl)
				. ' SET checked_out = 0, checked_out_time = '.$this->_db->Quote($this->_db->getNullDate())
				. ' WHERE '.$this->_tbl_key.' = '.(int)($this->$k)
				. ' AND (checked_out = '.(int)$user_id.' OR checked_out = 0)';

		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->checked_out = 0;
		$this->checked_out_time = '';

		return true;
	}

	function checkout($user_id, $oid = null)
	{
		jimport('joomla.utilities.date');

		$k = $this->_tbl_key;

		if ($oid !== null) {
			$this->$k = $oid;
		}

		$this->$k	= (int) $this->$k;
		$user_id	= (int) $user_id;

		// Get a MySQL formatted time.
		$date = new JDate();
		$time = $date->toMysql();

		// Prepare the query to check-out the row.
		$query	= 'UPDATE '.$this->_db->nameQuote($this->_tbl)
				. ' SET checked_out = '.(int)$user_id.', checked_out_time = '.$this->_db->Quote($time)
				. ' WHERE '.$this->_tbl_key.' = '.(int)($this->$k)
				. ' AND (checked_out = '.(int)$user_id.' OR checked_out = 0)';

		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Prepare the query to verify the item was checked-out.
		$query	= 'SELECT filter_id FROM '.$this->_db->nameQuote($this->_tbl)
				. ' WHERE '.$this->_tbl_key.' = '.(int)($this->$k)
				. ' AND checked_out = '.(int)$user_id;

		$this->_db->setQuery($query);
		$return = (int) $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->checked_out = $user_id;
		$this->checked_out_time = $time;

		if ($return === $this->$k) {
			return true;
		} else {
			return null;
		}
	}
}