<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Index controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerIndex extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Index', $prefix = 'FinderModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
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
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Remove the script time limit.
		@set_time_limit(0);

		// Initialize variables.
		$model = &$this->getModel('Index', 'FinderModel');

		// Attempt to purge the index.
		$return = $model->purge();

		if (!$return) {
			$message = JText::_('COM_FINDER_INDEX_PURGE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=index', $message);
			return false;
		} else {
			$message = JText::_('COM_FINDER_INDEX_PURGE_SUCCESS');
			$this->setRedirect('index.php?option=com_finder&view=index', $message);
			return true;
		}
	}
}
