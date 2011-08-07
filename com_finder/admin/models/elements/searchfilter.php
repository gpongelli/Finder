<?php
/**
 * @version		$Id: searchfilter.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die('Restricted access');

jx('jx.database.databasequery');

/**
 * Search Filter parameters element for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class JElementSearchFilter extends JElement
{
   /**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'SearchFilter';

	function fetchElement($name, $value, &$node, $control_name)
	{
		// Build the query.
		$query = new JDatabaseQuery();
		$query->select('f.title AS text, f.filter_id AS value');
		$query->from('#__finder_filters AS f');
		$query->where('f.state = 1');
		$query->order('f.title ASC');

		$db = &JFactory::getDBO();
		$db->setQuery($query->toString());

		$options = $db->loadObjectList();

		array_unshift($options, JHTML::_('select.option', '', JText::_('FINDER_SELECT_SEARCH_FILTER'), 'value', 'text'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
}