<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$user		= JFactory::getUser();
$userId		= $user->get('id');
?>

<form action="<?php echo JRoute::_('index.php?option=com_finder&view=adapters');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::sprintf('COM_FINDER_SEARCH_LABEL', JText::_('COM_FINDER_ADAPTERS')); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_FINDER_FILTER_SEARCH_DESCRIPTION'); ?>" />
			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_FINDER_INDEX_FILTER_BY_STATE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('finder.statelist'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="5%">
					<?php echo JText::_('NUM'); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'p.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'p.enabled', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_FINDER_ADAPTER_ELEMENT', 'p.folder', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'p.extension_id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->items) == 0): ?>
			<tr class="row0">
				<td class="center" colspan="11">
					<?php echo JText::_('COM_FINDER_NO_ADAPTERS'); ?>
				</td>
			</tr>
			<?php endif; ?>

			<?php $n = 0; $o = 0; $c = count($this->items); ?>
			<?php foreach ($this->items as $item):
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_finder') && $canCheckin;
			?>

			<tr class="row<?php echo $n % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $n, $item->extension_id); ?>
				</td>
				<td>
					<?php echo $n+1+$this->state->get('list.start'); ?>
				</td>
				<td>
					<?php if ($item->checked_out) {
						echo JHtml::_('jgrid.checkedout', $n, $item->editor, $item->checked_out_time, 'adapters.', $canCheckin);
					} ?>
					<?php //TODO: Need to load the plugin .sys.ini files to get the names or come up with a way to show the names with the DB info, preferably without duplicating the strings
					echo JText::_(strtoupper($item->name)); ?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('jgrid.published', $item->enabled, $n, 'adapters.', $canChange, 'cb'); ?>
				</td>
				<td class="nowrap">
					<?php echo $item->folder; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->extension_id; ?>
				</td>
			</tr>

			<?php $n++; $o = ++$o % 2; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="11" nowrap="nowrap">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_finder" />
	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="view" value="adapters" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
