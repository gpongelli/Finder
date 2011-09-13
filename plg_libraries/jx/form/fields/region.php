<?php
/**
 * @version		$Id: region.php 462 2009-09-23 18:49:29Z louis $
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
class JFormFieldRegion extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @access	public
	 * @var		string
	 * @since	1.1
	 */
	var	$type = 'Region';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @access	protected
	 * @return	array		An array of JHtml options.
	 * @since	1.1
	 */
	function _getOptions()
	{
		$options	= array();

		// If allow none is an option, use it.
		if ($this->_element->attributes('allow_none')) {
			$options[] = JHtml::_('select.option', '', 'None');
		}

		if ($this->_element->attributes('show_groups')) {
			$options[] = JHtml::_('select.optgroup', 'United States');
		}

		$options[]	= JHtml::_('select.option', 'AL', 'Alabama');
		$options[]	= JHtml::_('select.option', 'AK', 'Alaska');
		$options[]	= JHtml::_('select.option', 'AZ', 'Arizona');
		$options[]	= JHtml::_('select.option', 'AR', 'Arkansas');
		$options[]	= JHtml::_('select.option', 'CA', 'California');
		$options[]	= JHtml::_('select.option', 'CO', 'Colorado');
		$options[]	= JHtml::_('select.option', 'CT', 'Connecticut');
		$options[]	= JHtml::_('select.option', 'DE', 'Delaware');
		$options[]	= JHtml::_('select.option', 'FL', 'Florida');
		$options[]	= JHtml::_('select.option', 'GA', 'Georgia');
		$options[]	= JHtml::_('select.option', 'HI', 'Hawaii');
		$options[]	= JHtml::_('select.option', 'ID', 'Idaho');
		$options[]	= JHtml::_('select.option', 'IL', 'Illinois');
		$options[]	= JHtml::_('select.option', 'IN', 'Indiana');
		$options[]	= JHtml::_('select.option', 'IA', 'Iowa');
		$options[]	= JHtml::_('select.option', 'KS', 'Kansas');
		$options[]	= JHtml::_('select.option', 'KY', 'Kentucky');
		$options[]	= JHtml::_('select.option', 'LA', 'Louisiana');
		$options[]	= JHtml::_('select.option', 'ME', 'Maine');
		$options[]	= JHtml::_('select.option', 'MD', 'Maryland');
		$options[]	= JHtml::_('select.option', 'MA', 'Massachusetts');
		$options[]	= JHtml::_('select.option', 'MI', 'Michigan');
		$options[]	= JHtml::_('select.option', 'MN', 'Minnesota');
		$options[]	= JHtml::_('select.option', 'MS', 'Mississippi');
		$options[]	= JHtml::_('select.option', 'MO', 'Missouri');
		$options[]	= JHtml::_('select.option', 'MT', 'Montana');
		$options[]	= JHtml::_('select.option', 'NE', 'Nebraska');
		$options[]	= JHtml::_('select.option', 'NV', 'Nevada');
		$options[]	= JHtml::_('select.option', 'NH', 'New Hampshire');
		$options[]	= JHtml::_('select.option', 'NJ', 'New Jersey');
		$options[]	= JHtml::_('select.option', 'NM', 'New Mexico');
		$options[]	= JHtml::_('select.option', 'NY', 'New York');
		$options[]	= JHtml::_('select.option', 'NC', 'North Carolina');
		$options[]	= JHtml::_('select.option', 'ND', 'North Dakota');
		$options[]	= JHtml::_('select.option', 'OH', 'Ohio');
		$options[]	= JHtml::_('select.option', 'OK', 'Oklahoma');
		$options[]	= JHtml::_('select.option', 'OR', 'Oregon');
		$options[]	= JHtml::_('select.option', 'PA', 'Pennsylvania');
		$options[]	= JHtml::_('select.option', 'RI', 'Rhode Island');
		$options[]	= JHtml::_('select.option', 'SC', 'South Carolina');
		$options[]	= JHtml::_('select.option', 'SD', 'South Dakota');
		$options[]	= JHtml::_('select.option', 'TN', 'Tennessee');
		$options[]	= JHtml::_('select.option', 'TX', 'Texas');
		$options[]	= JHtml::_('select.option', 'UT', 'Utah');
		$options[]	= JHtml::_('select.option', 'VT', 'Vermont');
		$options[]	= JHtml::_('select.option', 'VA', 'Virginia');
		$options[]	= JHtml::_('select.option', 'WA', 'Washington');
		$options[]	= JHtml::_('select.option', 'DC', 'Washington D.C.');
		$options[]	= JHtml::_('select.option', 'WV', 'West Virginia');
		$options[]	= JHtml::_('select.option', 'WI', 'Wisconsin');
		$options[]	= JHtml::_('select.option', 'WY', 'Wyoming');

		if ($this->_element->attributes('show_groups')) {
			$options[] = JHtml::_('select.optgroup', 'Canada');
		}

		$options[]	= JHtml::_('select.option', 'AB', 'Alberta');
		$options[]	= JHtml::_('select.option', 'BC', 'British Columbia');
		$options[]	= JHtml::_('select.option', 'MB', 'Manitoba');
		$options[]	= JHtml::_('select.option', 'NB', 'New Brunswick');
		$options[]	= JHtml::_('select.option', 'NF', 'Newfoundland');
		$options[]	= JHtml::_('select.option', 'NT', 'Northwest Territories');
		$options[]	= JHtml::_('select.option', 'NS', 'Nova Scotia');
		$options[]	= JHtml::_('select.option', 'NU', 'Nunavut');
		$options[]	= JHtml::_('select.option', 'ON', 'Ontario');
		$options[]	= JHtml::_('select.option', 'PE', 'Prince Edward Island');
		$options[]	= JHtml::_('select.option', 'QC', 'Quebec');
		$options[]	= JHtml::_('select.option', 'SK', 'Saskatchewan');
		$options[]	= JHtml::_('select.option', 'YT', 'Yukon');

		if ($this->_element->attributes('show_groups')) {
			$options[] = JHtml::_('select.optgroup', 'US Military');
		}

		$options[]	= JHtml::_('select.option', 'AE', 'Military AE');
		$options[]	= JHtml::_('select.option', 'AP', 'Military AP');
		$options[]	= JHtml::_('select.option', 'AA', 'Military AA');

		return $options;
	}
}