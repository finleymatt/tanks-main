<?php defined('SYSPATH') or die('No direct script access.');

class Model extends Model_Core {

	public $table_name = '';	// table name must include schema name
	public $pks = array('ID');	// primary key(s)
	public $parent_pks = array();	// foreign keys to the parent object
					// in format: array('model_name' => array('key1', key2'))
	public $more_select = array();	// any additional fields to select from sql
					// str or array of 'field_name', 'func'

	public $more_where = array();	// any additional where criterias for sql
					// str or array of 'field_name', 'func'

	public $lookup_code = '';	// used for lookup and dropdown
	public $lookup_desc = '';	// used for lookup and dropdown

	public $date_fields = array();  // specify date fields so they can be formatted


	/**
	 * Called with name of a child Model class name to reuse an existing instance
	 */
	public static function instance($class_name) {
		static $instance;

		if (empty($instance[$class_name])) {
			$full_name = "{$class_name}_Model";
			$instance[$class_name] = new $full_name();
		}

		return $instance[$class_name];
	}

	public function __construct() {
		parent::__construct();

		// include mm/dd/yyyy formatted date fields into all select queries
		foreach($this->date_fields as $field) {
			$this->more_select[] = "to_char({$field}, 'MM/DD/YYYY') {$field}_FMT";
		}
	}

	/**
 	 * Returns single value from custom query. The field to be returned must be named VAL.
 	 * Used for aggregate queries.
 	 */
	public function get_value($query, $bound_vars) {
		$rows = $this->db->query($query, $bound_vars)->as_array();
		return($rows[0]['VAL']);
	}

	/**
	 * Accepts both single value and array of values
	 */
	public function get_row($id) {
		if (! $this->check_priv('SELECT')) return(FALSE);

		$this->db->select("{$this->table_name}.*");
		foreach ($this->more_select as $select)
			$this->db->select($select);

		$this->db->from($this->table_name);

		foreach ($this->more_where as $more_wh)
			$this->db->where($more_wh);

		if (! is_array($id)) $id = array($id);
		$bound_vars = array();
		foreach ($this->pks as $index => $pk) {
			$this->db->where("{$pk} = :PK_{$index}");
			$bound_vars[":PK_{$index}"] = $id[$index];
		}

		$rows = $this->db->query(NULL, $bound_vars)->as_array();
		if (count($rows))
			return($rows[0]);
		else
			return(NULL);
	}

	public function get_list($where=NULL, $orderby=NULL, $bound_vars=array()) {
		if (! $this->check_priv('SELECT')) return(FALSE);

		$this->db->select("{$this->table_name}.*");
		foreach ($this->more_select as $select)
			$this->db->select($select);

		$this->db->from($this->table_name);

		foreach ($this->more_where as $more_wh)
			$this->db->where($more_wh);
		if ($where) $this->db->where($where);

		if ($orderby) $this->db->orderby($orderby);

		return($this->db->query(NULL, $bound_vars)->as_array());
	}

	/**
	 * Used for creating dropdown form inputs
	 * Returns in array(0th_column => 1st column) format
	 */
	public function get_dropdown($dropdown_id=NULL, $dropdown_desc=NULL, $where=NULL, $bound_vars=array()) {
		$dropdown_id = ($dropdown_id ? $dropdown_id : $this->lookup_code);
		$dropdown_desc = ($dropdown_desc ? $dropdown_desc : $this->lookup_desc);

		$this->db->select("{$dropdown_id} as ID", "{$dropdown_desc} as DESCRIPTION");
		if (isset($this->lookup_category))
			$this->db->select("{$this->lookup_category} as CATEGORY");
		$this->db->from($this->table_name);
		if ($where)
			$this->db->where($where);
		$this->db->orderby($this->lookup_code, 'ASC');

		return(arr::make_dropdown($this->db->query(NULL, $bound_vars)->as_array()));
	}

