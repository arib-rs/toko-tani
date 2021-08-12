<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Universalmodel extends CI_Model {
    
    function li_data($table, $order = "") {
        $sql = $this->db->from($table);
        if ($order != "") {
            $sql->order_by($order);
        }
        return $sql->get();
    }

    function get_data($table, $where, $order = "") {
        $sql = $this->db->where($where)->from($table);
        if ($order != "") {
            $sql->order_by($order);
        }
        return $sql->get();
    }
    
    function sv_data($table, $data, $update = false, $where = array()) {
        if (!$update) {
            $sql = $this->db->insert($table, $data);
        } else {
            $sql = $this->db->where($where)->update($table, $data);
        }
        return $sql;
    }

    function del_data($table, $where) {
        $sql = $this->db->delete($table, $where);
        return $sql;
    }
    
    function find_data($table, $like) {
        $sql = $this->db->from($table)
                ->like($like)
                ->get()
                ->result_array();
        return $sql;
    }

}
