<?php
/**
 * @version		$Id: close.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::_('behavior.mootools');
?>

<script type="text/javascript">
	window.parent.location.href=window.parent.location.href;
	window.parent.SqueezeBox.close();
</script>
