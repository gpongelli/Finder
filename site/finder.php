<?php
/**
 * @version		$Id: finder.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

// Load the custom language files.
$lang = JFactory::getLanguage();
$lang->load('com_finder.custom');

// Detect if we have full UTF-8 and unicode support.
define('JX_FINDER_UNICODE', (bool)@preg_match('/\pL/u', 'a'));

// Register dependent classes.
JLoader::register('FinderHelperRoute', JPATH_SITE.DS.'components'.DS.'com_finder'.DS.'helpers'.DS.'route.php');

// Instantiate and execute the requested task.
jx('jx.application.component.helper.controller');
$controller = JControllerHelper::getInstance('Finder');
$controller->execute(JRequest::getVar('task'));
$controller->redirect();