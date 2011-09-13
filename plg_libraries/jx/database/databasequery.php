<?php
/**
 * @version		$Id: databasequery.php 498 2010-05-06 22:22:56Z robs $
 * @package		JXtended.Libraries
 * @subpackage	Database
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

/**
 * Query Building Class
 *
 * @package		JXtended.Libraries
 * @subpackage	Database
 * @since		1.0
 */
class JDatabaseQuery
{
	/**
	 * If the query is added to a union only select distinct values from the union.
	 *
	 * @var		boolean
	 * @since	1.0
	 */
	public $unionDistinct = true;

	/**
	 * Queries to merge via a UNION.
	 *
	 * @var		array
	 * @since	1.0
	 */
	protected $_union = array();

	/**
	 * The SELECT clause query element.
	 *
	 * @var		array
	 * @since	1.0
	 */
	protected $_select = array();

	/**
	 * The FROM clause query element.
	 *
	 * @var		array
	 * @since	1.0
	 */
	protected $_from = array();

	/**
	 * The array of JOIN clause query elements.
	 *
	 * @var		array
	 * @since	1.0
	 */
	protected $_join = array();

	/**
	 * The WHERE clause query element.
	 *
	 * @var		object
	 * @since	1.0
	 */
	protected $_where;

	/**
	 * The GROUP BY clause query element.
	 *
	 * @var		array
	 * @since	1.0
	 */
	protected $_group = array();

	/**
	 * The HAVING clause query element.
	 *
	 * @var		array
	 * @since	1.0
	 */
	protected $_having = array();

	/**
	 * The ORDER BY clause query element.
	 *
	 * @var		array
	 * @since	1.0
	 */
	protected $_order = array();

	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param	string	The name of the clause to clear.
	 * @return	void
	 * @since	1.1
	 */
	public function clear($clause = null)
	{
		switch ($clause)
		{
			case 'union':
				$this->unionDistinct = true;
				$this->_union = array();
				break;
			case 'select':
				$this->_select = array();
				break;
			case 'from':
				$this->_from = array();
				break;
			case 'join':
				$this->_join = array();
				break;
			case 'where':
				$this->_where = null;
				break;
			case 'group':
				$this->_group = array();
				break;
			case 'having':
				$this->_having = array();
				break;
			case 'order':
				$this->_order = array();
				break;
			default:
				$this->unionDistinct = true;
				$this->_union = array();
				$this->_select = array();
				$this->_from = array();
				$this->_join = array();
				$this->_where = null;
				$this->_group = array();
				$this->_having = array();
				$this->_order = array();
				break;
		}

		return $this;
	}

	/**
	 * Add a column or array of columns to the SELECT clause.
	 *
	 * @param	mixed	A string or array of columns to select.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	public function select($columns)
	{
		if (is_array($columns)) {
			$this->_select = array_unique(array_merge($this->_select, $columns));
		}
		else {
			$this->_select = array_unique(array_merge($this->_select, array($columns)));
		}

		return $this;
	}

	/**
	 * Add a table or array of tables to the FROM clause.
	 *
	 * @param	mixed	A string or array of tables.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	public function from($tables)
	{
		if (is_array($tables)) {
			$this->_from = array_unique(array_merge($this->_from, $tables));
		}
		else {
			$this->_from = array_unique(array_merge($this->_from, array($tables)));
		}

		return $this;
	}

	/**
	 * Add an explicit JOIN to the query.
	 *
	 * @param	string	Join type such as INNER, LEFT, etc.
	 * @param	string	The join table and conditions.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	public function join($type, $conditions)
	{
		$this->_join[] = strtoupper($type).' JOIN '.$conditions;

		return $this;
	}

	/**
	 * Short hand way of adding an inner join.
	 *
	 * @param	string	The join table and conditions.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	function innerJoin($conditions)
	{
		return $this->join('INNER', $conditions);
	}

	/**
	 * Short hand way of adding an outer join.
	 *
	 * @param	string	The join table and conditions.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	function outerJoin($conditions)
	{
		return $this->join('OUTER', $conditions);
	}

	/**
	 * Short hand way of adding a left join.
	 *
	 * @param	string	The join table and conditions.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	function leftJoin($conditions)
	{
		return $this->join('LEFT', $conditions);
	}

	/**
	 * Short hand way of adding a right join.
	 *
	 * @param	string	The join table and conditions.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	function rightJoin($conditions)
	{
		return $this->join('RIGHT', $conditions);
	}

	/**
	 * Add condition statements to the WHERE clause object.
	 *
	 * @param	mixed	Conditional statement string, array of strings or JDatabaseQueryWhere object.
	 * @param	string	Logical operator glue for the condition. e.g. OR | AND
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	public function where($condition, $glue='AND')
	{
		if (is_null($this->_where)) {
			$this->_where = new JDatabaseQueryWhere($condition, $glue);
		} else {
			$this->_where->append($condition, $glue);
		}

		return $this;
	}

	/**
	 * Add a column to the GROUP BY clause.
	 *
	 * @param	mixed	A string or array of grouping columns.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	public function group($columns)
	{
		if (is_array($columns)) {
			$this->_group = array_unique(array_merge($this->_group, $columns));
		} else {
			$this->_group = array_unique(array_merge($this->_group, array($columns)));
		}

		return $this;
	}

	/**
	 * Add a column or condition to the HAVING clause.
	 *
	 * @param	mixed	A string or array of columns or conditions.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	public function having($columns)
	{
		if (is_array($columns)) {
			$this->_having = array_unique(array_merge($this->_having, $columns));
		} else {
			$this->_having = array_unique(array_merge($this->_having, array($columns)));
		}

		return $this;
	}

	/**
	 * Add a column and optional direction to the ORDER BY clause.
	 *
	 * @param	mixed	A string or array of ordering columns.
	 * @return	object	The current JDatabaseQuery instance for easy method chaining.
	 * @since	1.0
	 */
	public function order($columns)
	{
		if (is_array($columns)) {
			$this->_order = array_unique(array_merge($this->_order, $columns));
		} else {
			$this->_order = array_unique(array_merge($this->_order, array($columns)));
		}

		return $this;
	}

