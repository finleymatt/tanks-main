<?php defined('SYSPATH') or die('No direct script access.');
 /**
 * @package		NMED
 * @subpackage 	libraries[drivers][Database]
 * ML: Extended this library to add bound vars option to regular SQL
 * ML: Modified escape_table method to not include owner name as default
 */

/**
 * A constant used in string concatenation:
 *
 * phpCrLf = carriage return + line feed
 */
define("phpCrLf", Chr(13).Chr(10));

/**
 * A constant used in string concatenation:
 *
 * phpLf = line feed
 */
define('phpLf', Chr(10));

/**
 * A constant used in string concatenation:
 *
 * phpTab = tab
 */
define("phpTab",Chr(9));

/**
 * Oracle Database driver
 *
 * Provides api layer for Oracle database server connectivity and requests
 * via oci8 library calls
 *
 * This class provides the Kohana database driver functionality for Oracle databases
 *
 * @todo modify the whole 'fetch' type thing; Only 'oci_fetch_all' returns <strong>all</strong> the records; {@link http://us2.php.net/manual/en/function.oci-fetch-all.php};
 * 'oci_fetch_object' returns each row of the result, as does 'oci_fetch_row', 'oci_fetch_array', 'oci_fetch_assoc' and 'oci_fetch';
 * We need to possibly refactor this class to provide for these other fetch types
 * @todo figure out what the deal is with the static variable <i>query_cache</i> and document it
 * @todo finish unfinished methods
 * @todo determine if phpTab and phpCrLF are needed; delete those lines if not
 * @package		dbconnectr
 * @subpackage 	libraries[drivers][Database]
 * @author     	Kim Kleyboecker, original design & implementation
 * @author Todd Hochman, modifications, bugfixes & improvements
 * @version		1.2
 *
 *
 */
class Database_Oracle_Driver extends Database_Driver {

	/**
	 * Database connection link
	 *
	 * @var resource 	the connection resource
	 *
	 */
	protected $link;

	/**
	 * The  resource returned from oci_parse()
	 *
	 * @var resource 	the database statement resource;
	 */
	protected $statement;

	/**
	 * @var config the Database configuration
	 */
	protected $db_config;

	/**
	 *
	 * @var unknown_type
	 */
	//protected $query_cache;
	static $query_cache;

	/**
	 * Sets the $db_config for the class upon object construction
	 *
	 * @todo clean up commented out lines
	 * @param  array  $config	database configuration
	 * @return VOID
	 */
	public function __construct($config)
	{
		$this->db_config = $config;
		//Log::add('debug', 'Oracle Database Driver Initialized');
	}

	/**
	 * Closes the database connection.
	 *
	 * @return VOID
	 */
	public function __destruct()
	{
		is_resource($this->link) and oci_close($this->link);
	}

	/**
	 * Connect to our database.
	 *
	 * Returns FALSE on failure or a connection resource.
	 * @todo clean up commented out lines
	 * @return object|boolean	the connection resource or a failure flag
	 */
	public function connect()
	{
		// Check if link already exists
		if (is_resource($this->link)){
			//$sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'DD-MON-YY HH24:MI:SS'";
			//$this->stmt_prepare($sql);
			//$bOCIExecuteResult = oci_execute($this->statement);
			return $this->link;
		}
		// Import the connect variables
		extract($this->db_config['connection']);

		// Persistent connections enabled?
		$connect = ($this->db_config['persistent'] == TRUE) ? 'oci_pconnect' : 'oci_connect';

		// Build the connection info
		$database = isset($database) ? $database : null;

		// Make the connection and select the database
		if (($this->link = $connect($user, $pass, $database))){
			// Clear password after successful connect
			$this->config['connection']['pass'] = NULL;

			//$sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'DD-MON-YY HH24:MI:SS'";
			//$this->stmt_prepare($sql);
			//$bOCIExecuteResult = oci_execute($this->statement);
			return $this->link;
		}
		return FALSE;
	}

