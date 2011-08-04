<?php
/**
 * @version		$Id: view.html.php 981 2010-06-15 18:38:02Z robs $
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC.  All rights reserved.
 * @license		GNU General Public License
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Groups view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @stats		1.1
 */
class FinderViewAbout extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		// Load the view data.
		$version	= new FinderVersion;
		$versions	= $version->getVersions();
		$data		= &$this->get('Data');
		$state		= &$this->get('State');
		$params		= &$state->get('params');

		$upgrades	= $this->get('Upgrades');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Push out the view data.
		$this->assignRef('data',		$data);
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);
		$this->assignRef('versions',	$versions);

		parent::display($tpl);
	}
}