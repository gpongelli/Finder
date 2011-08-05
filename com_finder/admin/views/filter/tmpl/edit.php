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

				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>

				<li><?php echo $this->form->getLabel('map_count'); ?>
				<?php echo $this->form->getInput('map_count'); ?></li>
			</ul>
		</fieldset>
	</div>
	<div id="finder-filter-window">
		<?php echo JHTML::_('filter.slider', array('selected_nodes' => $this->filter->data)); ?>
	</div>

	<div style="float:left; width:400px;">
		<fieldset>
			<legend><?php echo JText::_('Finder Filter Parameters'); ?></legend>

			<table class="paramlist admintable">
				<tbody>
					<?php foreach($this->form->getFields('params') as $field): ?>
						<?php
						if (strcasecmp($field->getType(), 'hidden') == 0) {
							echo $field->input;
							continue;
						}
						?>
					<tr>
						<td class="paramlist_key">
							<?php echo $field->label; ?><br />
						</td>
						<td class="paramlist_value">
							<?php echo $field->input; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>


	<input type="hidden" name="option" value="com_finder" />
	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="view" value="filter" />
	<input type="hidden" name="layout" value="edit" />
	<?php echo JHTML::_('form.token'); ?>
</form>
