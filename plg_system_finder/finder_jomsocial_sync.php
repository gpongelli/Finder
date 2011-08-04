<?php
/**
 * @version		$Id: finder_jomsocial_sync.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgSystemFinder_JomSocial_Sync
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('JPATH_BASE') or die;

/**
 * System plugin class for Finder to synchronize content with JomSocial.
 *
 * @package		JXtended.Finder
 * @subpackage	plgSystemFinder_JomSocial_Sync
 */
class plgSystemFinder_JomSocial_Sync extends JPlugin
{
	/**
	 * Method to catch the onAfterRoute system event. We catch this event so we
	 * can monitor requests and catch certain actions that do not have triggers
	 * such as trashing and deleting content; archiving content; and things of
	 * that nature.
	 */
	public function onAfterRoute()
	{
		// Get the application.
		$app = JFactory::getApplication();

		// Get the user object.
		$user = JFactory::getUser();

		// Check if the user is a guest.
		if ($user->get('guest')) {
			return true;
		}

		// Get the option and task.
		$option	= strtolower(JRequest::getCmd('option'));
		$view	= strtolower(JRequest::getCmd('view'));
		$layout	= strtolower(JRequest::getCmd('layout'));
		$task	= strtolower(JRequest::getCmd('task'));

		// Check if we are in com_community.
		if ($option !== 'com_community') {
			return true;
		}

		// Check if we are in a supported mode.
		if (
			// Site profile view.
			!(!$app->isAdmin() && $view === 'profile')
		&&
			// Administrator users view.
			!($app->isAdmin() && $view === 'users')
		) {
			return true;
		}

		// Handle the wonky task variable in the front-end.
		if (!$app->isAdmin() && $view === 'profile' && ($task === 'edit' || $task === 'editDetails') && JRequest::getCmd('action') === 'save') {
			$task = 'save';
		}

		// Check for tasks that we care about.
		switch ($task)
		{
			// Handle save tasks.
			case 'save':
			{
				// Get the item id.
				$id = $app->isAdmin() ? JRequest::getInt('userid') : $user->get('id');

				// Fire the onBeforeSaveJomSocialProfile event.
				$this->_fireEvent('onBeforeSaveJomSocialProfile', array($id));

				break;
			}

			// Handle published state tasks.
			case 'publish':
			case 'unpublish':
			{
				// Get the ids.
				$cid = JRequest::getVar('cid', array(), 'method', 'array');
				JArrayHelper::toInteger($cid);

				// Get the new value.
				switch ($task) {
					default:
					case 'publish':
						$value = 1;
						break;
					case 'unpublish':
						$value = 0;
						break;
				}

				// Fire the onChangeJomSocialProfile event.
				$this->_fireEvent('onChangeJomSocialProfile', array($cid, 'published', $value));
				break;
			}

			// Handle remove task.
			case 'remove':
			{
				// Get the ids.
				$cid = JRequest::getVar('cid', array(), 'method', 'array');
				JArrayHelper::toInteger($cid);

				// Fire the onDeleteJomSocialProfile event.
				$this->_fireEvent('onDeleteJomSocialProfile', array($cid));
				break;
			}
		}

		return true;
	}

	/**
	 * Method to fire the event triggers that we are monitoring and handle any
	 * errors that might have been encountered during execution of the plugins.
	 *
	 * @param	string		The event to fire.
	 * @param	array		The parameters for that event.
	 * @return	boolean		True on success, false on failure.
	 */
	private function _fireEvent($event, $options = array())
	{
		// Get the event dispatcher.
		$dispatcher	= JDispatcher::getInstance();

		// Load the finder plugin group.
		JPluginHelper::importPlugin('finder');

		try {
			// Trigger the event.
			$results = $dispatcher->trigger($event, $options);

			// Check the returned results. This is for plugins that don't throw
			// exceptions when they encounter serious errors.
			if (in_array(false, $results)) {
				throw new Exception($dispatcher->getError(), 500);
			}
		}
		catch (Exception $e) {
			// Handle a caught exception.
			JError::raiseError(500, $e->getMessage());
			return false;
		}

		return true;
	}
}