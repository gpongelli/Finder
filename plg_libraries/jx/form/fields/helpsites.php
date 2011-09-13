<?php
/**
 * @version		$Id: helpsites.php 397 2009-07-09 06:56:49Z eddieajau $
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
require_once dirname(__FILE__).DS.'list.php';

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldHelpsites extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'Helpsites';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getOptions()
	{
		jimport('joomla.language.help');
		$options	= array_merge(
						parent::_getOptions(),
						JHelp::createSiteList(JPATH_ADMINISTRATOR.DS.'help'.DS.'helpsites-15.xml', $this->value)
					);

		return $options;
	}
}