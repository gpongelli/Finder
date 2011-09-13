<?php
/**
 * @version		$Id: usergroups.php 397 2009-07-09 06:56:49Z eddieajau $
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die('Restricted Access');

jimport('joomla.html.html');
jx('jx.form.fields.list');

/**
 * Form Field class for JXtended Libraries.
 *
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @since		1.1
 */
class JFormFieldAccess extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'UserGroups';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getOptions()
	{
		// Get a database object.
		$db = JFactory::getDbo();

		// Get the user groups from the database.
		$db->setQuery(
			'SELECT a.id AS value, a.name AS text' .
			' FROM #__groups AS a' .
			' ORDER BY a.id ASC'
		);
		$options = $db->loadObjectList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::_getOptions(), $options);

		return $options;
	}
}