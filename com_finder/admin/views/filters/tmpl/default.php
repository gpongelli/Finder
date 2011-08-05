<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
?>

<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'filters.delete') {
		if (confirm(<?php echo JText::_('COM_FINDER_INDEX_CONFIRM_DELETE_PROMPT');?>)) {
			Joomla.submitform(pressbutton);
		}
	}
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_finder&view=filters');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::sprintf('COM_FINDER_SEARCH_LABEL', JText::_('COM_FINDER_FILTERS')); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_FINDER_FILTER_SEARCH_DESCRIPTION'); ?>" />
			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
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
					<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%" class="nowrap" style="padding: 0px 15px;">
					<?php echo JHTML::_('grid.sort', 'JSTATUS', 'a.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%" style="padding: 0px 15px;" class="center nowrap">
					<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'a.created_by_alias', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%" style="padding: 0px 15px;" class="center nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_FINDER_FILTER_TIMESTAMP', 'a.created', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%" style="padding: 0px 15px;" class="center nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_FINDER_FILTER_MAP_COUNT', 'a.map_count', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->filters) == 0): ?>
			<tr class="row0">
				<td align="center" colspan="11">
					<?php
					if ($this->total == 0):
						echo JText::_('COM_FINDER_NO_FILTERS');
						?>
						<a href="<?php echo JRoute::_('index.php?option=com_finder&task=filter.createnew'); ?>" title="<?php echo JText::_('COM_FINDER_CREATE_FILTER'); ?>">
							<?php echo JText::_('COM_FINDER_CREATE_FILTER'); ?>
						</a>
						<?php
					else:
						echo JText::_('COM_FINDER_NO_RESULTS');
					endif;
					?>
				</td>
			</tr>
			<?php endif; ?>

			<?php $n = 0; $o = 0; $c = count($this->filters); ?>
			<?php foreach ($this->filters as $filter): ?>

			<tr class="row<?php echo $n % 2; ?>">
				<td class="center" title="<?php echo (int) $row->link_id;?>">
					<?php echo JHtml::_('grid.id', $n, $row->link_id); ?>
				</td>
				<td>
					<?php echo $n+1+$this->state->get('list.start'); ?>
				</td>
				<td style="padding-left: 10px; padding-right: 10px;">
					<?php echo JHTML::_('grid.checkedOut', $filter, $n, 'filter_id'); ?>
					<?php $filter->url = JURI::base().'index.php?option=com_finder&task=filter.edit&filter_id='.$filter->filter_id; ?>
					<a href="<?php echo $filter->url; ?>" title="<?php echo $filter->title; ?>"><?php echo $filter->title; ?></a>
				</td>
				<td class="center nowrap" style="padding: 0px 20px;">
					<?php echo JHTML::_('finder.state', $n, $filter->state, true, 'filters'); ?>
				</td>
				<td class="center nowrap" style="padding: 0px 20px;">
					<?php echo $filter->created_by_alias ? $filter->created_by_alias : $filter->created_by; ?>
				</td>
				<td class="center nowrap" style="padding: 0px 20px;">
					<?php
						$date = &JFactory::getDate($filter->created);
						$date->setOffset($this->user->_params->get('timezone'));

						echo $date->toFormat();
					?>
				</td>
				<td class="center nowrap" style="padding: 0px 20px;">
					<?php echo $filter->map_count; ?>
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
	<input type="hidden" name="view" value="filters" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>

<?php JHtml::_('finder.footer'); ?>
