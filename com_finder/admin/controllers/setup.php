<?php
/**
 * @version		$Id: setup.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * The JXtended Finder Setup Controller
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @since		1.0
 */
class FinderControllerSetup extends JController
{
	/**
	 * Method to manually install JXtended Finder.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function install()
	{
		// Get the setup model.
		$model = $this->getModel('Setup');

		// Attempt to run the manual install routine.
		$result	= $model->install();

		// Check for installation routine errors.
		if (!$result) {
			$this->setMessage(JText::sprintf('Finder_Manual_Install_Failed', $model->getError()), 'notice');
		}
		else {
			$this->setMessage(JText::_('Finder_Manual_Install_Success'));
		}

		// Set the redirect.
		$this->setRedirect('index.php?option=com_finder');
	}

	/**
	 * Method to process any available database upgrades.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function upgrade()
	{
		// Check for request forgeries.
		JRequest::checkToken('request') or jexit(JText::_('JX_Invalid_Token'));

		// Get the setup model.
		$model = $this->getModel('Setup');

		// Attempt to run the upgrade routine.
		$result	= $model->upgrade();

		// Check for upgrade routine errors.
		if (!$result)
		{
			$this->setMessage(JText::sprintf('JX_Database_Upgrade_Failed', $model->getError()), 'notice');
			$this->setRedirect('index.php?option=com_finder');
		}
		else
		{
			$this->setMessage(JText::_('JX_Database_Upgrade_Success'));
			$this->setRedirect('index.php?option=com_finder');
		}
	}
}
