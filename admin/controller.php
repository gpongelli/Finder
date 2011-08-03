<?php
/**
 * @version		$Id: controller.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Base controller class for JXtended Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function display()
	{
		// Alert the user about any upgrades.
		FinderVersion::showUpgrades();

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName		= JRequest::getWord('view', 'index');
		$vFormat	= $document->getType();
		$lName		= JRequest::getWord('layout', 'default');
		$vParams	= array('base_path' => $this->_basePath);

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			switch ($vName)
			{
				default:
					$model = $this->getModel($vName);
					break;
			}

			// Push the model into the view (as default).
			if ($model) {
				$view->setModel($model, true);
			}

			// Push document object into the view.
			$view->assignRef('document', $document);

			// Set the layout for the view.
			$view->setLayout($lName);

			// Set the search query state for the view.
			$view->set('search', (bool)JRequest::getVar('q'));

			$view->display();
		}
		else {
			// Error condition.
		}

		// Setup the sub-menu.
		JSubMenuHelper::addEntry(JText::_('FINDER_SUBMENU_INDEX'), 'index.php?option=com_finder&view=index', $vName == 'index');
		JSubMenuHelper::addEntry(JText::_('FINDER_SUBMENU_MAPS'), 'index.php?option=com_finder&view=maps', $vName == 'maps');
		JSubMenuHelper::addEntry(JText::_('FINDER_SUBMENU_FILTERS'), 'index.php?option=com_finder&view=filters', $vName == 'filters');
		JSubMenuHelper::addEntry(JText::_('FINDER_SUBMENU_ADAPTERS'), 'index.php?option=com_finder&view=adapters', $vName == 'adapters');
	}
}