<?php
/**
 * @version		$Id: smartgrid.php 405 2009-07-14 01:18:28Z louis $
 * @package		JXtended.Libraries
 * @subpackage	HTML
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

JHtml::_('behavior.tooltip');

/**
 * HTML helper class for rendering extended grid widgets.
 *
 * @package 	JXtended.Libraries
 * @subpackage	HTML
 * @static
 */
class JHtmlSmartGrid
{
	/**
	 * Display a boolean setting widget.
	 *
	 * @static
	 * @param	integer	The row index.
	 * @param	integer	The value of the boolean field.
	 * @param	string	Task to turn the boolean setting on.
	 * @param	string	Task to turn the boolean setting off.
	 * @return	string	The boolean setting widget.
	 * @since	1.0
	 */
	function boolean($i, $value, $taskOn = null, $taskOff = null)
	{
		// Load the behavior.
		JHtmlSmartGrid::behavior();

		// Build the title.
		$title = ($value) ? JText::_('Yes') : JText::_('No');
		$title .= '::'.JText::_('JX_Click_To_Toggle');

		// Build the <a> tag.
		$bool	= ($value) ? 'true' : 'false';
		$task	= ($value) ? $taskOff : $taskOn;
		$toggle	= (!$task) ? false : true;

		if ($toggle) {
			$html = '<a class="grid_'.$bool.' hasTip" title="'.$title.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#toggle"></a>';
		} else {
			$html = '<a class="grid_'.$bool.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}"></a>';
		}

		return $html;
	}

	/**
	 * Display the checked out icon
	 *
	 * @param	string	The editor name
	 * @param	string	The checked out time
	 *
	 * @return	string
	 */
	function checkedout($editor, $time)
	{
		// Load the behavior.
		JHtmlSmartGrid::behavior();

		// Get the date and time.
		$date = JHtml::_('date', $time, '%A, %d %B %Y');
		$time = JHtml::_('date', $time, '%H:%M');

		$text = addslashes(htmlspecialchars($editor.'<br />'.$date.'<br />'.$time));
		$html = '<a class="checkedout hasTip" title="'.JText::_('Checked_Out').'::'.$text.'">'.JText::_('Checked_Out').'</a>';

		return $html;
	}

	/**
	 * Return the icon to move an item UP
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item up or a space
	 * @since	1.0
	 */
	function orderUpIcon($i, $n, $pagination, $task = 'orderup', $alt = 'Move Up', $enabled = true)
	{
		// Load the behavior.
		JHtmlSmartGrid::behavior();

		$alt = JText::_($alt);

		if($enabled) {
			$html = '<a class="move_up" title="'.$alt.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#move_up"></a>';
		} else {
			$html = '<span class="move_up"></span>';
		}

		return $html;
	}

	/**
	 * Return the icon to move an item DOWN
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	int		$n The number of items in the list
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item down or a space
	 * @since	1.0
	 */
	function orderDownIcon($i, $n, $pagination, $task = 'orderdown', $alt = 'Move Down', $enabled = true)
	{
		// Load the behavior.
		JHtmlSmartGrid::behavior();

		$alt = JText::_($alt);

		if($enabled) {
			$html = '<a class="move_down" title="'.$alt.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#move_down"></a>';
		} else {
			$html = '<span class="move_down"></span>';
		}

		return $html;
	}

	/**
	 * Display the published setting widget.
	 *
	 * @static
	 * @param	integer	The row index.
	 * @param	integer	The value of the published field.
	 * @param	string	Task prefix.
	 * @return	string	The published setting widget.
	 * @since	1.0
	 */
	function published($i, $value, $prefix = '')
	{
		// Load the behavior.
		JHtmlSmartGrid::behavior();

		// Initialize variables based on value.
		switch ($value)
		{
			case -2:
				$state = 'trash';
				$task = $prefix.'unpublish';
				$title	= JText::_('Trash');
				break;

			case 0:
				$state = 'false';
				$task = $prefix.'publish';
				$title	= JText::_('Unpublished');
				break;

			case 1:
				$state = 'true';
				$task = $prefix.'unpublish';
				$title	= JText::_('Published');
				break;
		}

		// Build the title.
		$title .= '::'.JText::_('JX_Click_To_Toggle');

		// Build the <a> tag.
		$html = '<a class="grid_'.$state.' hasTip" title="'.$title.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#toggle"></a>';

		return $html;
	}

	function behavior()
	{
		static $loaded;

		if (!$loaded)
		{
			// Build the behavior script.
			$js = '
		window.addEvent(\'domready\', function(){
			actions = $$(\'a.move_up\');
			actions.merge($$(\'a.move_down\'));
			actions.merge($$(\'a.grid_true\'));
			actions.merge($$(\'a.grid_false\'));
			actions.merge($$(\'a.grid_trash\'));
			actions.each(function(a){
				a.addEvent(\'click\', function(){
					args = Json.evaluate(this.rel);
					listItemTask(args.id, args.task);
				});
			});
			$$(\'input.check-all-toggle\').each(function(el){
				el.addEvent(\'click\', function(){
					if (el.checked) {
						$(this.form).getElements(\'input[type=checkbox]\').each(function(i){
							i.checked = true;
						})
					}
					else {
						$(this.form).getElements(\'input[type=checkbox]\').each(function(i){
							i.checked = false;
						})
					}
				});
			});
		});';

			// Add the behavior to the document head.
			$document = & JFactory::getDocument();
			$document->addScriptDeclaration($js);

			$loaded = true;
		}
	}
}
