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
 * Groups view class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class FinderViewMaps extends JView
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
	function display($tpl = null)
	{
		// Initialize variables.
		$user		= JFactory::getUser();

		// Load the view data.
		$this->items		= $this->get('Items');
		$this->total		= $this->get('Total');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Prepare the view.
		JHtml::stylesheet('administrator/components/com_finder/media/css/finder.css', false, false, false);
		$this->addToolbar();

		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
		parent::display($tpl);
	}

	/**
	 * Method to configure the toolbar for this view.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	function addToolbar()
	{
		// For whatever reason, the helper isn't being found
		include_once JPATH_COMPONENT.'/helpers/finder.php';
		$canDo	= FinderHelper::getActions();

		JToolBarHelper::title(JText::_('COM_FINDER_MAPS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::publish('maps.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('maps.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'maps.delete', 'JTOOLBAR_DELETE');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.admin'))
		{
			$toolbar->appendButton('Popup', 'options', 'JTOOLBAR_OPTIONS', 'index.php?option=com_finder&view=config&tmpl=component', 875, 550);
		}
		$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}
