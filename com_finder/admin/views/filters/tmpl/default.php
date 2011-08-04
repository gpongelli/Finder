<?php
/**
 * @version		$Id: default.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::stylesheet('finder.css', 'administrator/components/com_finder/media/css/');
?>

<form action="index.php?option=com_finder&amp;view=filters" method="post" name="adminForm">
	<div class="form-filter" style="float: left;">
		<label for="filter_search"><?php echo JText::sprintf('FINDER_SEARCH_LABEL', JText::_('FINDER_FILTERS')); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('FINDER_SEARCH_GO'); ?></button>
		<button onclick="document.getElementById('filter_search').value='';document.getElementById('filter_state').value='10';this.form.submit();"><?php echo JText::_('FINDER_SEARCH_RESET'); ?></button>
	</div>

	<div class="form-filter" style="float: right;">
		<?php echo JText::sprintf('FINDER_FILTER_BY', JText::_('FINDER_FILTERS')); ?>
		<?php echo JHTML::_('finder.statelist', $this->state->get('filter.state')) ?>
	</div>

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('NUM'); ?>
				</th>
				<th width="5">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo (count($this->filters)+1); ?>);" />
				</th>
				<th nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'FINDER_FILTER_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="4%" nowrap="nowrap" style="padding: 0px 15px;">
					<?php echo JHTML::_('grid.sort', 'FINDER_FILTER_STATE', 'a.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th align="center" width="7%" style="padding: 0px 15px;" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'FINDER_FILTER_AUTHOR', 'a.created_by_alias', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th align="center" width="10%" style="padding: 0px 15px;" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'FINDER_FILTER_TIMESTAMP', 'a.created', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th align="center" width="4%" style="padding: 0px 15px;" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'FINDER_FILTER_MAP_COUNT', 'a.map_count', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->filters) == 0): ?>
			<tr class="row0">
				<td align="center" colspan="11">
					<?php
					if ($this->total == 0):
						echo JText::_('FINDER_NO_FILTERS');
						?>
						<a href="<?php echo JRoute::_('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1'); ?>" title="<?php echo JText::_('FINDER_CREATE_FILTER'); ?>">
							<?php echo JText::_('FINDER_CREATE_FILTER'); ?>
						</a>
						<?php
					else:
						echo JText::_('FINDER_NO_RESULTS');
					endif;
					?>
				</td>
			</tr>
			<?php endif; ?>

			<?php $n = 0; $o = 0; $c = count($this->filters); ?>
			<?php foreach ($this->filters as $filter): ?>

			<tr class="<?php echo 'row', $o; ?>">
				<td>
					<?php echo $n+1+$this->state->get('list.start'); ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.checkedOut', $filter, $n, 'filter_id'); ?>
				</td>
				<td style="padding-left: 10px; padding-right: 10px;">
					<?php $filter->url = JURI::base().'index.php?option=com_finder&task=filter.edit&filter_id='.$filter->filter_id; ?>
					<a href="<?php echo $filter->url; ?>" title="<?php echo $filter->title; ?>"><?php echo $filter->title; ?></a>
				</td>
				<td nowrap="nowrap" style="padding: 0px 20px; text-align: center">
					<?php echo JHTML::_('finder.state', $n, $filter->state, true, 'filters'); ?>
				</td>
				<td nowrap="nowrap" style="padding: 0px 20px; text-align: center">
					<?php echo $filter->created_by_alias ? $filter->created_by_alias : $filter->created_by; ?>
				</td>
				<td nowrap="nowrap" style="padding: 0px 20px; text-align: center">
					<?php
						$date = &JFactory::getDate($filter->created);
						$date->setOffset($this->user->_params->get('timezone'));

						echo $date->toFormat();
					?>
				</td>
				<td nowrap="nowrap" style="padding: 0px 20px; text-align: center">
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