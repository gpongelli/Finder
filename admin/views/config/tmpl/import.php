<?php
/**
 * @version		$Id: import.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
$this->document->addStyleSheet('templates/system/css/system.css');
?>

<div id="finder-config">
	<form action="index.php?option=com_finder" method="post" name="adminForm" autocomplete="off" enctype="multipart/form-data">
		<fieldset>
			<div style="float: right">
				<button type="button" onclick="submitbutton('config.import');">
					<?php echo JText::_('FINDER_IMPORT');?>
				</button>
				<button type="button" onclick="submitbutton('config.export');">
					<?php echo JText::_('FINDER_EXPORT');?>
				</button>
				<button type="button" onclick="window.location = '<?php echo JRoute::_('index.php?option=com_finder&view=config&tmpl=component'); ?>';">
					<?php echo JText::_('CANCEL');?>
				</button>
			</div>
			<div class="configuration" >
				<?php echo JText::_('FINDER_CONFIG_IMPORT_TOOLBAR_TITLE'); ?>
			</div>
		</fieldset>

		<fieldset>
			<legend><?php echo JText::_('FINDER_CONFIG_IMPORT_EXPORT_HELP'); ?></legend>
			<p><?php echo JText::_('FINDER_CONFIG_IMPORT_EXPORT_INSTRUCTIONS'); ?></p>
		</fieldset>

		<fieldset>
			<legend><?php echo JText::_('FINDER_CONFIG_IMPORT'); ?></legend>

			<label for="import_file"><?php echo JText::_('FINDER_CONFIG_IMPORT_FROM_FILE'); ?></label><br />
			<input type="file" name="configFile" id="import_file" size="40" />

			<br /><br />

			<label for="import_string"><?php echo JText::_('FINDER_CONFIG_IMPORT_FROM_STRING'); ?></label><br />
			<textarea name="configString" rows="10" cols="50"></textarea>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>