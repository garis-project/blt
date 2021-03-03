<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }
    public function index()
    {
        $data['title'] = "SISTEM BLT";
        templates('admin/index', $data, 'admin/index-js');
    }
    // public function create()
    // {
    //     $data['title'] = "BLT";
    //     templates('admin/create', $data);
    // }
    // public function search()
    // {
    //     $data['title'] = "BLT";
    //     templates('admin/search', $data);
    // }
    public function reference()
    {
        $data['title'] = "Import Reference";
        templates('admin/reference', $data);
    }


}
