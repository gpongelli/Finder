<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Require com_config component controller
require_once JPATH_ADMINISTRATOR.'/components/com_config/controllers/component.php';

/**
 * Configuration controller class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class FinderControllerConfig extends ConfigControllerComponent
{
	/**
	 * Class Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name.
	 * @param   string  $prefix  The class prefix.
	 *
	 * @return  object  The model.
	 *
	 * @since   2.5
	 */
	public function &getModel($name = 'Config', $prefix = 'FinderModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Method to import the configuration via string or upload.
	 *
	 * @return  bool  True on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function import()
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$string = JRequest::getVar('configString', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$file	= JRequest::getVar('configFile', array(), 'files', 'array');
		$return	= null;

		// Handle the possible import methods.
		if (!empty($file) && ($file['error'] == 0) && ($file['size'] > 0) && (is_readable($file['tmp_name'])))
		{
			// Handle import via uploaded file.
			$string = implode("\n", file($file['tmp_name']));
			$model	= &$this->getModel();
			$return	= $model->import($string);
		}
		else if (strlen($string) > 1)
		{
			// Handle import via pasted string.
			$model	= &$this->getModel();
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
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function export()
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

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
	 * Save the configuration.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   2.5
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set FTP credentials, if given.
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel();
		$form	= $model->getForm();
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$id		= JRequest::getInt('id');
		$option	= JRequest::getCmd('component');

		// Check if the user is authorized to do this.
		if (!JFactory::getUser()->authorise('core.admin', $option))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i]))
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_finder&view=config&tmpl=component', false));
			return false;
		}

		// Attempt to save the configuration.
		$data	= array(
					'params'	=> $return,
					'id'		=> $id,
					'option'	=> $option
					);
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = JText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=config&tmpl=component', $message, 'error');
			return false;
		}

		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$message = JText::_('COM_CONFIG_SAVE_SUCCESS');
				$this->setRedirect('index.php?option=com_finder&view=config&tmpl=component&refresh=1', $message);
				break;

			case 'save':
			default:
				$this->setRedirect('index.php?option=com_finder&view=config&layout=close&tmpl=component');
				break;
		}

		return true;
	}
}