	/**
	 * Perform a query based on a manually written query.
	 *
	 * @todo clean up commented out lines
	 * @param  string  $sql	SQL query to execute
	 * @return object 	Database Result
	 */
	public function query($sql, $bound_vars=array())
	{
		// Only cache if it's turned on, and only cache if it's not a write statement
		if ($this->db_config['cache'] AND ! preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET)\b#i', $sql))
		{

			$hash = $this->query_hash($sql);

			if ( ! isset(self::$query_cache[$hash])){
				// Set the cached object

				$this->stmt_prepare($sql);
				self::$query_cache[$hash] = new Oracle_Result($this->statement, $this->link, $this->db_config['object'], $sql);
			}

			// Return the cached query
			return self::$query_cache[$hash];
		}

		if(preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET)\b#i', $sql))
		{
			/***********************************************************
			 ML commented out if block that was here for INSERT, since
			 not all tables have ID  nor just a single one if it does
			************************************************************/
			$this->stmt_prepare($sql);
			foreach ($bound_vars as $name => $value)
				oci_bind_by_name($this->statement, $name, $bound_vars[$name]);
			oci_execute($this->statement);
			return TRUE;
		}
		else{
			if(preg_match('/SEP_SECURITY/i', $sql)){
				/*
				 * probably NMED SEP package call
				 *
				 */
				$this->stmt_prepare($sql);
				oci_bind_by_name($this->statement, ":rID", $rowid, 50, SQLT_CHR);
				oci_execute($this->statement);
				return new Oracle_Result($this->statement, $this->link, $this->db_config['object'], $sql, $rowid);
			}
			elseif(preg_match('/\bBEGIN\b/i', $sql))//begin Oracle package dealage
			{
				//NMED insert package call or other package call
				$this->stmt_prepare($sql);//PARSES the statement
				//fork here on presence of :rID; if it's in there, it's a CRUD package call, otherwise it's a different pkg call
				if(strpos($sql, ':rID'))//std CRUD
				{
					oci_bind_by_name($this->statement, ":rID", $rowid, -1, SQLT_INT);
					//skips down to end of Oracle package dealage
				}
				else //different (generic, i.e. non-CRUD) package call
				{
					//look for oracle variable placeholders:
					//if(preg_match_all('/(\s|\(|,)\:[A-Za-z]+\b/i', $sql, $bindings))
					if(preg_match('/(:[A-Za-z]*)\b/i', $sql))
					{
						if(($numargs = func_num_args()) > 1)//check the arguments for args>1
						{
							//2 args
							//if the arg[1] is an array, use it as the source of the bind variables:
							if(is_array($extra_arg_1 = func_get_arg(1)))
							{
								$bind_vars = $extra_arg_1;
							}
							else //make an array out of any additional arguments; cursors are not available this way
							{
								$bind_vars = array_slice(func_get_args(), 1);
							}
						}
						//error trap if no bindings array given:
						if( ! (func_num_args()) > 1)
						{
							//Added this throw and corresponding error msg, TH, 01-06-10
							throw new Kohana_Database_Exception('database.no_binding_found');
						}
						//we got the stuff; go do the package binding etc. and return us an oracle result:
						return $this->process_package_call($bind_vars, $sql);
					}
					else//preg match fails
					{
						//Throw exception here; nothing to bind to
						throw new Kohana_Database_Exception('database.no_binding_found');
					}//**** end modified section, TH, 2010.06.24
					//*************************************
					//*************************************
				}//end of Oracle package dealage
				oci_execute($this->statement);
				return new Oracle_Result($this->statement, $this->link, $this->db_config['object'], $sql, $rowid);
			}
			else//Regular ol' pass thru SQL:
			{
				$this->stmt_prepare($sql);

				foreach ($bound_vars as $name => $value)
					oci_bind_by_name($this->statement, $name, $bound_vars[$name]);  // oci_bind_by_name($stmt, $name, $value) does not work
				oci_execute($this->statement);
				return new Oracle_Result($this->statement, $this->link, $this->db_config['object'], $sql);
			}
		}
	}

	/**
	 * Returns only the first row if exists.  Useful for aggregate queries.
	 *
	 * @param string	$sql	SQL query to execute
	 * @return array	array
	 */
	public function query_row($sql, $bound_vars=array())
	{
		$result = $this->query($sql, $bound_vars)->as_array();
		if (count($result)) {
			return($result[0]);
		}
		else {
			return(array());
		}
	}

	/**
	 * Returns only the first value of first row.  Useful for lookups.
	 *
	 * @param string        $sql    SQL query to execute
	 * @return object       mixed
	 */
	public function query_field($sql, $bound_vars=array())
	{
		$result = $this->query($sql, $bound_vars)->as_array();
		if (count($result)) {
			$keys = array_keys($result[0]);
			return($result[0][$keys[0]]);
		}
		else {
			return('');
		}
	}

	public function procedure_call($sql, $bound_vars=array())
	{
		$this->stmt_prepare($sql);
		foreach ($bound_vars as $name => $value)
			oci_bind_by_name($this->statement, $name, $bound_vars[$name]);
		return(oci_execute($this->statement));
		//return new Oracle_Result($this->statement, $this->link, $this->db_config['object'], $sql);
	}

	/**
	 * processes Genereric (non-CRUD) Oracle package calls and delivers an Oracle result object
	 *
	 * Currently only set up up to handle calls for the Reportr app/module, i.e. X number of IN parameters
	 * and a single OUT Cursor parameter
	 *
	 * @todo add in means to handle other types of parameters that may be needed in a package call:
	 * - in cursor
	 * - in/out param
	 * - in/out cursor
	 * - out BLOB or CLOB
	 * - multiple OUT params
	 *
	 * @todo make sure this works with FUNCTION calls
	 *
	 * @todo see if we need some means of determining bound variable TYPE, as some may be integers etc
	 *
	 * @todo we may need to include some means of determining the return sizes of the bound variables?
	 * - not sure if (-1) will work all the time for bound variable sizes; perhaps if we allowed for the size to be in parens, square brackets or something
	 * that we could parse the query string for and pass along to this? Not sure if this is even necessary...
	 *
	 * @todo clean up commented-out lines
	 *
	 * @param string-array	$bind_vars	the array of variables to bind and the values to bind them to
	 * @param string	$sql	the PL/SQL string to use
	 * @return object	Oracle Result object
	 */
	protected function process_package_call($bind_vars, $sql)
	{

		//determine the convention for out variables:
		$outparam_indicators = Kohana::config('database.outparam_indicators');
		//this will make sense in a moment, but it's so the bound variables don't get overwritten:
		$bind_ctr = -1;
		foreach($bind_vars as $bind_var => $bind_value)
		{
			++$bind_ctr;//increment here so we don't get confused if there's an OUT param
			//see if our bind variable is any sort of OUT parameter:
			foreach($outparam_indicators as $indicator)
			{
				if(strpos($bind_var, $indicator))
				{
					//lose the suffix:
					$outparam = substr($bind_var, 0, (strlen($bind_var) - strlen($indicator)));
					//flag if we have an OUT cursor:
					$out_cursor = ($indicator = $outparam_indicators[1] ? TRUE : FALSE);
					continue 2;//skip to the next bind variable
				}
			}
			//use non-changing variables so the bind doesn't get improperly bound:
			$bound_var[$bind_ctr] = ":$bind_var";
			$bound_value[$bind_ctr] = $bind_value;
			//bind the IN parameter:
			oci_bind_by_name($this->statement, $bound_var[$bind_ctr], $bound_value[$bind_ctr], 32);
		}//done binding IN params
		if(isset($outparam))//there cannot be >1 outparam ##########MODIFY TO ALLOW >1 OUT PARAM!!!!!
		{
			//give it the right format:
			$bound_outparam = ":$outparam";
			//determine if we're expecting a cursor as the OUT param:
			if($out_cursor)//special handling for CURSOR type OUT params:
			{
				//Create a new cursor resource
				$cursor = oci_new_cursor($this->link);

				// Bind the cursor resource to the Oracle argument
				oci_bind_by_name($this->statement, $bound_outparam, $cursor, -1, OCI_B_CURSOR);

				//execute the statement first:
				oci_execute($this->statement);

				//THEN execute the cursor:
				oci_execute($cursor);

				//return the result from the cursor:
				return new Oracle_Result($cursor, $this->link, $this->db_config['object'], $sql);
			}
			else//regular ol' OUT param, i.e., NOT a cursor:
			{
				//bind the variable to the statement:
				oci_bind_by_name($this->statement, $bound_outparam, $bind_value);
				//execute the statement:
				oci_execute($this->statement);
				//return $outparam;//MAY HAVE TO DO IT THIS WAY? NEED TO VERIFY THE RESULT OBJECT WILL BEHAVE AS EXPECTED...
				return new Oracle_Result($this->statement, $this->link, $this->db_config['object'], $sql);
			}
		}
	}


