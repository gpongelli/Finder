<?php
/**
 * @version		$Id: default.php 1074 2010-10-11 19:21:37Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::stylesheet('indexer.css', 'administrator/components/com_finder/media/css/');
if (class_exists('plgSystemMTUpgrade')) {
	JHTML::script('indexer12.js', 'administrator/components/com_finder/media/js/', true);
}
else {
	JHTML::script('indexer.js', 'administrator/components/com_finder/media/js/', true);
}
?>

<div id="finder-indexer-container">
	<br /><br />
	<h1 id="finder-progress-header"><?php echo JText::_('FINDER_INDEXER_HEADER_INIT'); ?></h1>

	<p id="finder-progress-message"><?php echo JText::_('FINDER_INDEXER_MESSAGE_INIT'); ?></p>

	<form id="finder-progress-form"></form>

	<div id="finder-progress-container"></div>

	<input id="finder-indexer-token" type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
</div>