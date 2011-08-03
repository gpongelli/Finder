<?php
/**
 * @version		$Id: view.html.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Configuration view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderViewConfig extends JView
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
		// Initialize variables.
		$user		= &JFactory::getUser();

		// Load the view data.
		$state		= &$this->get('State');
		$params		= &$state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Prepare the view.
		$this->document->addStyleSheet('components/com_finder/media/css/finder.css');

		JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');

		// Push out the view data.
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);

		// Load the view template.
		$result = $this->loadTemplate($tpl);

		// Check for an error.
		if (JError::isError($result)) {
			return $result;
		}

		echo $result;
	}
}