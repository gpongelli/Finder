<?php
/**
 * @package		JXtended.Finder
 * @subpackage	plgFinderJoomla_Articles
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 */
class plgFinderJoomla_ArticlesInstallerScript {

	/**
	 * Function to perform changes when plugin is initially installed
	 *
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.6
	 */
	function install($parent) {
		$this->activateButton();
	}

	/**
	 * Function to activate the button at installation
	 *
	 * @return	void
	 * @since	1.7
	 */
	function activateButton() {
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'));
		$query->set($db->quoteName('enabled').' = 1');
		$query->where($db->quoteName('name').' = '.$db->quote('plg_finder_joomla_articles'));
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseNotice(1, JText::_('PLG_FINDER_JOOMLA_ARTICLES_ERROR_ACTIVATING_PLUGIN'));
		}
	}
}
