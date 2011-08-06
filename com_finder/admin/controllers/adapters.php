<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Adapters controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderControllerAdapters extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Adapters', $prefix = 'FinderModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
