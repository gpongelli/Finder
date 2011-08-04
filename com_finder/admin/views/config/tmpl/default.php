<?php
/**
 * @version		$Id: default.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::stylesheet('system.css', 'templates/system/css/');
JHTML::stylesheet('finder.css', 'components/com_finder/media/css/');
?>

<div id="finder-config">
	<form action="index.php?option=com_finder" method="post" name="adminForm" autocomplete="off">
		<fieldset>
			<div style="float: right">
				<button type="button" onclick="submitbutton('config.save');">
					<?php echo JText::_('SAVE');?>
				</button>
				<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();">
					<?php echo JText::_('CANCEL');?>
				</button>
				<button type="button" onclick="window.location = '<?php echo JRoute::_('index.php?option=com_finder&amp;view=config&amp;layout=import&amp;tmpl=component'); ?>';">
					<?php echo JText::_('FINDER_CONFIG_IMPORT_EXPORT');?>
				</button>
			</div>
			<div class="configuration" >
				<?php echo JText::_('FINDER_CONFIG_TOOLBAR_TITLE'); ?>
			</div>
		</fieldset>

		<div id="submenu-box">
			<div class="t">
				<div class="t">
					<div class="t"></div>
		 		</div>
			</div>
			<div class="m">
				<ul id="submenu">
					<li><a id="search" class="active"><?php echo JText::_('FINDER_CONFIG_CONFIGURE_SEARCH'); ?></a></li>
					<li><a id="index"><?php echo JText::_('FINDER_CONFIG_CONFIGURE_INDEX'); ?></a></li>
				</ul>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
		 			<div class="b"></div>
				</div>
			</div>
		</div>

		<div id="config-document">
			<div id="page-search">
				<fieldset>
					<legend><?php echo JText::_('FINDER_CONFIG_CONFIGURE_SEARCH'); ?></legend>
					<?php echo JHTML::_('finder.params', 'params', $this->params->toString(), 'models/forms/config/search.xml'); ?>
				</fieldset>
			</div>

			<div id="page-index">
				<fieldset>
					<legend><?php echo JText::_('FINDER_CONFIG_CONFIGURE_INDEX'); ?></legend>
					<?php echo JHTML::_('finder.params', 'params', $this->params->toString(), 'models/forms/config/index.xml'); ?>
				</fieldset>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_finder" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>