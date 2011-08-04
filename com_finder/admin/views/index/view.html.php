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
 * Index view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderViewIndex extends JView
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
		// Initialise variables
		$this->data			= $this->get('Data');
		$this->total		= $this->get('Total');
		$this->pagination	= $this->get('Pagination');
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

		JToolBarHelper::title(JText::_('FINDER_INDEX_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		$toolbar->appendButton('Popup', 'archive', 'FINDER_INDEX', 'index.php?option=com_finder&view=indexer&tmpl=component', 500, 210);
		JToolBarHelper::divider();

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publish('index.publish');
			JToolBarHelper::unpublish('index.unpublish');
			JToolBarHelper::divider();
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'index.delete', 'FINDER_INDEX_CONFIRM_DELETE_PROMPT');
			JToolBarHelper::divider();
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('index.purge', 'FINDER_INDEX_CONFIRM_PURGE_PROMPT');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_finder');
		}
		//$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}