	/**
	 * Builds a DELETE query.
	 *
	 * @param   string  $table	table name
	 * @param   array   $where	where clause
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
	 * @todo figure out if we need this functionality (probably not) and implement it if so
	 * @param  string  character set to use
	 */
	public function set_charset($charset)
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}

	/**
	 * Wrap the tablename in backticks, has support for: table.field syntax.
	 *
	 * @param   string  $table	table name
	 * @return  string	the escaped tablename string
	 */
	public function escape_table($table){ // ML: removed owner name prefix default
		if (!empty($this->db_config['connection']['schema']))
			return $this->db_config['connection']['schema'] . '.' . $table;
		else
			return $table;
	}

	/**
	 * Escape a column/field name, has support for special commands.
	 *
	 * @todo clean up commented out lines
	 * @param   string  $column	column name
	 * @return  string	the escaped columnname string
	 */
	public function escape_column($column){

		/*
		 * no special escapes for now
		 */

		/*
		 * if somebody passed in dot notation (ORM does) escape dot
		 */
		//if(strpos($column,'.')!==FALSE){
			/*
			 * but not when there's an asterisk
			 */
			//if(strpos($column,"*")===FALSE){
				//$column = str_replace('.','"."',$column);
			//}
		//}

		//return '"'.$column.'"';
		return $column;
	}

	/**
	 * Builds a WHERE portion of a query.
	 *
	 * @todo clean up commented out lines
	 * @param   mixed    $key			key
	 * @param   string   $value			value
	 * @param   string   $type			type
	 * @param   int      $num_wheres	number of where clauses
	 * @param   boolean  $quote			whether to escape the value
	 * @return  string	the SQL string WHERE portion
	 */
	public function where($key, $value, $type, $num_wheres, $quote)
	{
		$prefix = ($num_wheres == 0) ? '' : $type;

		if ($quote === -1)
		{
			$value = '';
		}
		else
		{
			if ($value === NULL)
			{
				if ( ! $this->has_operator($key))
				{
					//$key .= ' IS';
					$key = $this->escape_column($key).' IS';
				}

				$value = ' NULL';
			}
			elseif (is_bool($value))
			{
				if ( ! $this->has_operator($key))
				{
					$key .= ' =';
				}

				$value = ($value == TRUE) ? ' 1' : ' 0';
			}
			else
			{
				if ( ! $this->has_operator($key))
				{
					$key = $this->escape_column($key).' =';
				}
				else
				{
					preg_match('/^(.+?)([<>!=]+|\bIS(?:\s+NULL))\s*$/i', $key, $matches);
					if (isset($matches[1]) AND isset($matches[2]))
					{
						$key = $this->escape_column(trim($matches[1])).' '.trim($matches[2]);
					}
				}

				$value = ' '.(($quote == TRUE) ? $this->escape($value) : $value);
			}
		}

		return $prefix.$key.$value;
	}

	/**
	 * Builds a LIKE portion of a query.
	 *
	 * @param   mixed    $field			field name
	 * @param   string   $match			value to match with field
	 * @param   boolean  $auto			add wildcards before and after the match
	 * @param   string   $type			clause type (AND or OR)
	 * @param   int      $num_likes		number of likes
	 * @return  string	the SQL string LIKE portion
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
	 * @param   mixed    $field			field name
	 * @param   string   $match			value to match with field
	 * @param   boolean  $auto			add wildcards before and after the match
	 * @param   string   $type			clause type (AND or OR)
	 * @param   int      $num_likes		number of likes
	 * @return  string	the SQL string NOT LIKE portion
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
	 * @todo determine the need for the functionality this method provides and implement it if necessary
	 * @param   string   $field			field name
	 * @param   string   $match			value to match with field
	 * @param   string   $type			clause type (AND or OR)
	 * @param   integer  $num_regexs	number of regexes
	 * @return  string	the SQL string REGEX portion
	 */
	public function regex($field, $match, $type, $num_regexs)
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}

	/**
	 * Builds a NOT REGEX portion of a query.
	 *
	 * @todo determine the need for the functionality this method provides and implement it if necessary
	 * @param   string   $field			field name
	 * @param   string   $match			value to match with field
	 * @param   string   $type			clause type (AND or OR)
	 * @param   integer  $num_regexs	number of regexes
	 * @return  string	the SQL string NOT REGEX portion
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
	 * @return  string	the SQL string
	 */
	public function insert($table, $keys, $values){

		//Escape the column names
		for($i=0;$i<count($keys);$i++){
			if($keys[$i]!="ID"){
				$ColNames[$i] = $this->escape_column($keys[$i]);
				$ColValues[$i] = $values[$i];
			}
		}
		return 'INSERT INTO '.$this->escape_table($table).phpLf.'('.implode(', ', $ColNames).')'.phpLf.'VALUES'.phpLf.'('.implode(', ', $ColValues).')'.phpLf.' ';

	}
	/**
	 /** Builds a MERGE portion of a query.
	 *
	 * @todo determine the need for the functionality this method provides and implement it if necessary
	 * @param   string  $table		table name
	 * @param   array   $keys		keys
	 * @param   array   $values		values
	 * @return  string	the SQL string MERGE portion
	 */
	public function merge($table, $keys, $values)
	{
		throw new Kohana_Database_Exception('database.not_implemented', __FUNCTION__);
	}


	/**
	 * Builds a LIMIT portion of a query.
	 *
	 * Limits are tricky in Oracle -- have to do some gyrations to get them
	 *
	 * @todo replace the current limit method with something that does the example code
	 * @todo test this LIMIT method
	 * @link http://www.club-oracle.com/forums/limit-clause-for-oracle-sql-t637/ this is one of the better solutions IMO
	 * @link http://forums.oracle.com/forums/thread.jspa?messageID=1430123	see this Oracle forum thread
	 * @link http://www.phpbuilder.com/board/archive/index.php/t-610278.html see this php forum thread
	 * @link http://www.webdeveloper.com/forum/showthread.php?t=107205 see this other forum thread
	 * <strong>NOTE:</strong> The method currently outlined below does NOT work.
	 *
	 *
	 * @param   integer  $limit		record limit
	 * @param   integer  offset		row offset at which to start
	 * @param	boolean	$UsesAND	whether the limit is part of a more complex WHERE statement
	 * @return  string	the SQL string LIMIT portion
	 */
	public function limit($limit, $offset = 0, $UsesAND = FALSE)
	{
		//return 'LIMIT '.$offset.', '.$limit;//MySQL version
		/*
		 * EXAMPLE WORKING ORACLE LIMIT:
		 *
		 * SELECT * FROM (
				SELECT p.PERMITID, p.PROCESSINGNUMBER, p.COMPANYNAME,
					p.OWNERLASTNAME, p.OWNERFIRSTNAME, p.OWNERMIDDLEINITIAL, p.INSTALLERCOMPANYNAME,
          p.INSTALLERLASTNAME, p.SYSTEMADDRESS, p.SYSTEMADDRESS2, p.SYSTEMADDRESS3,
          p.SYSTEMCITY, p.SUBDIVISION, p.BLOCK, p.LOT, p.SECTION, p.RANGE_, p.PROPERTYCODE,
					RANK() OVER (ORDER BY ROWNUM ASC) rn
				FROM LWB.MV_PERMITS p
				WHERE (((((p.OWNERLASTNAME)  LIKE '%GARCIA')
						OR ((p.OWNERLASTNAME)  LIKE '%GARCIA%')
						OR ((p.OWNERLASTNAME)  LIKE 'GARCIA%')))  ))
		WHERE rn >=400 AND rn <=500;
		 */
		$rowcount = $offset + $limit;
		$limit_SQL = "";
		if($UsesAND){
			$limit_SQL .= " AND ROWNUM >= $offset ";
			$limit_SQL .= " AND ROWNUM <= $rowcount ";
		}
		else{
			$limit_SQL .= " WHERE ROWNUM >= $offset ";
			$limit_SQL .= " AND ROWNUM <= $rowcount ";
		}
		return $limit_SQL;
	}

	/**
	 * Creates a prepared statement.
	 *
	 * Runs oci_parse()
	 *
	 * @param   string  $sql	SQL query
	 * @return VOID
	 *
	 */
	public function stmt_prepare($sql = '')
	{
		$this->statement = oci_parse($this->link,$sql);
	}

	/**
	 *  Compiles the SELECT statement.
	 *
	 *  Generates a query string based on which functions were used.
	 *
	 *  Should not be called directly, the get() function calls it.
	 *
	 * @param   array   $database	seleprotectedy values
	 * @return  string 	the SQL string
	 */
	public function compile_select($database){

		$sql = ($database['distinct'] == TRUE) ? 'SELECT DISTINCT ' : 'SELECT ';
		$sql .= (count($database['select']) > 0) ? implode(', ', $database['select']) : '*';
		if(strpos($sql,'.*"')!==FALSE){
			$sql = str_replace('.*"','".*',$sql);
		}

		if (count($database['from']) > 0)
		{
			// Escape the tables
			$froms = array();
			foreach ($database['from'] as $from)
				$froms[] = $this->escape_table($from);
			$sql .= "\nFROM ";
			$sql .= implode(', ', $froms);
		}

		if (count($database['join']) > 0)
		{
			$sql .= ' '.$database['join']['type'].'JOIN ('.implode(', ', $database['join']['tables']).') ON '.implode(' AND ', $database['join']['conditions']);
		}

		$limitUsesAND = FALSE;
		if (count($database['where']) > 0)
		{
			$sql .= "\nWHERE ";
			$limitUsesAND = TRUE;
		}

		$sql .= implode("\n", $database['where']);

		if (is_numeric($database['limit']))
		{
			$sql .= "\n";
			$sql .= $this->limit($database['limit'], $database['offset'], $limitUsesAND);
		}

		if (count($database['groupby']) > 0)
		{
			$sql .= "\nGROUP BY ";
			$sql .= implode(', ', $database['groupby']);
		}

		if (count($database['having']) > 0)
		{
			$sql .= "\nHAVING ";
			$sql .= implode("\n", $database['having']);
		}

		if (count($database['orderby']) > 0)
		{
			$sql .= "\nORDER BY ";
			$sql .= implode(', ', $database['orderby']);
		}

		return $sql;

	}

	/**
	 * Determines if the string has an arithmetic operator in it.
	 *
	 * @param   string   $str	string to check
	 * @return  boolean		whether the string contains an arithmetic operator
	 */
	public function has_operator($str)
	{
		return (bool) preg_match('/[<>!=]|\sIS(?:\s+NOT\s+)?\b/i', trim($str));
	}

	/**
	 * Escapes any input value.
	 *
	 * @param   mixed   $value	value to escape
	 * @return  string the escaped value
	 */
	public function escape($value)
	{
		if ( ! $this->db_config['escape'])
			return $value;

		if(empty($value) && !is_numeric($value)){  // ML: 0 is not null
			$value = 'NULL';
		}
		else{
			switch (gettype($value)){
				case 'string':
					if(is_numeric($value)){
						if(is_float($value)){
							$value = sprintf('%F', $value);
						}
						elseif(is_int($value)){
							$value = (int) $value;
						}
					}
					else{
						if(strpos($value,"TO_DATE(")===FALSE){
							$value = '\''.$this->escape_str($value).'\'';
						}
						else{
							$value = str_replace('", "','"|"',$value);
						}
					}
					break;
				case 'boolean':
					$value = (int) $value;
				break;
				case 'double':
					// Convert to non-locale aware float to prevent possible commas
					$value = sprintf('%F', $value);
				break;
				default:
					$value = ($value === NULL) ? 'NULL' : $value;
				break;
			}
		}
		return (string) $value;
	}

	/**
	 * Escapes a string for a query.  ML: created feature to escape apostrophe
	 *
	 * @param   mixed   $str	string to escape
	 * @return  string	the escaped string
	 */
	public function escape_str($str){

		if (!$this->db_config['escape']){
			return $str;
		}

		return str_replace("'", "''", $str);
	}

	/**
	 * Lists all tables in the database.
	 *
	 * @return  string-array	the array of tablenames
	 */
	public function list_tables(){

		/*
		 * Import the connect variables. Read PHP documentation on extract function to understand
		 * how the value of $schema gets set
		 */
		extract($this->db_config['connection']);

		$sql    = "SELECT * FROM ALL_TABLES WHERE OWNER = '$schema' ";
		$retval = $this->query($sql)->result_array(FALSE, OCI_ASSOC);

		return $retval;

	}

	/**
	 * Lists all views in the schema.
	 *
	 * @return  string-array	the array of viewnames
	 */
	public function list_views(){

		/*
		 * Import the connect variables. Read PHP documentation on extract function to understand
		 * how the value of $schema gets set
		 */
		extract($this->db_config['connection']);

		$sql    = "SELECT * FROM ALL_VIEWS WHERE OWNER = '$schema' ";
		$retval = $this->query($sql)->result_array(FALSE, OCI_ASSOC);

		return $retval;

	}


	/**
	 * Lists all fields in a table.
	 *
	 * Since we are querying the data dictionary everything must be upper case.
	 *
	 * @todo clean up commented out lines
	 * @param   string  $table	target table name
	 * @return  string-array	the array of field names
	 */
	public function list_fields($table){

		/*
		 * Since we are querying the data dictionary everything will be upper case.
		 */
		//$table = strtoupper($table);

		$sql="";
		$sql.="select table_name AS TNAME, ";
		$sql.="       column_name AS NAME, ";
		$sql.="       data_type AS TYPE, ";
		$sql.="       substr(decode( data_type, 'NUMBER', decode( data_precision, NULL, NULL,'('||data_precision||','||data_scale||')' ),data_length),1,11) AS LENGTH, ";
		$sql.="       decode( nullable, 'Y', 'Yes', 'No' ) AS nullable ";
		$sql.="from all_tab_columns ";
		//$sql.="where table_name = upper('$table') ";
		$sql.="where table_name = '$table' ";
		$sql.="order by column_id ";
		//$sql.="";

		$resultarray = array();
		//$numrows

		$statement = oci_parse($this->link,$sql);
		oci_execute($statement);

		$numrows = oci_fetch_all($statement, $resultarray, null, null, OCI_FETCHSTATEMENT_BY_ROW);

		$arFieldData = array();
		foreach($resultarray as $row){
			/*
		 	 * Since we are querying the data dictionary everything will be upper case.
		 	 * convert column names to lower case since it will not matter in the query building stuff what the case is(?)
		 	 */
			$sColName = $row['NAME'];
			$arFieldData[$sColName] = $row['TYPE'];
		}

		return $arFieldData;

	}

	/**
	 * Returns the last database error.
	 *
	 * @todo finish this Oracle show_error method? It is blank....
	 * @return  string	the most recent database error
	 */
	public function show_error(){

	}

	/**
	 * Returns field data about a table.
	 *
	 * Since we are querying the data dictionary everything will be upper case.
	 *
	 * @todo clean up commented out lines
	 * @param   string  $table	target table name
	 * @return  string-array	the array of field data
	 */
	public function field_data($table){

		/*
		 * Since we are querying the data dictionary everything will be upper case.
		 */
		//$table = strtoupper($table);

		$sql="";
		$sql.="select table_name AS TNAME, ";
		$sql.="       column_name AS NAME, ";
		$sql.="       data_type AS TYPE, ";
		$sql.="       substr(decode( data_type, 'NUMBER', decode( data_precision, NULL, NULL,'('||data_precision||','||data_scale||')' ),data_length),1,11) AS LENGTH, ";
		$sql.="       decode( nullable, 'Y', 'Yes', 'No' ) AS nullable ";
		$sql.="from all_tab_columns ";
		//$sql.="where table_name = upper('$table') ";
		$sql.="where table_name = '$table' ";
		$sql.="order by column_id ";
		//$sql.="";

		$resultarray = array();
		//$numrows

		$statement = oci_parse($this->link,$sql);
		oci_execute($statement);

		$numrows = oci_fetch_all($statement, $resultarray, null, null, OCI_FETCHSTATEMENT_BY_ROW);

		$arFieldData = array();
		foreach($resultarray as $row){
			/*
		 	 * Since we are querying the data dictionary everything will be upper case.
		 	 * convert column names to lower case since it will not matter in the query building stuff what the case is(?)
		 	 */
			$sColName = $row['NAME'];
			$arFieldData[$sColName] = array('name'=>$sColName,
											'type'=>$row['TYPE'],
											'size'=>$row['LENGTH'],
											'nullable'=>$row['NULLABLE']);
		}

		return $arFieldData;
	}

	/**
	 * Fetches Oracle server connection info.
	 *
	 * @return  boolean|string 	FALSE or the server version info
	 */
	public function oracle_server_version(){

		if (!is_resource($this->link)){
			return FALSE;
		}
		else{
			$sRetStr = oci_server_version($this->link);
			return $sRetStr;
		}

	}

	/**
	 * Fetches SQL type information about a field, in a generic format.
	 *
	 * @see Database_Driver::sql_type()
	 * @param   string  $str	field datatype
	 * @return  string-array	sql field type information
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
	 * Creates a hash for a SQL query string.
	 *
	 * Replaces newlines with spaces, trims, and hashes.
	 *
	 * @todo replace SHA1 with SHA256 or another Hash that is still considered secure; SHA1 is no longer considered to be sufficient security
	 * @param   string  	$sql	SQL query
	 * @return  string	the hashed string
	 */
	protected function query_hash($sql)
	{
		return sha1(str_replace("\n", ' ', trim($sql)));
	}

} // End Database Driver Interface

