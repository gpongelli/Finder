<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_finder_status
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

// Pause if the main menu is disabled.
if (JRequest::getBool('hidemainmenu'))
{
	$text = JText::_('MOD_FINDER_STATUS_PAUSED');
}
else
{
	$text = JText::_('MOD_FINDER_STATUS_WAITING');
	JHtml::script('administrator/modules/mod_finder_status/media/js/status.js', false, false);
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
