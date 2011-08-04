<?php
/**
 * @version		$Id: setup.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2008 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * Component setup helper class for JXtended Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @since		2.0
 */
class JXtendedSetupHelper
{
	/**
	 * Extension name string.
	 *
	 * @var		string
	 * @since	2.0
	 */
	const EXTENSION = 'com_finder';

	/**
	 * Legacy version table name.
	 *
	 * @var		string
	 * @since	2.0
	 */
	const LEGACY_TABLE = '#__jxfinder';

	/**
	 * Method to log a version for a component.
	 *
	 * @param	string	A version string.
	 * @param	string	The component for which to log the version.
	 * @return	mixed	Boolean true on success, JException object on failure.
	 * @since	2.0
	 */
	public static function registerVersion($version, $log)
	{
		// Get a database connection object.
		$db = JFactory::getDBO();

		// Check for an existing version.
		$db->setQuery(
			'SELECT COUNT(*) FROM #__jxtended WHERE extension = '.$db->quote(self::EXTENSION)
		);

		// Skip if already registered.
		if ($db->loadResult()) {
			return true;
		}

		// Register the row.
		$db->setQuery(
			'INSERT INTO #__jxtended (extension, version, log)' .
			' VALUES ('.$db->quote(self::EXTENSION).', '.$db->quote(self::_sanitizeVersionString($version)).', '.$db->quote($log).')'
		);

		// Check for errors.
		if (!$db->query()) {
			return new JException(JText::_('JXtended_Register_Version').': '.$db->getErrorMsg());
		}

		return true;
	}

	/**
	 * Method to register a plugin if not already registered.
	 *
	 * @param	string	The name of the plugin.
	 * @param	string	The main file (exluding .php) of the plugin.
	 * @param	string	The plugin group.
	 * @param	integer	The published state.
	 * @param	integer	The ordering of the plugin.
	 * @return	mixed	Boolean true on success, JException object on failure.
	 * @since	2.0
	 */
	public static function registerPlugin($name, $element, $group, $published = 1, $ordering = 0)
	{
		// Get a database connection object.
		$db = JFactory::getDBO();

		// Check if the plugin is registered.
		$db->setQuery(
			'SELECT COUNT(id)' .
			' FROM #__plugins' .
			' WHERE element = '.$db->quote($element) .
			' AND folder = '.$db->quote($group)
		);
		$installed = $db->loadResult();

		// Check for error.
		if ($db->getErrorNum()) {
			return new JException(JText::_('JXtended_Register_Plugin').': '.$db->getErrorMsg());
		}

		// Install the plugin if not installed.
		if (!$installed)
		{
			// Insert the plugin row.
			$db->setQuery(
				'INSERT INTO #__plugins (name, element, folder, published, ordering) VALUES' .
				' ('.$db->quote($name).', '.$db->quote($element).', '.$db->quote($group).','.(int) $published.', '.(int) $ordering.')'
			);

			// Check for error.
			if (!$db->query()) {
				return new JException(JText::_('JXtended_Register_Plugin').': '.$db->getErrorMsg());
			}
		}

		return true;
	}

	/**
	 * Method to fix any broken menu links for the component for older Joomla! installations.
	 *
	 * @return	mixed	Boolean true on success, JException object on failure.
	 * @since	2.0
	 */
	public static function fixMenuLinks()
	{
		// Get a database connection object.
		$db = JFactory::getDBO();

		// Update the menu item mappings.
		$db->setQuery(
			'UPDATE #__menu SET' .
			' componentid =' .
			' (' .
			'  SELECT id' .
			'  FROM #__components' .
			'  WHERE '.$db->nameQuote('option').' = '.$db->quote(self::EXTENSION) .
			' )' .
			' WHERE link LIKE '.$db->quote('index.php?option='.self::EXTENSION.'&%')
		);

		// Check for errors.
		if (!$db->query()) {
			return new JException(JText::_('JX_SETUP_FIX_MENU').': '.$db->getErrorMsg());
		}

		return true;
	}

