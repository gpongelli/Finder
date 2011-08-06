<?php
/**
 * @version		$Id: default.php 922 2010-03-11 20:17:33Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::stylesheet('finder.css', 'administrator/components/com_finder/media/css/');
?>

<form action="index.php?option=com_finder&amp;view=adapters" method="post" name="adminForm">
	<div class="form-filter" style="float: left;">
		<label for="filter_search"><?php echo JText::sprintf('COM_FINDER_SEARCH_LABEL', JText::_('COM_FINDER_ADAPTERS')); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('COM_FINDER_SEARCH_GO'); ?></button>
		<button onclick="document.getElementById('filter_search').value='';document.getElementById('filter_state').value='*';this.form.submit();"><?php echo JText::_('COM_FINDER_SEARCH_RESET'); ?></button>
	</div>

	<div class="form-filter" style="float: right;">
		<?php echo JText::sprintf('COM_FINDER_FILTER_BY', JText::_('COM_FINDER_ADAPTERS')); ?>
		<select name="filter_state" class="inputbox" onchange="this.form.submit()">
		<?php echo JHtml::_('select.options', JHtml::_('finder.statelist'), 'value', 'text', $this->state->get('filter.state'), true);?>
		</select>
	</div>

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('NUM'); ?>
				</th>
				<th width="5">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo (count($this->items)+1); ?>);" />
				</th>
				<th nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_FINDER_ADAPTER_TITLE', 'p.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="4%" nowrap="nowrap" style="padding: 0px 15px;">
					<?php echo JHtml::_('grid.sort', 'COM_FINDER_ADAPTER_STATE', 'p.published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th align="center" width="10%" style="padding: 0px 15px;" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_FINDER_ADAPTER_ELEMENT', 'p.element', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th align="center" width="1%" style="padding: 0px 15px;" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_FINDER_ADAPTER_ID', 'p.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->items) == 0): ?>
			<tr class="row0">
				<td align="center" colspan="11">
					<?php
					if ($this->total == 0):
						echo JText::_('COM_FINDER_NO_ADAPTERS');
					else:
						echo JText::_('COM_FINDER_NO_RESULTS');
					endif;
					?>
				</td>
			</tr>
			<?php endif; ?>

			<?php $n = 0; $o = 0; $c = count($this->items); ?>
			<?php $canChange	= JFactory::getUser()->authorise('core.manage',	'com_finder'); ?>
			<?php foreach ($this->items as $item): ?>

			<tr class="<?php echo 'row', $o; ?>">
				<td>
					<?php echo $n+1+$this->state->get('list.start'); ?>
				</td>
				<td align="center">
					<?php echo JHtml::_('grid.checkedOut', $item, $n, 'id'); ?>
				</td>
				<td style="padding-left: 10px; padding-right: 10px;">
					<?php echo str_replace('Finder - ', '', $item->name); ?>
				</td>
				<td nowrap="nowrap" style="padding: 0px 20px; text-align: center">
					<?php echo JHtml::_('jgrid.published', $item->published, $n, 'adapters.', $canChange, 'cb'); ?>
				</td>
				<td nowrap="nowrap" style="padding: 0px 20px;">
					<?php echo $item->element; ?>
				</td>
				<td nowrap="nowrap" style="padding: 0px 20px; text-align: center">
					<?php echo $item->id; ?>
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

<?php JHtml::_('finder.footer'); ?>