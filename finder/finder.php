<?php
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
$parts = explode(DS, dirname(__FILE__));
array_pop($parts); // Remove the current folder.
define('JPATH_BASE', implode(DS, $parts));

// Joomla framework path definitions
define('JPATH_ROOT',			JPATH_BASE);
define('JPATH_SITE',			JPATH_ROOT);
define('JPATH_CONFIGURATION',	JPATH_ROOT);
define('JPATH_THEMES',			JPATH_ROOT . '/templates');
define('JPATH_LIBRARIES',		JPATH_ROOT . '/libraries');
define('JPATH_PLUGINS',			JPATH_ROOT . '/plugins');

// Needed to deal with the JApplicationHelper::getClientInfo() hijack.
define('JPATH_ADMINISTRATOR', 	JPATH_ROOT . '/administrator');
define('JPATH_INSTALLATION',	JPATH_ROOT . '/installation');

// System Checks
@set_magic_quotes_runtime(0);
@ini_set('zend.ze1_compatibility_mode', '0');

// System includes
require_once(JPATH_LIBRARIES . '/joomla/import.php');

// Joomla! library imports
jimport('joomla.application.menu');
jimport('joomla.user.user');
jimport('joomla.environment.uri');
jimport('joomla.html.html');
jimport('joomla.utilities.utility');
jimport('joomla.event.event');
jimport('joomla.event.dispatcher');
jimport('joomla.language.language');
jimport('joomla.utilities.string');
jimport('joomla.plugin.helper');
jimport('joomla.utilities.date');
jimport('joomla.plugin.plugin');
jimport('joomla.registry.registry');

// Configure error reporting.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set error handling levels
JError::setErrorHandling(E_ALL, 'echo');

// Initialize the application.
$mainframe = &JFactory::getApplication('site');

/*
 * Handle the arguments
 */
$args = $_SERVER['argv'];

// Remove the file
array_shift($args);

// Get the command
$command = array_shift($args);

switch (strtolower($command))
{
	case 'index' :
		require ('methods/index.php');
		$testConverter = new IndexMethod();

		$rows = null;
		if (!empty($args[0]) and (strpos($args[0], '--rows=') !== false)) {
			$rows = array_shift($args);
			$rows = intval(str_replace('--rows=', '', $rows));
		}

		$testConverter->run($rows);
		break;

	case 'help' :
	default :

		$subcmd = 'main';
		if (isset($args[0]) and in_array($args[0], $commands)) {
			$subcmd = array_shift($args);
		}

		include 'help/'.$subcmd.'.txt';
		break;
}

exit(0);