<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2008 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

// Restricted access
defined('_JEXEC') or die;

class FinderHelper
{
	public static $extension = 'com_finder';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('FINDER_SUBMENU_INDEX'),
			'index.php?option=com_finder&view=index',
			$vName == 'index');
		JSubMenuHelper::addEntry(
			JText::_('FINDER_SUBMENU_MAPS'),
			'index.php?option=com_finder&view=maps',
			$vName == 'maps');
		JSubMenuHelper::addEntry(
			JText::_('FINDER_SUBMENU_FILTERS'),
			'index.php?option=com_finder&view=filters',
			$vName == 'filters');
		JSubMenuHelper::addEntry(
			JText::_('FINDER_SUBMENU_ADAPTERS'),
			'index.php?option=com_finder&view=adapters',
			$vName == 'adapters');
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject	$result	A JObject containing the allowed actions
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$assetName	= 'com_finder';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
