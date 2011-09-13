<?php
/**
 * @version		$Id: formrule.php 483 2009-12-14 23:49:05Z eddieajau $
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Detect if we have full UTF-8 and unicode support.
if (!defined('JXFORM_UNICODE')) {
	define('JXFORM_UNICODE', (bool)@preg_match('/\pL/u', 'a'));
}

/**
 * Form Rule class for JXtended Libraries.
 *
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @since		1.1
 */
class JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @var		string
	 */
	protected $_regex;

	/**
	 * The regular expression modifiers.
	 *
	 * @var		string
	 */
	protected $_modifiers;

	/**
	 * Method to test the value.
	 *
	 * @param	object		$field		A reference to the form field.
	 * @param	mixed		$values		The values to test for validiaty.
	 * @return	boolean		True if the value is valid, false otherwise.
	 * @throws	JException on invalid rule.
	 */
	public function test(&$field, &$values)
	{
		$return = false;
		$name	= $field->attributes('name');

		// Check for a valid regex.
		if (empty($this->_regex)) {
			throw new JException('Invalid Form Rule :: '.get_class($this));
		}

		// Add unicode support if available.
		if (JXFORM_UNICODE) {
			$this->_modifiers = strpos($this->_modifiers, 'u') ? $this->_modifiers : $this->_modifiers.'u';
		}

		// Test the value against the regular expression.
		if (preg_match('#'.$this->_regex.'#'.$this->_modifiers, $values[$name])) {
			$return = true;
		}

		return $return;
	}
}