<?php
/**
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
	 * Method to display a view.
	 *
	 * @param	boolean	$cachable	If true, the view output will be cached
	 * @param	array	$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController			This object is to support chaining.
	 * @since	1.6
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Initialise variables.
		$cachable	= true;
		$user		= JFactory::getUser();

		// Set the default view name and format from the Request.
		$viewName	= JRequest::getWord('view', 'search');
		JRequest::setVar('view', $viewName);

		if ($user->get('id') ||($_SERVER['REQUEST_METHOD'] == 'POST' && $vName = 'feed')) {
			$cachable = false;
		}

		$safeurlparams = array(
			'id'				=> 'INT',
			'limit'				=> 'INT',
			'limitstart'		=> 'INT',
			'filter_order'		=> 'CMD',
			'filter_order_Dir'	=> 'CMD',
			'lang'				=> 'CMD'
		);

		return parent::display($cachable, $safeurlparams);
	}
}
