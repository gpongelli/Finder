<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Adapters view class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class FinderViewAdapters extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load.
	 *
	 * @return  void
	 *
	 * @since   2.5
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
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

		JHtml::stylesheet('administrator/components/com_finder/media/css/finder.css', false, false, false);

		// Configure the toolbar.
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Method to configure the toolbar for this view.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function addToolbar()
	{
		$canDo	= FinderHelper::getActions();

		JToolBarHelper::title(JText::_('COM_FINDER_ADAPTERS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::publishList('adapters.publish');
			JToolBarHelper::unpublishList('adapters.unpublish');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_finder');
		}

		JToolBarHelper::divider();
		$toolbar->appendButton('Popup', 'help', 'COM_FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}
