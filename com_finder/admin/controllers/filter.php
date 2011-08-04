<?php
/**
 * @version		$Id: filter.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

/**
 * Indexer controller class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderControllerFilter extends FinderController
{
	/**
	 * Method to save the changes to the current filter and return
	 * back to the filter edit view.
	 *
	 * @access	public
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	function apply()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Filter');
		$data	= JRequest::getVar('JForm', array(), 'post', 'array');

		// Validate the posted data.
		$data = $model->validate($data);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_finder.edit.filter.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', false));
			return false;
		}

		// Get and sanitize the filter data.
		$data['data'] = JRequest::getVar('t', array(), 'post', 'array');
		$data['data'] = array_unique($data['data']);
		JArrayHelper::toInteger($data['data']);

		// Remove any values of zero.
		if (array_search(0, $data['data'], true)) {
			unset($data['data'][array_search(0, $data['data'], true)]);
		}

		// Attempt to save the filter.
		$return = $model->save($data);

		if ($return === false)
		{
			// Save failed, go back to the filter and display a notice.
			$message = JText::sprintf('FINDER_FILTER_APPLY_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message, 'error');
			return false;
		}
		else
		{
			// Attempt to check-out the new filter for editing and redirect.
			if (!$model->checkout($return))
			{
				// Check-out failed, go back to the list and display a notice.
				$message = JText::sprintf('FINDER_FILTER_CHECKOUT_FAILED', $model->getError());
				$this->setRedirect('index.php?option=com_finder&view=filters', $message, 'error');
				return false;
			}
			else
			{
				// Save succeeded, go back to the filter and display a message.
				$app->setUserState('com_finder.edit.filter.id', $return);
				$message = JText::_('FINDER_FILTER_APPLY_SUCCESS');
				$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message);
				return true;
			}
		}
	}

	/**
	 * Method to cancel the edit operation, check-in the checked-out
	 * filter and go back to the filter list view.
	 *
	 * @access	public
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	function cancel()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Filter', 'FinderModel');

		// Get the filter id.
		$filter_id = (int) $app->getUserState('com_finder.edit.filter.id');

		// Attempt to check-in the current filter.
		if ($filter_id)
		{
			if (!$model->checkin($filter_id))
			{
				// Check-in failed, go back to the filter and display a notice.
				$message = JText::sprintf('FINDER_FILTER_CHECKIN_FAILED', $model->getError());
				$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message, 'error');
				return false;
			}
		}

		// Clean the session data and redirect.
		$app->setUserState('com_finder.edit.filter.id', null);
		$this->setRedirect('index.php?option=com_finder&view=filters');
	}

	/**
	 * Method to checkout a filter for editing.  If a different filter
	 * was previously checked-out, the previous filter will be checked
	 * in first.
	 *
	 * @access	public
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	function edit()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Filter', 'FinderModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Get the previous filter id (if any) and the current filter id.
		$previous_id	= (int) $app->getUserState('com_finder.edit.filter.id');
		$filter_id		= (int) (count($cid) ? $cid[0] : JRequest::getInt('filter_id'));

		// If filter ids do not match, checkin previous filter.
		if (($previous_id > 0) && ($filter_id != $previous_id))
		{
			if (!$model->checkin($previous_id))
			{
				// Check-in failed, go back to the filter and display a notice.
				$message = JText::sprintf('FINDER_FILTER_CHECKIN_FAILED', $model->getError());
				$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message, 'error');
				return false;
			}
		}

		// Attempt to check-out the new filter for editing and redirect.
		if (!$model->checkout($filter_id))
		{
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('FINDER_FILTER_CHECKOUT_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filter&filter_id='.$filter_id, $message, 'error');
			return false;
		}
		else
		{
			// Check-out succeeded, push the new filter id into the session.
			$app->setUserState('com_finder.edit.filter.id', $filter_id);
			$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1');
			return true;
		}
	}

	/**
	 * Method to get a fresh filter form for creating a new filter.
	 *
	 * @access	public
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	function createnew()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$app = &JFactory::getApplication();

		// Prepare the session data and redirect.
		$app->setUserState('com_finder.edit.filter.id', -1);
		$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1');
		return true;
	}

	/**
	 * Method to save the changes to the current filter and return
	 * back to the filter list view.
	 *
	 * @access	public
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	function save()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Filter');
		$data	= JRequest::getVar('JForm', array(), 'post', 'array');

		// Validate the posted data.
		$data = $model->validate($data);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_finder.edit.filter.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', false));
			return false;
		}

		// Get and sanitize the filter data.
		$data['data'] = JRequest::getVar('t', array(), 'post', 'array');
		$data['data'] = array_unique($data['data']);
		JArrayHelper::toInteger($data['data']);

		// Remove any values of zero.
		if (array_search(0, $data['data'], true)) {
			unset($data['data'][array_search(0, $data['data'], true)]);
		}

		// Attempt to save the filter.
		$return = $model->save($data);

		if ($return === false)
		{
			// Save failed, go back to the filter and display a notice.
			$message = JText::sprintf('FINDER_FILTER_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message, 'error');
			return false;
		}

		// Save succeeded, check-in the filter.
		if (!$model->checkin())
		{
			// Check-in failed, go back to the filter and display a notice.
			$message = JText::sprintf('FINDER_FILTER_CHECKIN_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message, 'error');
			return false;
		}

		// Clean the session data.
		$app->setUserState('com_finder.edit.filter.id', null);

		$message = JText::_('FINDER_FILTER_SAVE_SUCCESS');
		$this->setRedirect('index.php?option=com_finder&view=filters', $message);
		return true;
	}

	/**
	 * Method to save the changes to the current filter and return
	 * back to the filter edit view with a clean form.
	 *
	 * @access	public
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	function savenew()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Filter');
		$data	= JRequest::getVar('JForm', array(), 'post', 'array');

		// Validate the posted data.
		$data = $model->validate($data);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_finder.edit.filter.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', false));
			return false;
		}

		// Get and sanitize the filter data.
		$data['data'] = JRequest::getVar('t', array(), 'post', 'array');
		$data['data'] = array_unique($data['data']);
		JArrayHelper::toInteger($data['data']);

		// Remove any values of zero.
		if (array_search(0, $data['data'], true)) {
			unset($data['data'][array_search(0, $data['data'], true)]);
		}

		// Attempt to save the filter.
		$return = $model->save($data);

		if ($return === false)
		{
			// Save failed, go back to the filter and display a notice.
			$message = JText::sprintf('FINDER_FILTER_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message, 'error');
			return false;
		}

		// Save succeeded, check-in the filter.
		if (!$model->checkin())
		{
			// Check-in failed, go back to the filter and display a notice.
			$message = JText::sprintf('FINDER_FILTER_CHECKIN_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message, 'error');
			return false;
		}

		// Prepare the session data.
		$app->setUserState('com_finder.edit.filter.id', -1);

		$message = JText::_('FINDER_FILTER_SAVE_SUCCESS');
		$this->setRedirect('index.php?option=com_finder&view=filter&layout=edit&hidemainmenu=1', $message);
		return true;
	}

	/**
	 * Method to view a filter.
	 *
	 * @access	public
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	function view()
	{
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		$app		= &JFactory::getApplication();
		$model		= &$this->getModel('Filter', 'FinderModel');
		$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$filter_id	= (int) (count($cid) ? $cid[0] : JRequest::getInt('id'));

		// Check-in the filter just to be safe.
		$model->checkin($filter_id);

		$app->setUserState('com_finder.view.filter.id', $filter_id);
		$this->setRedirect('index.php?option=com_finder&view=filter&layout=default&filter_id='.$filter_id.'&hidemainmenu=1');
		return true;
	}
}