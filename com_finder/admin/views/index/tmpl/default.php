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

<form action="index.php?option=com_finder&amp;view=index" method="post" name="adminForm">
	<div class="form-filter" style="float: left;">
		<label for="filter_search"><?php echo JText::sprintf('FINDER_SEARCH_LABEL', JText::_('FINDER_ITEMS')); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('FINDER_SEARCH_GO'); ?></button>
		<button onclick="document.getElementById('filter_search').value='';document.getElementById('filter_type').value='0';document.getElementById('filter_state').value='*';this.form.submit();"><?php echo JText::_('FINDER_SEARCH_RESET'); ?></button>
	</div>

	<div class="form-filter" style="float: right;">
		<?php echo JText::sprintf('FINDER_FILTER_BY', JText::_('FINDER_ITEMS')); ?>
		<?php echo JHTML::_('finder.typeslist', $this->state->get('filter.type')); ?>
		<?php echo JHTML::_('finder.statelist', $this->state->get('filter.state')); ?>
	</div>

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('NUM'); ?>
				</th>
				<th width="5">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo (count($this->data)+1); ?>);" />
				</th>
				<th nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'FINDER_INDEX_LINK_TITLE', 'l.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" width="6%">
					<?php echo JHTML::_('grid.sort', 'FINDER_INDEX_LINK_PUBLISHED', 'l.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" width="5%">
					<?php echo JHTML::_('grid.sort', 'FINDER_INDEX_INDEX_TYPE', 'l.type_id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" width="20%">
					<?php echo JHTML::_('grid.sort', 'FINDER_INDEX_LINK_URL', 'l.url', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" width="9%">
					<?php echo JHTML::_('grid.sort', 'FINDER_INDEX_INDEX_DATE', 'l.indexdate', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->data) == 0): ?>
			<tr class="row0">
				<td align="center" colspan="7">
					<?php
					if ($this->total == 0) {
						echo JText::_('FINDER_INDEX_NO_DATA');
						echo JText::_('FINDER_INDEX_TIP');
					} else {
						echo JText::_('FINDER_INDEX_NO_CONTENT');
					}
					?>
				</td>
			</tr>
			<?php endif; ?>

			<?php $n = 1 + $this->state->get('list.start'); $o = 0; ?>
			<?php foreach ($this->data as $row): ?>

			<tr class="<?php echo 'row', $o; ?>">
				<td>
					<?php echo $n; ?>
				</td>
				<td align="center" title="<?php echo (int) $row->link_id;?>">
					<?php echo JHTML::_('grid.id', $n, $row->link_id); ?>
				</td>
				<td>
					<?php if (intval($row->publish_start_date) OR intval($row->publish_end_date) OR intval($row->start_date) OR intval($row->end_date)) : ?>
					<img src="<?php echo $this->baseurl;?>/components/com_finder/media/images/calendar.png" style="border:1;float:right" class="hasTip" title="<?php echo JText::sprintf('FINDER_INDEX_DATE_INFO', $row->publish_start_date, $row->publish_end_date, $row->start_date, $row->end_date);?>" />
					<?php endif; ?>
					<?php echo $this->escape($row->title); ?>
				</td>
				<td nowrap="nowrap" style="text-align: center">
					<?php echo JHTML::_('finder.state', $n, $row->published); ?>
				</td>
				<td nowrap="nowrap" style="text-align: center">
					<?php
					$key = $lang->hasKey('FINDER_TYPE_S_'.strtoupper(str_replace(' ', '_', $row->t_title))) ? 'FINDER_TYPE_S_'.strtoupper(str_replace(' ', '_', $row->t_title)) : $row->t_title;
					echo JText::_($key); ?>
				</td>
				<td nowrap="nowrap">
					<?php
					if (strlen($row->url) > 80) {
						echo substr($row->url, 0, 70).'...';
					} else {
						echo $row->url;
					}
					?>
				</td>
				<td nowrap="nowrap" style="text-align: center;">
					<?php echo JHtml::date($row->indexdate, '%Y-%m-%d %H:%M:%S'); ?>
				</td>
			</tr>

			<?php $n++; $o = ++$o % 2; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7" nowrap="nowrap">
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

<?php JHtml::_('finder.footer'); ?>