/**
 * Oracle_Result
 *
 * @todo confirm with Kley that the class variable types and purposes are correct
 * @package		NMED
 * @subpackage 	libraries[drivers][Database]
 * @author     	Kim Kleyboecker
 * @version		1.0
 *
 */
class Oracle_Result extends Database_Result
{
	/**
	 * @var object 	query result, usually a 2d associattive array of field names and values
	 */
	protected $result;

	/**
	 * @var string	the sql string
	 */
	protected $sql;

	/**
	 * @var integer 	rows to skip?
	 */
	protected $skip = 0;

	/**
	 * @var integer	maximum count of rows to return? Not sure...
	 */
	protected $maxrows = -1;

	/**
	 * @var long-integer	the row number of the current row; Current and total rows
	 */
	protected $current_row = 0;

	/**
	 * @var  long-integer	the row total count
	 */
	protected $total_rows = 0;

	/**
	 * @var  long-integer	insert identifier...I think
	 */

	protected $insert_id;

	/** Fetch function and return type
	 *
	 * @var string the type of fetched result
	 */
	protected $fetch_type = 'oci_fetch_all';

	/**
	 * @todo verify this variable is necessary; it only shows up in one commented-out line!
	 * @var unknown_type
	 */
	protected $return_type;

	/**
	 * @var mixed-array	the array of result rows...I think
	 */
	protected $resultarray;

