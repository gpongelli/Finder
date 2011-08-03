<?php
/**
 * @version		$Id: filters.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

/**
 * Filters controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerFilters extends FinderController
{
	/**
	 * Method to delete item(s) from the database.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	void
	 */
	function delete()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Filters');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		JArrayHelper::toInteger($cid);

		// Attempt to delete the filters.
		$return = $model->delete($cid);

		// Delete the filters
		if ($return === false)
		{
			$message = JText::sprintf('FINDER_FILTER_DELETE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filters', $message, 'error');
			return false;
		}
		else
		{
			$message = JText::_('FINDER_FILTER_DELETE_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=filters', $message);
			return true;
		}
	}

	/**
	 * Method to publish unpublished item(s).
	 *
	 * @since	1.0
	 * @access	public
	 * @return	void
	 */
	function publish()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$model	= &$this->getModel('Filters', 'FinderModel');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		JArrayHelper::toInteger($cid);

		// Check for items.
		if (count( $cid ) < 1) {
			$message = JText::_('FINDER_PUBLISH_FAILED_SELECT_AN_ITEM');
			$this->setRedirect('index.php?option=com_finder&view=filters', $message, 'warning');
			return false;
		}

		// Attempt to publish the items.
		$return = $model->setStates($cid, 1);

		if ($return === false)
		{
			$message = JText::sprintf('FINDER_FILTER_PUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filters', $message, 'error');
			return false;
		}
		else
		{
			$message = JText::_('FINDER_FILTER_PUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=filters', $message);
			return true;
		}
	}

	/**
	 * Method to unpublish published item(s).
	 *
	 * @since	1.0
	 * @access	public
	 * @return	void
	 */
	function unpublish()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$model	= &$this->getModel('Filters', 'FinderModel');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		JArrayHelper::toInteger($cid);

		// Check for items.
		if (count( $cid ) < 1) {
			$message = JText::_('FINDER_UNPUBLISH_FAILED_SELECT_AN_ITEM');
			$this->setRedirect('index.php?option=com_finder&view=filters', $message, 'warning');
			return false;
		}

		// Attempt to unublish the items.
		$return = $model->setStates($cid, 0);

		if ($return === false)
		{
			$message = JText::sprintf('FINDER_FILTER_UNPUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filters', $message, 'error');
			return false;
		}
		else
		{
			$message = JText::_('FINDER_FILTER_UNPUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=filters', $message);
			return true;
		}
	}
}