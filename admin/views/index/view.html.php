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
		// Load the view data.
		$data		= &$this->get('Data');
		$total		= &$this->get('Total');
		$pagination	= &$this->get('Pagination');
		$state		= &$this->get('State');
		$params		= &$state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Configure the toolbar.
		$this->setToolbar();

		// Push out the view data.
		$this->assignRef('data',		$data);
		$this->assignRef('total',		$total);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);

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
		JToolBarHelper::title(JText::_('FINDER_INDEX_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		$toolbar->appendButton('Popup', 'archive', 'FINDER_INDEX', 'index.php?option=com_finder&view=indexer&tmpl=component', 500, 210);
		$toolbar->appendButton('Confirm', 'FINDER_INDEX_CONFIRM_PURGE_PROMPT', 'trash', 'Purge', 'index.purge', false);
		$toolbar->appendButton('Separator', 'divider');

		JToolBarHelper::publishList('index.publish', 'FINDER_INDEX_TOOLBAR_PUBLISH');
		JToolBarHelper::unpublishList('index.unpublish', 'FINDER_INDEX_TOOLBAR_UNPUBLISH');
		JToolBarHelper::deleteList('FINDER_INDEX_CONFIRM_DELETE_PROMPT', 'index.delete', 'Delete');

		$toolbar->appendButton('Separator', 'divider');
		$toolbar->appendButton('Popup', 'config', 'FINDER_OPTIONS', 'index.php?option=com_finder&view=config&tmpl=component', 570, 500);
		$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}