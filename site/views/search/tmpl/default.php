<?php
/**
 * @version		$Id: default.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHTML::_('behavior.mootools');
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::stylesheet('finder.css', 'components/com_finder/media/css/');

// Check if we need to show the page title.
if ($this->params->get('show_page_title', 1)):
?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php
endif;

// Display the search form if enabled.
if ($this->params->get('show_search_form', 1)):
?>
	<div id="search-form">
		<?php echo $this->loadTemplate('form'); ?>
	</div>
<?php
endif;

// Load the search results layout if we are performing a search.
if ($this->query->search === true):
?>
	<div id="search-results">
		<?php echo $this->loadTemplate('results'); ?>
	</div>
<?php
endif;