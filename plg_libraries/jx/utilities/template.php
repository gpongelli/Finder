<?php
/**
 * @version		$Id: template.php 466 2009-09-23 22:28:36Z louis $
 * @package		JXtended.Libraries
 * @subpackage	Utilities
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.base.object');
jimport('joomla.filesystem.path');

/**
 * Template Class for JXtended Libraries.
 *
 * This class implements an API for constructing and populating
 * simple templates with data.
 *
 * @package		JXtended.Libraries
 * @subpackage	Utilities
 * @since		2.0
 */
class JTemplate extends JObject
{
	/**
	 * The name of the template.
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected $_name;

	/**
	 * Layout name.
	 *
	 * @var		string
	 * @since	2.0
	 */
	protected $_layout = 'default';

	/**
	 * Layout file extension.
	 *
	 * @var		string
	 * @since	2.0
	 */
	protected $_layoutExt = 'php';

	/**
	 * Layout file path.
	 *
	 * @var		string
	 * @since	2.0
	 */
	protected $_layoutFile;

	/**
	 * The search paths for layouts.
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected $_paths = array();

	/**
     * Callback for escaping.
     *
	 * @var		callback
	 * @since	2.0
     */
    protected $_escape = 'htmlspecialchars';

	/**
     * Charset to use in escaping mechanisms; defaults to utf8 (UTF-8).
     *
	 * @var		string
	 * @since	2.0
     */
    protected $_charset = 'UTF-8';

	/**
     * Output buffer container.
     *
	 * @var		string
	 * @since	2.0
     */
    protected $_output;

	/**
	 * Constructor
	 *
	 * @param	array	Array of class configuration options.
	 * @return	void
	 * @since	2.0
	 */
	public function __construct($config = array())
	{
		// Set the name for the template.
		if (array_key_exists('name', $config))  {
			$this->_name = $config['name'];
		}

		// Set the character set for the template.
        if (array_key_exists('charset', $config)) {
            $this->_charset = $config['charset'];
        }

		 // Set the user defined escaping callback.
        if (array_key_exists('escape', $config)) {
            $this->setEscape($config['escape']);
        }

		// If a default layout search path is set, add it.
		if (array_key_exists('layout_path', $config)) {
			$this->addLayoutPath($config['layout_path']);
		}

		// Set the default layout.
		if (array_key_exists('layout', $config)) {
			$this->setLayout($config['layout']);
		}
		else {
			$this->setLayout('default');
		}

		// Set the base URL.
		$this->baseurl = rtrim(JURI::root(), '/');
	}

	/**
	 * Adds to the stack of view script paths in LIFO order.
	 *
	 * @param	mixed	A path or array of paths to add to th layout search stack.
	 * @return	array	The array of layout paths to search for layouts in.
	 * @since	2.0
	 */
	public function addLayoutPath($paths)
	{
		// Force the input argument to be an array.
		settype($paths, 'array');

		// Iterate over the paths.
		foreach ($paths as $path)
		{
			// Trim all surrounding spaces.
			$path = trim($path);

			// Add trailing separators as necessary.
			if (substr($path, -1) != DS) {
				$path .= DS;
			}

			// Add the path to the top of the search stack.
			array_unshift($this->_paths, $path);
		}

		// Return the existing layout paths.
		return $this->_paths;
	}

	/**
	 * Method to assign a variable for the template.
	 *
	 * Note: You are not allowed to set variables that begin with an underscore as they
	 * are considered private properties for the template class.
	 *
	 * <code>
	 *	<?php
	 *	$template = new JTemplate($options);
	 *
	 *	// Assign the value by key and value.
	 *	$template->assign('var1', $ref);
	 *
	 *	// Asssign the value directly.
	 *	$template->var1 = & $ref;
	 *	?>
	 * </code>
	 *
	 * @param	string	The key reference for the variable in the template.
	 * @param	mixed	The variable to assign to the template.
	 * @return	boolean	True on success.
	 * @since	2.0
	 */
	public function assign($key, $value)
	{
		if (!empty($key) && is_string($key) && ($key[0] != '_'))
		{
			$this->$key = $value;
			return true;
		}

		return false;
	}

