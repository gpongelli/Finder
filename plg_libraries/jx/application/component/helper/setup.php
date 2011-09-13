<?php
/**
 * @version		$Id: setup.php 458 2009-09-23 05:13:02Z louis $
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.filesystem.file');

/**
 * Component package setup helper.
 *
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @since		2.0
 */
class JComponentHelperSetup
{
	/**
	 * Registers a component.
	 *
	 * @param	string	The name of the component.
	 * @param	string	The option of the component.
	 * @param	string	True if the site component is available.
	 * @param	string	The admin menu icon path.
	 * @return	mixed	True on success, JException object otherwise.
	 * @since	2.0
	 */
	public static function registerComponent($name, $option, $site = false, $icon = '')
	{
		$db = JFactory::getDbo();

		// Get the number of relevant rows in the components table.
		$db->setQuery(
			'SELECT COUNT(id)' .
			' FROM `#__components`' .
			' WHERE `option` = '.$db->quote($option)
		);
		$installed = $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum()) {
			return new JException($db->getErrorMsg());
		}

		// Check to see if the component is installed.
		if ($installed > 0) {
			return new JException(JText::_('JX_Install_Extension_Already_Installed'));
		}

		// Attempt to add the necessary rows to the components table.
		$db->setQuery(
			'INSERT INTO `jos_components` VALUES ' .
			'(0, '.$db->quote($name).', '.$db->quote($site ? 'option='.$option : '').', 0, 0, '.$db->quote('option='.$option).', '.$db->quote($name).', '.$db->quote($option).', 0, '.$db->quote($icon).', 0, \'\', 1)'
		);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			return new JException($db->getErrorMsg());
		}

		// Verify the schema file.
		$file = JPATH_ADMINISTRATOR.'/components/'.$option.'/install/installsql.mysql.utf8.php';
		if (!JFile::exists($file)) {
			return new JException(JText::_('JX_Install_Schema_File_Missing'));
		}

		// Set the SQL from the schema file.
		$db->setQuery(JFile::read($file));

		// Attempt to import the component schema.
		$return = $db->queryBatch(false);

		// Check for a database error.
		if ($db->getErrorNum()) {
			return new JException($db->getErrorMsg());
		}

		return true;
	}

	/**
	 * Method to execute database upgrade scripts for a component.
	 *
	 * @param	array	An array of upgrade files to process.
	 * @param	string	The component for which to log the version.
	 * @return	mixed	True on success, JException object otherwise.
	 * @since	2.0
	 */
	public static function upgradeComponent($upgrades, $option)
	{
		// If there are upgrades to process, attempt to process them.
		if (is_array($upgrades) && count($upgrades))
		{
			// Sort the upgrades, lowest version first.
			uksort($upgrades, 'version_compare');

			// Get the database object.
			$db = JFactory::getDbo();

			// Get the number of relevant rows in the components table.
			$db->setQuery(
				'SELECT COUNT(id)' .
				' FROM `#__components`' .
				' WHERE `option` = '.$db->quote($option)
			);
			$installed = $db->loadResult();

			// Check for a database error.
			if ($db->getErrorNum()) {
				return new JException($db->getErrorMsg());
			}

			// Check to see if the component is installed.
			if ($installed < 1) {
				return new JException(JText::_('JXtended_Setup_Extension_Not_Installed'));
			}

			foreach ($upgrades as $upgradeVersion => $file)
			{
				$file = JPATH_COMPONENT.DS.'install'.DS.$file;
				if (JFile::exists($file))
				{
					// Set the upgrade SQL from the file.
					$db->setQuery(JFile::read($file));

					// Execute the upgrade SQL.
					$return = $db->queryBatch(false);

					// Check for a database error.
					if ($db->getErrorNum()) {
						return new JException($db->getErrorMsg());
					}

					// Upgrade was successful, attempt to log it to the versions table.
					$db->setQuery(
						'INSERT INTO `#__jxtended` (`extension`,`version`,`log`) VALUES' .
						' ('.$db->quote($option).','.$db->Quote($upgradeVersion).', '.$db->Quote(JText::sprintf('JXTENDED_SETUP_DATABASE_UPGRADE_VERSION', $upgradeVersion)).')'
					);
					$db->query();

					// Check for a database error.
					if ($db->getErrorNum()) {
						return new JException($db->getErrorMsg());
					}
				}
			}
		}

		return true;
	}

	/**
	 * Registers a plugin.
	 *
	 * @param	string	The name of the plugin.
	 * @param	string	The main file (exluding .php) of the plugin.
	 * @param	string	The plugin group.
	 * @param	integer	The published state.
	 * @param	integer	The ordering of the plugin.
	 * @return	mixed	True on success, JException object otherwise.
	 * @since	2.0
	 */
	public static function registerPlugin($name, $element, $group, $published = 1, $ordering = 0)
	{
		$db = JFactory::getDbo();

		// Check if the Members System plugin is installed.
		$db->setQuery(
			'SELECT COUNT(id)' .
			' FROM `#__plugins`' .
			' WHERE `element` = '.$db->quote($element) .
			' AND `folder` = '.$db->quote($group)
		);
		$installed = $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum()) {
			return new JException($db->getErrorMsg());
		}

		// Install the plugin if not installed.
		if (!$installed)
		{
			// Plugins
			$db->setQuery(
				'INSERT INTO `#__plugins`  (`name`, `element`, `folder`, `published`, `ordering`) VALUES' .
				' ('.$db->quote($name).', '.$db->quote($element).', '.$db->quote($group).','.(int) $published.', '.(int) $ordering.')'
			);
			if (!$db->query()) {
				return new JException($db->getErrorMsg());
			}
		}

		return true;
	}

	/**
	 * Method to log a version for a component.
	 *
	 * @param	string	A version string.
	 * @param	string	The component for which to log the version.
	 * @return	mixed	True on success, JException object otherwise.
	 * @since	2.0
	 */
	public static function logVersion($version, $option)
	{
		$db = JFactory::getDbo();
		$db->setQuery(
			'INSERT IGNORE INTO `#__jxtended` (`extension`,`version`,`log`)' .
			' VALUES ('.$db->quote($option).','.$db->quote($version).', '.$db->quote('JX_Manual_Install').')'
		);
		if (!$db->query()) {
			return new JException($db->getErrorMsg());
		}

		return true;
	}
}