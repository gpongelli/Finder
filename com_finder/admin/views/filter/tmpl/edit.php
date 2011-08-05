<?php
/**
 * @version		$Id: edit.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHTML::addIncludePath(JPATH_SITE.'/components/com_finder/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<form action="<?php echo JRoute::_('index.php?option=com_finder&view=filter&layout=edit&id='.(int) $this->item->filter_id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('Need a label!'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>

				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>
			</ul>
		</fieldset>
	</div>
	<div id="finder-filter-window">
		<?php echo JHTML::_('filter.slider', array('selected_nodes' => $this->filter->data)); ?>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'filter-sliders-'.$this->item->filter_id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_FINDER_FILTER_FIELDSET_DETAILS'), 'filter-details'); ?>
			<?php $details = $this->form->getGroup('details'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('created'); ?>
					<?php echo $this->form->getInput('created'); ?></li>

					<?php if ($this->item->modified_by) : ?>
					<li><?php echo $this->form->getLabel('modified_by'); ?>
					<?php echo $this->form->getInput('modified_by'); ?></li>

					<li><?php echo $this->form->getLabel('modified'); ?>
					<?php echo $this->form->getInput('modified'); ?></li>
					<?php endif; ?>

					<li><?php echo $this->form->getLabel('state'); ?>
					<?php echo $this->form->getInput('state'); ?></li>

					<li><?php echo $this->form->getLabel('map_count'); ?>
					<?php echo $this->form->getInput('map_count'); ?></li>
				</ul>
			</fieldset>

			<?php echo JHtml::_('sliders.panel', JText::_('COM_FINDER_FILTER_FIELDSET_PARAMS'), 'filter-params'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach($this->form->getGroup('params') as $field): ?>
					<li>
						<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
						<?php endif; ?>
						<?php echo $field->input; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>

			<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div class="clr"></div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
