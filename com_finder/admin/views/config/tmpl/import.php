<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('behavior.switcher');
JHtml::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_finder');?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate">
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="submitbutton('config.import');">
				<?php echo JText::_('COM_FINDER_IMPORT');?></button>
			<button type="button" onclick="submitbutton('config.export');">
				<?php echo JText::_('COM_FINDER_EXPORT');?></button>
			<button type="button" onclick="window.location = '<?php echo JRoute::_('index.php?option=com_finder&view=config&layout=default&tmpl=component'); ?>';">
				<?php echo JText::_('JCANCEL');?></button>
		</div>
		<div class="configuration" >
			<?php echo JText::_('COM_FINDER_CONFIG_IMPORT_TOOLBAR_TITLE') ?>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('COM_FINDER_CONFIG_IMPORT_EXPORT_HELP'); ?></legend>
		<p><?php echo JText::_('COM_FINDER_CONFIG_IMPORT_EXPORT_INSTRUCTIONS'); ?></p>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('COM_FINDER_IMPORT'); ?></legend>
		<ul class="config-option-list">
			<?php foreach($this->import->getGroup('import') as $field): ?>
			<li>
				<?php if (!$field->hidden): ?>
				<?php echo $field->label; ?>
				<?php endif; ?>
				<?php echo $field->input; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