	/**
	 * public properties
	 *
	 *
	 * @var database-resource	the DB resource...I think
	 */
	public $stmt;

	/**
	 * @todo verify this variable is necessary; it only shows up in one place!
	 * @var database-resource	the DB link info...I think
	 */
	public $link;

	/**
	 * @var long-integer	the SEP row identifier...I think
	 */
	public $SEP_return_val;

	/**
	 * Sets up the result variables.
	 *
	 *
	 *
	 * @todo clean up commented out lines
	 * @todo put in means to handle generic package return types that are NOT cursors, e.g. single values
	 * @todo put in means to handle generic package calls that return >1 OUT parameter???
	 * @todo modify to provide other fetch types that only return <em>each row</em> of the result:
	 * - 'oci_fetch_object'
	 * - 'oci_fetch_row'
	 * - 'oci_fetch_array'
	 * - 'oci_fetch_assoc'
	 * - 'oci_fetch'
	 * @todo put in means to assign values for skip rows and max rows either on construction
	 * @link http://us2.php.net/manual/en/function.oci-fetch-all.php
	 * @param  	resource  		$stmt		query result
	 * @param  	resource  		$link		database link
	 * @param  	boolean   		$object		return an object that contains the data if TRUE, or simply retrun the array of data if FALSE
	 * @param  	string    		$sql		SQL query that was run
	 * @param	long-integer	$rowid		row identifier or operation signifier
	 * @return VOID
	 */
	public function __construct($stmt, $link, $object = TRUE, $sql, $rowid = NULL)
	{
		$this->stmt = $stmt;
		$this->link = $link;
		$fetch_type = $this->fetch_type;
		// If the query is a resource, it was a SELECT or SHOW query
		if (is_resource($stmt))
		{
			$this->current_row = 0;
			//get the OCI fetch type to use: REMOVED as all but 'fetch_all' return only a single row; add this functionality in later elsewhere
			//$fetch_type = $this->fetchtype($object);//#########################

			//many methods in the result class depend on the OCI_FETCHSTATEMENT_BY_ROW option
			//it is not the default value for this switch
			if(preg_match('/SEP_SECURITY/i', $sql))//SEP call
			{
				//for NMED use only - the rowid contructor parameter is the return values from SEP package calls
				$this->SEP_return_val = $rowid;
			}
			elseif(preg_match('/\bBEGIN\b/i', $sql))//package call
			{
				//for NMED use with standardized delete, update or insert package operations
				if( ! is_null($rowid))
				{
					switch($rowid)
					{
						//customized Oracle error messages that Kley built into his CRUD packages:
						case -20001:
						case -20002:
						case -20003:
						case -20004:
							throw new Kohana_Database_Exception('database.error', oci_error($this->stmt).' - '.$sql);
							break;
						case 0://don't know when the package returns 0 (?)//####### IS THIS NEEDED? ##############
						case -1://update or delete
						default://insert
							/*
							 * the WHOLE POINT of the package insert for Oracle is to get the value of the pk after the insert
							 * sheesh!
							 */
							$this->insert_id  = $rowid;
					}
				}
				else //HANDLE GENERIC PACKAGE CALLS
				{
					//CURRENTLY THIS ONLY WORKS if the result is a data array!!!!
					//NEED TO ADD MEANS TO ASSIGN VALUES TO SKIP AND MAXROWS BEFORE EXECUTION
					//NEED TO ADD MEANS TO HANDLE SINGLE VALUE RETURNS AS WELL AS RETURNS THAT CONSIST OF MULTIPLE 'OUT' PARAMETERS

					$this->total_rows = oci_fetch_all($stmt, $this->resultarray, $this->skip, $this->maxrows, OCI_FETCHSTATEMENT_BY_ROW);
					//$this->total_rows = oci_fetch_all($stmt, $res, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
					//$this->total_rows = oci_fetch_all($stmt, $this->resultarray, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
					//can either be a cursor, or a returned value;
					//cursor: determine the fetch type and proceed accordingly;
					//returned value: return that value;
				}
			}
			else//pass-thru SQL
			{
				if(preg_match('#\b(?:DELETE)\b#i', $sql))//is this necessary if we've moved the total rows=0 up to the top?
				{
					//$this->total_rows = 0;
				}
				elseif( ! empty($rowid))
				{
					//its an INSERT
					$this->insert_id  = $rowid;
				}
				else
				{
					//sets the total rows = # of result rows; assigns the result to $this->resultarray:
					$this->total_rows = oci_fetch_all($this->stmt, $this->resultarray, $this->skip, $this->maxrows, OCI_FETCHSTATEMENT_BY_ROW);
				}
			}
		}
		elseif (is_bool($this->stmt))//error trap
		{
			if ($this->stmt === FALSE)
			{
				// SQL error
				throw new Kohana_Database_Exception('database.error', oci_error($this->stmt).' - '.$sql);
			}
			else//################# FIX THIS IN CASE THE RESULT IS A BOOLEAN RETURN FROM A FUNCTION OR AN 'OUT' PARAM FROM A PROCEDURE????
			{
				throw new Kohana_Database_Exception('database.error', oci_error($this->stmt).' - '.$sql);
			}
		}
		// Store the SQL:
		$this->sql = $sql;
		// Set result type:
		$this->result($object);
	}

