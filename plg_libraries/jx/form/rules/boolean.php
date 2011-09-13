<?php
/**
 * @version		$Id: boolean.php 483 2009-12-14 23:49:05Z eddieajau $
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jx('jx.form.formrule');

/**
 * Form Rule class for JXtended Libraries.
 *
 * @package		JXtended.Libraries
 * @subpackage	Form
 * @since		1.1
 */
class JFormRuleBoolean extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @var		string
	 */
	protected $_regex = '^0|1|true|false$';

	/**
	 * The regular expression modifiers.
	 *
	 * @var		string
	 */
	protected $_modifiers = 'i';
}