<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mastermodel extends CI_Model
{

    var $sys = 'zx_xvrty';

    function get_user($id)
    {
        $sql = $this->db->from($this->sys)
            ->where('usr_id', $id)
            ->get()
            ->row_array();
        $user = array(
            'id' => $sql['usr_id'],
            'nama' => $sql['usr_nama'],
            'username' => $sql['usr_username'],
            'password' => $sql['usr_password'],
            'level' => $sql['usr_level'],
            'gudang' => (is_null($sql['usr_gdg_id'])) ? 0 : $sql['usr_gdg_id'],
            'daerah' => (is_null($sql['usr_dae_id'])) ? 0 : $sql['usr_dae_id']
        );
        return $user;
    }

    function cek_user($username)
    {
        $sql = $this->db->from($this->sys)
            ->where('usr_username', $username)
            ->get();
        if ($sql->num_rows() > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function li_user()
    {
        $sql = $this->db->from($this->sys)
            ->join('m_gudang', 'usr_gdg_id = gdg_id', 'left')
            ->order_by('usr_level', 'usr_nama')
            ->get()
            ->result_array();
        return $sql;
    }

    function sv_user($data)
    {
        if ($data['usr_id'] == '') {
            $data['usr_id'] = $this->ascfunc->newid_($this->sys, 'usr_id');
            $sql = $this->db->insert($this->sys, $data);
        } else {
            $sql = $this->db->where('usr_id', $data['usr_id'])
                ->update($this->sys, $data);
        }
        return $sql;
    }

    function del_user($id)
    {
        $sql = $this->db->where('usr_id', $id)->delete($this->sys);
        return $sql;
    }

    function sv_info($data)
    {
        foreach ($data as $key => $val) {
            $q = $this->db->where('sis_kode', 'inf_' . $key)
                ->update('m_sistem', array('sis_deskripsi' => $val));
            if (!$q) {
                return FALSE;
            }
        }
        return TRUE;
    }

    function get_produk($id)
    {
        $sql = $this->db->from('m_produk')
            ->where('prd_id', $id)
            ->get()
            ->row_array();
        return $sql;
    }

    function sspproduk($aColumns, $sWhere, $sOrder, $sLimit, $kategori)
    {
        if ($kategori != 0) {

            $query = $this->db->query("SELECT * FROM (
            SELECT m_produk.*,m_supplier.sup_nama FROM m_produk 
            JOIN m_supplier ON prd_sup_id = sup_id 
            WHERE prd_ktg_id = '$kategori'
        ) A
        $sWhere
        $sOrder
        $sLimit");
        } else {
            $query = $this->db->query("SELECT * FROM (
            SELECT m_produk.*,m_supplier.sup_nama FROM m_produk 
            JOIN m_supplier ON prd_sup_id = sup_id 
        ) A
        $sWhere
        $sOrder
        $sLimit");
        }
        return $query->result_array();
    }

    function sspproduk_total($sIndexColumn, $sWhere, $sOrder, $kategori)
    {
        if ($kategori != 0) {
            $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT m_produk.*,m_supplier.sup_nama FROM m_produk 
            JOIN m_supplier ON prd_sup_id = sup_id 
            WHERE prd_ktg_id = '$kategori'
        ) A
        $sWhere
        $sOrder");
        } else {
            $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT m_produk.*,m_supplier.sup_nama FROM m_produk 
            JOIN m_supplier ON prd_sup_id = sup_id 
        ) A
        $sWhere
        $sOrder");
        }
        return $query->num_rows();
    }

    function sv_produk($data)
    {
        if ($data['prd_id'] == '') {
            $data['prd_id'] = $this->ascfunc->newid_('m_produk', 'prd_id');
            $sql = $this->db->insert('m_produk', $data);
        } else {
            $sql = $this->db->where('prd_id', $data['prd_id'])
                ->update('m_produk', $data);
        }
        return $sql;
    }

    function del_produk($id)
    {
        $sql = $this->db->where('prd_id', $id)->delete('m_produk');
        return $sql;
    }

    function sspstok($aColumns, $sWhere, $sOrder, $sLimit, $gudang)
    {
        $query = $this->db->query("SELECT * FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang 
            GROUP BY prd_id 
        ) A
        $sWhere
        $sOrder
        $sLimit");
        return $query->result_array();
    }

    function sspstok_total($sIndexColumn, $sWhere, $sOrder, $gudang)
    {
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang 
            GROUP BY prd_id 
        ) A
        $sWhere
        $sOrder");
        return $query->num_rows();
    }

    function sspstokdetail($aColumns, $sWhere, $sOrder, $sLimit, $id, $gudang)
    {
        $query = $this->db->query("SELECT * FROM (
            SELECT * FROM d_stok 
            JOIN d_nota ON stk_nota_id = nota_id 
            WHERE stk_prd_id = '$id' AND stk_gdg_id = '$gudang'  
            ORDER BY stk_tanggal DESC, stk_jam DESC
        ) A
        $sWhere
        $sOrder
        $sLimit");
        return $query->result_array();
    }

    function sspstokdetail_total($sIndexColumn, $sWhere, $sOrder, $id, $gudang)
    {
        $query = $this->db->query("SELECT * FROM (
            SELECT * FROM d_stok 
            JOIN d_nota ON stk_nota_id = nota_id 
            WHERE stk_prd_id = '$id' AND stk_gdg_id = '$gudang'   
            ORDER BY stk_tanggal DESC, stk_jam DESC
        ) A
        $sWhere
        $sOrder");
        return $query->num_rows();
    }

    function sspbarangmasuk($aColumns, $sWhere, $sOrder, $sLimit, $supplier, $kategori)
    {
        $query = $this->db->query("SELECT * FROM (
            SELECT * FROM m_produk 
            WHERE prd_sup_id = '$supplier' 
                AND prd_ktg_id IN ($kategori)
        ) A
        $sWhere
        $sOrder
        $sLimit");
        return $query->result_array();
    }

    function sspbarangmasuk_total($sIndexColumn, $sWhere, $sOrder, $supplier, $kategori)
    {
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM m_produk 
            WHERE prd_sup_id = '$supplier' 
                AND prd_ktg_id IN ($kategori)
        ) A
        $sWhere
        $sOrder");
        return $query->num_rows();
    }

    function ssppindahbarang($aColumns, $sWhere, $sOrder, $sLimit, $gudang, $kategori)
    {
        $query = $this->db->query("SELECT * FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang  
                AND prd_ktg_id IN ($kategori) 
            GROUP BY prd_id 
            HAVING stok > 0
        ) A
        $sWhere
        $sOrder
        $sLimit");
        return $query->result_array();
    }

    function ssppindahbarang_total($sIndexColumn, $sWhere, $sOrder, $gudang, $kategori)
    {
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang  
                AND prd_ktg_id IN ($kategori) 
            GROUP BY prd_id 
            HAVING stok > 0
        ) A
        $sWhere
        $sOrder");
        return $query->num_rows();
    }

    function ssppenjualan($aColumns, $sWhere, $sOrder, $sLimit, $gudang, $kategori)
    {
        $where = '';
        if ($kategori > 0) {
            $where = " AND prd_ktg_id = '$kategori' ";
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang $where  
            GROUP BY prd_id 
            HAVING stok > 0
        ) A
        $sWhere
        $sOrder
        $sLimit");
        return $query->result_array();
    }

    function ssppenjualan_total($sIndexColumn, $sWhere, $sOrder, $gudang, $kategori)
    {
        $where = '';
        if ($kategori > 0) {
            $where = " AND prd_ktg_id = '$kategori' ";
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang $where 
            GROUP BY prd_id 
            HAVING stok > 0
        ) A
        $sWhere
        $sOrder");
        return $query->num_rows();
    }

    function sspretur($aColumns, $sWhere, $sOrder, $sLimit, $gudang, $supplier)
    {
        $query = $this->db->query("SELECT * FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang AND prd_sup_id = $supplier  
            GROUP BY prd_id 
            HAVING stok > 0
        ) A
        $sWhere
        $sOrder
        $sLimit");
        return $query->result_array();
    }

    function sspretur_total($sIndexColumn, $sWhere, $sOrder, $gudang, $supplier)
    {
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT m_produk.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch FROM m_produk 
            JOIN d_stok ON prd_id = stk_prd_id 
            WHERE stk_gdg_id = $gudang AND prd_sup_id = $supplier 
            GROUP BY prd_id 
            HAVING stok > 0
        ) A
        $sWhere
        $sOrder");
        return $query->num_rows();
    }
}
