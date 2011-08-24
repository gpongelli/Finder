<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Register dependent classes.
JLoader::register('FinderHelperRoute', JPATH_SITE.'/components/com_finder/helpers/route.php');

// Include the helper.
require_once dirname(__FILE__).'/helper.php';

// Initialize module parameters.
$params->def('field_size', 20);

// Get the route.
$route = FinderHelperRoute::getSearchRoute($params->get('f', null));

require JModuleHelper::getLayoutPath('mod_finder');
