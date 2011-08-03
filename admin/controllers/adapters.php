<?php
/**
 * @version		$Id: adapters.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

/**
 * Adapters controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderControllerAdapters extends FinderController
{
	/**
	 * Method to publish unpublished item(s).
	 *
	 * @return	void
	 */
	function publish()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$model	= &$this->getModel('Adapters', 'FinderModel');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		JArrayHelper::toInteger($cid);

		// Check for items.
		if (count( $cid ) < 1) {
			$message = JText::_('FINDER_PUBLISH_FAILED_SELECT_AN_ITEM');
			$this->setRedirect('index.php?option=com_finder&view=adapters', $message, 'warning');
			return false;
		}

		// Attempt to publish the items.
		$return = $model->setStates($cid, 1);

		if ($return === false)
		{
			$message = JText::sprintf('FINDER_ADAPTER_PUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=adapters', $message, 'error');
			return false;
		}
		else
		{
			$message = JText::_('FINDER_ADAPTER_PUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=adapters', $message);
			return true;
		}
	}

	/**
	 * Method to unpublish published item(s).
	 *
	 * @return	void
	 */
	function unpublish()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$model	= &$this->getModel('Adapters', 'FinderModel');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		JArrayHelper::toInteger($cid);

		// Check for items.
		if (count( $cid ) < 1) {
			$message = JText::_('FINDER_UNPUBLISH_FAILED_SELECT_AN_ITEM');
			$this->setRedirect('index.php?option=com_finder&view=adapters', $message, 'warning');
			return false;
		}

		// Attempt to unublish the items.
		$return = $model->setStates($cid, 0);

		if ($return === false)
		{
			$message = JText::sprintf('FINDER_ADAPTER_UNPUBLISH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=adapters', $message, 'error');
			return false;
		}
		else
		{
			$message = JText::_('FINDER_ADAPTER_UNPUBLISH_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=adapters', $message);
			return true;
		}
	}
}