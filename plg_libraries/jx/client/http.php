<?php
/**
 * @version		$Id: http.php 458 2009-09-23 05:13:02Z louis $
 * @package		JXtended.Libraries
 * @subpackage	Client
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.environment.uri');

/**
 * HTTP client class.
 *
 * @package		JXtended.Libraries
 * @subpackage	Client
 * @since		2.0
 */
class JHttp
{
	/**
	 * Server connection resources array.
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected $_connections = array();

	/**
	 * Timeout limit in seconds for the server connection.
	 *
	 * @var		integer
	 * @since	2.0
	 */
	protected $_timeout = 5;

	/**
	 * Server response string.
	 *
	 * @var		string
	 * @since	2.0
	 */
	protected $_response;

	/**
	 * Client object constructor.
	 *
	 * @param	array	Array of configuration options for the client.
	 * @return	void
	 * @since	2.0
	 */
	public function __construct($options = array())
	{
		// If a connection timeout is set, use it.
		if (isset($options['timeout'])) {
			$this->_timeout = $options['timeout'];
		}
	}

	/**
	 * Client object destructor.
	 *
	 * @return	void
	 * @since	2.0
	 */
	public function __destruct()
	{
		// Close all the connections.
		foreach ($this->_connections as $connection)
		{
			@fclose($connection);
		}
	}

	/**
	 * Method to send the HEAD command to the server.
	 *
	 * @param	string	Path to the resource.
	 * @return	boolean	True on success.
	 * @since	2.0
	 *
	 * @throws	JException
	 */
	public function head($url, $headers = null)
	{
		// Parse the request url.
		$uri = JUri::getInstance($url);

		try {
			$connection = $this->_connect($uri);
		}
		catch (Exception $e) {
			return false;
		}

		// Send the command to the server.
		if (!$this->_sendRequest($connection, 'HEAD', $uri, null, $headers)) {
			return false;
		}

		return $this->_getResponseObject();
	}

	/**
	 * Method to send the HEAD command to the server.
	 *
	 * @param	string	Path to the resource.
	 * @return	boolean	True on success.
	 * @since	2.0
	 *
	 * @throws	JException
	 */
	public function get($url, $headers = null)
	{
		// Parse the request url.
		$uri = JUri::getInstance($url);

		try {
			$connection = $this->_connect($uri);
		}
		catch (Exception $e) {
			return false;
		}

		// Send the command to the server.
		if (!$this->_sendRequest($connection, 'GET', $uri, null, $headers)) {
			return false;
		}

		return $this->_getResponseObject();
	}

	/**
	 * Method to send the HEAD command to the server.
	 *
	 * @param	string	Path to the resource.
	 * @return	boolean	True on success.
	 * @since	2.0
	 *
	 * @throws	JException
	 */
	public function post($url, $data, $headers = null)
	{
		// Parse the request url.
		$uri = JUri::getInstance($url);

		try {
			$connection = $this->_connect($uri);
		}
		catch (Exception $e) {
			return false;
		}

		// Send the command to the server.
		if (!$this->_sendRequest($connection, 'POST', $uri, $data, $headers)) {
			return false;
		}

		return $this->_getResponseObject();
	}

