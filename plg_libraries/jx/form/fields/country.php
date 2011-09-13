<?php
/**
 * @version		$Id: country.php 462 2009-09-23 18:49:29Z louis $
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
class JFormFieldCountry extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @access	public
	 * @var		string
	 * @since	1.1
	 */
	var	$type = 'Country';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @access	protected
	 * @return	array		An array of JHtml options.
	 * @since	1.1
	 */
	function _getOptions()
	{
		// Initialize variables.
		$options = array();

		// If allow none is an option, use it.
		if ($this->_element->attributes('allow_none')) {
			$options[] = JHtml::_('select.option', '', 'None');
		}

		// Build available countries array.
		$options[] = JHtml::_('select.option', 'US', 'United States');
		$options[] = JHtml::_('select.option', 'AF', 'Afghanistan');
		$options[] = JHtml::_('select.option', 'AL', 'Albania');
		$options[] = JHtml::_('select.option', 'DZ', 'Algeria');
		$options[] = JHtml::_('select.option', 'AS', 'American Samoa');
		$options[] = JHtml::_('select.option', 'AD', 'Andorra');
		$options[] = JHtml::_('select.option', 'AO', 'Angola');
		$options[] = JHtml::_('select.option', 'AI', 'Anguilla');
		$options[] = JHtml::_('select.option', 'AU', 'Antarctica');
		$options[] = JHtml::_('select.option', 'AG', 'Antigua and Barbuda');
		$options[] = JHtml::_('select.option', 'AR', 'Argentina');
		$options[] = JHtml::_('select.option', 'AM', 'Armenia');
		$options[] = JHtml::_('select.option', 'AW', 'Aruba');
		$options[] = JHtml::_('select.option', 'AT', 'Austria');
		$options[] = JHtml::_('select.option', 'AU', 'Australia');
		$options[] = JHtml::_('select.option', 'AX', 'Åland Islands');
		$options[] = JHtml::_('select.option', 'AZ', 'Azerbaijan');
		$options[] = JHtml::_('select.option', 'BS', 'Bahamas');
		$options[] = JHtml::_('select.option', 'BH', 'Bahrain');
		$options[] = JHtml::_('select.option', 'BD', 'Bangladesh');
		$options[] = JHtml::_('select.option', 'BB', 'Barbados');
		$options[] = JHtml::_('select.option', 'BY', 'Belarus');
		$options[] = JHtml::_('select.option', 'BE', 'Belgium');
		$options[] = JHtml::_('select.option', 'BZ', 'Belize');
		$options[] = JHtml::_('select.option', 'BJ', 'Benin');
		$options[] = JHtml::_('select.option', 'BM', 'Bermuda');
		$options[] = JHtml::_('select.option', 'BT', 'Bhutan');
		$options[] = JHtml::_('select.option', 'BO', 'Bolivia');
		$options[] = JHtml::_('select.option', 'BA', 'Bosnia and Herzegovena');
		$options[] = JHtml::_('select.option', 'BW', 'Botswana');
		$options[] = JHtml::_('select.option', 'BV', 'Bouvet Island');
		$options[] = JHtml::_('select.option', 'BR', 'Brazil');
		$options[] = JHtml::_('select.option', 'IO', 'British Indian Ocean Territory');
		$options[] = JHtml::_('select.option', 'BN', 'Brunei Darussalam');
		$options[] = JHtml::_('select.option', 'BG', 'Bulgaria');
		$options[] = JHtml::_('select.option', 'BF', 'Burkina Faso');
		$options[] = JHtml::_('select.option', 'BI', 'Burundi');
		$options[] = JHtml::_('select.option', 'CA', 'Canada');
		$options[] = JHtml::_('select.option', 'KH', 'Cambodia');
		$options[] = JHtml::_('select.option', 'CM', 'Cameroon');
		$options[] = JHtml::_('select.option', 'CV', 'Cape Verde');
		$options[] = JHtml::_('select.option', 'KY', 'Cayman Islands');
		$options[] = JHtml::_('select.option', 'CF', 'Central African Republic');
		$options[] = JHtml::_('select.option', 'TD', 'Chad');
		$options[] = JHtml::_('select.option', 'CL', 'Chile');
		$options[] = JHtml::_('select.option', 'CN', 'China');
		$options[] = JHtml::_('select.option', 'CX', 'Christmas Island');
		$options[] = JHtml::_('select.option', 'CC', 'Cocos (Keeling) Islands');
		$options[] = JHtml::_('select.option', 'CO', 'Columbia');
		$options[] = JHtml::_('select.option', 'KM', 'Comoros');
		$options[] = JHtml::_('select.option', 'CG', 'Congo');
		$options[] = JHtml::_('select.option', 'CD', 'Congo, the Democratic Republic of the');
		$options[] = JHtml::_('select.option', 'CK', 'Cook Islands');
		$options[] = JHtml::_('select.option', 'CR', 'Costa Rica');
		$options[] = JHtml::_('select.option', 'CI', 'Côte d\'Ivoire');
		$options[] = JHtml::_('select.option', 'HR', 'Croatia');
		$options[] = JHtml::_('select.option', 'CU', 'Cuba');
		$options[] = JHtml::_('select.option', 'CY', 'Cyprus');
		$options[] = JHtml::_('select.option', 'CZ', 'Czech Republic');
		$options[] = JHtml::_('select.option', 'DK', 'Denmark');
		$options[] = JHtml::_('select.option', 'DJ', 'Djibouti');
		$options[] = JHtml::_('select.option', 'DM', 'Dominica');
		$options[] = JHtml::_('select.option', 'DO', 'Dominican Republic');
		$options[] = JHtml::_('select.option', 'EC', 'Ecuador');
		$options[] = JHtml::_('select.option', 'EG', 'Egypt');
		$options[] = JHtml::_('select.option', 'GQ', 'Equatorial Guinea');
		$options[] = JHtml::_('select.option', 'SV', 'El Salvador');
		$options[] = JHtml::_('select.option', 'ER', 'Eritrea');
		$options[] = JHtml::_('select.option', 'EE', 'Estonia');
		$options[] = JHtml::_('select.option', 'ET', 'Ethiopia');
		$options[] = JHtml::_('select.option', 'FK', 'Falkland Islands (Malvinas)');
		$options[] = JHtml::_('select.option', 'FO', 'Faroe Islands');
		$options[] = JHtml::_('select.option', 'FJ', 'Fiji');
		$options[] = JHtml::_('select.option', 'FI', 'Finland');
		$options[] = JHtml::_('select.option', 'FR', 'France');
		$options[] = JHtml::_('select.option', 'GF', 'French Guiana');
		$options[] = JHtml::_('select.option', 'TF', 'French Southern Territories');
		$options[] = JHtml::_('select.option', 'PF', 'French Polynesia');
		$options[] = JHtml::_('select.option', 'GA', 'Gabon');
		$options[] = JHtml::_('select.option', 'GM', 'Gambia');
		$options[] = JHtml::_('select.option', 'GE', 'Georgia');
		$options[] = JHtml::_('select.option', 'DE', 'Germany');
		$options[] = JHtml::_('select.option', 'GH', 'Ghana');
		$options[] = JHtml::_('select.option', 'GI', 'Gibraltar');
		$options[] = JHtml::_('select.option', 'GR', 'Greece');
		$options[] = JHtml::_('select.option', 'GL', 'Greenland');
		$options[] = JHtml::_('select.option', 'GD', 'Grenada');
		$options[] = JHtml::_('select.option', 'GP', 'Guadeloupe');
		$options[] = JHtml::_('select.option', 'GU', 'Guam');
		$options[] = JHtml::_('select.option', 'GT', 'Guatemala');
		$options[] = JHtml::_('select.option', 'GG', 'Guernsey');
		$options[] = JHtml::_('select.option', 'GN', 'Guinea');
		$options[] = JHtml::_('select.option', 'GW', 'Guinea-Bissau');
		$options[] = JHtml::_('select.option', 'GY', 'Guyana');
		$options[] = JHtml::_('select.option', 'HT', 'Haiti');
		$options[] = JHtml::_('select.option', 'HM', 'Heard Island and McDonald Islands');
		$options[] = JHtml::_('select.option', 'HN', 'Honduras');
		$options[] = JHtml::_('select.option', 'HK', 'Hong Kong');
		$options[] = JHtml::_('select.option', 'HU', 'Hungary');
		$options[] = JHtml::_('select.option', 'IS', 'Iceland');
		$options[] = JHtml::_('select.option', 'IN', 'India');
		$options[] = JHtml::_('select.option', 'ID', 'Indonesia');
		$options[] = JHtml::_('select.option', 'IE', 'Ireland');
		$options[] = JHtml::_('select.option', 'IM', 'Isle of Man');
		$options[] = JHtml::_('select.option', 'IL', 'Israel');
		$options[] = JHtml::_('select.option', 'IR', 'Iran');
		$options[] = JHtml::_('select.option', 'IQ', 'Iraq');
		$options[] = JHtml::_('select.option', 'IT', 'Italy');
		$options[] = JHtml::_('select.option', 'JM', 'Jamaica');
		$options[] = JHtml::_('select.option', 'JP', 'Japan');
		$options[] = JHtml::_('select.option', 'JE', 'Jersey');
		$options[] = JHtml::_('select.option', 'JO', 'Jordan');
		$options[] = JHtml::_('select.option', 'KZ', 'Kazakhstan');
		$options[] = JHtml::_('select.option', 'KE', 'Kenya');
		$options[] = JHtml::_('select.option', 'KI', 'Kiribati');
		$options[] = JHtml::_('select.option', 'KW', 'Kuwait');
		$options[] = JHtml::_('select.option', 'KG', 'Kyrgyzstan');
		$options[] = JHtml::_('select.option', 'LA', 'Laos');
		$options[] = JHtml::_('select.option', 'LV', 'Latvia');
		$options[] = JHtml::_('select.option', 'LB', 'Lebanon');
		$options[] = JHtml::_('select.option', 'LI', 'Leichtenstein');
		$options[] = JHtml::_('select.option', 'LS', 'Lesotho');
		$options[] = JHtml::_('select.option', 'LR', 'Liberia');
		$options[] = JHtml::_('select.option', 'LY', 'Libya');
		$options[] = JHtml::_('select.option', 'LT', 'Lithuania');
		$options[] = JHtml::_('select.option', 'LU', 'Luxembourg');
		$options[] = JHtml::_('select.option', 'MO', 'Macao');
		$options[] = JHtml::_('select.option', 'MK', 'Macedonia');
		$options[] = JHtml::_('select.option', 'MG', 'Madagascar');
		$options[] = JHtml::_('select.option', 'MW', 'Malawi');
		$options[] = JHtml::_('select.option', 'MY', 'Malaysia');
		$options[] = JHtml::_('select.option', 'MV', 'Maldives');
		$options[] = JHtml::_('select.option', 'ML', 'Mali');
		$options[] = JHtml::_('select.option', 'MT', 'Malta');
		$options[] = JHtml::_('select.option', 'MH', 'Marshall Islands');
		$options[] = JHtml::_('select.option', 'MQ', 'Martinique');
		$options[] = JHtml::_('select.option', 'MR', 'Mauritania');
		$options[] = JHtml::_('select.option', 'MU', 'Mauritius');
		$options[] = JHtml::_('select.option', 'YT', 'Mayotte');
		$options[] = JHtml::_('select.option', 'MX', 'Mexico');
		$options[] = JHtml::_('select.option', 'FM', 'Micronesia, Federated States of');
		$options[] = JHtml::_('select.option', 'MD', 'Moldova');
		$options[] = JHtml::_('select.option', 'MC', 'Monaco');
		$options[] = JHtml::_('select.option', 'MN', 'Mongolia');
		$options[] = JHtml::_('select.option', 'ME', 'Montenegro');
		$options[] = JHtml::_('select.option', 'MS', 'Montserrat');
		$options[] = JHtml::_('select.option', 'MA', 'Morocco');
		$options[] = JHtml::_('select.option', 'MZ', 'Mozambique');
		$options[] = JHtml::_('select.option', 'MM', 'Myanmar');
		$options[] = JHtml::_('select.option', 'NA', 'Namibia');
		$options[] = JHtml::_('select.option', 'NR', 'Nauru');
		$options[] = JHtml::_('select.option', 'NP', 'Nepal');
		$options[] = JHtml::_('select.option', 'NL', 'Netherlands');
		$options[] = JHtml::_('select.option', 'AN', 'Netherlands Antilles');
		$options[] = JHtml::_('select.option', 'NC', 'New Caledonia');
		$options[] = JHtml::_('select.option', 'NZ', 'New Zealand');
		$options[] = JHtml::_('select.option', 'NI', 'Nicaragua');
		$options[] = JHtml::_('select.option', 'NE', 'Niger');
		$options[] = JHtml::_('select.option', 'NG', 'Nigeria');
		$options[] = JHtml::_('select.option', 'NU', 'Niue');
		$options[] = JHtml::_('select.option', 'NF', 'Norfolk Island');
		$options[] = JHtml::_('select.option', 'KP', 'North Korea');
		$options[] = JHtml::_('select.option', 'MP', 'Northern Mariana Islands');
		$options[] = JHtml::_('select.option', 'NO', 'Norway');
		$options[] = JHtml::_('select.option', 'OM', 'Oman');
		$options[] = JHtml::_('select.option', 'PK', 'Pakistan');
		$options[] = JHtml::_('select.option', 'PW', 'Palau');
		$options[] = JHtml::_('select.option', 'PS', 'Palestinian Territory');
		$options[] = JHtml::_('select.option', 'PA', 'Panama');
		$options[] = JHtml::_('select.option', 'PG', 'Papua New Guinea');
		$options[] = JHtml::_('select.option', 'PY', 'Paraguay');
		$options[] = JHtml::_('select.option', 'PE', 'Peru');
		$options[] = JHtml::_('select.option', 'PH', 'Philippines');
		$options[] = JHtml::_('select.option', 'PN', 'Pitcairn');
		$options[] = JHtml::_('select.option', 'PL', 'Poland');
		$options[] = JHtml::_('select.option', 'PT', 'Portugal');
		$options[] = JHtml::_('select.option', 'PR', 'Puerto Rico');
		$options[] = JHtml::_('select.option', 'QA', 'Qatar');
		$options[] = JHtml::_('select.option', 'RE', 'Réunion');
		$options[] = JHtml::_('select.option', 'RO', 'Romania');
		$options[] = JHtml::_('select.option', 'RU', 'Russian Federation');
		$options[] = JHtml::_('select.option', 'RW', 'Rwanda');
		$options[] = JHtml::_('select.option', 'BL', 'Saint Barthélemy');
		$options[] = JHtml::_('select.option', 'SH', 'Saint Helena');
		$options[] = JHtml::_('select.option', 'KN', 'Saint Kitts and Nevis');
		$options[] = JHtml::_('select.option', 'LC', 'Saint Lucia');
		$options[] = JHtml::_('select.option', 'MF', 'Saint Martin');
		$options[] = JHtml::_('select.option', 'PM', 'Saint Pierre and Miquelon');
		$options[] = JHtml::_('select.option', 'VC', 'Saint Vincent and the Grenadines');
		$options[] = JHtml::_('select.option', 'WS', 'Samoa');
		$options[] = JHtml::_('select.option', 'SM', 'San Marino');
		$options[] = JHtml::_('select.option', 'ST', 'Sau Tome and Principe');
		$options[] = JHtml::_('select.option', 'SA', 'Saudi Arabia');
		$options[] = JHtml::_('select.option', 'SN', 'Senegal');
		$options[] = JHtml::_('select.option', 'RS', 'Serbia');
		$options[] = JHtml::_('select.option', 'SC', 'Seychelles');
		$options[] = JHtml::_('select.option', 'SL', 'Sierra Leone');
		$options[] = JHtml::_('select.option', 'SG', 'Singapore');
		$options[] = JHtml::_('select.option', 'SK', 'Slovakia');
		$options[] = JHtml::_('select.option', 'SI', 'Slovenia');
		$options[] = JHtml::_('select.option', 'SB', 'Solomon Islands');
		$options[] = JHtml::_('select.option', 'SO', 'Somalia');
		$options[] = JHtml::_('select.option', 'ZA', 'South Africa');
		$options[] = JHtml::_('select.option', 'GS', 'South Georgia and the South Sandwich Islands');
		$options[] = JHtml::_('select.option', 'KR', 'South Korea');
		$options[] = JHtml::_('select.option', 'ES', 'Spain');
		$options[] = JHtml::_('select.option', 'LK', 'Sri Lanka');
		$options[] = JHtml::_('select.option', 'SD', 'Sudan');
		$options[] = JHtml::_('select.option', 'SJ', 'Svalbard and Jan Mayen');
		$options[] = JHtml::_('select.option', 'SE', 'Sweden');
		$options[] = JHtml::_('select.option', 'SR', 'Suriname');
		$options[] = JHtml::_('select.option', 'SZ', 'Swaziland');
		$options[] = JHtml::_('select.option', 'CH', 'Switzerland');
		$options[] = JHtml::_('select.option', 'SY', 'Syria');
		$options[] = JHtml::_('select.option', 'TW', 'Taiwan');
		$options[] = JHtml::_('select.option', 'TJ', 'Tajikstan');
		$options[] = JHtml::_('select.option', 'TZ', 'Tanzania');
		$options[] = JHtml::_('select.option', 'TH', 'Thailand');
		$options[] = JHtml::_('select.option', 'TL', 'Timor-Leste');
		$options[] = JHtml::_('select.option', 'TG', 'Togo');
		$options[] = JHtml::_('select.option', 'TO', 'Tonga');
		$options[] = JHtml::_('select.option', 'TK', 'Tokelau');
		$options[] = JHtml::_('select.option', 'TT', 'Trinidad and Tobago');
		$options[] = JHtml::_('select.option', 'TN', 'Tunesia');
		$options[] = JHtml::_('select.option', 'TR', 'Turkey');
		$options[] = JHtml::_('select.option', 'TM', 'Turkmenistan');
		$options[] = JHtml::_('select.option', 'TC', 'Turks and Caicos Islands');
		$options[] = JHtml::_('select.option', 'TV', 'Tuvalu');
		$options[] = JHtml::_('select.option', 'UG', 'Uganda');
		$options[] = JHtml::_('select.option', 'UA', 'Ukraine');
		$options[] = JHtml::_('select.option', 'AE', 'United Arab Emirates');
		$options[] = JHtml::_('select.option', 'GB', 'United Kingdom');
		$options[] = JHtml::_('select.option', 'UM', 'United States Minor Outlying Islands');
		$options[] = JHtml::_('select.option', 'UY', 'Uruguay');
		$options[] = JHtml::_('select.option', 'UZ', 'Uzbekistan');
		$options[] = JHtml::_('select.option', 'VU', 'Vanuatu');
		$options[] = JHtml::_('select.option', 'VA', 'Vatican City State (Holy See)');
		$options[] = JHtml::_('select.option', 'VE', 'Venezuela');
		$options[] = JHtml::_('select.option', 'VN', 'Viet Nam');
		$options[] = JHtml::_('select.option', 'VG', 'Virgin Islands, British');
		$options[] = JHtml::_('select.option', 'VI', 'Virgin Islands, United States');
		$options[] = JHtml::_('select.option', 'WF', 'Wallace and Futuna');
		$options[] = JHtml::_('select.option', 'EH', 'Western Sahara');
		$options[] = JHtml::_('select.option', 'YE', 'Yemen');
		$options[] = JHtml::_('select.option', 'ZM', 'Zambia');
		$options[] = JHtml::_('select.option', 'ZW', 'Zimbabwe');

		return $options;
	}
}