<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Filters controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerFilters extends JControllerAdmin
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
}