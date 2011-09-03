<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Statistics view class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class FinderViewStatistics extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	function display($tpl = null)
	{
		// Load the view data.
		$this->data		= $this->get('Data');
		$this->state	= $this->get('State');
		$this->upgrades	= $this->get('Upgrades');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Prepare the view.
		JHtml::stylesheet('com_finder/finderadmin.css', false, true, false);

		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

		parent::display($tpl);
	}
}
