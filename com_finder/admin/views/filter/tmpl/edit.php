<?php
/**
 * @version		$Id: edit.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_finder'.DS.'helpers'.DS.'html');
JHTML::stylesheet('finder.css', 'administrator/components/com_finder/media/css/');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

// Get the form fields.
$fieldsMain		= $this->form->getFields();
$fieldsDetails	= $this->form->getFields('details');
?>

<form action="<?php echo JRoute::_('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1'); ?>" method="post" name="adminForm">
	<div class="col width-100" style="width: 100%">
		<table class="adminform">
			<tbody>
				<tr>
					<td>
						<strong><?php echo $fieldsMain['title']->label; ?>:</strong><br />
						<?php echo $fieldsMain['title']->input; ?>
					</td>
					<td>
						<strong><?php echo $fieldsMain['alias']->label; ?>:</strong><br />
						<?php echo $fieldsMain['alias']->input; ?>
					</td>
					<td>
						<strong><?php echo $fieldsDetails['state']->label; ?></strong><br />
						<?php echo $fieldsDetails['state']->input; ?>
					</td>
					<td>
						<strong><?php echo $fieldsDetails['map_count']->label; ?></strong><br />
						<?php echo $fieldsDetails['map_count']->input; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="clr"></div>

	<div id="finder-filter-window">
		<?php echo JHTML::_('filter.slider', array('selected_nodes' => $this->filter->data)); ?>
	</div>

	<div style="float:left; width:400px;">
		<fieldset>
			<legend><?php echo JText::_('Finder Filter Parameters'); ?></legend>

			<table class="paramlist admintable">
				<tbody>
					<?php foreach($this->form->getFields('params') as $field): ?>
						<?php
						if (strcasecmp($field->getType(), 'hidden') == 0) {
							echo $field->input;
							continue;
						}
						?>
					<tr>
						<td class="paramlist_key">
							<?php echo $field->label; ?><br />
						</td>
						<td class="paramlist_value">
							<?php echo $field->input; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>


	<input type="hidden" name="option" value="com_finder" />
	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="view" value="filter" />
	<input type="hidden" name="layout" value="edit" />
	<?php echo JHTML::_('form.token'); ?>
</form>