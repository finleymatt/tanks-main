<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package		NMED
 * @subpackage 	libraries[drivers]
 */
/**
 * NMED Database driver for Oracle
 *
 * provides abstraction layer for Oracle driver written by NMED
 *
 *
 * @package		dbconnectr
 * @subpackage 	libraries[drivers]
 * @author     	Kim Kleyboecker
 * @version		1.0
 * @todo 	create Oracle-specific overloads of the remaining db driver methods, e.g. 'limit'
 */
abstract class Database_Driver {

	/**
	 * holds previouly run queries if requested in config
	 *
	 * @var string-array	array of previously executed queries
	 */
	static $query_cache;

	/**
	 * Connect to NMED Oracle database.
	 *
	 * Returns FALSE on failure or a MySQL resource.
	 *
	 * @return mixed 	db connection object
	 */
	abstract public function connect();

	/**
	 * Perform a query based on a manually written query.
	 *
	 * @param  string	$sql	SQL query to execute
	 * @param  string	$bound_vars	any bound variables to be used
	 * @return object	Database_Result
	 */
	abstract public function query($sql, $bound_vars=array());

	/**
	 * Returns only the first row if exists.  Useful for aggregate queries.
	 *
	 * @param string	$sql	SQL query to execute
	 * @return array	array
	 */
	abstract public function query_row($sql, $bound_vars=array());

	/**
	 * Returns only the first value of first row.  Useful for lookups.
	 * 2017-02-18: ML created
	 *
	 * @param string	$sql	SQL query to execute
	 * @return object	mixed
	 */
	abstract public function query_field($sql, $bound_vars=array());

	/**
	 * ML created this method since existing process_package_call only works
	 * if there is an out cursor
	 *
	 * @param  string	$sql	SQL query to execute
	 * @param  string       $bound_vars	any bound variables to be used
	 * @return object	Database_Result
	 **/
	abstract public function procedure_call($sql, $bound_vars=array());

	/**
	 * Builds a DELETE query.
	 *
	 * @param   string  		$table	table name
	 * @param   string-array   	$where	where clause
	 * @return  string	the SQL string
	 */
	public function delete($table, $where)
	{
		return 'DELETE FROM '.$this->escape_table($table).' WHERE '.implode(' ', $where);
	}

