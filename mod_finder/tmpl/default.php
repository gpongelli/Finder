<?php
/**
 * @version		$Id:default.php 80 2008-04-24 19:57:50Z rob.schley $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

JHtml::_('behavior.mootools');
JHtml::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_finder'.DS.'helpers'.DS.'html');

if (!defined('FINDER_PATH_INDEXER')) {
	define('FINDER_PATH_INDEXER', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_finder'.DS.'helpers'.DS.'indexer');
}
JLoader::register('FinderIndexerQuery', FINDER_PATH_INDEXER.DS.'query.php');

// Instantiate a query object.
$query = new FinderIndexerQuery(array('filter' => $params->get('f')));

$formId	= 'mod-finder-'.$module->id;
$fldId	= 'mod_finder_q'.$module->id;
$suffix = $params->get('moduleclass_sfx');
$output = '<input type="text" name="q" id="'.$fldId.'" class="inputbox" size="'.$params->get('field_size', 20).'" value="'.htmlspecialchars(JRequest::getVar('q')).'" />';
$button = '';
$label	= '';

if ($params->get('show_label', 1)) {
	$label	= '<label for="'.$fldId.'" class="finder'.$suffix.'">'
			. $params->get('alt_label', JText::_('FINDER_MOD_SEARCH_LABEL'))
			. '</label>';
}

if ($params->get('show_button', 1)) {
	$button	= '<button class="button'.$suffix.' finder'.$suffix.'" type="submit">'.JText::_('FINDER_MOD_SEARCH_BUTTON').'</button>';
}

switch ($params->get('label_pos', 'left')):
    case 'top' :
	    $label = $label.'<br />';
	    $output = $label.$output;
	    break;

    case 'bottom' :
	    $label = '<br />'.$label;
	    $output = $output.$label;
	    break;

    case 'right' :
	    $output = $output.$label;
	    break;

    case 'left' :
    default :
	    $output = $label.$output;
	    break;
endswitch;

switch ($params->get('button_pos', 'right')):
    case 'top' :
	    $button = $button.'<br />';
	    $output = $button.$output;
	    break;

    case 'bottom' :
	    $button = '<br />'.$button;
	    $output = $output.$button;
	    break;

    case 'right' :
	    $output = $output.$button;
	    break;

    case 'left' :
    default :
	    $output = $button.$output;
	    break;
endswitch;

JHtml::stylesheet('finder.css', 'components/com_finder/media/css/');
?>

<script type="text/javascript">
//<![CDATA[
	window.addEvent('domready', function() {
<?php if ($params->get('show_text', 1)): ?>
		var value;

		// Set the input value if not already set.
		if (!$('<?php echo $fldId; ?>').getProperty('value')) {
			$('<?php echo $fldId; ?>').setProperty('value', '<?php echo JText::_('FINDER_MOD_SEARCH_VALUE', true); ?>');
		}

		// Get the current value.
		value = $('<?php echo $fldId; ?>').getProperty('value');

		// If the current value equals the previous value, clear it.
		$('<?php echo $fldId; ?>').addEvent('focus', function() {
			if (this.getProperty('value') == value) {
				this.setProperty('value', '');
			}
		});

		// If the current value is empty, set the previous value.
		$('<?php echo $fldId; ?>').addEvent('blur', function() {
			if (!this.getProperty('value')) {
				this.setProperty('value', value);
			}
		});
<?php endif; ?>

		$('<?php echo $formId; ?>').addEvent('submit', function(e){
			e = new Event(e);
			e.stop();

			// Disable select boxes with no value selected.
			if ($chk($('<?php echo $formId; ?>-advanced'))) {
				$('<?php echo $formId; ?>-advanced').getElements('select').each(function(s){
					if (!s.getProperty('value')) {
						s.setProperty('disabled', 'disabled');
					}
				});
			}

			$('<?php echo $formId; ?>').submit();
		});

		/*
		 * This segment of code sets up the autocompleter.
		 */
<?php if ($params->get('show_autosuggest', 1)): ?>
	<?php if (class_exists('plgSystemMTUpgrade')): ?>
		<?php JHtml::script('autocompleter12.js', 'components/com_finder/media/js/'); ?>
		var url = '<?php echo JRoute::_('index.php?option=com_finder&task=suggestions.display&protocol=json&tmpl=component', false); ?>';
		var ModCompleter = new Autocompleter.Request.JSON($('<?php echo $fldId; ?>'), url, {'postVar': 'q'});
	<?php else: ?>
		<?php JHtml::script('autocompleter.js', 'components/com_finder/media/js/'); ?>
		var url = '<?php echo JRoute::_('index.php?option=com_finder&task=suggestions.display&protocol=json&tmpl=component', false); ?>';
		var ModCompleter = new Autocompleter.Ajax.Json($('<?php echo $fldId; ?>'), url, {'postVar': 'q'});
	<?php endif; ?>
<?php endif; ?>
	});
//]]>
</script>

<div class="finder<?php echo $suffix; ?>">
	<form id="<?php echo $formId; ?>" action="<?php echo JRoute::_($route); ?>" method="get">
		<?php
		echo modFinderHelper::_getGetFields($route);

		// Show the form fields.
		echo $output;
		?>

<?php if ($params->get('show_advanced', 1)): ?>
	<?php if ($params->get('show_advanced', 1) == 2): ?>
		<br />
		<a href="<?php echo JRoute::_($route); ?>"><?php echo JText::_('FINDER_ADVANCED_SEARCH'); ?></a>
	<?php elseif ($params->get('show_advanced', 1) == 1): ?>
		<div id="mod-finder-advanced">
			<?php echo JHTML::_('filter.select', $query, $params); ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
	</form>
</div>