<?php
/**
 * @version		$Id: finder.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

/**
 * HTML behavior class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class JHTMLFinder
{
	function footer()
	{
		JHtml::_('behavior.modal', 'a.modal');
		echo '<div id="jxfooter">';
		echo  '<a href="'.JRoute::_('index.php?option=com_finder&view=about&tmpl=component').'" class="modal" rel="{handler: \'iframe\'}">';
		echo 'JXtended Finder '.FinderVersion::VERSION.'.'.FinderVersion::SUBVERSION.':'.FinderVersion::getBuild().' '.FinderVersion::STATUS.'</a>';
		echo ' &copy; 2010 <a href="http://jxtended.com" target="_blank">JXtended LLC</a>. All rights reserved.';
		echo '</div>';
	}

	function typeslist($active)
	{
		$lang = &JFactory::getLanguage();

		// Load the finder types.
		$db = &JFactory::getDBO();
		$db->setQuery('SELECT DISTINCT t.title AS text, t.id AS value FROM #__jxfinder_types AS t' .
			' JOIN #__jxfinder_links AS l ON l.type_id = t.id' .
			' ORDER BY t.title ASC');
		$rows = $db->loadObjectList();

		// Check for database errors.
		if ($db->getErrorNum()) {
			return;
		}

		// Compile the options.
		$options	= array();
		$options[]	= JHTML::_('select.option', '0', JText::_('COM_FINDER_INDEX_TYPE_FILTER'));

		foreach ($rows as $row) {
			$key		= $lang->hasKey('COM_FINDER_TYPE_P_'.strtoupper(str_replace(' ', '_', $row->text))) ? 'FINDER_TYPE_P_'.strtoupper(str_replace(' ', '_', $row->text)) : $row->text;
			$string		= JText::sprintf('COM_FINDER_ITEM_X_ONLY', JText::_($key));
			$options[]	= JHTML::_('select.option', $row->value, $string);
		}

		return $options;
	}

	function mapslist($active, $branches = true)
	{
		$lang = &JFactory::getLanguage();

		// Load the finder types.
		$db = &JFactory::getDBO();
		$db->setQuery('SELECT title AS text, id AS value FROM #__jxfinder_taxonomy WHERE parent_id = 1 ORDER BY ordering, title ASC');
		$rows = $db->loadObjectList();

		// Check for database errors.
		if ($db->getErrorNum()) {
			return;
		}

		// Compile the options.
		$options	= array();
		$options[]	= JHTML::_('select.option', '1', JText::_('COM_FINDER_MAPS_BRANCHES'));

		foreach ($rows as $row) {
			$key		= $lang->hasKey('COM_FINDER_TYPE_P_'.strtoupper($row->text)) ? 'COM_FINDER_TYPE_P_'.strtoupper(str_replace(' ', '_', $row->text)) : $row->text;
			$string		= JText::sprintf('COM_FINDER_ITEM_X_ONLY', JText::_($key));
			$options[]	= JHTML::_('select.option', $row->value, $string);
		}

		return $options;
	}

	function statelist($active)
	{
		$options	= array();
		$options[]	= JHTML::_('select.option', '', JText::_('COM_FINDER_INDEX_FILTER_BY_STATE'));
		$options[]	= JHTML::_('select.option', '1', JText::sprintf('COM_FINDER_ITEM_X_ONLY', JText::_('JPUBLISHED')));
		$options[]	= JHTML::_('select.option', '0', JText::sprintf('COM_FINDER_ITEM_X_ONLY', JText::_('JUNPUBLISHED')));

		return $options;
	}

	/**
	 * Method to render a given parameters form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$name	The name of the array for form elements.
	 * @param	string	$ini	An INI formatted string.
	 * @param	string	$file	The XML file to render.
	 * @return	string	A HTML rendered parameters form.
	 */
	function params($name, $ini, $file)
	{
		jimport('joomla.html.parameter');

		// Load and render the parameters
		$path	= JPATH_COMPONENT.DS.$file;
		$params	= new JParameter($ini, $path);
		$output	= $params->render($name);

		return $output;
	}

	function state($i, $state, $linked = true, $controller = 'index')
	{
		$img 	= ($state > 0) ? 'tick.png' : 'publish_x.png';
		$task 	= ($state > 0) ? $controller.'.unpublish' : $controller.'.publish';
		$alt 	= ($state > 0) ? JText::_('FINDER_STATE_PUBLISHED') : JText::_('FINDER_STATE_UNPUBLISHED');
		$action = ($state > 0) ? JText::_('FINDER_INDEX_LINK_UNPUBLISH') : JText::_('FINDER_INDEX_LINK_PUBLISH');

		$html	= '';

		if ($linked === true) {
			$html	.= '<a href="#" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">';
			$html	.= '<img src="images/'. $img .'" border="0" alt="'. $alt .'" />';
			$html	.= '</a>';
		} else {
			$html	.= '<img src="images/'. $img .'" border="0" alt="'. $alt .'" />';
		}

		return $html;
	}
}
