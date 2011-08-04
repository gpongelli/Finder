<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Map controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerMap extends JControllerForm
{
	/**
	 * Method to delete taxonomy map(s).
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$model	= &$this->getModel('Map', 'FinderModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		jimport('joomla.utilities.array');
		JArrayHelper::toInteger($cid);

		// Attempt to delete the items.
		$return = $model->delete($cid);

		if (!$return) {
			$message = JText::sprintf('FINDER_MAP_DELETE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=maps', $message, 'error');
			return false;
		} else {
			$message = JText::_('FINDER_MAP_DELETE_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=maps', $message);
			return true;
		}
	}
	/**
	 * Method to publish taxonomy map(s).
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function publish()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$model	= &$this->getModel('Map', 'FinderModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		jimport('joomla.utilities.array');
		JArrayHelper::toInteger($cid);

		// Attempt to publish the items.
		$return = $model->publish($cid);

		if (!$return) {
			$message = JText::sprintf('COM_FINDER_MAP_PUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=maps', $message, 'error');
			return false;
		} else {
			$message = JText::_('COM_FINDER_MAP_PUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=maps', $message);
			return true;
		}
	}

	/**
	 * Method to unpublish taxonomy map(s).
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function unpublish()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$model	= &$this->getModel('Map', 'FinderModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		jimport('joomla.utilities.array');
		JArrayHelper::toInteger($cid);

		// Attempt to unpublish the items.
		$return = $model->unpublish($cid);

		if (!$return) {
			$message = JText::sprintf('COM_FINDER_MAP_UNPUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=maps', $message, 'error');
			return false;
		} else {
			$message = JText::_('COM_FINDER_MAP_UNPUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=maps', $message);
			return true;
		}
	}

	/**
	 * Method to purge all taxonomy maps from the database.
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function purge()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$model	= &$this->getModel('Map', 'FinderModel');

		// Attempt to purge the index.
		$return = $model->purge();

		if (!$return) {
			$message = JText::_('FINDER_MAP_PURGE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=maps', $message);
			return false;
		} else {
			$message = JText::_('FINDER_MAP_PURGE_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=maps', $message);
			return true;
		}
	}
}
