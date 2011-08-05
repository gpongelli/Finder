<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Filters view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderViewFilters extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		// Load the view data.
		$user				= &JFactory::getUser();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->total		= $this->get('Total');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		JHTML::stylesheet('administrator/components/com_finder/media/css/finder.css', false, false, false);

		// Configure the toolbar.
		$this->addToolbar();

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

		JToolBarHelper::title(JText::_('COM_FINDER_FILTERS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('filter.add');
			JToolBarHelper::editList('filter.edit');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publish('filters.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('filters.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'filters.delete', 'JTOOLBAR_DELETE');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_finder');
		}
		//$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}