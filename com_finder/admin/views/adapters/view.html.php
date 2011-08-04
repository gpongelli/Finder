<?php
/**
 * @version		$Id: view.html.php 922 2010-03-11 20:17:33Z robs $
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
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$total		= $this->get('Total');
		$state		= $this->get('State');
		$params		= $state->get('params');
		$user		= JFactory::getUser();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Configure the toolbar.
		$this->setToolbar();

		// Push out the view data.
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('total',		$total);
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);
		$this->assignRef('user',		$user);

		parent::display($tpl);
	}

	/**
	 * Method to configure the toolbar for this view.
	 *
	 * @return	void
	 */
	public function setToolbar()
	{
		JToolBarHelper::title(JText::_('FINDER_ADAPTERS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		$toolbar->appendButton('Standard', 'publish', 'Publish', 'adapters.publish', true, false);
		$toolbar->appendButton('Standard', 'unpublish', 'Unpublish', 'adapters.unpublish', true, false);
		$toolbar->appendButton('Separator', 'divider');
		$toolbar->appendButton('Popup', 'config', 'FINDER_OPTIONS', 'index.php?option=com_finder&view=config&tmpl=component', 570, 500);
		$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}