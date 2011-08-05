<?php
/**
 * @version		$Id: default.php 1074 2010-10-11 19:21:37Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::_('behavior.mootools');
JHTML::stylesheet('administrator/components/com_finder/media/css/indexer.css', false, false, false);
JHTML::script('administrator/components/com_finder/media/js/indexer12.js', false, false);
?>

<div id="finder-indexer-container">
	<br /><br />
	<h1 id="finder-progress-header"><?php echo JText::_('COM_FINDER_INDEXER_HEADER_INIT'); ?></h1>

	<p id="finder-progress-message"><?php echo JText::_('COM_FINDER_INDEXER_MESSAGE_INIT'); ?></p>

	<form id="finder-progress-form"></form>

	<div id="finder-progress-container"></div>

	<input id="finder-indexer-token" type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
</div>