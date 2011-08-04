<?php
/**
 * @version		$Id: view.html.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC.  All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Groups view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderViewMaps extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();

		// Load the view data.
		$data		= &$this->get('Items');
		$total		= &$this->get('Total');
		$pagination	= &$this->get('Pagination');
		$state		= &$this->get('State');
		$params		= &$state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Prepare the view.
		$this->document->addStyleSheet('components/com_finder/media/css/finder.css');
		$this->setToolbar();

		JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');

		// Push out the view data.
		$this->assignRef('data',		$data);
		$this->assignRef('total',		$total);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);

		// Load the view template.
		$result = $this->loadTemplate($tpl);

		// Check for an error.
		if (JError::isError($result)) {
			return $result;
		}

		echo $result;
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
		JToolBarHelper::title(JText::_('FINDER_MAPS_TOOLBAR_TITLE'), 'finder');
		$toolbar = &JToolBar::getInstance('toolbar');

		$toolbar->appendButton('Standard', 'publish', 'FINDER_INDEX_TOOLBAR_PUBLISH', 'map.publish', true, false);
		$toolbar->appendButton('Standard', 'unpublish', 'FINDER_INDEX_TOOLBAR_UNPUBLISH', 'map.unpublish', true, false);
		$toolbar->appendButton('Confirm', 'FINDER_MAP_CONFIRM_DELETE_PROMPT', 'delete', 'Delete', 'map.delete', true, false);
		$toolbar->appendButton('Separator', 'divider');
		$toolbar->appendButton('Popup', 'config', 'FINDER_OPTIONS', 'index.php?option=com_finder&view=config&tmpl=component', 570, 500);
		$toolbar->appendButton('Popup', 'help', 'FINDER_ABOUT', 'index.php?option=com_finder&view=about&tmpl=component', 550, 500);
	}
}