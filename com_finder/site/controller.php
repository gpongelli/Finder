<?php
/**
 * @version		$Id$
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Finder Component Controller.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderController extends JController
{
	/**
	 * Method to display a the requested view.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display()
	{
		$params		= JFactory::getApplication()->getParams();
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= JRequest::getWord('view', 'search');
		$viewLayout	= JRequest::getWord('layout', 'default');
		$viewParams	= array('base_path' => $this->_basePath);

		// Instantiate the view and model.
		$view	= &$this->getView($viewName, $viewType, '', $viewParams);
		$model	= &$this->getModel($viewName);

		// Configure the view.
		$view->setModel($model, true);
		$view->setLayout($viewLayout);
		$view->assignRef('document', $document);

		// Display the view.
		$view->display();
	}
}