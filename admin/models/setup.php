<?php
/**
 * @version		$Id: setup.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jx('jx.application.component.helper.setup');
require_once(JPATH_ADMINISTRATOR.'/components/com_finder/version.php');

/**
 * Setup Model for JXtended Finder
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @since		1.0
 */
class FinderModelSetup extends JModel
{
	/**
	 * Method to manually install JXtended Finder.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function install()
	{
		// Register the component.
		$return = JComponentHelperSetup::registerComponent('Finder', 'com_finder', true, 'components/com_finder/media/images/icon-16-jx.png');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the JXtended Libraries plugin.
		$return = JComponentHelperSetup::registerPlugin('System - JXtended Libraries 2.0', 'jx', 'system');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Finder system plugin.
		$return = JComponentHelperSetup::registerPlugin('System - Finder', 'finder', 'system');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Catalog Nodes plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Catalog Nodes', 'catalog_nodes', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Joomla Articles plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Joomla Articles', 'joomla_articles', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Joomla Contacts plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Joomla Contacts', 'joomla_contacts', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Joomla Weblinks plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Joomla Weblinks', 'joomla_weblinks', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Labels plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Labels', 'labels_labels', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Magazine Articles plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Magazine Articles', 'zine_articles', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Magazine Authors plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Magazine Authors', 'zine_authors', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Magazine Issues plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Magazine Issues', 'zine_issues', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Register the Magazine Publications plugin.
		$return = JComponentHelperSetup::registerPlugin('Finder - Magazine Publications', 'zine_publications', 'finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Get the current component version information and log it.
		$version = FinderVersion::VERSION.'.'.FinderVersion::SUBVERSION.' '.FinderVersion::STATUS;
		$return = JComponentHelperSetup::logVersion($version, 'com_finder');
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * Method to run necessary database upgrade scripts for JXtended Finder.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function upgrade()
	{
		// Get the component upgrade information.
		$upgrades = FinderVersion::getUpgrades();

		// If there are upgrades to process, attempt to process them.
		if (is_array($upgrades) && count($upgrades))
		{
			$return = JComponentHelperSetup::upgradeComponent($upgrades, 'com_finder');
			if (JError::isError($return))
			{
				$this->setError($return->getMessage());
				return false;
			}
		}

		return true;
	}
}