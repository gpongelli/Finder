<?php
/**
 * @version		$Id: finder.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

// Detect if we have full UTF-8 and unicode support.
define('JX_FINDER_UNICODE', (bool)@preg_match('/\pL/u', 'a'));

// Import the component version class.
require_once(dirname(__FILE__).'/version.php');

// Execute the task.
$controller	= JController::getInstance('Finder');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
