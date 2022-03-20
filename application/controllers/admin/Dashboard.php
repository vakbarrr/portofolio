<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('logged_in') != 1) {
			redirect('admin/login');
		}
		$this->data = [
			'page_title' => 'Dashboard'
		];
	}
	public function index()
	{
		$this->template->_admin('admin/dashboard', $this->data);
	}
}
