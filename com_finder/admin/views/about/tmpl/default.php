<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC.  All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.html.pane');
$pane = JPane::getInstance('tabs');
?>
<div id="jx-about">
	<div class="configuration" >
		<?php echo JText::_('COM_FINDER_ABOUT_TITLE'); ?>
	</div>

	<?php echo $pane->startPane('jx-finder-about'); ?>
		<?php echo $pane->startPanel(JText::_('COM_FINDER_ABOUT_TAB_VERSION'), 'pane-version'); ?>
		<div style="text-align:center;">
			<img src="<?php echo $this->baseurl; ?>/components/com_finder/media/images/icon-48-jx.png" alt="Logo" />
			<h2>
				JXtended Finder
			</h2>
			<h3>
				<?php echo JText::sprintf('COM_FINDER_ABOUT_VERSION_STRING', FinderVersion::VERSION.'.'.FinderVersion::SUBVERSION.' '.FinderVersion::STATUS); ?>
			</h3>

			<h4><a href="http://jxtended.com/support/finder.html" target="_new"><?php echo JText::_('COM_FINDER_ABOUT_GET_HELP'); ?></a></h4>
		</div>
		<?php echo $pane->endPanel(); ?>

		<?php echo $pane->startPanel(JText::_('COM_FINDER_ABOUT_TAB_HISTORY'), 'pane-history'); ?>
		<table class="adminlist">
			<thead>
				<tr>
					<th>
						<?php echo JText::_('COM_FINDER_ABOUT_VERSION_NAME');?>
					</th>
					<th>
						<?php echo JText::_('COM_FINDER_ABOUT_VERSION_DATE'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_FINDER_ABOUT_VERSION_LOG'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo JText::_('COM_FINDER_ABOUT_VERSION_NOTE'); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->versions as $version) : ?>
				<tr>
					<td>
						<?php echo $version->version;?>
					</td>
					<td>
						<?php echo JHtml::date($version->installed_date); ?>
					</td>
					<td>
						<?php echo nl2br($version->log); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pane->endPanel(); ?>

		<?php echo $pane->startPanel(JText::_('COM_FINDER_ABOUT_TAB_STATS'), 'pane-stats'); ?>
		<p>
			<?php echo JText::sprintf('COM_FINDER_ABOUT_STATS_DESCRIPTION',
			number_format($this->data->term_count),
			number_format($this->data->link_count),
			number_format($this->data->taxonomy_node_count),
			number_format($this->data->taxonomy_branch_count));
			?>
		</p>
		<table class="adminlist">
			<thead>
				<tr>
					<th>
						<?php echo JText::_('COM_FINDER_ABOUT_LINK_TYPE_HEADING');?>
					</th>
					<th>
						<?php echo JText::_('COM_FINDER_ABOUT_LINK_TYPE_COUNT');?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->data->type_list AS $type) :?>
				<tr>
					<td>
						<?php echo $type->type_title;?>
					</td>
					<td align="right">
						<?php echo number_format($type->link_count);?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td>
						<strong><?php echo JText::_('COM_FINDER_ABOUT_LINK_TYPE_TOTAL'); ?></strong>
					</td>
					<td align="right">
						<strong><?php echo number_format($this->data->link_count); ?></strong>
					</td>
				</tr>
			</tbody>
		</table>
		<?php echo $pane->endPanel(); ?>

	<?php echo $pane->endPane(); ?>
</div>
