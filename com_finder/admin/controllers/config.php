<?php
/**
 * @version		$Id: config.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

/**
 * Configuration controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerConfig extends FinderController
{
	/**
	 * Method to import the configuration via string or upload.
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function import()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$string = JRequest::getVar('configString', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$file	= JRequest::getVar('configFile', array(), 'files', 'array');
		$return	= null;

		// Handle the possible import methods.
		if (!empty($file) && ($file['error'] == 0) && ($file['size'] > 0) && (is_readable($file['tmp_name'])))
		{
			// Handle import via uploaded file.
			$string = implode("\n", file($file['tmp_name']));
			$model	= &$this->getModel('Config');
			$return	= $model->import($string);
		}
		elseif (strlen($string) > 1)
		{
			// Handle import via pasted string.
			$model	= &$this->getModel('Config');
			$return	= $model->import($string);
		}

		// Handle the response.
		if ($return === false)
		{
			$message = JText::sprintf('FINDER_CONFIG_IMPORT_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=config&layout=import&tmpl=component', $message, 'notice');
			return false;
		}
		else
		{
			$this->setRedirect('index.php?option=com_finder&view=config&layout=close&tmpl=component');
			return true;
		}
	}

	/**
	 * Method to export the configuration via download.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function export()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$app	= &JFactory::getApplication();
		$config = &JComponentHelper::getParams('com_finder');
		$string	= $config->toString();

		header('Content-type: application/force-download');
	    header('Content-Transfer-Encoding: Binary');
	    header('Content-length: '.strlen($string));
	    header('Content-disposition: attachment; filename="jxfinder.config.ini"');
		header('Pragma: no-cache');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');

	    echo $string;

		$app->close();
	}

	/**
	 * Method to save the configuration.
	 *
	 * @access	public
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	function save()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		// Save the configuration.
		$model	= &$this->getModel('Config');
		$return	= $model->save();

		if ($return === false)
		{
			$message = JText::sprintf('FINDER_CONFIG_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=config&tmpl=component', $message, 'notice');
			return false;
		}
		else
		{
			$this->setRedirect('index.php?option=com_finder&view=config&layout=close&tmpl=component');
			return true;
		}
	}
}