	/**
	 * Builds an UPDATE query.
	 *
	 * @param   string  $table	table name
	 * @param   array   $values	key => value pairs
	 * @param   array   $where	where clause
	 * @return  string	the SQL string
	 */
	public function update($table, $values, $where)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $this->escape_column($key).' = '.$val;
		}
		return 'UPDATE '.$this->escape_table($table).' SET '.implode(', ', $valstr).' WHERE '.implode(' ',$where);
	}

	/**
	 * Set the charset using 'SET NAMES <charset>'.
	 *
	 * @todo implement the set_charset method!
	 *
	 * @param  string  $charset	character set to use
	 */
	public function set_charset($charset)
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}

	/**
	 * Wrap the tablename in backticks, has support for: table.field syntax.
	 *
	 * @param   string  $table	table name
	 * @return  string	the escaped table name
	 */
	abstract public function escape_table($table);

	/**
	 * Escape a column/field name, has support for special commands.
	 *
	 * @param   string  $column	column name
	 * @return  string	the escaped column name
	 */
	abstract public function escape_column($column);

	/**
	 * Builds a WHERE portion of a query.
	 *
	 * @param   mixed    $key			key
	 * @param   string   $value			value
	 * @param   string   $type			type
	 * @param   int      $num_wheres	number of where clauses
	 * @param   boolean  $quote			escape the value?
	 * @return  string	the SQL string
	 */
	abstract public function where($key, $value, $type, $num_wheres, $quote);

	/**
	 * Builds a LIKE portion of a query.
	 *
	 * @param   mixed    $field		field name
	 * @param   string   $match		value to match with field
	 * @param   boolean  $auto		add wildcards before and after the match
	 * @param   string   $type		clause type (AND or OR)
	 * @param   int      $num_likes	number of likes
	 * @return  string	the SQL string
	 */
	public function like($field, $match = '', $auto = TRUE, $type = 'AND ', $num_likes)
	{
		$prefix = ($num_likes == 0) ? '' : $type;

		$match = $this->escape_str($match);

		if ($auto === TRUE)
		{
			// Add the start and end quotes
			$match = '%'.$match.'%';
		}

		return $prefix.' '.$this->escape_column($field).' LIKE \''.$match . '\'';
	}

	/**
	 * Builds a NOT LIKE portion of a query.
	 *
	 * @param   mixed   $field		field name
	 * @param   string  $match		value to match with field
	 * @param   string  $auto		clause type (AND or OR)
	 * @param   int     $num_likes	number of likes
	 * @return  string	the SQL string
	 */
	public function notlike($field, $match = '', $auto = TRUE, $type = 'AND ', $num_likes)
	{
		$prefix = ($num_likes == 0) ? '' : $type;

		$match = $this->escape_str($match);

		if ($auto === TRUE)
		{
			// Add the start and end quotes
			$match = '%'.$match.'%';
		}

		return $prefix.' '.$this->escape_column($field).' NOT LIKE \''.$match.'\'';
	}

	/**
	 * Builds a REGEX portion of a query.
	 *
	 * @todo finish this regex method!
	 * @param   string   $field			field name
	 * @param   string   $match			value to match with field
	 * @param   string   $type			clause type (AND or OR)
	 * @param   integer  $num_regexs	number of regexes
	 * @return  string	the SQL string
	 */
	public function regex($field, $match, $type, $num_regexs)
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}

	/**
	 * Builds a NOT REGEX portion of a query.
	 *
	 * @todo finish this notregex method!
	 * @param   string   $field			field name
	 * @param   string   $match			value to match with field
	 * @param   string   $type			clause type (AND or OR)
	 * @param   integer  $num_regexs	number of regexes
	 * @return  string	the SQL string
	 */
	public function notregex($field, $match, $type, $num_regexs)
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}

	/**
	 * Builds an INSERT query.
	 *
	 * @param   string  $table	table name
	 * @param   array   $keys	keys
	 * @param   array   $values	values
	 * @return  string 	the SQL string
	 */
	abstract public function insert($table, $keys, $values);

	/**
	 * Builds a MERGE portion of a query.
	 *
	 * @param   string  $table	table name
	 * @param   array   $keys	keys
	 * @param   array   $values	values
	 * @return  string	the SQL string portion
	 */
	public function merge($table, $keys, $values)
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}

	/**
	 * Builds a LIMIT portion of a query.
	 *
	 * @param   integer  $limit		the record limit
	 * @param   integer  $offset	the record row offset
	 * @return  string	the SQL string portion
	 */
	abstract public function limit($limit, $offset = 0);

	/**
	 * Creates a prepared statement.
	 *
	 * @param   string  $sql	SQL query
	 * @return  the prepared SQL statement including the passed query
	 */
	public function stmt_prepare($sql = '')
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}

	/**
	 *  Compiles the SELECT statement.
	 *
	 *  Generates a query string based on which functions were used.
	 *  Should not be called directly, the get() function calls it.
	 *
	 * @param   array   $database	select query values
	 * @return  string	the SQL string
	 */
	abstract public function compile_select($database);

	/**
	 * Determines if the string has an arithmetic operator in it.
	 *
	 * @param   string   $str	string to check
	 * @return  boolean		whether the string includes an arithmetic operator
	 */
	public function has_operator($str)
	{
		return (bool) preg_match('/[<>!=]|\sIS(?:\s+NOT\s+)?\b/i', trim($str));
	}

	/**
	 * Escapes any input value.
	 *
	 * @param   mixed   $value	value to escape
	 * @return  string	the escaped value string
	 */
	abstract public function escape($value);

	/**
	 * Escapes a string for a query.
	 *
	 * @param   mixed   $str	value to escape
	 * @return  string	the escaped query string
	 */
	abstract public function escape_str($str);

	/**
	 * Lists all tables in the database.
	 *
	 * @return  array
	 */
	abstract public function list_tables();

	/**
	 * Lists all views owned by current conection owner.
	 *
	 * @return  array
	 */
	abstract public function list_views();

	/**
	 * Lists all fields in a table.
	 *
	 * @param   string  $table	table name
	 * @return  string-array	an array of the table column names
	 */
	abstract function list_fields($table);

	/**
	 * Returns the last database error.
	 *
	 * @return  string	the error string
	 */
	abstract public function show_error();

	/**
	 * Returns field data about a table.
	 *
	 * @param   string  $result	table name
	 * @return  array	an array of metadata about the table's fields
	 */
	abstract public function field_data($result);

	/**
	 * execute oci_server_version and return output
	 *
	 * assumptions: needs valid link (connection set up in driver call)
	 *
 	 * @return string	the oracle server version
	 */
	abstract public function oracle_server_version();

	/**
	 * Fetches SQL type information about a field, in a generic format.
	 *
	 * @param   string  $str	field datatype
	 * @return  array	data type information
	 */
	protected function sql_type($str)
	{
		static $sql_types;

		if ($sql_types === NULL)
		{
			// Load SQL data types
			$sql_types = Kohana::config('sql_types');
		}

		$str = strtolower(trim($str));

		if (($open  = strpos($str, '(')) !== FALSE)
		{
			// Find closing bracket
			$close = strpos($str, ')', $open) - 1;

			// Find the type without the size
			$type = substr($str, 0, $open);
		}
		else
		{
			// No length
			$type = $str;
		}

		empty($sql_types[$type]) and exit
		(
			'Unknown field type: '.$type.'. '.
			'Please report this: http://trac.kohanaphp.com/newticket'
		);

		// Fetch the field definition
		$field = $sql_types[$type];

		switch ($field['type'])
		{
			case 'string':
			case 'float':
				if (isset($close))
				{
					// Add the length to the field info
					$field['length'] = substr($str, $open + 1, $close - $open);
				}
			break;
			case 'int':
				// Add unsigned value
				$field['unsigned'] = (strpos($str, 'unsigned') !== FALSE);
			break;
		}

		return $field;
	}

	/**
	 * Clears the internal query cache.
	 *
	 * @param  string  $sql	SQL query
	 * @return VOID
	 */
	public function clear_cache($sql = NULL)
	{
		if (empty($sql))
		{
			self::$query_cache = array();
		}
		else
		{
			unset(self::$query_cache[$this->query_hash($sql)]);
		}

		Kohana::log('debug', 'Database cache cleared: '.get_class($this));
	}

	/**
	 * Creates a hash for an SQL query string. Replaces newlines with spaces,
	 * trims, and hashes.
	 *
	 * @param   string  $sql	SQL query
	 * @return  string	the hashed string
	 */
	protected function query_hash($sql)
	{
		return sha1(str_replace("\n", ' ', trim($sql)));
	}

} // End Database Driver Interface

