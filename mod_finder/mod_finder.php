<?php
/**
 * @version		$Id:mod_finder.php 80 2008-04-24 19:57:50Z rob.schley $
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

// Register dependent classes.
JLoader::register('FinderHelperRoute', JPATH_SITE.DS.'components'.DS.'com_finder'.DS.'helpers'.DS.'route.php');

// Include the helper.
require_once dirname(__FILE__).DS.'helper.php';

// Initialize module parameters.
$params->def('field_size', 20);

// Get the route.
$route = FinderHelperRoute::getSearchRoute($params->get('f', null));

require JModuleHelper::getLayoutPath('mod_finder');