	/**
	 * Used for creating dropdown form inputs -- same as get_dropdown, except used for chained selects
	 * Not used yet
	 * Returns in array(ID, DESCRIPTION, CATEGORY) format
	 */
	public function get_dropdown_chained($dropdown_id=NULL, $dropdown_desc=NULL, $dropdown_category=NULL, $where=NULL, $bound_vars=array()) {
		$dropdown_id = ($dropdown_id ? $dropdown_id : $this->lookup_code);
		$dropdown_desc = ($dropdown_desc ? $dropdown_desc : $this->lookup_desc);
		$dropdown_category = ($dropdown_category ? $dropdown_category : $this->lookup_category);

		$this->db->select("{$dropdown_id} as ID", "{$dropdown_desc} as DESCRIPTION", "{$dropdown_category} as CATEGORY");
		$this->db->from($this->table_name);
		if ($where)
			$this->db->where($where);
		$this->db->orderby($this->lookup_code, 'ASC');
		return($this->db->query(NULL, $bound_vars)->as_array());
	}

	/**
	 * Used for lookup tables having CODE and DESCRIPTION
	 * Allows single or multiple-keyed fields
	 */
	public function get_lookup_desc($codes, $include_code=TRUE) {
		if ($codes === NULL || $codes === '0' ) return('');  // sometimes code is 0

		if ( (! $this->lookup_code) || (! $this->lookup_desc) ) {
			Session::instance()->set('error_message', 'Error occurred during DESC lookup.');
			return('');
		}
		else {
			$codes = (! is_array($codes) ? array($codes) : $codes);
			$lookup_codes = (! is_array($this->lookup_code) ? array($this->lookup_code) : $this->lookup_code);
			$this->db->select("{$this->lookup_desc} as DESCRIPTION");
			$this->db->from($this->table_name);
			//$this->db->where(array($this->lookup_code => $code));
			$bound_vars = array();
			foreach ($lookup_codes as $index => $lookup_code) {
				$this->db->where("{$lookup_code} = :FIELD_{$index}");
				$bound_vars[":FIELD_{$index}"] = $codes[$index];
			}
			$rows = $this->db->query(NULL, $bound_vars)->as_array();
			if (count($rows))
				return( ($include_code ? "({$codes[0]}) " : '') . $rows[0]['DESCRIPTION'] );
			else
				return('not found');
		}
	}

	/**
	 * $ids must be an array
	 */
	public function update($ids, $data) {
		if (! $this->check_priv('UPDATE')) return(FALSE);
		if ($this->rows_impacted($ids) > 1) return(FALSE); // disallow multiple changes

		if (method_exists($this, '_validate_rules')) {
			$vdata = $this->_validate_rules(new Validation($data));
			if (! $vdata->validate())
				return(FALSE);
		}

		unset($data['submit']);
		$this->_set_date_fields($data);
		$this->_selective_set($data);
		$this->db->where(text::where_pk($this->pks, $ids));
		return($this->db->update($this->table_name));
	}

	/**
	 * $parent_ids (array) It's not used due to all fields should
	 * already be set in $data including parent PK if it exists.
	 */
	public function insert($parent_ids, $data) {
		if (! $this->check_priv('INSERT')) return(FALSE);

		if (method_exists($this, '_validate_rules')) {
			$vdata = $this->_validate_rules(new Validation($data));
			if (! $vdata->validate())
				return(FALSE);
		}

		unset($data['submit']);  // remove submit button value
		$this->_set_date_fields($data);
		$this->_selective_set($data);
		return($this->db->insert($this->table_name));
	}

	/**
	 * $ids (array)
	 */
	public function delete($ids) {
		if (! $this->check_priv('DELETE')) return(FALSE);
		if ($this->rows_impacted($ids) > 1) return(FALSE); // disallow multiple changes

		$this->db->where(text::where_pk($this->pks, $ids));
		return($this->db->delete($this->table_name));
	}

	public function rows_impacted($ids) {
		$temp_row = $this->get_row($ids);
		$benchmark = array_pop(Database::$benchmarks);
		return($benchmark['rows']);
	}

