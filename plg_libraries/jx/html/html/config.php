<?php
/**
 * @version		$Id: config.php 405 2009-07-14 01:18:28Z louis $
 * @package		JXtended.Libraries
 * @subpackage	HTML
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

/**
 * Extended Utility class for all HTML drawing classes.
 *
 * @package 	JXtended.Libraries
 * @subpackage	HTML
 * @static
 */
class JHtmlConfig
{
	/**
	 * Method to render a given parameters form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$name	The name of the array for form elements.
	 * @param	string	$ini	An INI formatted string.
	 * @param	string	$file	The XML file to render.
	 * @param	string	$base	The base path of the file to render.
	 * @return	string	A HTML rendered parameters form.
	 */
	function params($name, $ini, $file, $base = null)
	{
		jimport('joomla.html.parameter');

		$base = $base ? $base : JPATH_COMPONENT;

		// Load and render the parameters
		$path	= $base.DS.$file;
		$params	= new JParameter($ini, $path);
		$output	= $params->renderToArray($name);

		return $output;
	}
}