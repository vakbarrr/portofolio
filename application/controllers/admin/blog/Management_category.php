<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Management_category extends CI_Controller
{
    public function __construct(){
        parent::__construct();
		if ($this->session->userdata('logged_in') != 1) {
			redirect('admin/login');
		}
		$this->data = [
			'page_title' => 'Category'
		];
        $this->load->model('Model_category');
    }

    public function _mkdir()
	{
		$root   = './storage';
		if (!is_dir($root)) {
			mkdir($root);
		}
		// create folder company if not exist
		$path   = $root . '/category';
		if (!is_dir($path)) {
			mkdir($path);
		}
	}

	public function index()
	{
		$this->data['list'] = $this->Model_category->getall();
		$this->template->_admin('admin/blog/category/view', $this->data);
	}

    public function add()
	{
		$this->template->_admin('admin/blog/category/add', $this->data);
	}

	public function files_check($str, $param)
	{
		$param = explode('|', $param);
		$namefield = $param[0];
		$method = $param[1];
		$size = $param[2];

		$allowed_file_types_image     = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/x-png'];

		$type_file = $allowed_file_types_image;
		$msg = 'Please select only jpeg/jpg/png.';

		if ($_FILES[$namefield]['size'] != 0) {
			if (is_uploaded_file($_FILES[$namefield]['tmp_name'])) {
				// Notice how to grab MIME type
				$mime_type = mime_content_type($_FILES[$namefield]['tmp_name']);
				// If you want to allow certain files
				if (!in_array($mime_type, $type_file)) {
					// File type is NOT allowed
					$this->form_validation->set_message('files_check', '[' . $namefield . '] Please select only jpeg/jpg/png');
					return false;
				} else {
					$size = ($size * 1);
					if ($_FILES[$namefield]['size'] > $size) {
						$this->form_validation->set_message('files_check', '[' . $namefield . '] File size not allowed');
						return false;
					} else {
						return true;
					}
				}
			}
		} else {
			if ($method == 'insert') {
				$this->form_validation->set_message('files_check', '[' . $namefield . '] Please choose a file to upload.');
				return false;
			} else {
				$this->form_validation->set_message('files_check', '[' . $namefield . '] Please choose a file to upload.');
				return false;
				// return true;
			}
		}
	}

	private function rename_file($files, $path, $flag = null)
	{
		$tmp             	= $files['tmp_name'];
		$temp             	= explode(".", $files["name"]);
		$rename         	= $flag .'_'. date('ymd') . round(microtime(true)) . rand(100, 999) .  '.' . end($temp);
		move_uploaded_file($tmp, $path . $rename);
		return $rename;
	}

	public function insert()
	{
		$post = $this->input->post(null, false);
		$path 	= 'storage/category/';
		$this->_mkdir();
		$this->form_validation
			->set_rules('category', 'Category', 'trim|required')
			->set_rules('image', '', 'trim|callback_files_check[image|insert|1000000]')
			->set_error_delimiters('', '');

		if ($this->form_validation->run() == FALSE) {
			$json = ['error' => true, 'msg' => $this->form_validation->get_errors(), 'validation' => true];
		} else {
			$this->db->trans_begin();
			$data = array(
				'category' => $post['category'],
				'image' => 'storage/category/' . $this->rename_file($_FILES['image'], $path,'category') ,
				'date_added' => date('Y-m-d H:i:s')
			);
			$this->db->insert('category', $data);
			if ($this->db->trans_status() === FALSE) :
				$this->db->trans_rollback();
				$json = ['error' => true, 'msg' => 'Data failed to save'];
			else :
				$this->db->trans_commit();
				$json = ['error' => false, 'msg' => 'Data saved successfully', 'redirect' => base_url('admin/blog/management_category/add')];
			endif;
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

    public function edit($id = null)
	{
		if (!empty($id)) {
			$this->data['get'] = $this->Model_category->getbyid($id);

			$this->template->_admin('admin/blog/category/edit', $this->data);
		}
	}

	public function update()
	{
		$post 	= $this->input->post(null, false);
		$path 	= 'storage/category/';
		$this->_mkdir();
		$id 	= $post['category_id'];
		$this->form_validation
			->set_rules('category', 'Category', 'trim|required')
			->set_rules('image', '', 'trim|callback_files_check[image|update|2000000]')
			->set_error_delimiters('', '');

		if ($this->form_validation->run() == FALSE) {
			$json['error'] =  $this->form_validation->get_errors();
		} else {
			$this->db->trans_begin();
			$data = array(
				'category' => $post['category'],
				'date_updated' => date('Y-m-d H:i:s')
			);
			if($_FILES['image']['size'] > 0):
				$data['image'] = 'storage/category/' . $this->rename_file($_FILES['image'], $path,'category');
			endif;
			$this->db->update('category', $data, array('category_id' => $id));
			if ($this->db->trans_status() === FALSE) :
				$this->db->trans_rollback();
				$json = ['error' => true, 'msg' => 'failed'];
			else :
				$this->db->trans_commit();
				$json = ['error' => false, 'msg' => 'Data edited successfully', 'redirect' => base_url('admin/blog/management_category')];
			endif;
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

    public function delete($id = null)
	{
		$data = array(
			'date_deleted' => date('Y-m-d H:i:s'),
		);
		$this->db->update('category', $data, array('category_id' => $id));
		// $this->Model_mdb_business_category->soft_delete($id);
		$json = ['error' => false, "msg" => "Deleted sucessfully!"];
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}
}