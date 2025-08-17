<?php defined('SYSPATH') or die('No direct script access.');
/**
* SEP Applicator Module (SAM)
*
* Extends existing module class authenticatr/sam.php
*
* requires sam.php KF config file
*
* @uses sam.php
* @package onestop
* @subpackage libraries
* @author Min Lee
*/

class Sam extends Sam_Core {

	protected $onestop_db;

	/**
	 * Defined child constructor to initialize onestop db connection
	 * and to convert new SEP DB call to instance call.
	 */
	public function __construct($sessionID = NULL) {
		$this->onestop_db = Database::instance();

		// following lines from the parent, Sam_Core class
		$this->config = Kohana::config('sam');//gets the entire $config array

		$this->SEPAppID = $this->config['sepappid'];
		$this->SEPsessionID = $sessionID;
		$this->session = Session::instance();

		$this->db = Database::instance('sepdb', $this->config['sepdb']);

		$sLoginURL = $this->GetSEPLoginURL();
		$sRegURL = $this->GetSEPRegistrationURL();
	}

	/**
	 * Way of setting SEPsessionID after object has been created
	 *
	 * @return none
	 */
	public function SetSessionId($sessionId) {
		$this->SEPsessionID = $sessionId;
	}

	public function GetSessionId() {
		if ( (! $this->SEPsessionID) && (isset($_SESSION['SEPSessionID'])) )
			$this->SEPsessionID = $_SESSION['SEPSessionID'];

		return($this->SEPsessionID);
	}

	/**
	 * One public method to handle all authentication to simplify authentication.
	 * It's purpose is to simplify and to minimize connections to SEP DB.
	 * Order of sessionID lookup: _SESSION, _GET, this->SEPsessionID
	 * Verifies: 1) logged into sep  2) user has sep priv to app
	 */
	public function IsAuthenticated($url_sessionId=NULL) {
		// on local machine of George Huang
                if(strtolower($_SERVER['SERVER_NAME']) == 'waste.web-ghuang.nmenv.state.nm.us') {
                        $_SESSION['UserID'] = 'georgehuang';
                        $_SESSION['Roles'] = array('LUST_ADMIN', 'PST_SELECT_ONLY', 'UST_FIN');
			return(TRUE);
                }

		if (!empty($_SESSION['SEPSessionID']))  // shortcut authentication
			return(TRUE);

		if ($url_sessionId)
			$this->SEPsessionID = $url_sessionId;

		if (! $this->SEPsessionID)
			return(FALSE);

		if ($this->logged_in())
			if ($this->IsUserAppApproved()) {  // SEP check and sets 'SEPuserID'
				$_SESSION['UserID'] = $this->get_userid();
				$_SESSION['Roles'] = $this->get_roles();
				//$_SESSION['ProfileUrl'] = "{$this->GetSEPLoginURL()}/user-profile-form";
				return(TRUE);
			}

		return(FALSE);
	}

	public function logged_in(){

		$retVal = FALSE;

		/**
		* if the SEPsessionID property is set then it means that it has been
		* passed in to the constructor so validate it first; if its valid then
		* set value of $_SESSION['SEPSessionID']
		*/
		if($this->SEPsessionID !== NULL){
			if($this->IsValidSession($this->SEPsessionID)){
				$_SESSION['SEPSessionID'] = $this->SEPsessionID;
				$retVal = TRUE;
			}
		}
		else{
			/**
			* if the SEPsessionID property is not set then check to see that there is a
			* valid $_SESSION['SEPSessionID']
			*/
			if(!empty($_SESSION['SEPSessionID'])){
				if($this->IsValidSession($_SESSION['SEPSessionID'])){
					$this->SEPsessionID = $_SESSION['SEPSessionID']; //only change
					$retVal = TRUE;
				}
			}
		}

		return $retVal;
	}

	public function get_userid() {
		$session = Session::instance();

		if ($session->get('UserID'))
			return($session->get('UserID'));

		if (! $session->get('SEPuserID'))
			return(NULL);

		return($session->get('SEPuserID')); // 2014--04-15 ML

		/*** 2014--04-15 ML: Following method of using LOGIN_ID from USTX.STAFF
		 has been determined to be not as direct and safe as using SEP_LOGIN_ID
		 directly due to LOGIN_ID sometimes not being filled in by admin.
		return($this->onestop_db->query_field(
			'SELECT LOGIN_ID FROM USTX.STAFF WHERE SEP_LOGIN_ID = :SEP_LOGIN_ID'
			, array(':SEP_LOGIN_ID' => $session->get('SEPuserID')) ));
		****************************************************************/
	}

	public function get_roles() {
		$session = Session::instance();

		if ($session->get('Roles'))
			return($session->get('Roles'));

		if (! $session->get('SEPuserID'))
			return(array());

		$roles = $this->onestop_db->query("
			SELECT ROLE_CODE FROM USTX.STAFF_ROLES SR, USTX.STAFF S
			WHERE SR.STAFF_CODE = S.CODE
				AND S.RESTRICTED = 'Y'
				AND SEP_LOGIN_ID = :SEP_LOGIN_ID"
			, array(':SEP_LOGIN_ID' => $session->get('SEPuserID')))->as_array();

		$result = array();
		foreach($roles as $role)
			$result[] = $role['ROLE_CODE']; // flatten array

		return($result);
	}

	public function has_priv($table, $priv) {
		foreach ($_SESSION['Roles'] as $role) { // looping w bound faster and simpler
			if ( $this->onestop_db->query_field('SELECT Count(*) FROM USTX.UST_ROLE_PRIVS
				WHERE (TABLENAME = :TABLENAME) AND (ROLENAME = :ROLENAME) AND (SECURITY = :SECURITY)',
				array(':TABLENAME' => $table, ':ROLENAME' => $role, ':SECURITY' => $priv))
				> 0 )
				return(TRUE);
		}
		return(FALSE);
	}

	public function if_priv($table, $priv,  $t_val, $f_val='') {
		return ($this->has_priv($table, $priv) ? $t_val : $f_val);
	}
}
