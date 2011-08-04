<?php
/**
 * @version		$Id: indexer.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Indexer model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderModelIndexer extends JModel
{
	/**
	 * @var		boolean		Flag to indicate model state initialization.
	 */
	protected $__state_set;

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	public function getState($property = null)
	{
		// If the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$application	= &JFactory::getApplication();
			$context		= 'com_finder.indexer.';

			// Load the parameters.
			$this->setState('params', JComponentHelper::getParams('com_finder'));

			$this->__state_set = true;
		}

		return parent::getState($property);
	}
}