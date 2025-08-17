<?php

class Reports_Controller extends Template_Controller {
	public $name = 'reports';

	public $template = 'tpl_blank';  // default template for all reports


	public function __construct() {
		parent::__construct();

		// Report/PHPExcel class
		require_once Kohana::find_file('vendor', 'reports/Report', TRUE);
	}

	public function index() {
		$this->template = new View('tpl_internal');
		$this->template->nav_id = $this->name;

		$view = new View('reports_menu');
		$this->template->content = $view;
	}

	// Inspection reports =======================================================

	public function facility_summary() {
		$this->_validate_req_custom(array('facility_id'));

		$view = new View('reports/facility_summary');
		$view->facility_id = $this->input->post('facility_id');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function field_inspection() {
		$this->_validate_req_custom(array('facility_id'));

		$view = new View('reports/field_inspection');
		$view->facility_id = $this->input->post('facility_id');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function thirty_month_no_comp() {
		$this->_validate_req_custom(array('before_date', 'tank_types'));

		$view = new View('reports/thirty_month_no_comp');
		$view->before_date = $this->input->post('before_date');
		$view->county = $this->input->post('county');
		$view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function eighteen_month_no_comp() {
		$this->_validate_req_custom(array('before_date'));

		$view = new View('reports/eighteen_month_no_comp');
		$view->before_date = $this->input->post('before_date');
		$view->county = $this->input->post('county');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;

	}

	public function inspections_review() {
		$this->_validate_req_custom(array('start_date', 'end_date', 'tank_types'));

		$view = new View('reports/inspections_review');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->inspector_id = $this->input->post('inspector_id');
		$view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tank_inspection_dates() {
		$this->_validate_req_custom(array('before_date', 'tank_types'));

		$view = new View('reports/tank_inspection_dates');
		$view->before_date = $this->input->post('before_date');
		$view->county = $this->input->post('county');
		$view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tank_inspection_2_dates() {
		$this->_validate_req_custom(array('tank_types'));

		$view = new View('reports/tank_inspection_2_dates');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function all_storage_tanks() {
		$view = new View('reports/all_storage_tanks');
		$view->county = $this->input->post('county');
		$view->get_active_only = $this->input->post('get_active_only');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}
	
	public function all_tanks_details () {
		$this->_validate_req_custom(array('tank_types'));
		$view = new View('reports/all_tanks_details');
                $view->county = $this->input->post('county');
                $view->get_active_only = $this->input->post('get_active_only');
                $view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
                $this->template->content = $view;
	
	}

	public function active_storage_tanks() {
		$this->_validate_req_custom();

		$view = new View('reports/active_storage_tanks');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function lust_compliance() {
		$this->_validate_req_custom(array('before_date'));

		$view = new View('reports/lust_compliance');
		$view->before_date = $this->input->post('before_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tank_fee_compliance() {
		$this->_validate_req_custom(array('facility_id'));

		$view = new View('reports/tank_fee_compliance');
		$view->facility_id = $this->input->post('facility_id');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tank_owner_stat() {
		$this->_validate_req_custom(array('start_date', 'end_date'));

		$view = new View('reports/tank_owner_stat');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tanks_installed() {
		$this->_validate_req_custom(array('fy'));

		$view = new View('reports/tanks_installed');
		$view->fy = $this->input->post('fy');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tanks_removed() {
		$this->_validate_req_custom(array('fy'));

		$view = new View('reports/tanks_removed');
		$view->fy = $this->input->post('fy');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function nov_report() {
		$this->_validate_req_custom(array('start_date', 'end_date', 'tank_types'));

		$view = new View('reports/nov_report');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function nov_report_all() {
		$this->_validate_req_custom(array('start_date', 'end_date', 'tank_types'));

		$view = new View('reports/nov_report_all');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function nov_report_financial() {
		$this->_validate_req_custom(array('start_date', 'end_date', 'tank_types'));

		$view = new View('reports/nov_report_financial');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->tank_types = $this->input->post('tank_types');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function soc_performance() {
		$this->_validate_req_custom(array('start_date', 'end_date'));

		$view = new View('reports/soc_performance');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function soc_compliance_stat() {
		$this->_validate_req_custom(array('start_date', 'end_date'));

		$view = new View('reports/soc_compliance_stat');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tcr_compliance_stat() {
		$this->_validate_req_custom(array('start_date', 'end_date'));

		$view = new View('reports/tcr_compliance_stat');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function nonfuel_tanks() {
		$view = new View('reports/nonfuel_tanks');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function facilities_eg_tanks() {
		$view = new View('reports/facilities_eg_tanks');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function facilities_count() {
		$view = new View('reports/facilities_count');
		$view->tank_types = $this->input->post('tank_types');
		$view->tos_only = $this->input->post('tos_only');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function facilities_abc_op() {
		$view = new View('reports/facilities_abc_op');
		$view->cert_level = $this->input->post('cert_level');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function dp_stat() {
		$this->_validate_req_custom(array('start_date', 'end_date', 'tank_types'));

		$view = new View('reports/dp_stat');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->tank_types = $this->input->post('tank_types');	
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function dp_master() {
		$this->_validate_req_custom(array('start_date', 'end_date'));

		$view = new View('reports/dp_master');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function emails_review() {
		$this->_validate_req_custom(array('entity_type', 'contact_type'));

		$view = new View('reports/emails_review');
		$view->entity_type = $this->input->post('entity_type');
		$view->contact_type = $this->input->post('contact_type');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function inspector_report() {
		$this->_validate_req_custom(array('start_date', 'end_date'));

		$view = new View('reports/inspector_report');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->inspector_id = $this->input->post('inspector_id');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function insurance_review() {
		$view = new View('reports/insurance_review');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function suspected_release() {
		$this->_validate_req_custom(array('start_date', 'end_date'));

		$view = new View('reports/suspected_release');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->release_status = $this->input->post('release_status');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function facility_tank_detail() {
		$view = new View('reports/facility_tank_detail');
		$view->tank_detail_codes = $this->input->post('tank_detail_codes');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function inspections_by_certified_installer() {
		$view = new View('reports/inspections_by_certified_installer');
		$view->certified_installer_id = $this->input->post('certified_installer_id');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function quarterly_performance_measures() {
		$view = new View('reports/quarterly_performance_measures');
		$view->year = $this->input->post('year');
		$view->quarter = $this->input->post('quarter');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	// Finanical reports =======================================================

	public function accounts_aging() {
		$this->_validate_req_custom(array('days'));

		$view = new View('reports/accounts_aging');
		$view->days = $this->input->post('days');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	//not active
	public function preinvoice_tank_counts() {
		$this->_validate_req_custom(array('owner_id', 'fy'));

		$view = new View('reports/preinvoice_tank_counts');
		$view->owner_id = $this->input->post('owner_id');
		$view->fy = $this->input->post('fy');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function outstanding_liab_active() {
		$this->_validate_req_custom(array('fy'));

		$view = new View('reports/outstanding_liab_active');
		$view->fy = $this->input->post('fy');
		$view->include_prior_years = $this->input->post('include_prior_years', FALSE);
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function outstanding_liab_inactive() {
		$this->_validate_req_custom(array('fy'));

		$view = new View('reports/outstanding_liab_inactive');
		$view->fy = $this->input->post('fy');
		$view->include_prior_years = $this->input->post('include_prior_years', FALSE);
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}
 
	public function delinquent_owners() {
		$this->_validate_req_custom(array('fy'));

		$view = new View('reports/delinquent_owners');
		$view->fy = $this->input->post('fy');
		$view->include_prior_years = $this->input->post('include_prior_years', FALSE);
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function delinquent_owners_no_fed() {
		$view = new View('reports/delinquent_owners_no_fed');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	// only used via CLI for now:
	// cd /home/env/var/tanks/htdocs
	// php index.php reports/delinquent_owners_fed > ~/output.xls
	public function delinquent_owners_fed() {
		$view = new View('reports/delinquent_owners_fed');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function fee_summary() {
		$view = new View('reports/fee_summary');
		$view->transaction_code = $this->input->post('transaction_code');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function fee_summary_fy() {
		$view = new View('reports/fee_summary_fy');
		$view->transaction_code = $this->input->post('transaction_code');
		$view->start_date = $this->input->post('start_date');
		$view->end_date = $this->input->post('end_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	// not active
	public function current_fy_tank_fees() {
		$this->_validate_req_custom(array('start_date', 'fy'));

		$view = new View('reports/current_fy_tank_fees');
		$view->start_date = $this->input->post('start_date');
		$view->fy = $this->input->post('fy');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function owner_tank_fee_history() {
		$view = new View('reports/owner_tank_fee_history');
		$view->owner_id = $this->input->post('owner_id');
		$view->fy = $this->input->post('fy');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function owner_balance_tanks() {
		$view = new View('reports/owner_balance_tanks');
		$view->tank_status_codes = $this->input->post('tank_status_codes');
		$view->balance_type = $this->input->post('balance_type');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	public function tank_fee_billing_exceptions() {
		$view = new View('reports/tank_fee_billing_exceptions');
		$view->invoice_date = $this->input->post('invoice_date');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	// GoNM reports =======================================================

	public function facility_score() {
		$view = new View('reports/facility_score');
		$view->inspector_lname = $this->input->post('inspector_lname');
		$view->output_format = $this->input->post('output_format');
		$this->template->content = $view;
	}

	protected function _validate_req_custom($fields=array()) {
		$fields[] = 'output_format';
		return($this->_validate_req($fields, 'reports/'));
	}
}
