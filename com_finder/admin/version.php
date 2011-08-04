<?php
/**
 * @version		$Id: version.php 1094 2010-11-17 22:38:21Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * Finder Version Object
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @since		1.0
 */
final class FinderVersion
{
	/**
	 * Extension name string.
	 *
	 * @var		string
	 * @since	1.0
	 */
	const EXTENSION = 'com_finder';

	/**
	 * Major.Minor version string.
	 *
	 * @var		string
	 * @since	1.0
	 */
	const VERSION = '2.0';

	/**
	 * Maintenance version string.
	 *
	 * @var		string
	 * @since	1.0
	 */
	const SUBVERSION = '1';

	/**
	 * Version status string.
	 *
	 * @var		string
	 * @since	1.0
	 */
	const STATUS = '';

	/**
	 * Version release time stamp.
	 *
	 * @var		string
	 * @since	1.0
	 */
	const DATE = '2010-11-17 00:00:00';

	/**
	 * The minimum version of the JXtended Libraries required.
	 *
	 * @var		string
	 * @since	2.0
	 */
	const LIBRARIES_MINIMUM = '2.0.0';

	/**
	 * Source control revision string.
	 *
	 * @var		string
	 * @since	1.0
	 */
	const REVISION = '$Revision: 1094 $';

	/**
	 * Container for version information.
	 *
	 * @var		array
	 * @since	1.0
	 */
	private static $versions = array();

	/**
	 * Container for upgrade information.
	 *
	 * @var		array
	 * @since	1.0
	 */
	private static $upgrades = array();

	/**
	 * Method to check for dependencies.
	 *
	 * @return	boolean	True if dependencies are met.
	 * @since	2.0
	 */
	public static function checkDependencies()
	{
		if (self::LIBRARIES_MINIMUM)
		{
			if (!defined('JXVERSION') || !version_compare(JXVERSION, self::LIBRARIES_MINIMUM, '>='))
			{
				JError::raiseWarning(500, JText::sprintf('JX_Libraries_Outdated', self::LIBRARIES_MINIMUM));
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get the build number from the source control revision string.
	 *
	 * @return	integer	The version build number.
	 * @since	1.0
	 */
	public static function getBuild()
	{
		return intval(substr(self::REVISION, 11));
	}

	/**
	 * Method to get version history information.
	 *
	 * @return	array	Array of installed versions.
	 * @since	1.0
	 */
	public static function getVersions()
	{
		// Only load the versions once.
		if (empty(self::$versions))
		{
			// Initialize variables.
			self::$versions = array();

			// Load the version information.
			$db	= JFactory::getDBO();
			$db->setQuery(
				'SELECT *' .
				' FROM #__jxtended' .
				' WHERE extension = '.$db->quote(self::EXTENSION) .
				' ORDER BY id DESC'
			);
			self::$versions = (array) $db->loadObjectList();

			// Check for a database error.
			if ($db->getErrorNum()) {
				JError::raiseWarning(500, $db->getErrorMsg());
			}
		}

		return self::$versions;
	}

	/**
	 * Method to get version upgrade information.
	 *
	 * @return	mixed	False on failure, array otherwise.
	 * @since	1.0
	 */
	public static function getUpgrades()
	{
		// Only load the upgrades once.
		if (empty(self::$upgrades))
		{
			// Initialize variables.
			self::$upgrades = array();

			// Get the version log data.
			$versions = self::getVersions();

			// If we have a previously installed version, get the most recent.
			if ($last = array_shift($versions))
			{
				// Get the current and installed version strings.
				$currentVersion = self::VERSION.'.'.self::SUBVERSION.' '.self::STATUS;
				$installedVersion = $last->version;

				// Is the current version newer than the last version recorded?
				if (version_compare(strtolower($currentVersion), strtolower($installedVersion)) == 1)
				{
					// Import library dependencies.
					jimport('joomla.filesystem.folder');

					// Yes, so look for upgrade SQL files.
					$files = JFolder::files(JPATH_COMPONENT.DS.'install', '^upgradesql');

					// Grab only the upgrade SQL files that are newer than the current version.
					foreach ($files as $file)
					{
						$parts = explode('.', $file);
						$fileVersion = str_replace('_', '.', $parts[1]);
						$fileVersion = preg_replace('#(\d)(\.)([a-z])#i', '$1 $3', $fileVersion);

						if (version_compare($fileVersion, $installedVersion) > 0) {
							self::$upgrades[$fileVersion] = $file;
						}
					}
				}
			}
		}

		return self::$upgrades;
	}

	/**
	 * Method to raise error warning messages if upgrades exist.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function showUpgrades()
	{
		if (count(self::getUpgrades()))
		{
			$url = JRoute::_('index.php?option='.self::EXTENSION.'&task=setup.upgrade&'.JUtility::getToken().'=1');
			JError::raiseWarning(500, JText::sprintf('JX_Database_Upgrade_Required', $url));
		}
	}
}
