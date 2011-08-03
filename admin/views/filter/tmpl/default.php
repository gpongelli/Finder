<?php
/**
 * @version		$Id: default.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_finder'.DS.'helpers'.DS.'html');
?>

<form action="index.php?option=com_finder&amp;view=filter&amp;layout=default&amp;hidemainmenu=1" method="post" name="adminForm">

	<div id="finder-filter-window">
		<?php echo JHTML::_('filter.slider', array('selected_nodes' => $this->filter ? $this->filter->data : array())); ?>
	</div>

	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="view" value="filter" />
	<input type="hidden" name="layout" value="default" />
	<?php JHTML::_('form.token'); ?>
</form>