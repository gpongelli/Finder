<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$lang = &JFactory::getLanguage();
?>

<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'index.purge') {
		if (confirm(<?php echo JText::_('COM_FINDER_INDEX_CONFIRM_PURGE_PROMPT');?>)) {
			Joomla.submitform(pressbutton);
		} else {
			return false;
		}
	}
	if (pressbutton == 'index.delete') {
		if (confirm(<?php echo JText::_('COM_FINDER_INDEX_CONFIRM_DELETE_PROMPT');?>)) {
			Joomla.submitform(pressbutton);
		} else {
			return false;
		}
	}

	Joomla.submitform(pressbutton);
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_finder&view=index');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::sprintf('COM_FINDER_SEARCH_LABEL', JText::_('COM_FINDER_ITEMS')); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_FINDER_FILTER_SEARCH_DESCRIPTION'); ?>" />
			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_type" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_FINDER_INDEX_TYPE_FILTER');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('finder.typeslist'), 'value', 'text', $this->state->get('filter.type'), true);?>
			</select>
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
				<th>
					<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'l.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'JSTATUS', 'l.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'COM_FINDER_INDEX_HEADING_INDEX_TYPE', 'l.type_id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="20%">
					<?php echo JHTML::_('grid.sort', 'COM_FINDER_INDEX_HEADING_LINK_URL', 'l.url', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort', 'COM_FINDER_INDEX_HEADING_INDEX_DATE', 'l.indexdate', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->items) == 0): ?>
			<tr class="row0">
				<td align="center" colspan="7">
					<?php
					if ($this->total == 0) {
						echo JText::_('COM_FINDER_INDEX_NO_DATA');
						echo JText::_('COM_FINDER_INDEX_TIP');
					} else {
						echo JText::_('COM_FINDER_INDEX_NO_CONTENT');
					}
					?>
				</td>
			</tr>
			<?php endif; ?>

			<?php $n = 1 + $this->state->get('list.start'); $o = 0; ?>
			<?php $canChange	= JFactory::getUser()->authorise('core.manage',	'com_finder'); ?>
			<?php foreach ($this->items as $row): ?>

			<tr class="row<?php echo $n % 2; ?>">
				<td class="center" title="<?php echo (int) $row->link_id;?>">
					<?php echo JHtml::_('grid.id', $n, $row->link_id); ?>
				</td>
				<td>
					<?php echo $n; ?>
				</td>
				<td>
					<?php if (intval($row->publish_start_date) OR intval($row->publish_end_date) OR intval($row->start_date) OR intval($row->end_date)) : ?>
					<img src="<?php echo $this->baseurl;?>/components/com_finder/media/images/calendar.png" style="border:1;float:right" class="hasTip" title="<?php echo JText::sprintf('COM_FINDER_INDEX_DATE_INFO', $row->publish_start_date, $row->publish_end_date, $row->start_date, $row->end_date);?>" />
					<?php endif; ?>
					<?php echo $this->escape($row->title); ?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('jgrid.published', $row->published, $n, 'index.', $canChange, 'cb'); ?>
				</td>
				<td class="center nowrap">
					<?php
					$key = $lang->hasKey('COM_FINDER_TYPE_S_'.strtoupper(str_replace(' ', '_', $row->t_title))) ? 'COM_FINDER_TYPE_S_'.strtoupper(str_replace(' ', '_', $row->t_title)) : $row->t_title;
					echo JText::_($key); ?>
				</td>
				<td class="nowrap">
					<?php
					if (strlen($row->url) > 80) {
						echo substr($row->url, 0, 70).'...';
					} else {
						echo $row->url;
					}
					?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('date', $row->indexdate, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
			</tr>

			<?php $n++; $o = ++$o % 2; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7" class="nowrap">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering') ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction') ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
