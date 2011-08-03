<?php
/**
 * @version		$Id: index.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

/**
 * Index controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerIndex extends FinderController
{
	/**
	 * Method to delete indexed item(s).
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$model	= &$this->getModel('Index', 'FinderModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		jimport('joomla.utilities.array');
		JArrayHelper::toInteger($cid);

		// Attempt to delete the items.
		$return = $model->delete($cid);

		if (!$return) {
			$message = JText::sprintf('FINDER_INDEX_DELETE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=index', $message, 'error');
			return false;
		} else {
			$message = JText::_('FINDER_INDEX_DELETE_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=index', $message);
			return true;
		}
	}

	/**
	 * Method to publish indexed item(s).
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function publish()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$model	= &$this->getModel('Index', 'FinderModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		jimport('joomla.utilities.array');
		JArrayHelper::toInteger($cid);

		// Attempt to publish the items.
		$return = $model->publish($cid);

		if (!$return) {
			$message = JText::sprintf('FINDER_INDEX_PUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=index', $message, 'error');
			return false;
		} else {
			$message = JText::_('FINDER_INDEX_PUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=index', $message);
			return true;
		}
	}

	/**
	 * Method to unpublish indexed item(s).
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function unpublish()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$model	= &$this->getModel('Index', 'FinderModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		jimport('joomla.utilities.array');
		JArrayHelper::toInteger($cid);

		// Attempt to unpublish the items.
		$return = $model->unpublish($cid);

		if (!$return) {
			$message = JText::sprintf('FINDER_INDEX_UNPUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=index', $message, 'error');
			return false;
		} else {
			$message = JText::_('FINDER_INDEX_UNPUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=index', $message);
			return true;
		}
	}

	/**
	 * Method to purge all indexed links from the database.
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function purge()
	{
		JRequest::checkToken('request') or jexit(JText::_('JX_INVALID_TOKEN'));

		// Remove the script time limit.
		@set_time_limit(0);

		// Initialize variables.
		$model = &$this->getModel('Index', 'FinderModel');

		// Attempt to purge the index.
		$return = $model->purge();

		if (!$return) {
			$message = JText::_('FINDER_INDEX_PURGE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=index', $message);
			return false;
		} else {
			$message = JText::_('FINDER_INDEX_PURGE_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=index', $message);
			return true;
		}
	}
}
