<?php
/**
 * @version		$Id: controller.php 464 2009-09-23 19:46:16Z louis $
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.controller');

/**
 * Static class to import and instantiate a controller based on a context
 * and the request.
 *
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @since		2.0
 */
class JControllerHelper
{
	/**
	 * Method to get the appropriate controller.
	 *
	 * @param	string	The component for which to load a controller.
	 * @return	object	JController object.
	 * @since	2.0
	 */
	public static function getInstance($context)
	{
		static $instance;

		if (!empty($instance)) {
			return $instance;
		}

		if (!file_exists(JPATH_COMPONENT.DS.'controller.php')) {
			JError::raiseError(500, 'controller.php not found');
		}
		require_once JPATH_COMPONENT.DS.'controller.php';

		// If the task is an array only use the first offset.
		if (isset($_REQUEST['task']) && is_array($_REQUEST['task'])) {
			$_REQUEST['task'] = array_pop(array_keys($_REQUEST['task']));
		}
		$cmd = JRequest::getCmd('task', 'display');

		// Check for a controller.task command.
		if (strpos($cmd, '.') != false)
		{
			// Explode the controller.task command.
			list($type, $task) = explode('.', $cmd);

			// Define the controller name and path
			$protocol	= JRequest::getWord('protocol');
			$type		= strtolower($type);
			$file		= (!empty($protocol)) ? $type.'.'.$protocol.'.php' : $type.'.php';
			$path		= JPATH_COMPONENT.DS.'controllers'.DS.$file;

			// If the controller file path exists, include it ... else lets die with a 500 error
			if (file_exists($path)) {
				require_once($path);
			}
			else {
				JError::raiseError(500, JText::sprintf('JX_Invalid_Controller', $type));
			}

			JRequest::setVar('task', $task);
		}
		else
		{
			// Base controller, just set the task :)
			$type = null;
			$task = $cmd;
		}

		// Set the name for the controller and instantiate it
		$class = ucfirst($context).'Controller'.ucfirst($type);
		if (class_exists($class)) {
			$instance = new $class();
		}
		else {
			JError::raiseError(500, JText::sprintf('JX_Invalid_Controller_Class', $class));
		}

		return $instance;
	}
}