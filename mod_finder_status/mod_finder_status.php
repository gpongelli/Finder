<?php
/**
 * @package		JXtended.Finder
 * @subpackage	plgFinderZoo_Items
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Pause if the main menu is disabled.
if (JRequest::getBool('hidemainmenu')) {
	$text = JText::_('MOD_FINDER_STATUS_PAUSED');
} else {
	$text = JText::_('MOD_FINDER_STATUS_WAITING');
	if (class_exists('plgSystemMTUpgrade')) {
		JHtml::script('status12.js', 'administrator/modules/mod_finder_status/media/js/');
	}
	else {
		JHtml::script('status.js', 'administrator/modules/mod_finder_status/media/js/');
	}
}

// We need to add some CSS to fix the status bar display.
$doc = &JFactory::getDocument();
$doc->addStyleDeclaration(
	'div#module-status { background: none; }' .
	'#finder-status-message {' .
	'	background: transparent url(components/com_finder/media/images/icon-16-jx.png) no-repeat scroll 2px 4px' .
	'}'
);
?>
<span id="finder-status-message"><?php echo $text; ?></span>