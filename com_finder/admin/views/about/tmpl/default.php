<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC.  All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_finder');?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate">
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.SqueezeBox.close();">
				<?php echo JText::_('JTOOLBAR_CLOSE');?></button>
		</div>
		<div class="configuration" >
			<?php echo JText::_('COM_FINDER_ABOUT_TITLE') ?>
		</div>
	</fieldset>

	<?php
	echo JHtml::_('tabs.start', 'config-tabs-com_finder_about', array('useCookie'=>1));
		echo JHtml::_('tabs.panel', JText::_('COM_FINDER_ABOUT_TAB_VERSION'), 'pane-version'); ?>
		<div class="center">
			<img src="<?php echo $this->baseurl; ?>/components/com_finder/media/images/icon-48-jx.png" alt="Logo" />
			<h2>JXtended Finder</h2>
			<h3><?php echo JText::sprintf('COM_FINDER_ABOUT_VERSION_STRING', FinderVersion::VERSION.'.'.FinderVersion::SUBVERSION.' '.FinderVersion::STATUS); ?></h3>
			<h4><a href="http://jxtended.com/support/finder.html" target="_new"><?php echo JText::_('COM_FINDER_ABOUT_GET_HELP'); ?></a></h4>
		</div>
		<div class="clr"></div>

		<?php echo JHtml::_('tabs.panel', JText::_('COM_FINDER_ABOUT_TAB_HISTORY'), 'pane-history'); ?>
		<p class="tab-description"><?php echo JText::_('COM_FINDER_ABOUT_VERSION_NOTE'); ?></p>
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
		<div class="clr"></div>

		<?php echo JHtml::_('tabs.panel',JText::_('COM_FINDER_ABOUT_TAB_STATS'), 'pane-stats'); ?>
		<p class="tab-description"><?php echo JText::sprintf('COM_FINDER_ABOUT_STATS_DESCRIPTION', number_format($this->data->term_count), number_format($this->data->link_count), number_format($this->data->taxonomy_node_count), number_format($this->data->taxonomy_branch_count)); ?></p>
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
		<div class="clr"></div>
	<?php echo JHtml::_('tabs.end'); ?>
	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
