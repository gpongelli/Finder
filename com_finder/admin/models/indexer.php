<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Indexer model class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class FinderModelIndexer extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var    boolean
	 * @since  2.5
	 */
	protected $__state_set;

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param   string  $property  Optional parameter name.
	 *
	 * @return  object	The property where specified, the state object where omitted.
	 *
	 * @since   2.5
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
