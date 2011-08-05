<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Filters controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerFilters extends JControllerForm
{
	/**
	 * Proxy for getModel.
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Filter', $prefix = 'FinderModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