	/**
 	 * Launches a new shell process with script and args passed in
 	 */
	protected function batch($script, $args) {
		$args_str = implode(' ', array_map('escapeshellarg', $args));

		$script = Kohana::find_file('vendor', "onestop_batch/{$script}", TRUE);
		$envfile = ENVFILE;  // send path to DB configs
		$out_redir = Kohana::log_directory() . 'batch_log.txt';
		//$err_redir = '/dev/null';

		// loads oci8, which is not included in CLI PHP php.ini
		//shell_exec("echo \"/usr/bin/php -q -d extension=oci8.so {$script} '{$envfile}' {$args_str}>>{$out_redir}\" | at now 2>>{$err_redir}");

		// in new web-t, web-q, web servers
		putenv('SHELL=/bin/bash'); // since apache is run as nobody
		shell_exec("echo \"/usr/bin/php -q {$script} '{$envfile}' {$args_str} &>> {$out_redir}\" | /usr/bin/at now &>> {$out_redir}");
	}

	/**
	 * Checks if logged in user has needed priv to add/update/delete data
	 * Side effect: saves error message to session.
	 **/
	public function check_priv($priv) {
		if (! Sam::instance()->has_priv($this->table_name, $priv)) {
			//var_dump($this->table_name);
			//die();
			Session::instance()->set('error_message', 'Your account does not have permission for this operation.');
			return(FALSE);
		}
		else
			return(TRUE);
	}

	/**
	 * Returns table name without the schema name if it has one
	 */
	public function short_table_name() {
		if (($dot_idx = strpos($this->table_name, '.')) !== FALSE)
			return(substr($this->table_name, $dot_idx + 1));
		else
			return($this->table_name);
	}

	/**
	 * Given array of fields (db row), returns only values of primary keys in order
	 */
	public function ids($row) {
		return(arr::subset($row, $this->pks));
	}

	/**
	 * Given ids, returns values of foreign keys of parent object
	 */
	public function parent_ids($ids, $parent_model=NULL) {
		if (!$ids || !count($this->parent_pks))
			return(NULL);

		if ($parent_model) // if parent was specified
			$pks = $this->parent_pks[$parent_model];
		else {  // not specified, so choose first one
			$vals = array_values($this->parent_pks);
			$pks = $vals[0];
		}

		return(arr::subset($this->get_row($ids), $pks));
	}

	protected function _search_syntax($field_name) {
		return("(Instr(Upper({$field_name}), Upper(:{$field_name})) > 0)");
	}

	/**
	 * Method to set into db query builder without overwriting previous set fields
	 * Good for calling from parent model to not overwrite what child model has set
	 */
	protected function _selective_set($data) {
		foreach($data as $field_name => $field_value) {
			if (! array_key_exists($field_name, $this->db->set)) // if custom set, skip
				$this->db->set($field_name, $field_value);
		}
		return(TRUE);
	}

	protected function _set_date_fields($data) {
		foreach($this->date_fields as $field) {
			if (isset($data[strtolower($field)]))
				$this->db->set(strtolower($field), Model::sql_date_db($data[strtolower($field)]), FALSE);
			elseif (isset($data[strtoupper($field)]))
				$this->db->set(strtoupper($field), Model::sql_date_db($data[strtoupper($field)]), FALSE);
				//$data[strtoupper($field)] = Model::sql_date_db($data[strtoupper($field)]);
		}
	}

	public function sql_date_web($field_name, $new_name=NULL) {
		if (! $new_name) $new_name = $field_name;
		return("TO_CHAR({$field_name}, 'mm/dd/yyyy') as {$new_name}");
	}

	public static function sql_date_db($str_date) {
		return("TO_DATE('{$str_date}', 'mm/dd/yyyy')");
	}

	public static function str_date_db($str_date) {
		return(date('d-M-y', strtotime($str_date)));
	}
}
?>
