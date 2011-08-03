<?php
/**
 * @version		$Id: helper.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

class modFinderHelper
{
	/**
	 * Method to get hidden input fields for a get form so that control variables
	 * are not lost upon form submission
	 *
	 * @access	private
	 * @return	string	A string of hidden input form fields
	 * @since	1.0
	 */
	function _getGetFields($route = null)
	{
		$fields = null;
		$uri	= new JURI(JRoute::_($route));

		// Create hidden input elements for each part of the URI.
		foreach ($uri->getQuery(true) as $n => $v) {
			$fields .= '<input type="hidden" name="'.$n.'" value="'.$v.'" />';
		}

		return $fields;
	}
}