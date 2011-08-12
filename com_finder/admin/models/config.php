<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Require com_config component model
require_once JPATH_ADMINISTRATOR.'/components/com_config/models/component.php';

/**
 * Configuration model class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class FinderModelConfig extends ConfigModelComponent
{
	/**
	 * Get the component information.
	 *
	 * @return  object  The component information
	 *
	 * @since   2.5
	 */
	function getComponent()
	{
		// Initialise variables.
		$option = 'com_finder';

		// Load common and local language files.
		$lang = JFactory::getLanguage();
			$lang->load($option, JPATH_BASE, null, false, false)
		||	$lang->load($option, JPATH_BASE . "/components/$option", null, false, false)
		||	$lang->load($option, JPATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load($option, JPATH_BASE . "/components/$option", $lang->getDefault(), false, false);

		$result = JComponentHelper::getComponent($option);

		return $result;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   2.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		// Add the search path for the admin component config.xml file.
		JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR);

		// Get the form.
		$form = $this->loadForm(
				'com_config.component',
				'config',
				array('control' => 'jform', 'load_data' => $loadData),
				false,
				'/config'
			);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   2.5
	 */
	public function getImport($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_finder.import', 'import', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
}
