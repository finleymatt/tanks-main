<?php defined('SYSPATH') or die('No direct script access.');

class Validation extends Validation_Core {
	/**
	 * Overidden to save errors to session
	 */
	public function validate() {
		$session = Session::instance();
		$session->delete('error_message');  // clear out any errors from prev in case

		if (parent::validate()) {
			return(TRUE);
		}
		else {
			$session->set('error_message', $this->errors());
			return(FALSE);
		}
	}

}

?>
