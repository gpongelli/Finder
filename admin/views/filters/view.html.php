<?php
/**
 * @version		$Id: view.html.php 981 2010-06-15 18:38:02Z robs $
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
		$user		= &JFactory::getUser();
		$filters	= &$this->get('Filters');
		$pagination	= &$this->get('Pagination');
		$total		= $this->get('Total');
		$state		= $this->get('State');
		$params		= $state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Configure the toolbar.
		$this->setToolbar();

		// Push out the view data.
		$this->assignRef('filters', $filters);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('user', $user);
		$this->assign('total', $total);
		$this->assign('state', $state);
		$this->assign('params', $params);

		parent::display($tpl);
	}

	/**
	 * Method to configure the toolbar for this view.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function setToolbar()
	{
		JToolBarHelper::title(JText::_('FINDER_FILTERS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		$toolbar->appendButton('Standard', 'publish', 'Publish', 'filters.publish', true, false);
		$toolbar->appendButton('Standard', 'unpublish', 'Unpublish', 'filters.unpublish', true, false);
		$toolbar->appendButton('Separator', 'divider');
		$toolbar->appendButton('Standard', 'new', 'New', 'filter.createnew', false, false);
		$toolbar->appendButton('Standard', 'edit', 'Edit', 'filter.edit', true, false);
		$toolbar->appendButton('Confirm', 'FINDER_FILTERS_DELETE_CONFIRMATION', 'delete', 'Delete', 'filters.delete', true, false);
		$toolbar->appendButton('Separator', 'divider');
		$toolbar->appendButton('Popup', 'config', 'FINDER_OPTIONS', 'index.php?option=com_finder&view=config&tmpl=component', 570, 500);
		$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}