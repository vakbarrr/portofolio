<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_category extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getall()
	{
		return	$query = $this->db
			->select('*')
			->from('category')
			->where('date_deleted', NULL)
			->order_by('category_id', 'desc')
			->get()
			->result_array();
	}

	public function getbyid($id = null)
	{
		return	$query = $this->db
			->select('*')
			->from('category')
			->where('date_deleted', NULL)
			->where('category_id', $id)
			->get()
			->row_array();
	}
}
