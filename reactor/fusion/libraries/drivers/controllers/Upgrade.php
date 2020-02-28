<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upgrade extends R_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->load->library('migration');

		if ( ! $this->migration->current())
		{
			show_error($this->migration->error_string());
		} else {
			echo "success";
		}
	}

}