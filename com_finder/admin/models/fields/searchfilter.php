<?php
/**
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Search Filter field for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class JFormFieldSearchFilter extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.7
	 */
	protected $type = 'SearchFilter';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.7
	 */
	public function getOptions()
	{
		// Build the query.
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('f.title').' AS text, '.$db->quoteName('f.filter_id').' AS value');
		$query->from($db->quoteName('#__jxfinder_filters').' AS f');
		$query->where($db->quoteName('f.state').' = 1');
		$query->order('f.title ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		array_unshift($options, JHTML::_('select.option', '', JText::_('COM_FINDER_SELECT_SEARCH_FILTER'), 'value', 'text'));

		return $options;
	}
}