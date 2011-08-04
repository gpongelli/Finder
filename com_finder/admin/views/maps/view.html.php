<?php
/**
 * @version		$Id: view.html.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC.  All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Groups view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderViewMaps extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		// Initialize variables.
		$user		= JFactory::getUser();

		// Load the view data.
		$this->data			= $this->get('Items');
		$this->total		= $this->get('Total');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Prepare the view.
		JHTML::stylesheet('administrator/components/com_finder/media/css/finder.css', false, false, false);
		$this->addToolbar();

		JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
		parent::display($tpl);
	}

	/**
	 * Method to configure the toolbar for this view.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function addToolbar()
	{
		$canDo	= FinderHelper::getActions();

		JToolBarHelper::title(JText::_('COM_FINDER_MAPS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publish('map.publish');
			JToolBarHelper::unpublish('map.unpublish');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'map.delete', 'JTOOLBAR_DELETE');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_finder');
		}
		//$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}