<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class Data extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }
    public function create()
    {
        $data = $this->input->post();
        if ($data) {
            switch ($data['kind_of_social_assistance']) {
                case "BPNT":
                    $field = "bpnt";
                    break;
                case "BLT DESA":
                    $field = "blt_desa";
                    break;
                case "BST":
                    $field = "bst";
                    break;
                case "PKH":
                    $field = "pkh";
                    break;
            }
            unset($data['kind_of_social_assistance']);
            $data[$field] = 1;
            $res = $this->blt->create($data);
            echo json_encode(["status" => $res]);
        }
    }
    public function update()
    {
        $data = $this->input->post();
        if ($data) {
            $id = $data['id'];
            unset($data['id']);
            switch ($data['kind_of_social_assistance']) {
                case "BPNT":
                    $field = "bpnt";
                    break;
                case "BLT DESA":
                    $field = "blt_desa";
                    break;
                case "BST":
                    $field = "bst";
                    break;
                case "PKH":
                    $field = "pkh";
                    break;
            }
            unset($data['kind_of_social_assistance']);
            $data[$field] = 1;
            $res = $this->blt->update($id, $data);
            if ($res) $res = ["status" => "success"];
            echo json_encode($res);
        }
    }
    public function get()
    {
        $id = $this->input->post('id');
        if ($id) {
            $data = $this->blt->get($id);
            echo json_encode($data);
        }
    }
    public function getKK()
    {
        $id = $this->input->post('val');
        if ($id) {
            $data = $this->blt->getCustome(['family_card_number' => $id]);
            echo json_encode($data);
        }
    }
    public function getNik()
    {
        $id = $this->input->post('val');
        if ($id) {
            $data = $this->blt->getCustome(['number_id' => $id]);
            echo json_encode($data);
        }
    }

    public function delete()
    {
        superAdmin();
        $id = $this->input->post('id');
        if ($id) {
            $data = $this->blt->delete($id);
            echo json_encode($data);
        }
    }

    public function list()
    {
        $sess_admin = $this->session->userdata();
        $report = $this->input->post('report');
        $filter = $this->input->post('filter');
        $key = $this->input->post('key');
        $list = $this->blt->get_datatables($filter, $key);
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $value) {
            $social = "";
            if ($value->pkh == 1) {
                $social ? $social .= " | PKH" : $social = "PKH";
            }
            if ($value->bst == 1) {
                $social ? $social .= " | BST" : $social = "BST";
            }
            if ($value->blt_desa == 1) {
                $social ? $social .= " | BLT DESA" : $social = "BLT DESA";
            }
            if ($value->bpnt == 1) {
                $social ? $social .= " | BPNT" : $social = "BPNT";
            }
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $value->fullname;
            $row[] = $value->number_id;
            $row[] = $value->family_card_number;
            $row[] = $value->neighborhood_association;
            $row[] = $value->citizens_association;
            $row[] = $value->village;
            if ($sess_admin['username'] == "garis" || $sess_admin['username'] == "admin") {
                $delete = "
                <button type='button' class='btn btn-outline-danger btn-sm' onclick='remove(" . "\"" . $value->id . "\")' >
                    <i class='fas fa-trash fa-xs'></i>
                </button>";
            } else {
                $delete = " ";
            }
            $action = "
            <div class='row'>
                <div class='col-6'>
                " . $social . "
                </div>
                <div class='col-6'>
                    <div class='row'>
                    <button type='button' class='btn btn-outline-success btn-sm' data-toggle='modal' data-target='#dataBlt' onclick='get(" . "\"" . $value->id . "\")'>
                        <i class='fas fa-pen fa-xs'></i>
                    </button>
                    " . $delete . "
                    </div>
                </div>
            </div>";
            if ($sess_admin['username'] != "admin") {
                if ($sess_admin['username'] != "garis") {
                    if ($value->blt_desa != 1) $action = $social;
                }
            }
            $report == 1 ? $row[] = $social : $row[] = $action;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "data" => $data,
            "recordsTotal" => $this->blt->count_all(),
            "recordsFiltered" => $this->blt->count_filtered($filter, $key)
        );
        echo json_encode($output);
    }

    public function export($filter, $custom = null)
    {
        superAdmin();
        // $title1 = "DAFTAR NAMA-NAMA PENERIMA BANTUAN LANGSUNG TUNAI (BLT) DANA DESA";
        // $title2 = "AKIBAT DAMPAK PANDEMI CORONA VIRUS DISEASE 2019 (COVID 19)";
        // $title3 = "DESA SINDANGSARI KECAMATAN CIKONENG KABUPATEN CIAMIS";
        // $title4 = "TAHUN 2020";
        $data = $this->blt->getList(urldecode($filter), urldecode($custom));
        $spreadsheet = new Spreadsheet;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        // $spreadsheet->setActiveSheetIndex(0)
        //     ->setCellValue('I5', date('d F Y'));
        // $spreadsheet->getActiveSheet()->mergeCells('A6:I6')
        //     ->mergeCells('A7:I7')
        //     ->mergeCells('A8:I8')
        //     ->mergeCells('A9:I9');
        // $spreadsheet->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('A8')->getAlignment()->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('A9')->getAlignment()->setHorizontal('center');
        // $spreadsheet->setActiveSheetIndex(0)
        //     ->setCellValue('A6', $title1)
        //     ->setCellValue('A7', $title2)
        //     ->setCellValue('A8', $title3)
        //     ->setCellValue('A9', $title4);
        $spreadsheet->getActiveSheet()
            ->mergeCells('E1:G1')
            ->mergeCells('A1:A2')
            ->mergeCells('B1:B2')
            ->mergeCells('C1:C2')
            ->mergeCells('D1:D2')
            ->mergeCells('H1:H2')
            ->mergeCells('I1:I2');
        $spreadsheet->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('I11')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('A1:H10000')->getAlignment()->setVertical('center');
        // $spreadsheet->getActiveSheet()->getStyle('A1:I1000')->getAlignment()->setVertical('center');
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'NO')
            ->setCellValue('B1', 'NIK')
            ->setCellValue('C1', 'NO KK')
            ->setCellValue('D1', 'NAMA')
            ->setCellValue('E1', 'ALAMAT')
            ->setCellValue('H1', 'JENIS BANSOS');
        // ->setCellValue('H11', 'HASIL VERIFIKASI')
        // ->setCellValue('I11', 'BESAR ANGGARAN');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('E2', 'DUSUN')
            ->setCellValue('F2', 'RT')
            ->setCellValue('G2', 'RW');

        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleArray);
        $row = 3;
        $no = 1;
        foreach ($data as $value) {
            // switch ($value->result) {
            //     case 1:
            //         $result = "MEMENUHI SYARAT";
            //         $amount = 18000000;
            //         break;
            //     case 0:
            //         $result = "TIDAK MEMENUHI SYARAT";
            //         $amount = 0;
            //         break;
            // }
            $social = "";
            if ($value->pkh == 1) {
                $social ? $social .= "| PKH" : $social = "PKH";
            }
            if ($value->bst == 1) {
                $social ? $social .= "| BST" : $social = "BST";
            }
            if ($value->blt_desa == 1) {
                $social ? $social .= "| BLT DESA" : $social = "BLT DESA";
            }
            if ($value->bpnt == 1) {
                $social ? $social .= "| BPNT" : $social = "BPNT";
            }
            $formatNik = new RichText();
            $formatKK = new RichText();
            $formatNik->createText($value->number_id);
            $formatKK->createText($value->family_card_number);
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $no)
                ->setCellValue('B' . $row,  $formatNik)
                ->setCellValue('C' . $row, $formatKK)
                ->setCellValue('D' . $row, $value->fullname)
                ->setCellValue('E' . $row, $value->village)
                ->setCellValue('F' . $row, $value->neighborhood_association)
                ->setCellValue('G' . $row, $value->citizens_association)
                ->setCellValue('H' . $row, $social);
            // ->setCellValue('I' . $row, $amount);

            $spreadsheet->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->applyFromArray($styleArray);
            $row++;
            $no++;
        }
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        // $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);

        $fileName = "Laporan BLT " . date('d F Y');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function reference()
    {
        exit();
        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (isset($_FILES['reference']['name']) && in_array($_FILES['reference']['type'], $file_mimes)) {
            $arr_file = explode('.', $_FILES['reference']['name']);
            $extension = end($arr_file);
            if ('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $reader->load($_FILES['reference']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet(0)->toArray();
            $data = [];
            foreach ($sheetData as $value) {
                $rowData = [
                    'neighborhood_association' =>  substr($value[0], 1),
                    'citizens_association' => substr($value[1], 1),
                    'village' => $value[2],
                ];
                array_push($data, $rowData);
                // $this->db->insert('tb_konten', $rowData);
            }
            $this->db->insert_batch('tb_reference', $data);
        }
    }
    public function import()
    {
        superAdmin();
        $type = $this->input->post('kind_of_social_assistance');
        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
            $arr_file = explode('.', $_FILES['file']['name']);
            $extension = end($arr_file);
            if ('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet(0)->toArray();
            $i = 0;
            $u = 0;
            $data = [];
            //PKH 
            foreach ($sheetData as $value) {
                switch ($type) {
                    case "BPNT":
                        $field = 'bpnt';
                        break;
                    case "BLT DESA":
                        $field = 'blt_desa';
                        break;
                    case "BST":
                        $field = 'bst';
                        break;
                    case "PKH":
                        $field = 'pkh';
                        break;
                }
                $id = "";
                $id = validateId($value[0]);

                if ($id == null || $id == "" || $id == " ") {
                } else {
                    $old = [];
                    $old = $this->blt->getCustome(['number_id' => $id]);
                    if ($i != 0) {
                        if ($old) {
                            $u++;
                            // $updated =;
                            $this->blt->updateByNik($id,  [$field => 1]);
                        } else {
                            $rowData = [
                                'number_id' => $id,
                                'family_card_number' => validateId($value[1]),
                                'fullname' => $value[2],
                                'neighborhood_association' => sprintf("%02d", $value[3]),
                                'citizens_association' => sprintf("%02d", $value[4]),
                                'village' => $value[5],
                                $field => 1,
                                'result' => '1',
                            ];
                            array_push($data, $rowData);
                        }
                    }
                }
                $i++;
            }
            if ($data) {
                $this->db->insert_batch('tb_blt', $data);
            }
        }
        echo json_encode($u);
    }

    public function getAddress()
    {
        $id = $this->input->post('val');
        $data = $this->db->get_where('tb_reference', ['neighborhood_association' => $id])->row();
        echo json_encode($data);
    }

    public function createAdmin()
    {
        superAdmin();
        $res = "";
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if ($username && $password) {
            $data = [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'username' => $username,
                'password' => md5($password)
            ];
            $res = $this->db->insert('tb_admin', $data);
        }
        echo json_encode($res);
    }

    public function admin()
    {
        superAdmin();
        $data = $this->admin->getList();
        echo json_encode($data);
    }
    public function deleteAdmin()
    {
        superAdmin();
        $id = $this->input->post('id');
        $data = $this->admin->delete($id);
        echo json_encode($data);
    }

    public function clear()
    {
        superAdmin();
        $this->blt->truncate();
    }
}