/**
 * Database_Result class
 *
 * makes an object out of the result of a database query
 *
 * @package		NMED
 * @subpackage 	libraries
 * @author     	Kim Kleyboecker
 * @version		1.0
 * @author		Kim Kleyboecker
 */
abstract class Database_Result implements ArrayAccess, Iterator, Countable {

	/**
	 *
	 * @var object Result resource
	 */
	protected $result;

	/**
	 *
	 * @var mixed Result insert id
	 */
	protected $insert_id;

	/**
	 *
	 * @var string Result SQL
	 */
	protected $sql;

	/**
	 *
	 * @var long-integer	Current row
	 */
	protected $current_row = 0;

	/**
	 *
	 * @var long-integer	total rows
	 */
	protected $total_rows  = 0;

	/**
	 *
	 * @var mixed 	Fetch return type
	 */
	protected $fetch_type;

	/**
	 *
	 * @var string 	Fetch function
	 */
	protected $return_type;

	/**
	 * Returns the SQL used to fetch the result.
	 *
	 * @return  string	the SQL used to fetch the result
	 */
	public function sql()
	{
		return $this->sql;
	}

	/**
	 * Returns the insert id from the result.
	 *
	 * @return  mixed 	the insert id
	 */
	public function insert_id()
	{
		return $this->insert_id;
	}

