<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Base controller class for JXtended Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'index';

	/**
	 * Method to display a view.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function display()
	{
		require_once JPATH_COMPONENT.'/helpers/finder.php';

		// Load the submenu.
		FinderHelper::addSubmenu(JRequest::getWord('view', 'index'));

		$view		= JRequest::getWord('view', 'index');
		$layout 	= JRequest::getWord('layout', 'index');
		$id			= JRequest::getInt('id');
		$f_id		= JRequest::getInt('filter_id');

			// Check for edit form.
		if ($view == 'filter' && $layout == 'edit' && !$this->checkEditId('com_finder.edit.filter', $f_id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $f_id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_finder&view=filters', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
