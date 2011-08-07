<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Adapters view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderViewAdapters extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @param	string		A template file to load.
	 */
	public function display($tpl = null)
	{
		// Load the view data.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->total		= $this->get('Total');
		$this->state		= $this->get('State');
		$user				= JFactory::getUser();

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
	 * @return	void
	 */
	public function addToolbar()
	{
		$canDo	= FinderHelper::getActions();

		JToolBarHelper::title(JText::_('COM_FINDER_ADAPTERS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publish('adapters.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('adapters.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			$toolbar->appendButton('Popup', 'options', 'JTOOLBAR_OPTIONS', 'index.php?option=com_finder&view=config&tmpl=component', 875, 550);
		}

		JToolBarHelper::divider();
		$toolbar->appendButton('Popup', 'help', 'COM_FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}