	/**
	 * Method to migrate and normalize legacy version information to the unified
	 * #__jxtended table.
	 *
	 * @return	mixed	Boolean true on success, JException object on failure.
	 * @since	2.0
	 */
	public static function normalizeVersions()
	{
		// Get a database connection object.
		$db = JFactory::getDBO();

		// Check to see if legacy version information exists.
		$db->setQuery('SHOW TABLES LIKE '.self::LEGACY_TABLE);
		if ($db->loadResult())
		{
			// Get the number of legacy installations.
			$db->setQuery(
				'SELECT COUNT(version)' .
				' FROM '.self::LEGACY_TABLE
			);
			$legacy = (int) $db->loadResult();

			// If we have legacy version information migrate it to the new database tables.
			if ($legacy > 0)
			{
				// Migrate the old version information
				$db->setQuery(
					'INSERT INTO #__jxtended (extension, version, installed_date, log)'
					.' SELECT '.$db->quote(self::EXTENSION).', version, installed_date, log'
					.'  FROM '.$db->nameQuote(self::LEGACY_TABLE).' ORDER BY installed_date'
				);

				// Check for errors.
				if (!$db->query()) {
					return new JException(JText::_('JXtended_Version_Migration').': '.$db->getErrorMsg());
				}

				// Once the version data is migrated we can safely drop the legacy versions table.
				$db->setQuery(
					'DROP TABLE '.$db->nameQuote(self::LEGACY_TABLE)
				);

				// Check for errors.
				if (!$db->query()) {
					return new JException(JText::_('JXtended_Version_Migration').': '.$db->getErrorMsg());
				}

				// Load all of the migrated version data for sanitization.
				$db->setQuery(
					'SELECT id, version' .
					' FROM #__jxtended' .
					' WHERE extension = '.$db->quote(self::EXTENSION)
				);
				$legacyVersions = $db->loadObjectList();

				foreach ($legacyVersions as $v)
				{
					// Get the sanitized version string.
					$tmp = self::_sanitizeVersionString($v->version);

					// If the normalized version string is not the same as the current one update the row.
					if ($v->version != $tmp)
					{
						// Update the row.
						$db->setQuery(
							'UPDATE #__jxtended' .
							' SET version = '.$db->quote($tmp) .
							' WHERE id = '.(int) $v->id
						);

						// Check for errors.
						if (!$db->query()) {
							return new JException(JText::_('JXtended_Version_Migration').': '.$db->getErrorMsg());
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method sanitize and normalize a version string.
	 *
	 * @param	string	A version string.
	 * @return	string	A sanitized version string.
	 * @since	2.0
	 */
	protected static function _sanitizeVersionString($input)
	{
		// Initialize variables.
		$matches = array();
		$output  = '';

		if (($p = stripos($input, 'rc')) !== false)
		{
			// Build the version string.
			$output = trim(substr($input, 0, $p), '. -').' RC';

			// Append the status iteration number to the version string.
			preg_match('/[0-9]+/', (string) substr($input, $p), $matches);
			$output .= empty($matches[0]) ? '1' : $matches[0];
		}
		elseif (($p = stripos($input, 'beta')) !== false)
		{
			// Build the version string.
			$output = trim(substr($input, 0, $p), '. -').' beta';

			// Append the status iteration number to the version string.
			preg_match('/[0-9]+/', (string) substr($input, $p), $matches);
			$output .= empty($matches[0]) ? '1' : $matches[0];
		}
		elseif (($p = stripos($input, 'alpha')) !== false)
		{
			// Build the version string.
			$output = trim(substr($input, 0, $p), '. -').' alpha';

			// Append the status iteration number to the version string.
			preg_match('/[0-9]+/', (string) substr($input, $p), $matches);
			$output .= empty($matches[0]) ? '1' : $matches[0];
		}
		elseif (($p = stripos($input, 'stable')) !== false)
		{
			// Build the version string.
			$output = trim(substr($input, 0, $p), '. -');
		}
		else {
			// Build the version string.
			$output = trim($input, '. -');
		}

		return $output;
	}
}