<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Suggest controller for JXtended Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderControllerSuggestions extends JController
{
	/**
	 * Method to find search query suggestions.
	 *
	 * @return	void
	 */
	public function display()
	{
		// Get the suggestions.
		$model	= &$this->getModel('Suggestions', 'FinderModel');
		$return	= $model->getItems();

		// Check the data.
		if (empty($return)) {
			$return = array();
		}

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
}
