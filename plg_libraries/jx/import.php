<?php
/**
 * @version		$Id: import.php 463 2009-09-23 19:38:29Z louis $
 * @package		JXtended.Libraries
 * @subpackage	plgSystemJX
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Define the JXtended Libraries version constant.
if (!defined('JXVERSION')) {
	define('JXVERSION', '2.0.0');
}

// Define the JXtended Libraries path constant.
if (!defined('JXPATH_FRAMEWORK')) {
	define('JXPATH_FRAMEWORK', dirname(dirname(realpath(__FILE__))));
}

/**
 * JXtended Libraries intelligent file importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	2.0
 */
function jx($path)
{
	if (strpos($path, 'jx') === 0) {
		return JLoader::import($path, JXPATH_FRAMEWORK, '');
	}
	else {
		return JLoader::import($path, null, 'libraries.');
	}
}

/**
 * JXtended JavaScript language string support function.
 *
 * @return	void
 * @since	2.0
 */
function jxjs()
{
	// Only inject the language strings in an HTML document.
	$doc = JFactory::getDocument();
	if ($doc->getType() == 'html')
	{
		// Load the JXtended Javascript NameSpace.
		JHtml::script('jx.js', 'plugins/system/jx/');

		$lang = JFactory::getApplication()->get('jx.js', array());
		if (!empty($lang)) {
			$doc->addScriptDeclaration('	JX.JText.load('.json_encode($lang).');');
		}
	}
}

// Register the JavaScript language string injection function with the event dispatcher.
JDispatcher::getInstance()->register('onAfterRoute', 'jxjs');