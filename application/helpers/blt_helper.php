<?php
function templates($url, $data, $js = null)
{
    $ci = get_instance();
    $ci->load->view('templates/header', $data);
    $ci->load->view($url);
    $ci->load->view('templates/footer');
    if ($js) {
        $ci->load->view($js);
    } else {
        $ci->load->view('templates/end');
    }
}
function checkLogin()
{
    $ci = get_instance();
    $id = $ci->session->userdata('admin_id');
    $fullname = $ci->session->userdata('fullname');
    if ($id) {

        $data = $ci->db->get_where('tb_admin', ['admin_id' => $id, 'fullname' => $fullname])->row();
        if (!$data) {
            if ($id != 999) redirect('auth');
        }
    } else {
        redirect('auth');
    }
}

function superAdmin()
{
    $ci = get_instance();
    $username = $ci->session->userdata('username');
    if ($username == "admin" || $username == "garis") {
    } else {
        exit();
    }
}

function validateId($value)
{
    if (strlen($value) > 16) {
        $id = substr($value, 1, 16);
    } else {
        $id = $value;
    }
    return $id;
}
