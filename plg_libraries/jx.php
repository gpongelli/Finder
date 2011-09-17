<?php
/**
 * @version		$Id: jx.php 499 2010-08-16 18:39:16Z robs $
 * @package		JXtended.Libraries
 * @subpackage	plgSystemJX
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Bootstrap the JXtended Libraries.
require_once (dirname(__FILE__)).'/jx/import.php';

jimport('joomla.plugin.plugin');

/**
 * @package		JXtended.Libraries
 * @subpackage	plgSystemJX
 */
class plgSystemJX extends JPlugin
{
	/**
	 * Method to catch the onAfterRoute system event. We catch this event so we
	 * can monitor requests and catch certain actions that do not have triggers
	 * such as trashing and deleting content; archiving content; and things of
	 * that nature.
	 */
	public function onAfterRoute()
	{
		// Get the user object.
		$user = JFactory::getUser();

		// Check if the user is a guest.
		if ($user->get('guest')) {
			return true;
		}

		// Check if this is a site page.
		if (!JFactory::getApplication()->isAdmin()) {
			return true;
		}

		// Get the option and task.
		$option	= strtolower(JRequest::getCmd('option'));
		$task	= strtolower(JRequest::getCmd('task'));

		// Check for options that we care about.
		switch ($option)
		{
			// Handle com_categories.
			case 'com_categories':
			{
				// Check for tasks that we care about.
				switch ($task)
				{
					// Handle save tasks.
					case 'apply':
					case 'save':
					{
						// Get the item id.
						$id = JRequest::getInt('id');

						// Ignore new items.
						if (!$id) {
							break;
						}

						// Prepare values.
						$cid	= array($id);
						$state	= JRequest::getInt('published');
						$access	= JRequest::getInt('access');

						// Fire the onChangeJoomlaCategory event for the published state.
						$this->_fireEvent('onChangeJoomlaCategory', array($cid, 'published', $state));

						// Fire the onChangeJoomlaCategory event for the access level.
						$this->_fireEvent('onChangeJoomlaCategory', array($cid, 'access', $access));
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

						// Fire the onChangeJoomlaCategory event.
						$this->_fireEvent('onChangeJoomlaCategory', array($cid, 'published', $value));
						break;
					}

					// Handle access state tasks.
					case 'accesspublic':
					case 'accessregistered':
					case 'accessspecial':
					{
						// Get the ids.
						$cid = JRequest::getVar('cid', array(), 'method', 'array');
						JArrayHelper::toInteger($cid);

						// Get the new value.
						switch ($task) {
							default:
							case 'accesspublic':
								$value = 0;
								break;
							case 'accessregistered':
								$value = 1;
								break;
							case 'accessspecial':
								$value = 2;
								break;
						}

						// Fire the onChangeJoomlaCategory event.
						$this->_fireEvent('onChangeJoomlaCategory', array($cid, 'access', $value));
						break;
					}

				}

				// Break from com_categories.
				break;
			}

			// Handle com_contact.
			case 'com_contact':
			{
				// Check for tasks that we care about.
				switch ($task)
				{
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

						// Fire the onChangeJoomlaContact event.
						$this->_fireEvent('onChangeJoomlaContact', array($cid, 'published', $value));
						break;
					}

					// Handle access state tasks.
					case 'accesspublic':
					case 'accessregistered':
					case 'accessspecial':
					{
						// Get the ids.
						$cid = JRequest::getVar('cid', array(), 'method', 'array');
						JArrayHelper::toInteger($cid);

						// Get the new value.
						switch ($task) {
							default:
							case 'accesspublic':
								$value = 0;
								break;
							case 'accessregistered':
								$value = 1;
								break;
							case 'accessspecial':
								$value = 2;
								break;
						}

						// Fire the onChangeJoomlaContact event.
						$this->_fireEvent('onChangeJoomlaContact', array($cid, 'access', $value));
						break;
					}
				}

				// Break from com_contact.
				break;
			}

			// Handle com_content.
			case 'com_content':
			{
				// Check for tasks that we care about.
				switch ($task)
				{
					// Handle published state tasks.
					case 'publish':
					case 'unpublish':
					case 'archive':
					case 'unarchive':
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
							case 'unarchive':
							case 'unpublish':
								$value = 0;
								break;
							case 'archive':
								$value = -1;
								break;
						}

						// Fire the onChangeJoomlaArticle event.
						$this->_fireEvent('onChangeJoomlaArticle', array($cid, 'state', $value));
						break;
					}

					// Handle access state tasks.
					case 'accesspublic':
					case 'accessregistered':
					case 'accessspecial':
					{
						// Get the ids.
						$cid = JRequest::getVar('cid', array(), 'method', 'array');
						JArrayHelper::toInteger($cid);

						// Get the new value.
						switch ($task) {
							default:
							case 'accesspublic':
								$value = 0;
								break;
							case 'accessregistered':
								$value = 1;
								break;
							case 'accessspecial':
								$value = 2;
								break;
						}

						// Fire the onChangeJoomlaArticle event.
						$this->_fireEvent('onChangeJoomlaArticle', array($cid, 'access', $value));
						break;
					}
				}

				// Break from com_content.
				break;
			}

			// Handle com_weblinks.
			case 'com_weblinks':
			{
				// Check for tasks that we care about.
				switch ($task)
				{
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

						// Fire the onChangeJoomlaWeblink event.
						$this->_fireEvent('onChangeJoomlaWeblink', array($cid, 'published', $value));
						break;
					}
				}

				// Break from com_weblinks.
				break;
			}
		}
	}

	/**
	 * Method to fire the event triggers that we are monitoring and handle any
	 * errors that might have been encountered during execution of the plugins.
	 *
	 * @param	string		The event to fire.
	 * @param	array		The parameters for that event.
	 * @return	boolean		True on success, false on failure.
	 */
	protected function _fireEvent($event, $options = array())
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