	/**
	 * SEP_return_val - for SEP package calls only
	 *
	 * @return  string
	 */
	abstract public function SEP_return_val();


	/**
	 * Prepares the query result.
	 *
	 * @todo clarify what the $type refers to in this method and what the return value is
	 * @param   boolean   $object	return rows as objects
	 * @param   mixed     $type		type
	 * @return  object|array	the query result?
	 */
	abstract function result($object = TRUE, $type = FALSE);

	/**
	 * Builds an array of query results.
	 *
	 * @param   boolean   $object	return rows as objects
	 * @param   mixed     $type		type
	 * @return  array		the query results
	 */
	abstract function result_array($object = NULL, $type = FALSE);

	/**
	 * Gets the fields of an already run query.
	 *
	 * @return  array	the array of query fields
	 */
	abstract public function list_fields();

	/**
	 * Returns field data about the current result.
	 *
	 * <b>Due to Oracle contraints cannot get nullable flag.</b>
	 *
	 * @return  array	the array of column metadata
	 */
	abstract public function field_data();

	/**
	 * Seek to an offset in the results.
	 *
	 * @todo define what the boolean return value refers to
	 * @return  boolean
	 */
	abstract public function seek($offset);

	/**
	 * retrieves the total number of rows of the result
	 *
	 * Countable: count
	 *
	 * @return  integer	the count of rows
	 *
	 */
	public function count()
	{
		return $this->total_rows;
	}

	/**
	 * Determines whether a given row offset exists
	 *
	 * ArrayAccess: offsetExists
	 *
	 * @param integer 	$offset	the offset to test
	 * @return  boolean		the existence of the offset
	 */
	public function offsetExists($offset)
	{
		if ($this->total_rows > 0)
		{
			$min = 0;
			$max = $this->total_rows - 1;

			return ! ($offset < $min OR $offset > $max);
		}

		return FALSE;
	}

	/**
	 * retrieve the offset row if it exists
	 *
	 * ArrayAccess: offsetGet
	 *
	 * @param integer 	$offset	the offset to fetch
	 * @return boolean|array	the offset row or a failure flag
	 */
	public function offsetGet($offset)
	{
		if ( ! $this->seek($offset))
			return FALSE;

		// Return the row by calling the defined fetching callback
		return call_user_func($this->fetch_type, $this->result, $this->return_type);
	}

	/**
	 * ArrayAccess: offsetSet
	 *
	 * @todo determine if there is a need for a functional revision of this method
	 * throws a Kohana_Database_Exception
	 */
	final public function offsetSet($offset, $value)
	{
		throw new Kohana_Database_Exception('database.result_read_only');
	}

	/**
	 * ArrayAccess: offsetUnset
	 *
	 * @todo determine if there is a need for a functional revision of this method
	 * throws a Kohana_Database_Exception
	 */
	final public function offsetUnset($offset)
	{
		throw new Kohana_Database_Exception('database.result_read_only');
	}

	/**
	 * Get the current row of the result
	 *
	 * Iterator: current
	 *
	 * @return array	the row data
	 *
	 */
	public function current()
	{
		return $this->offsetGet($this->current_row);
	}

	/**
	 * Iterator: key
	 *
	 * @todo figure out why we need this method if it does the same thing as current()?
	 * @see current()
	 * @return array	the row data
	 */
	public function key()
	{
		return $this->current_row;
	}

	/**
	 * get the next row
	 *
	 * Iterator: next
	 *
	 * @return array	the row data
	 */
	public function next()
	{
		return ++$this->current_row;
	}

	/**
	 * get the previous row
	 *
	 * Iterator: prev
	 *
	 * @return array	the row data
	 */
	public function prev()
	{
		return --$this->current_row;
	}

	/**
	 * get the first row of the result
	 *
	 * Iterator: rewind
	 *
	 * @return array	the row data
	 */
	public function rewind()
	{
		return $this->current_row = 0;
	}

	/**
	 * determine if this is a valid (non-empty) result
	 *
	 * Iterator: valid
	 *
	 * @return boolean	whether there is a first row in the result
	 */
	public function valid()
	{
		return $this->offsetExists($this->current_row);
	}

} // End Database Result Interface