	/**
	 * Destruct, the cleanup crew!
	 *
	 * @return VOID
	 */
	public function __destruct()
	{
		if (is_resource($this->stmt))
		{
			oci_free_statement($this->stmt);
		}
	}

	/**
	 * Returns the SQL used to fetch the result.
	 *
	 * @return  string	the sql string
	 */
	public function sql()
	{
		return $this->sql;
	}

	/**
	 * Returns the insert id from the result.
	 *
	 * @todo clean up commented out line
	 * @return  mixed	the insert identifier
	 */
	public function insert_id()
	{
		return $this->insert_id;
		//return 0;
	}

	/**
	 * SEP_return_val - for SEP package calls only
	 *
	 * @return string	the SEP value
	 */
	public function SEP_return_val()
	{
		return $this->SEP_return_val;
	}

	//##################  NEEDED? ############################
	public function package_result_value()
	{

	}

	/**
	 * sets the oci fetch function type based on a variety of possible values for the database config 'object' key
	 * ############################## OBSOLETE ##########################
	 * ############################## NEEDS WORK ##########################
	 * @return string	the function call to use
	 */

	protected function fetchtype($object = FALSE)
	{
		//$fetch_type = ($object == FALSE) ? 'oci_fetch_all' : 'oci_fetch_object';
		//'oci_fetch_object' returns each row of the result, as does 'oci_fetch_row', 'oci_fetch_array', 'oci_fetch_assoc' and 'oci_fetch';
		//need to create specialized methods to return an array of values for each fetch type
		switch($object)
		{
			case 1: //object
			case 'object':
			case 'oci_fetch_object':
				$fetch_type = 'oci_fetch_object';
				break;
			case 2:
			case 'row':
			case 'oci_fetch_row':
				$fetch_type = 'oci_fetch_row';
				break;
			case 3:
			case 'array':
			case 'oci_fetch_array':
				$fetch_type = 'oci_fetch_array';
				break;
			case 4:
			case 'assoc':
			case 'oci_fetch_assoc':
				$fetch_type = 'oci_fetch_assoc';
				break;
			case 5:
			case 'fetch':
			case 'f':
			case 'oci_fetch':
				$fetch_type = 'oci_fetch';
				break;
			default:
				$fetch_type = 'oci_fetch_all';
		}
		$this->fetch_type = $fetch_type;
		return $fetch_type;
	}

