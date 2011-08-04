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
 * Filter view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderViewFilter extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @since	1.1
	 */
	function display($tpl = null)
	{
		// Load the view data.
		$filter	= $this->get('Filter');
		$form	= $this->get('Form');
		$state	= $this->get('State');
		$params	= $state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Configure the toolbar.
		$this->setToolbar();

		// Prepare the form.
		if ($form) {
			$form->bind($filter);
		}

		// Push out the view data.
		$this->assignRef('filter',	$filter);
		$this->assignRef('form',	$form);
		$this->assignRef('state',	$state);
		$this->assignRef('params',	$params);

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
		// Configure the toolbar.
		JToolBarHelper::title(JText::_('FINDER_FILTER_EDIT_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		$toolbar->appendButton('Standard', 'new', 'FINDER_SAVENEW', 'filter.savenew', false, false);
		$toolbar->appendButton('Separator', 'divider');
		$toolbar->appendButton('Standard', 'save', 'SAVE', 'filter.save', false, false);
		$toolbar->appendButton('Standard', 'apply', 'APPLY', 'filter.apply', false, false);
		$toolbar->appendButton('Standard', 'cancel', 'CANCEL', 'filter.cancel', false, false);
		$toolbar->appendButton('Separator', 'divider');
		$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}