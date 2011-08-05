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
 * Filter view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderViewFilter extends JView
{
	protected $form;
	protected $item;
	protected $state;

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
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Prepare the view.
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
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->filter_id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= FinderHelper::getActions();

		// Configure the toolbar.
		JToolBarHelper::title(JText::_('COM_FINDER_FILTER_EDIT_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

			// Set the actions for new and existing records.
		if ($isNew)  {
			// For new records, check the create permission.
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('filter.apply');
				JToolBarHelper::save('filter.save');
				JToolBarHelper::save2new('filter.save2new');
			}

			JToolBarHelper::cancel('filter.cancel');
		} else {
			// Since it's an existing record, check the edit permission.
			if ($canDo->get('core.edit')) {
				JToolBarHelper::apply('filter.apply');
				JToolBarHelper::save('filter.save');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) {
					JToolBarHelper::save2new('filter.save2new');
				}
			}
			// If an existing item, can save as a copy
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('filter.save2copy');
			}

			JToolBarHelper::cancel('filter.cancel', 'JTOOLBAR_CLOSE');
		}
		//$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}