	/**
	 * Returns either an instance of the result object, or the query result as a 2D array
	 *
	 * stub for setting up fetch type; right now fetch all rows
	 *
	 * @todo finish the fetch type functionality
	 * @see Database_Result::result()
	 * @param   boolean   $object	CURRENTLY: returns an object containing data if true, or an array of all data if false
	 * 								SHOULD BE: (from original abstract class: return rows as objects)
	 * @param   mixed     $type		The return type
	 * @return  object 	the Oracle Result
	 */
	public function result($object = TRUE, $type = FALSE)
	{
		if($object)
		{
			return $this;
		}
		else
		{
			return $this->as_array();
		}
	}

	/**
	 * return the result as an array
	 *
	 * @todo verify what the $type parameter does for us
	 * @param object 	$object	result object
	 * @param mixed		$type	the type? Of what?
	 * @return mixed-array 	the result array
	 */
	public function as_array($object = NULL, $type = FALSE)
	{
		return $this->result_array($object, $type);
	}

	/**
	 * returns the array of query results.
	 *
	 * @param   boolean   $object	return rows as objects; not currently used but is in the MY_Database class and so is needed here
	 * @param   mixed     $type		type; not currently used but is in the MY_Database class and so is needed here
	 * @return  array
	 */
	public function result_array($object = NULL, $type = FALSE)
	{
		if (! isset($this->resultarray))
		{
			//execute the fetch_all and populate the resultarray:
			$this->total_rows = $fetch_type($this->stmt, $this->resultarray, $this->skip, $this->maxrows, OCI_FETCHSTATEMENT_BY_ROW);
			$fetch_type = $this->fetchtype($type);
			$this->total_rows = $fetch_type($this->stmt, $this->resultarray, $this->skip, $this->maxrows, OCI_FETCHSTATEMENT_BY_ROW);
		}
		return $this->resultarray;
	}

