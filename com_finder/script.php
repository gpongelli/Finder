<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2008 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 */
class Com_FinderInstallerScript {
	/**
	* Function to perform changes when component is initially installed
	*
	* @param	string	$type	The action being performed
	* @param	string	$parent	The function calling this method
	*
	* @return	void
	* @since	1.6
	*/
	function postflight($type, $parent)
	{
		// Import the version class and build the version string.
		require_once JPATH_ADMINISTRATOR.'/components/com_finder/version.php';
		$version = FinderVersion::VERSION.'.'.FinderVersion::SUBVERSION.' '.FinderVersion::STATUS;

		// Import the setup helper class.
		require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/setup.php';

		// Migrate and normalize any legacy version data.
		$success = JXtendedSetupHelper::normalizeVersions();
		if (JError::isError($success)) {
			JError::raiseNotice(1, $success->getMessage());
		}

		// Register the new version.
		$success = JXtendedSetupHelper::registerVersion($version, JText::sprintf('COM_FINDER_INSTALL_VERSION', $version));
		if (JError::isError($success)) {
			JError::raiseNotice(1, $success->getMessage());
		}
	}
}
