<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_user extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getall()
	{
		return	$query = $this->db
			->select('*')
			->from('tb_user')
			->where('date_deleted', NULL)
			->order_by('id', 'desc')
			->get()
			->result_array();
	}

	public function getbyid($id = null)
	{
		return	$query = $this->db
			->select('*')
			->from('tb_user')
			->where('date_deleted', NULL)
			->where('id', $id)
			->get()
			->row_array();
	}

	public function userlogin($username, $password = null)
	{
		return $this->db
			->select('*')
			->from('tb_user')
			->where('date_deleted', NULL)
			->where('username', $username)
			->where('password', $password)
			->get()
			->result_array();
	}
}