	/**
	 * Gets the fields of an already run query.
	 *
	 * @return  string-array	the array of field names
	 */
	public function list_fields()
	{
		if(isset($this->resultarray))
		{
			$fields = array_keys($this->resultarray[0]);
		}
		else
		{
			$fields = array();
		}
		return $fields;
	}

	/**
	 * Returns field data about this result object.
	 *
	 * Does not need to go back to db server for this info.
	 *
	 * @see list_fields()
	 * @return  string-array	the array of field metadata
	 */
	public function field_data()
	{
		$arFieldNames = $this->list_fields();
		$arFieldData = array();
		foreach ($arFieldNames as $FieldName)
		{
			$type = oci_field_type($this->stmt, $FieldName);
			$rawtype = oci_field_type_raw($this->stmt, $FieldName);
			$size = oci_field_size($this->stmt, $FieldName);
			$precision = $type=='NUMBER' ? oci_field_precision($this->stmt, $FieldName) : null;
			$scale = $type=='NUMBER' ? oci_field_scale($this->stmt, $FieldName): null;

			$arFieldData[$FieldName] = array('type'=>$type,
											 'rawtype'=>$rawtype,
											 'size'=>$size,
											 'precision'=>$precision,
											 'scale'=>$scale);
		}
		return $arFieldData;
	}

	/**
	 * Seek to an offset in the results.
	 *
	 * @see offsetExists()
	 * @return  boolean	whether the sought offset row exists
	 */
	public function seek($offset)
	{
		if ( ! $this->offsetExists($offset))
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * get the total rows of the result
	 *
	 * Countable: count
	 * @return	integer	row count
	 */
	public function count()
	{
		if( ! isset($this->total_rows))
		{
			$this->total_rows = oci_fetch_all($this->stmt, $this->resultarray, $this->skip, $this->maxrows, OCI_FETCHSTATEMENT_BY_ROW);
		}
		return $this->total_rows;
	}

	/**
	 * determine if a given row offset exists in the result
	 *
	 * @param offset
	 * @return boolean	offset existence
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
	 * get the sought offset row
	 *
	 * ArrayAccess: offsetGet
	 *
	 * @todo clean up commented out lines
	 * @return boolean|mixed-array	a failure flag or the target row array
	 */
	public function offsetGet($offset)
	{
		if ( ! $this->seek($offset))
			return FALSE;
		// Return the row by calling the defined fetching callback
		//return call_user_func($this->fetch_type, $this->result, $this->return_type);

		/*
		 * make the result array into an object for use by Kohana calls expecting
		 * a result set row (as with the MySQL driver
		 */
		//return (object)$this->resultarray[$offset];
		/*
		 * bag that for now return an array
		 */
		return $this->resultarray[$offset];
	}

	/**
	 * retrieve the current row of data
	 *
	 * Iterator: current
	 * @return boolean|mixed-array	a failure flag or the target row array
	 */
	public function current()
	{
		return $this->offsetGet($this->current_row);
	}

	/**
	 * retrieve the current row of data
	 *
	 * Iterator: key
	 * @return long-integer		the row number of the current row
	 */
	public function key()
	{
		return $this->current_row;
	}

	/**
	 * increment the row number of the current row
	 *
	 * Iterator: next
	 * @return long-integer		the row number
	 */
	public function next()
	{
		return ++$this->current_row;
	}

	/**
	 * decrement the row number of the current row
	 *
	 * Iterator: prev
	 * @return long-integer		the row number
	 */
	public function prev()
	{
		return --$this->current_row;
	}

	/**
	 * reset the row number to the beginning of the result set
	 *
	 * Iterator: rewind
	 *  @return long-integer		the row number
	 */
	public function rewind()
	{
		return $this->current_row = 0;
	}

	/**
	 * determine if the current row exists
	 *
	 * Iterator: valid
	 * @return	boolean	the existence of the current row
	 */
	public function valid()
	{
		return $this->offsetExists($this->current_row);
	}

} // End Database Result Interface
