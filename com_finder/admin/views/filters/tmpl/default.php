<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'filters.delete') {
		if (confirm(Joomla.JText._('COM_FINDER_INDEX_CONFIRM_DELETE_PROMPT'))) {
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
					<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap">
					<?php echo JHTML::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="center nowrap">
					<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'a.created_by_alias', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="center nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_FINDER_FILTER_TIMESTAMP', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_FINDER_FILTER_MAP_COUNT', 'a.map_count', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.filter_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->items) == 0): ?>
			<tr class="row0">
				<td class="center" colspan="11">
					<?php
					if ($this->total == 0):
						echo JText::_('COM_FINDER_NO_FILTERS');
						?>
						<a href="<?php echo JRoute::_('index.php?option=com_finder&task=filter.add'); ?>" title="<?php echo JText::_('COM_FINDER_CREATE_FILTER'); ?>">
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

			<?php $n = 0; $o = 0; $c = count($this->items); ?>
			<?php foreach ($this->items as $filter):
			$canCreate	= $user->authorise('core.create',		'com_finder');
			$canEdit	= $user->authorise('core.edit',			'com_finder');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $filter->checked_out == $user->get('id') || $filter->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_finder') && $canCheckin;
			?>

			<tr class="row<?php echo $n % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $n, $filter->filter_id); ?>
				</td>
				<td>
					<?php echo $n+1+$this->state->get('list.start'); ?>
				</td>
				<td>
					<?php if ($filter->checked_out) {
						echo JHtml::_('jgrid.checkedout', $n, $filter->editor, $filter->checked_out_time, 'filters.', $canCheckin);
					} ?>
					<?php if ($canEdit) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_finder&task=filter.edit&filter_id='.(int) $filter->filter_id); ?>">
							<?php echo $this->escape($filter->title); ?></a>
					<?php } else {
							echo $this->escape($filter->title);
					} ?>
				</td>
				<td class="center nowrap">
					<?php //TODO: Why is it looking for a published field!?
					 echo JHtml::_('jgrid.published', $filter->state, $n, 'filters.', $canChange); ?>
				</td>
				<td class="center nowrap">
					<?php echo $filter->created_by_alias ? $filter->created_by_alias : $filter->user_name; ?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('date', $filter->created, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center nowrap">
					<?php echo $filter->map_count; ?>
				</td>
				<td class="center">
					<?php echo (int) $filter->filter_id; ?>
				</td>
			</tr>

			<?php $n++; $o = ++$o % 2; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="11" class="nowrap">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>

<?php JHtml::_('finder.footer'); ?>
