<?php
/**
 * @version		$Id:default_form.php 80 2008-04-24 19:57:50Z rob.schley $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;
?>

<script type="text/javascript">
//<![CDATA[
	window.addEvent('domready', function() {
<?php if ($this->params->get('show_advanced', 1)): ?>
		/*
		 * This segment of code adds the slide effect to the advanced search box.
		 */
		if ($chk($('advanced-search'))) {
			var searchSlider = new Fx.Slide('advanced-search');

			<?php if (!$this->params->get('expand_advanced', 0)): ?>
			searchSlider.hide();
			<?php endif; ?>

			$('advanced-search-toggle').addEvent('click', function(e) {
				e = new Event(e);
				e.stop();
				searchSlider.toggle();
			});
		}

		/*
		 * This segment of code disables select boxes that have no value when the
		 * form is submitted so that the URL doesn't get blown up with null values.
		 */
		if ($chk($('finder-search'))) {
			$('finder-search').addEvent('submit', function(e){
				e = new Event(e);
				e.stop();

				if ($chk($('advanced-search'))) {
					// Disable select boxes with no value selected.
					$('advanced-search').getElements('select').each(function(s){
						if (!s.getProperty('value')) {
							s.setProperty('disabled', 'disabled');
						}
					});
				}

				$('finder-search').submit();
			});
		}
<?php endif; ?>
		/*
		 * This segment of code sets up the autocompleter.
		 */
<?php if ($this->params->get('show_autosuggest', 1)): ?>
	<?php JHtml::script('components/com_finder/media/js/autocompleter.js', false, false); ?>
	var url = '<?php echo JRoute::_('index.php?option=com_finder&task=suggestions.display&protocol=json&tmpl=component', false); ?>';
	var completer = new Autocompleter.Request.JSON($('q'), url, {'postVar': 'q'});
<?php endif; ?>
	});
//]]>
</script>

<form id="finder-search" action="<?php echo JRoute::_($this->query->toURI()); ?>" method="get">
	<?php echo $this->_getGetFields(); ?>

	<?php
	/*
	 * DISABLED UNTIL WEIRD VALUES CAN BE TRACKED DOWN.
	 */
	if (false && $this->state->get('list.ordering') !== 'relevance_dsc'): ?>
		<input type="hidden" name="o" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" />
	<?php endif; ?>

	<input type="text" name="q" id="q" size="50" value="<?php echo $this->escape($this->query->input); ?>" />

	<button class="button" type="submit"><?php echo JText::_('FINDER_SEARCH_BUTTON'); ?></button>

	<?php if ($this->params->get('show_advanced', 1)): ?>
		<br />
		<a id="advanced-search-toggle"><?php echo JText::_('FINDER_ADVANCED_SEARCH_TOGGLE'); ?></a>

		<div id="advanced-search">
			<?php if ($this->params->get('show_advanced_tips', 1)): ?>
				<div class="advanced-search-tip">
					<?php echo JText::_('FINDER_ADVANCED_TIPS'); ?>
				</div>
			<?php endif; ?>
			<div id="finder-filter-window">
				<?php echo JHTML::_('filter.select', $this->query, $this->params); ?>
			</div>
		</div>
	<?php endif; ?>
</form>