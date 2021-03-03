<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class M_Blt extends CI_Model
{
    private $table = "tb_blt";
    private $primary = "id";
    private $family = "family_card_number";
    private $unique = "number_id";
    var $column_order = array(null, 'fullname', 'number_id', 'family_card_number', 'neighborhood_association', 'citizens_association', 'village', 'result', null); //set column field database for datatable orderable
    var $column_search = array('fullname', 'number_id', 'family_card_number'); //set column field database for datatable searchable 
    var $order = array('updated_at' => 'DESC'); // default order 

    private function _get_datatables_query($filter, $key)
    {
        $this->db->select('*');
        if ($filter && $key) {
            if ($filter == "kind_of_social_assistance") {
                $filter = $this->getSocial($key);
                $key = 1;
            }
            if ($filter)$this->db->like($filter, $key);
        }
        $this->db->from($this->table);
        $i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {
                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($filter, $key)
    {
        $this->_get_datatables_query($filter, $key);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    function count_filtered($filter, $key)
    {
        $this->_get_datatables_query($filter, $key);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['result'] = 1;
        if ($this->getCustome($data[$this->family])) return "exist";
        if ($this->db->insert($this->table, $data)) {
            return "success";
        } else {
            return "invalid";
        }
    }
    public function get($id)
    {
        return $this->db->select('*')
            ->where($this->primary, $id)
            ->get($this->table)->row();
    }

    public function getCustome($data)
    {
        return $this->db->select('*')
            ->where($data)
            ->get($this->table)->row();
    }

    public function getList($filter = null, $custom = null)
    {
        $this->db->select('*');
        if ($filter && $custom) {
            if ($filter == "kind_of_social_assistance") {
                $filter = $this->getSocial($custom);
                $custom = 1;
            }
            if ($filter) $this->db->where($filter, $custom);
        }
        $this->db->from($this->table);
        $data = $this->db->get()->result();
        return $data;
    }

    public function delete($id)
    {
        $this->db->where($this->primary, $id);
        $res = $this->db->delete($this->table);
        return $res;
    }
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where($this->primary, $id);
        $res = $this->db->update($this->table, $data);
        return $res;
    }
    public function updateByNik($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('number_id', $id);
        $res = $this->db->update($this->table, $data);
        return $res;
    }

    public function truncate()
    {
        $sql = "TRUNCATE tb_blt";
        $this->db->query($sql);
    }

    private function getSocial($kind)
    {
        $field = "";
        switch ($kind) {
            case "BPNT":
            case "bpnt":
                $field = "bpnt";
                break;
            case "BLT DESA":
            case "Blt Desa":
            case "blt desa":
                $field = "blt_desa";
                break;
            case "BST":
            case "bst":
                $field = "bst";
                break;
            case "PKH":
            case "pkh":
                $field = "pkh";
                break;
        }
        return $field;
    }
}