	/**
	 * Method to bind a complex variable (array or object) to the template object.
	 *
	 * NOTE: Properties that being with an underscore will be ignored as they are seen as
	 * private properties.
	 *
	 * <code>
	 *	<?php
	 *	$template = new JTemplate($options);
	 *
	 *	// Bind an associative array.
	 *	$array = array('var1' => 'something', 'var2' => 'else');
	 *	$template->bind($array);
	 *
	 *	// Bind an object
	 *	$obj = new stdClass;
	 *	$obj->var1 = 'something';
	 *	$obj->var2 = 'else';
	 *	$template->bind($obj);
	 *	?>
	 * </code>
	 *
	 * @param	mixed	The input variable to bind to the template object.
	 * @param	string	The prefix string to apply to the variable keys when assigning them
	 * 					to the template object.
	 * @return	boolean	True on success.
	 * @since	2.0
	 */
	public function bind($input, $prefix = null)
	{
		// Check the input type and get object properties as an array.
		switch(gettype($input))
		{
			case 'object':
				$input = get_object_vars($input);
				break;

			case 'array':
				break;

			default:
				return false;
				break;
		}

		// Sanitize the property prefix.
		$prefix = (string) $prefix;

		// Iterate over the input value properties.
		foreach($input as $k => $v)
		{
			// Ignore private properties.
			if ($k[0] == '_') {
				continue;
			}

			$key = $prefix.$k;
			$this->$key = $v;
		}

		return true;
	}

	/**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
     * {@link $_escape} setting.
     *
     * @param 	string	The string to escape.
     * @return	string	The escaped string.
     * @since	2.0
     */
    public function escape($input)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, (string) $input, ENT_COMPAT, $this->_charset);
        }

        return call_user_func($this->_escape, (string) $input);
    }

	/**
	 * Method to get the layout name.
	 *
	 * @return	string	The name of the layout.
	 * @since	2.0
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

	/**
	 * Method to get the template name.
	 *
	 * @return	string	The name of the template.
	 * @since	2.0
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Method to render a layout file.  If the sub-layout argument is used a sub-layout of the
	 * currently set layout will be rendered instead of the main layout.
	 *
	 * <code>
	 *	<?php
	 *	$template = new JTemplate($options);
	 *
	 *	// Bind an associative array.
	 *	$array = array('var1' => 'something', 'var2' => 'else');
	 *	$template->bind($array);
	 *
	 *	// Render the template.
	 *	echo $template->render();
	 *	?>
	 * </code>
	 *
	 * @param	string	The optional name of a sub-layout of the currently set layout to render.
	 * @return	string	The rendered template output.
	 * @since	2.0
	 */
	public function render($sub = '')
	{
		// Initialize variables.
		$this->_output = null;

		// Sanitize the sub-layout name.
		$sub = preg_replace('/[^A-Z0-9_\.-]/i', '', $sub);

		// Build and sanitize the layout file name based on the layout name and sub-layout argument.
		$this->_layoutFile = preg_replace('/[^A-Z0-9_\.-]/i', '', !empty($sub) ? $this->_layout.'_'.$sub : $this->_layout);

		// load the template script
		$this->_layoutFile = JPath::find($this->_paths, strtolower($this->_layoutFile).'.'.$this->_layoutExt);

		if ($this->_layoutFile != false)
		{
			// Unset unnecessary variables so as to not introduce them into layout scope.
			unset($sub);
			if (isset($this->this)) {
				unset($this->this);
			}

			// Initialize an output buffer.
			ob_start();

			// Include the layout file, capturing its output.
			include $this->_layoutFile;

			// Get the output buffer content and close the buffer.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else {
			return new JException(500, 'Layout "'.$this->_layout.($sub ? '::'.$sub : '').'" not found.');
		}
	}

	/**
	 * Method to set the callback for escaping variables.
	 *
	 * @param	callback	The new escaping callback.
	 * @return	callback	The previous escaping callback
	 * @since	2.0
	 */
    public function setEscape($callback)
    {
		// Get the previous callback and set the new one.
		$previous = $this->_escape;
		$this->_escape = $callback;

		return $previous;
    }

	/**
	 * Method to set the name of the layout to use for the template.
	 *
	 * @param	string	The name of the new layout.
	 * @return	string	The previous layout name.
	 * @since	2.0
	 */
	public function setLayout($layout)
	{
		// Get the previous layout and set the new one.
		$previous = $this->_layout;
		$this->_layout = $layout;

		return $previous;
	}

	/**
	 * Method to set the layout file extension to use when searching for layout files.
	 *
	 * @param	string	The new layout file extension.
	 * @return	string	The previous layout file extension.
	 * @since	2.0
	 */
	public function setLayoutExt($ext)
	{
		// Get the previous layout file extension and set the new one.
		$previous = $this->_layoutExt;
		if ($ext = preg_replace('#[^A-Za-z0-9]#', '', trim($ext))) {
			$this->_layoutExt = $ext;
		}

		return $previous;
	}
}