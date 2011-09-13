<?php
/**
 * @version		$Id: modelitem.php 498 2010-05-06 22:22:56Z robs $
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.model');
jx('jx.database.databasequery');

/**
 * Model class for handling singular items.  This class has the ability to autopopulate
 * its state from the request and also load/validate JForm objects.  When loaded, form
 * objects can optionally fire events for setup and manipulation.
 *
 * @package		JXtended.Libraries
 * @subpackage	Application.Component
 * @since		2.0
 */
abstract class JModelItem extends JModel
{
	/**
	 * Indicates if the internal state has been set.
	 *
	 * @var		bool
	 * @since	2.0
	 */
	protected $__state_set = false;

	/**
	 * Internal memory based cache array of data.
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected $_cache = array();

	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the _getStoreId() method and caching data structures.
	 *
	 * @var		string
	 * @since	2.0
	 */
	protected $_context = 'group.type';

	/**
	 * Internal array of form objects.
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected $_forms = array();

	/**
	 * Constructor.
	 *
	 * @param	array	The instance configuration array.
	 * @return	void
	 * @since	2.0
	 */
	function __construct($config = array())
	{
		// Call the parent model constructor.
		parent::__construct($config);

		// Set the internal state flag if the model should not autopopulate the state from the request.
		if (!empty($config['ignore_request'])) {
			$this->__state_set = true;
		}
	}

	/**
	 * Method to get the model state or state property value.
	 *
	 * @param	string	Optional property name.
	 * @param   mixed	Optional default value.
	 * @return	mixed	The property where specified, the state object where omitted.
	 * @since	2.0
	 */
	public function getState($property = null, $default = null)
	{
		if (!$this->__state_set)
		{
			// Private method to autopopulate the model state.
			$this->_populateState();

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		return $property === null ? $this->_state : $this->_state->get($property, $default);
	}

	/**
	 * Method to get a form object and optionally fire an event for form setup and manipulation.
	 *
	 * @param	string	The form data. Can be XML string if file flag is set to false.
	 * @param	array	Optional array of parameters.
	 * @param	boolean	Optional argument to force load a new form.
	 * @return	mixed	JForm object on success, boolean false on error.
	 * @since	2.0
	 */
	public function getForm($xml, $name = 'form', $options = array(), $clear = false)
	{
		// Handle the optional arguments.
		$options['array']	= isset($options['array']) ? $options['array'] : false;
		$options['file']	= isset($options['file'])  ? $options['file']  : true;
		$options['event']	= isset($options['event']) ? $options['event'] : null;
		$options['group']	= isset($options['group']) ? $options['group'] : null;

		// Create a signature hash.
		$hash = md5($xml.serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear) {
			return $this->_forms[$hash];
		}

		// Import the form library and setup paths.
		jx('jx.form.form');
		JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JForm::addFieldPath(JPATH_ROOT.'plugins/system/jx/form/fields');
		JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');

		// Get the form object.
		$form = JForm::getInstance($xml, $name, $options['file'], $options);

		// Check for an error.
		if (JError::isError($form))
		{
			$this->setError($form->getMessage());
			return false;
		}

		// Look for an event to fire.
		if ($options['event'] !== null)
		{
			// Get the event dispatcher.
			$dispatcher	= JDispatcher::getInstance();

			// Load an optional plugin group.
			if ($options['group'] !== null) {
				JPluginHelper::importPlugin($options['group']);
			}

			// Trigger the form preparation event.
			$results = $dispatcher->trigger($options['event'], array($form->getName(), $form));

			// Check for errors encountered while preparing the form.
			if (count($results) && in_array(false, $results, true))
			{
				// Get the last error.
				$error = $dispatcher->getError();

				// Convert to a JException if necessary.
				if (!JError::isError($error)) {
					$error = new JException($error, 500);
				}

				return $error;
			}
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param	object	The form to validate against.
	 * @param	array	The data to validate.
	 * @return	mixed	Array of filtered data if valid, boolean false otherwise.
	 * @since	2.0
	 */
	public function validate($form, $data)
	{
		// Filter and validate the form data.
		$data	= $form->filter($data);
		$return	= $form->validate($data);

		// Check for an error.
		if (JError::isError($return))
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message)
			{
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}

	/**
	 * Method to get a store id based on model the configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	An identifier string to generate the store id.
	 * @return	string	A store id.
	 * @since	2.0
	 */
	protected function _getStoreId($id = '')
	{
		return md5($this->_context.':'.$id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 * @since	2.0
	 */
	protected function _populateState()
	{
	}

	/**
	 * Method to retrieve data from cache.
	 *
	 * @param	string		The cache store id.
	 * @param	boolean		Flag to enable the use of external cache.
	 * @return	mixed		The cached data if found, null otherwise.
	 */
	protected function _retrieve($id, $persistent = true)
	{
		$data = null;

		// Use the internal cache if possible.
		if (isset($this->_cache[$id])) {
			return $this->_cache[$id];
		}

		// Use the external cache if data is persistent.
		if ($persistent) {
			$data = JFactory::getCache($this->_context, 'output')->get($id);
			$data = $data ? unserialize($data) : null;
		}

		// Store the data in internal cache.
		if ($data) {
			$this->_cache[$id] = $data;
		}

		return $data;
	}

	/**
	 * Method to store data in cache.
	 *
	 * @param	string		The cache store id.
	 * @param	mixed		The data to cache.
	 * @param	boolean		Flag to enable the use of external cache.
	 * @return	boolean		True on success, false on failure.
	 */
	protected function _store($id, $data, $persistent = true)
	{
		// Store the data in internal cache.
		$this->_cache[$id] = $data;

		// Store the data in external cache if data is persistent.
		if ($persistent) {
			return JFactory::getCache($this->_context, 'output')->store(serialize($data), $id);
		}

		return true;
	}
}