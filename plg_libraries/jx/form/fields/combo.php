<?php
/**
 * @version		$Id: combo.php 483 2009-12-14 23:49:05Z eddieajau $
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jx('jx.form.formfield');

/**
 * Form Field class for JXtended Libraries.
 *
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @since		1.1
 */
class JFormFieldCombo extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'Combo';

	/**
	 * Method to get a list of options for a combo input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getOptions()
	{
		$options = array();

		// Iterate through the children and build an array of options.
		foreach ($this->_element->children() as $option) {
			$options[] = JHtml::_('select.option', $option->attributes('value'), JText::_($option->data()));
		}

		return $options;
	}

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 * @since	1.1
	 */
	protected function _getInput()
	{
		$size		= $this->_element->attributes('size') ? ' size="'.$this->_element->attributes('size').'"' : '';
		$readonly	= $this->_element->attributes('readonly') == 'true' ? ' readonly="readonly"' : '';
		$onchange	= $this->_element->attributes('onchange') ? ' onchange="'.$this->_element->attributes('onchange').'"' : '';
		$class		= $this->_element->attributes('class') ? ' class="'.$this->_element->attributes('class').'"' : ' class="combobox"';
		$options	= $this->_getOptions();
		$return		= null;

		JHtml::_('behavior.combobox');

		// Build the input for the combo box.
		$return	.= '<input type="text" name="'.$this->inputName.'" id="'.$this->inputId.'" value="'.htmlspecialchars($this->value).'"'.$class.$size.$readonly.$onchange.' />';

		// Build the list for the combo box.
		$return	.= '<ul id="combobox-'.$this->inputId.'" style="display:none;">';
		foreach ($options as $option) {
			$return	.= '<li>'.$option->text.'</li>';
		}
		$return	.= '</ul>';

		return $return;
	}
}