	/**
	 * Send a command to the server and validate an expected response.
	 *
	 * @param	string	Command to send to the server.
	 * @param	mixed	Valid response code or array of response codes.
	 * @return 	boolean	True on success.
	 * @since	2.0
	 *
	 * @throws	JException
	 */
	protected function _sendRequest($connection, $method, JUri $uri, $data = null, $headers = null)
	{
		// Make sure the connection is a valid resource.
		if (is_resource($connection))
		{
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($connection);
			if ($meta['timed_out']) {
				throw new Exception('Server connection timed out.', 0, E_WARNING);
			}
		}
		else {
			throw new Exception('Not connected to server.', 0, E_WARNING);
		}

		// Get the request path from the URI object.
		$path = $uri->toString(array('path', 'query'));

		// Build the request payload.
		$request = array();
		$request[] = strtoupper($method).' '.((empty($path)) ? '/' : $path).' HTTP/1.0';
		$request[] = 'Host: '.$uri->getHost();

		// If no user agent is set use the base one.
		if (empty($headers) || !isset($headers['User-Agent'])) {
			$request[] = 'User-Agent: JHttp | JXtended/2.0';
		}

		// If there are custom headers to send add them to the request payload.
		if (is_array($headers))
		{
			foreach ($headers as $k => $v)
			{
				$request[] = $k.': '.$v;
			}
		}

		// If we have data to send add it to the request payload.
		if (!empty($data))
		{
			// If the data is an array, build the request query string.
			if (is_array($data)) {
				$data = http_build_query($data);
			}

			$request[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
			$request[] = 'Content-Length: '.strlen($data);
			$request[] = null;
			$request[] = $data;
		}

		// Send the request to the server.
		fwrite($connection, implode("\r\n", $request)."\r\n\r\n");

		// Get the response data from the server.
		$this->_response = null;
		while (!feof($connection))
		{
		    $this->_response .= fgets($connection, 1160);
		}

		return true;
	}

	/**
	 * Method to get a response object from a server response.
	 *
	 * @return	object	The response object.
	 * @since	2.0
	 */
	protected function _getResponseObject()
	{
		// Create the response object.
		$return = new JHttpResponse();

		// Split the response into headers and body.
		$response = explode("\r\n\r\n", $this->_response, 2);

		// Get the response headers as an array.
		$headers = explode("\r\n", $response[0]);

		// Get the response code from the first offset of the response headers.
		preg_match('/[0-9]{3}/', array_shift($headers), $matches);
		$code = $matches[0];
		if (is_numeric($code)) {
			$return->code = (int) $code;
		}
		// No valid response code was detected.
		else {
			throw new Exception('Invalid server response.', 0, E_WARNING, $this->_response);
		}

		// Add the response headers to the response object.
		foreach ($headers as $header)
		{
			$pos = strpos($header, ':');
			$return->headers[trim(substr($header, 0, $pos))] = trim(substr($header, ($pos + 1)));
		}

		// Set the response body if it exists.
		if (!empty($response[1])) {
			$return->body = $response[1];
		}

		return $return;
	}

	/**
	 * Method to connect to a server and get the resource.
	 *
	 * @param	string	The host name of the server for which to connect.
	 * @param	integer	The port number for which to make a connection.
	 * @return	mixed	Connection resource on success or boolean false on failure.
	 * @since	2.0
	 */
	protected function _connect(JUri $uri)
	{
		// Initialize variables.
		$errno = null;
		$err = null;

		// Get the host from the uri.
		$host = ($uri->isSSL()) ? 'ssl://'.$uri->getHost() : $uri->getHost();

		// If the port is not explicitly set in the URI detect it.
		if (!$uri->getPort())
		{
			$port = ($uri->getScheme() == 'https') ? 443 : 80;
		}
		// Use the set port.
		else {
			$port = $uri->getPort();
		}

		// Build the connection key for resource memory caching.
		$key = md5($host.$port);

		// If the connection already exists, use it.
		if (!empty($this->_connections[$key]) && is_resource($this->_connections[$key]))
		{
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($this->_connections[$key]);
			if (!$meta['timed_out']) {
				return $this->_connections[$key];
			}
		}

		// Attempt to connect to the server.
		if ($this->_connections[$key] = fsockopen($host, $port, $errno, $err, $this->_timeout)) {
			stream_set_timeout($this->_connections[$key], $this->_timeout);
		}

		return $this->_connections[$key];
	}
}

/**
 * HTTP response data object class.
 * *
 * @package		JXtended.Libraries
 * @subpackage	Client
 * @since		2.0
 */
class JHttpResponse
{
	public $code;
	public $headers = array();
	public $body;
}