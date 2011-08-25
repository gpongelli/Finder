<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

// Detect if we have full UTF-8 and unicode support.
define('JX_FINDER_UNICODE', (bool)@preg_match('/\pL/u', 'a'));

require_once JPATH_COMPONENT.'/helpers/route.php';

// Execute the task.
$controller = JController::getInstance('Finder');
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