	/**
	 * Add a query to UNION with the current query.
	 *
	 * @param	object	The JDatabaseQuery object to union.
	 * @param	boolean	True to only return distinct rows from the union.
	 * @return	mixed	The JDatabaseQuery object on success or boolean false on failure.
	 * @since	1.1
	 */
	public function union($query, $distinct = null)
	{
		if (!$query instanceof JDatabaseQuery) {
			return false;
		}

		// Apply the distinct flag to the union if set.
		if ($distinct !== null) {
			$query->unionDistinct = (bool) $distinct;
		}

		// Clear the ORDER BY clause in unioned query.
		$query->clear('order');

		// Add the query to union.
		$this->_union[] = $query;

		return $this;
	}

	/**
	 * Legacy function to return a string representation of the query element.
	 *
	 * @return	string	The query element.
	 * @since	1.0
	 */
	public function toString()
	{
		return (string) $this;
	}

	/**
	 * Render a string representation of the query element.
	 *
	 * @return	string	The query element.
	 * @since	1.1
	 */
	public function __toString()
	{
		// Initialize variables.
		$query = '';

		// Add the SELECT and FROM clauses.
		$query .= 'SELECT '.implode(',', $this->_select);
		$query .= "\nFROM ".implode(',', $this->_from);

		// Special case for JOIN clauses.
		if ($this->_join) {
			$query .= "\n".implode("\n", $this->_join);
		}

		// Add the WHERE clause if it exists.
		if ($this->_where) {
			$query .= "\nWHERE ".$this->_where;
		}

		// Add the optional GROUP BY and HAVING clauses if they exist.
		if ($this->_group) {
			$query .= "\nGROUP BY ".implode(',', $this->_group);
		}
		if ($this->_having) {
			$query .= "\nHAVING ".implode(',', $this->_having);
		}

		// Add any UNION queries.
		foreach ($this->_union as $union)
		{
			$query .= "\nUNION".((!$union->unionDistinct) ? ' ALL' : '');
			$query .= "\n".$union;
		}

		// Add the optional ORDER BY clause if it exists.
		if ($this->_order) {
			$query .= "\nORDER BY ".implode(',', $this->_order);
		}

		return $query;
	}

	/**
	 * Method to provide deep copy support to nested objects and arrays when cloning.
	 *
	 * @return	void
	 * @since	1.1
	 */
	public function __clone()
	{
		foreach ($this as $k => $v)
		{
			if ((is_object($v)) || is_array($v)) {
				$this->{$k} = unserialize(serialize($v));
			}
		}
	}
}

/**
 * Query Where Clause Class
 *
 * @package		JXtended.Libraries
 * @subpackage	Database
 * @since		1.1
 */
class JDatabaseQueryWhere
{
	/**
	 * An array of element data.
	 *
	 * @var		array
	 * @since	1.1
	 */
	protected $_conditions = array();

	/**
	 * The element glue string.
	 *
	 * @var		string
	 * @since	1.1
	 */
	protected $_glue;

	/**
	 * Constructor
	 *
	 * @param	mixed	Element data or array of data strings.
	 * @param	string	The glue for elements.
	 * @return	void
	 * @since	1.1
	 */
	public function __construct($conditions = array(), $glue='AND')
	{
		// Initialize object properties.
		$this->_conditions	= array();
		$this->_glue		= $glue;

		// Append the element data.
		$this->append($conditions);
	}

	/**
	 * Append condition statements to the WHERE clause object.
	 *
	 * @param	mixed	Conditional statement string, array of strings or JDatabaseQueryWhere object.
	 * @param	string	Logical operator glue for the condition. e.g. OR | AND
	 * @return	void
	 * @since	1.1
	 */
	public function append($conditions, $glue=null)
	{
		// If no explicit glue is set use the object glue.
		if ($glue === null) {
			$glue = $this->_glue;
		}

		// Sanitize the glue.
		$glue = strtoupper($glue);

		if ($conditions) {
			$this->_conditions[] = array('c'=>$conditions, 'g'=>$glue);
		}
	}

	/**
	 * Render a string representation of the query element.
	 *
	 * @return	string	The WHERE clause as a string.
	 * @since	1.1
	 */
	public function __toString()
	{
		$string = '';

		foreach ($this->_conditions as $i => $condition)
		{
			if ($i > 0) {
				$string .= ' '.$condition['g'].' ';
			}

			if (is_string($condition['c'])) {
				$string .= $condition['c'];
			}
			elseif (is_array($condition['c'])) {
				$string .= '('.implode(' '.$condition['g'].' ', $condition['c']).')';
			}
			else {
				$string .= '('.$condition['c'].')';
			}
		}

		return $string;
	}

	/**
	 * Method to provide deep copy support to nested objects and arrays when cloning.
	 *
	 * @return	void
	 * @since	1.1
	 */
	public function __clone()
	{
		foreach ($this as $k => $v)
		{
			if ((is_object($v)) || is_array($v)) {
				$this->{$k} = unserialize(serialize($v));
			}
		}
	}
}
