<?php
/**
 * @version		$Id: hidden.php 462 2009-09-23 18:49:29Z louis $
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die('Restricted Access');

jx('jx.form.formfield');

/**
 * Form Field class for JXtended Libraries.
 *
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @since		1.1
 */
class JFormFieldHidden extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Hidden';

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	protected function _getInput()
	{
		$class	= $this->_element->attributes('class') ? 'class="'.$this->_element->attributes('class').'"' : '';

		return '<input type="hidden" name="'.$this->inputName.'" value="'.htmlspecialchars($this->value).'" '.$class.' />';
	}
}