<?php
/**
 * @version		$Id: default.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC.  All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::stylesheet('finder.css', 'administrator/components/com_finder/media/css/');
$lang = &JFactory::getLanguage();
?>

<form action="index.php?option=com_finder&amp;view=maps" method="post" name="adminForm">
	<div class="form-filter" style="float: left;">
		<label for="filter_search"><?php echo JText::sprintf('FINDER_SEARCH_LABEL', JText::_('FINDER_MAPS')); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('FINDER_SEARCH_GO'); ?></button>
		<button onclick="document.getElementById('filter_search').value='';document.getElementById('filter_state').value='10';this.form.submit();"><?php echo JText::_('FINDER_SEARCH_RESET'); ?></button>
	</div>

	<div class="form-filter" style="float: right;">
		<?php echo JText::sprintf('FINDER_FILTER_BY', JText::_('FINDER_MAPS')); ?>
		<?php echo JHTML::_('finder.mapslist', $this->state->get('filter.branch')); ?>
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
					<?php echo JHTML::_('grid.sort', 'FINDER_MAPS_MAP_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" width="10%">
					<?php echo JHTML::_('grid.sort', 'FINDER_MAPS_MAP_STATE', 'a.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->data) == 0): ?>
			<tr class="row0">
				<td align="center" colspan="5">
					<?php echo JText::_('FINDER_MAPS_NO_CONTENT'); ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ($this->state->get('filter.branch') != 1) : ?>
			<tr class="row0">
				<td colspan="5" style="text-align:center">
					<a href="#" onclick="$('filter_branch').value=1;document.adminForm.submit();">
						<?php echo JText::_('FINDER_MAPS_RETURN_TO_BRANCHES'); ?></a>
				</td>
			</tr>
			<?php endif; ?>

			<?php $n = 1; $o = 0; ?>
			<?php foreach ($this->data as $row): ?>

			<tr class="<?php echo 'row', $n % 2; ?>">
				<td>
					<?php echo $n; ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $n, $row->id); ?>
				</td>
				<td>
					<?php
						$key = 'FINDER_TYPE_S_'.strtoupper(str_replace(' ', '_', $row->title));
						$title = $lang->hasKey($key) ? JText::_($key) : $row->title;
					?>
					<?php if ($this->state->get('filter.branch') == 1 && $row->num_children) : ?>
						<a href="#" onclick="$('filter_branch').value=<?php echo (int) $row->id;?>;document.adminForm.submit();" title="<?php echo JText::_('FINDER_MAPS_BRANCH_LINK'); ?>">
							<?php echo $this->escape($title); ?></a>
					<?php else: ?>
						<?php echo $this->escape($title); ?>
					<?php endif; ?>
					<?php if ($row->num_children > 0) : ?>
						<small>(<?php echo $row->num_children; ?>)</small>
					<?php elseif ($row->num_nodes > 0) : ?>
						<small>(<?php echo $row->num_nodes; ?>)</small>
					<?php endif; ?>
				</td>
				<td nowrap="nowrap" style="text-align: center">
					<?php echo JHTML::_('finder.state', $n, $row->state, true, 'map'); ?>
				</td>
			</tr>

			<?php $n++; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9" nowrap="nowrap">
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