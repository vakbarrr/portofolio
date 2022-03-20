<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if($this->session->userdata('logged_in') == 1){
      redirect('admin/dashboard');
    }
    $this->load->library('form_validation');

    $this->load->model('Model_user');
  }

  public function index()
  {
    $this->load
    ->layout(false)
    ->view('admin/login');
  }

  public function proseslog()
  {
    $post = $this->input->post(null, false);

    $this->form_validation
      ->set_rules('username', 'Username', 'trim|required')
      ->set_rules('password', 'Password', 'trim|required')
      ->set_error_delimiters('', '');

    if ($this->form_validation->run() == FALSE) {
      $json['error'] =  $this->form_validation->get_errors();
    }else{
      $username = $post['username'];
      $password = hash('sha512', $post['password']);
      $user = $this->Model_user->userlogin($username, $password);

      if(count($user) < 1){
        $json['gagal_login'] = 1;
      }else{

        $session_user = array(
          'user_id' => $user[0]['id'],
          'nama' => $user[0]['nama'],
          'username' => $user[0]['username'],
          'logged_in' => 1,
        );
        $this->session->set_userdata($session_user);

        $json['success'] = 1;
        $json['redirect'] = base_url('admin/dashboard');
      }

    }
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
