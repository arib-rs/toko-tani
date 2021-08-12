<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transaksimodel extends CI_Model
{

    function get_ntbm($id)
    {
        $data = $this->db->select('d_nota.*, m_supplier.*, m_gudang.gdg_nama as tujuan')
            ->from('d_nota')
            ->join('m_supplier', 'nota_asal = sup_id')
            ->join('m_gudang', 'nota_tujuan = gdg_id')
            ->where('nota_id', $id)
            ->get()
            ->row_array();
        $detail = $this->db->from('d_nota_detail')
            ->join('m_produk', 'dtn_prd_id = prd_id')
            ->where('dtn_nota_id', $id)
            ->order_by('prd_kode')
            ->get()
            ->result_array();
        $data['detail'] = $detail;
        $total = 0;
        foreach ($detail as $val) {
            $total += $val['dtn_hargabeli'] * $val['dtn_jumlah'];
        }
        $data['total'] = $total;
        $total = $total - $data['nota_diskon'];
        $data['ppn'] = $total * $data['nota_ppn'] / 100;
        $data['grandtotal'] = $total + $data['ppn'];
        $this->load->helper("terbilang");
        $data['terbilang'] = ucwords(number_to_words($data['grandtotal']));
        return $data;
    }

    function edit_ntbm($id)
    {
        $data = $this->db->from('d_nota')
            ->join('m_supplier', 'nota_asal = sup_id')
            ->where('nota_id', $id)
            ->get()
            ->row_array();
        $data['detail'] = $this->db->from('d_nota_detail')
            ->join('m_produk', "dtn_prd_id = prd_id")
            ->where('dtn_nota_id', $id)
            ->order_by('prd_kode')
            ->get()
            ->result_array();
        $rekening = $this->db->select('kas_rekening')
            ->from('d_kas')
            ->where('kas_id', 'BLI' . $id)
            ->get()
            ->row_array();
        $data['rekening'] = $rekening['kas_rekening'];
        $data['operasional'] = array();
        $armada = $this->db->from('d_operasional')
            ->where('ope_nota_id', $id)
            ->like('ope_uraian', 'Armada', 'after')
            ->get()
            ->row_array();
        if (count($armada) > 0) {
            $data['operasional'][] = $armada;
            $data['operasional'][] = $this->db->from('d_operasional')
                ->where('ope_nota_id', $id)
                ->like('ope_uraian', 'Sopir', 'after')
                ->get()
                ->row_array();
        }
        $operasional = $this->db->from('d_operasional')
            ->where('ope_nota_id', $id)
            ->not_like('ope_uraian', 'Armada', 'after')
            ->not_like('ope_uraian', 'Sopir', 'after')
            ->get()
            ->result_array();
        foreach ($operasional as $val) {
            $data['operasional'][] = $val;
        }
        return $data;
    }

    function sv_barangmasuk($data, $detail, $operasional, $rekening)
    {
        if ($data['nota_id'] == '') {
            $this->load->helper('string');
            $data['nota_id'] = date('ymdhis');
            $sql = $this->db->insert('d_nota', $data);
            if ($sql) {
                $barang = '';
                //rincian & stok
                foreach ($detail as $val) {
                    $dtl = array(
                        'dtn_id' => $data['nota_id'] . random_string('alnum', 5),
                        'dtn_prd_id' => $val['id'],
                        'dtn_hargabeli' => $val['hb'],
                        'dtn_jumlah' => $val['jumlah'],
                        'dtn_nota_id' => $data['nota_id'],
                        'dtn_kadaluarsa' => date('Y-m-d', strtotime(str_replace('/', '-', $val['kadaluarsa']))),
                        'dtn_nobatch' => $val['nobatch']

                    );
                    $this->db->insert('d_nota_detail', $dtl);
                    $stok = array(
                        'stk_id' => $dtl['dtn_id'],
                        'stk_prd_id' => $val['id'],
                        'stk_nota_id' => $data['nota_id'],
                        'stk_tanggal' => $data['nota_tanggal'],
                        'stk_jam' => $data['nota_jam'],
                        'stk_jumlah' => $val['jumlah'],
                        'stk_gdg_id' => $data['nota_tujuan'],
                        'stk_kadaluarsa' => date('Y-m-d', strtotime(str_replace('/', '-', $val['kadaluarsa']))),
                        'stk_nobatch' => $val['nobatch']
                    );
                    $this->db->insert('d_stok', $stok);
                    $produk = $this->db->get_where('m_produk', array('prd_id' => $val['id']))->row_array();
                    $barang .= $produk['prd_nama'] . ' ' . $val['jumlah'] . ' ' . $produk['prd_satuan'] . ' , ';
                }

                //kas            
                $sup = $this->db->get_where('m_supplier', array('sup_id' => $data['nota_asal']))->row_array();
                $iskredit = ($data['nota_iskredit']) ? ' [Kredit]' : '';
                $kas = array(
                    'kas_id' => 'BLI' . $data['nota_id'],
                    'kas_rekening' => $rekening,
                    'kas_uraian' => 'Pembelian' . $iskredit . ' ke ' . $sup['sup_nama'] . ' ' . substr($barang, 0, -2),
                    'kas_keterangan' => NULL,
                    'kas_tanggal' => $data['nota_tanggal'],
                    'kas_jam' => $data['nota_jam'],
                    'kas_debet' => NULL,
                    'kas_kredit' => $data['nota_total'],
                    'kas_jenis' => 'beli'
                );
                $this->db->insert('d_kas', $kas);
                //operasional
                $tgl = date('d/m/Y', strtotime($data['nota_tanggal']));
                foreach ($operasional as $val) {
                    $op = array(
                        'ope_id' => 'OPE' . random_string('alnum', 17),
                        'ope_uraian' => $val['op'],
                        'ope_biaya' => $val['biaya'],
                        'ope_nota_id' => $data['nota_id']
                    );
                    $this->db->insert('d_operasional', $op);
                    $kas = array(
                        'kas_id' => $op['ope_id'],
                        'kas_rekening' => $rekening,
                        'kas_uraian' => 'Operasional ' . $val['op'],
                        'kas_keterangan' => 'Pembelian ke ' . $sup['sup_nama'] . ' Tanggal ' . $tgl,
                        'kas_tanggal' => $data['nota_tanggal'],
                        'kas_jam' => $data['nota_jam'],
                        'kas_debet' => NULL,
                        'kas_kredit' => $val['biaya'],
                        'kas_jenis' => 'operasional'
                    );
                    $this->db->insert('d_kas', $kas);
                }
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            $sql = $this->db->where('nota_id', $data['nota_id'])
                ->update('d_nota', $data);
            if ($sql) {
                $barang = '';
                //rincian & stok
                foreach ($detail as $val) {
                    $dtl = array(
                        'dtn_prd_id' => $val['id'],
                        'dtn_hargabeli' => $val['hb'],
                        'dtn_jumlah' => $val['jumlah'],
                        'dtn_nota_id' => $data['nota_id'],
                        'dtn_kadaluarsa' => date('Y-m-d', strtotime(str_replace('/', '-', $val['kadaluarsa']))),
                        'dtn_nobatch' => $val['nobatch']
                    );
                    $this->db->where('dtn_prd_id', $val['id'])
                        ->where('dtn_nota_id', $data['nota_id'])
                        ->update('d_nota_detail', $dtl);
                    $stok = array(
                        'stk_jumlah' => $val['jumlah']
                    );
                    $this->db->where('stk_prd_id', $val['id'])
                        ->where('stk_nota_id', $data['nota_id'])
                        ->update('d_stok', $stok);
                    $produk = $this->db->get_where('m_produk', array('prd_id' => $val['id']))->row_array();
                    $barang .= $produk['prd_nama'] . ' ' . $val['jumlah'] . ' ' . $produk['prd_satuan'] . ' , ';
                }
                //kas    
                $sup = $this->db->get_where('m_supplier', array('sup_id' => $data['nota_asal']))->row_array();
                $kas = array(
                    'kas_rekening' => $rekening,
                    'kas_uraian' => 'Pembelian ke ' . $sup['sup_nama'] . ' ' . substr($barang, 0, -2),
                    'kas_keterangan' => NULL,
                    'kas_tanggal' => $data['nota_tanggal'],
                    'kas_jam' => $data['nota_jam'],
                    'kas_debet' => NULL,
                    'kas_kredit' => $data['nota_total'],
                    'kas_jenis' => 'beli'
                );
                $this->db->where('kas_id', 'BLI' . $data['nota_id'])
                    ->update('d_kas', $kas);
                //operasional
                if (count($operasional) > 0) {
                    $tgl = date('d/m/Y', strtotime($data['nota_tanggal']));
                    foreach ($operasional as $val) {
                        $op = array(
                            'ope_uraian' => $val['op'],
                            'ope_biaya' => $val['biaya']
                        );
                        $this->db->where('ope_id', $val['id'])
                            ->update('d_operasional', $op);
                        $kas = array(
                            'kas_rekening' => $rekening,
                            'kas_uraian' => 'Operasional ' . $val['op'],
                            'kas_keterangan' => 'Pembelian ke ' . $sup['sup_nama'] . ' Tanggal ' . $tgl,
                            'kas_tanggal' => $data['nota_tanggal'],
                            'kas_jam' => $data['nota_jam'],
                            'kas_debet' => NULL,
                            'kas_kredit' => $val['biaya'],
                            'kas_jenis' => 'operasional'
                        );
                        $this->db->where('kas_id', $val['id'])
                            ->update('d_kas', $kas);
                    }
                }
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function del_bm($id)
    {
        $q = $this->db->where('nota_id', $id)
            ->delete('d_nota');
        if ($q) {
            $this->db->where('dtn_nota_id', $id)->delete('d_nota_detail');
            $this->db->where('stk_nota_id', $id)->delete('d_stok');
            $this->db->where('kas_id', 'BLI' . $id)->delete('d_kas');
            $op_ = $this->db->where('ope_nota_id', $id)
                ->from('d_operasional')
                ->get()
                ->result_array();
            $op = array();
            foreach ($op_ as $value) {
                $op[] = $value['ope_id'];
            }
            if (count($op) > 0) {
                $this->db->where_in('kas_id', $op)->delete('d_kas');
            }
            $this->db->where('ope_nota_id', $id)->delete('d_operasional');
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function sspntbm($aColumns, $sWhere, $sLimit, $bulan, $tahun)
    {
        $where = "WHERE nota_jenis = 'BELI'";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(nota_tanggal) = $tahun AND MONTH(nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT a.*, d.sup_nama, (SELECT GROUP_CONCAT(CONCAT(dtn_jumlah, ' ', prd_satuan, ' - ', prd_nama) SEPARATOR '<br>') FROM d_nota_detail b JOIN m_produk c ON b.dtn_prd_id = c.prd_id WHERE b.dtn_nota_id = a.nota_id) AS detail 
            FROM d_nota a
            JOIN m_supplier d ON a.nota_asal = d.sup_id 
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntbm_total($sIndexColumn, $sWhere, $bulan, $tahun)
    {
        $where = "WHERE nota_jenis = 'BELI'";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(nota_tanggal) = $tahun AND MONTH(nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_nota 
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function get_ntpb($id)
    {
        $data = $this->db->select('a.*, b.gdg_nama as asal, c.*')
            ->from('d_nota a')
            ->join('m_gudang b', 'a.nota_asal = b.gdg_id')
            ->join('m_gudang c', 'a.nota_tujuan = c.gdg_id')
            ->where('nota_id', $id)
            ->get()
            ->row_array();
        $detail = $this->db->from('d_nota_detail')
            ->join('m_produk', 'dtn_prd_id = prd_id')
            ->where('dtn_nota_id', $id)
            ->order_by('prd_kode')
            ->get()
            ->result_array();
        $data['detail'] = $detail;
        return $data;
    }

    function edit_ntpb($id)
    {
        $data = $this->db->select('a.*, b.gdg_nama as asal, c.gdg_nama as tujuan')
            ->from('d_nota a')
            ->join('m_gudang b', 'a.nota_asal = b.gdg_id')
            ->join('m_gudang c', 'a.nota_tujuan = c.gdg_id')
            ->where('nota_id', $id)
            ->get()
            ->row_array();
        $data['detail'] = $this->db->from('d_nota_detail')
            ->join('m_produk', "dtn_prd_id = prd_id")
            ->where('dtn_nota_id', $id)
            ->order_by('prd_kode')
            ->get()
            ->result_array();
        $data['rekening'] = 0;
        $data['operasional'] = array();
        $armada = $this->db->from('d_operasional')
            ->where('ope_nota_id', $id)
            ->like('ope_uraian', 'Armada', 'after')
            ->get()
            ->row_array();
        if (count($armada) > 0) {
            $data['operasional'][] = $armada;
            $data['operasional'][] = $this->db->from('d_operasional')
                ->where('ope_nota_id', $id)
                ->like('ope_uraian', 'Sopir', 'after')
                ->get()
                ->row_array();
        }
        $operasional = $this->db->from('d_operasional')
            ->where('ope_nota_id', $id)
            ->not_like('ope_uraian', 'Armada', 'after')
            ->not_like('ope_uraian', 'Sopir', 'after')
            ->get()
            ->result_array();
        foreach ($operasional as $val) {
            $data['operasional'][] = $val;
        }
        return $data;
    }

    function sv_pindahbarang($data, $detail, $operasional, $rekening)
    {
        if ($data['nota_id'] == '') {
            $this->load->helper('string');
            $data['nota_id'] = date('ymdhis');
            $sql = $this->db->insert('d_nota', $data);
            if ($sql) {
                //rincian & stok
                foreach ($detail as $val) {
                    $id = $data['nota_id'] . random_string('alnum', 3);
                    $dtl = array(
                        'dtn_id' => 'D_' . $id,
                        'dtn_prd_id' => $val['id'],
                        'dtn_hargabeli' => $val['hb'],
                        'dtn_jumlah' => $val['jumlah'],
                        'dtn_nota_id' => $data['nota_id'],
                        'dtn_kadaluarsa' => $val['kadaluarsa'],
                        'dtn_nobatch' => $val['nobatch']
                    );
                    $this->db->insert('d_nota_detail', $dtl);
                    $stokasal = array(
                        'stk_id' => 'A_' . $id,
                        'stk_prd_id' => $val['id'],
                        'stk_nota_id' => $data['nota_id'],
                        'stk_tanggal' => $data['nota_tanggal'],
                        'stk_jam' => $data['nota_jam'],
                        'stk_jumlah' => -1 * $val['jumlah'],
                        'stk_gdg_id' => $data['nota_asal'],
                        'stk_kadaluarsa' => $val['kadaluarsa'],
                        'stk_nobatch' => $val['nobatch']
                    );
                    $this->db->insert('d_stok', $stokasal);
                    $stoktujuan = array(
                        'stk_id' => 'T_' . $id,
                        'stk_prd_id' => $val['id'],
                        'stk_nota_id' => $data['nota_id'],
                        'stk_tanggal' => $data['nota_tanggal'],
                        'stk_jam' => $data['nota_jam'],
                        'stk_jumlah' => $val['jumlah'],
                        'stk_gdg_id' => $data['nota_tujuan'],
                        'stk_kadaluarsa' => $val['kadaluarsa'],
                        'stk_nobatch' => $val['nobatch']
                    );
                    $this->db->insert('d_stok', $stoktujuan);
                }
                //kas            
                $gudang = $this->db->get_where('m_gudang', array('gdg_id' => $data['nota_tujuan']))->row_array();
                //operasional
                $tgl = date('d/m/Y', strtotime($data['nota_tanggal']));
                foreach ($operasional as $val) {
                    $op = array(
                        'ope_id' => 'OPE' . random_string('alnum', 17),
                        'ope_uraian' => $val['op'],
                        'ope_biaya' => $val['biaya'],
                        'ope_nota_id' => $data['nota_id']
                    );
                    $this->db->insert('d_operasional', $op);
                    $kas = array(
                        'kas_id' => $op['ope_id'],
                        'kas_rekening' => $rekening,
                        'kas_uraian' => 'Operasional ' . $val['op'],
                        'kas_keterangan' => 'Pemindahan barang ke ' . $gudang['gdg_nama'] . ' Tanggal ' . $tgl,
                        'kas_tanggal' => $data['nota_tanggal'],
                        'kas_jam' => $data['nota_jam'],
                        'kas_debet' => NULL,
                        'kas_kredit' => $val['biaya'],
                        'kas_jenis' => 'operasional'
                    );
                    $this->db->insert('d_kas', $kas);
                }
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            $sql = $this->db->where('nota_id', $data['nota_id'])
                ->update('d_nota', $data);
            if ($sql) {
                $barang = '';
                //rincian & stok
                foreach ($detail as $val) {
                    $dtl = array(
                        'dtn_prd_id' => $val['id'],
                        'dtn_hargabeli' => $val['hb'],
                        'dtn_jumlah' => $val['jumlah'],
                        'dtn_nota_id' => $data['nota_id']
                    );
                    $this->db->where('dtn_prd_id', $val['id'])
                        ->where('dtn_nota_id', $data['nota_id'])
                        ->update('d_nota_detail', $dtl);
                    $stokasal = array(
                        'stk_jumlah' => -1 * $val['jumlah']
                    );
                    $this->db->where('stk_prd_id', str_replace('D_', 'A_', $val['id']))
                        ->where('stk_nota_id', $data['nota_id'])
                        ->update('d_stok', $stokasal);
                    $stoktujuan = array(
                        'stk_jumlah' => $val['jumlah']
                    );
                    $this->db->where('stk_prd_id', str_replace('D_', 'T_', $val['id']))
                        ->where('stk_nota_id', $data['nota_id'])
                        ->update('d_stok', $stokasal);
                }
                //kas    
                $gudang = $this->db->get_where('m_gudang', array('gdg_id' => $data['nota_tujuan']))->row_array();
                //operasional
                if (count($operasional) > 0) {
                    $tgl = date('d/m/Y', strtotime($data['nota_tanggal']));
                    foreach ($operasional as $val) {
                        $op = array(
                            'ope_uraian' => $val['op'],
                            'ope_biaya' => $val['biaya']
                        );
                        $this->db->where('ope_id', $val['id'])
                            ->update('d_operasional', $op);
                        $kas = array(
                            'kas_debet' => NULL,
                            'kas_kredit' => $val['biaya'],
                            'kas_jenis' => 'operasional'
                        );
                        $this->db->where('kas_id', $val['id'])
                            ->update('d_kas', $kas);
                    }
                }
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function del_pb($id)
    {
        $q = $this->db->where('nota_id', $id)
            ->delete('d_nota');
        if ($q) {
            $this->db->where('dtn_nota_id', $id)->delete('d_nota_detail');
            $this->db->where('stk_nota_id', $id)->delete('d_stok');
            $op_ = $this->db->where('ope_nota_id', $id)
                ->from('d_operasional')
                ->get()
                ->result_array();
            $op = array();
            foreach ($op_ as $value) {
                $op[] = $value['ope_id'];
            }
            if (count($op) > 0) {
                $this->db->where_in('kas_id', $op)->delete('d_kas');
            }
            $this->db->where('ope_nota_id', $id)->delete('d_operasional');
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function sspntpb($aColumns, $sWhere, $sLimit, $bulan, $tahun)
    {
        $where = "WHERE nota_jenis = 'PINDAH'";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(nota_tanggal) = $tahun AND MONTH(nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT a.*, d.gdg_nama as asal, e.gdg_nama as tujuan, (SELECT GROUP_CONCAT(CONCAT(dtn_jumlah, ' ', prd_satuan, ' - ', prd_nama) SEPARATOR '<br>') FROM d_nota_detail b JOIN m_produk c ON b.dtn_prd_id = c.prd_id WHERE b.dtn_nota_id = a.nota_id) AS detail 
            FROM d_nota a
            JOIN m_gudang d ON a.nota_asal = d.gdg_id  
            JOIN m_gudang e ON a.nota_tujuan = e.gdg_id  
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntpb_total($sIndexColumn, $sWhere, $bulan, $tahun)
    {
        $where = "WHERE nota_jenis = 'PINDAH'";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(nota_tanggal) = $tahun AND MONTH(nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_nota 
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function ishargaecer($id)
    {
        $data = $this->db->select('d_nota.*, m_customer.*')
            ->from('d_nota')
            ->join('m_customer', 'nota_tujuan = cus_id', 'left')
            ->where('nota_id', $id)
            ->get()
            ->row_array();
        if ($data['nota_tujuan'] > 0) {
            if ($data['cus_harga'] == 'hargaecer') {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    function get_ntpj($id)
    {
        $data = $this->db->select('d_nota.*, m_customer.*, m_gudang.gdg_nama as asal')
            ->from('d_nota')
            ->join('m_customer', 'nota_tujuan = cus_id', 'left')
            ->join('m_gudang', 'nota_asal = gdg_id')
            ->where('nota_id', $id)
            ->get()
            ->row_array();
        $detail = $this->db->from('d_nota_detail')
            ->join('m_produk', 'dtn_prd_id = prd_id')
            ->where('dtn_nota_id', $id)
            ->order_by('prd_kode')
            ->get()
            ->result_array();
        $total = 0;
        foreach ($detail as $val) {
            $total += $val['dtn_hargajual'] * $val['dtn_jumlah'];
        }
        $data['total'] = $total;
        $total = $total - $data['nota_diskon'];
        $data['ppn'] = $total * $data['nota_ppn'] / 100;
        $data['grandtotal'] = $total + $data['ppn'];
        $this->load->helper("terbilang");
        $data['terbilang'] = ucwords(number_to_words($data['grandtotal']));
        $data['detail'] = $detail;
        $data['dp'] = 0;
        $data['sisa'] = 0;
        if ($data['nota_iskredit']) {
            $pembayaran = $this->db->limit(1)->order_by('pmb_tanggal, pmb_jam')->get_where('d_pembayaran', array('pmb_nota_id' => $id))->row_array();
            $data['dp'] = $this->ascfunc->nf_(abs($pembayaran['pmb_nominal']));
            $data['sisa'] = $this->ascfunc->nf_($data['grandtotal'] - abs($pembayaran['pmb_nominal']));
        }
        return $data;
    }

    function sv_penjualan($data, $detail, $operasional, $pembayaran, $rekening)
    {
        if ($data['nota_id'] == '') {
            $this->load->helper('string');
            $data['nota_id'] = date('ymdhis');
            $sql = $this->db->insert('d_nota', $data);
            if ($sql) {
                $barang = '';
                //rincian & stok
                foreach ($detail as $val) {
                    $dtl = array(
                        'dtn_id' => $data['nota_id'] . random_string('alnum', 5),
                        'dtn_prd_id' => $val['id'],
                        'dtn_hargabeli' => $val['hb'],
                        'dtn_hargajual' => $val['hj'],
                        'dtn_jumlah' => $val['jumlah'],
                        'dtn_nota_id' => $data['nota_id'],
                        'dtn_kadaluarsa' => date('Y-m-d'),
                        'dtn_nobatch' => 'SELL' . date('Ymd')
                    );
                    $this->db->insert('d_nota_detail', $dtl);
                    $stok = array(
                        'stk_id' => $dtl['dtn_id'],
                        'stk_prd_id' => $val['id'],
                        'stk_nota_id' => $data['nota_id'],
                        'stk_tanggal' => $data['nota_tanggal'],
                        'stk_jam' => $data['nota_jam'],
                        'stk_jumlah' => -1 * $val['jumlah'],
                        'stk_gdg_id' => $data['nota_asal'],
                        'stk_kadaluarsa' => date('Y-m-d'),
                        'stk_nobatch' => 'SELL' . date('Ymd')
                    );
                    $this->db->insert('d_stok', $stok);
                    $produk = $this->db->get_where('m_produk', array('prd_id' => $val['id']))->row_array();
                    $barang .= $produk['prd_nama'] . ' ' . $val['jumlah'] . ' ' . $produk['prd_satuan'] . ' , ';
                }
                //kas            
                if ($data['nota_tujuan'] > 0) {
                    $cus = $this->db->get_where('m_customer', array('cus_id' => $data['nota_tujuan']))->row_array();
                    $customer = 'Customer ' . $cus['cus_nama'];
                } else {
                    $customer = 'Customer Umum';
                }
                $iskredit = ($data['nota_iskredit']) ? ' [Kredit]' : '';
                $kas = array(
                    'kas_id' => 'JL' . $data['nota_id'],
                    'kas_rekening' => $rekening,
                    'kas_uraian' => 'Penjualan' . $iskredit . ' ke ' . $customer . ' ' . substr($barang, 0, -2),
                    'kas_keterangan' => NULL,
                    'kas_tanggal' => $data['nota_tanggal'],
                    'kas_jam' => $data['nota_jam'],
                    'kas_debet' => $data['nota_total'],
                    'kas_kredit' => NULL,
                    'kas_jenis' => 'jual'
                );
                $this->db->insert('d_kas', $kas);
                //dp
                if ($data['nota_iskredit']) {
                    $pm = array(
                        'pmb_id' => $this->ascfunc->newid_('d_pembayaran', 'pmb_id'),
                        'pmb_tanggal' => $pembayaran['tanggal'],
                        'pmb_jam' => $pembayaran['jam'],
                        'pmb_nominal' => $pembayaran['nominal'],
                        'pmb_nota_id' => $data['nota_id'],
                        'pmb_sup_id' => NULL
                    );
                    $this->db->insert('d_pembayaran', $pm);
                    $kas = array(
                        'kas_id' => 'BYRC' . $pm['pmb_id'],
                        'kas_rekening' => $rekening,
                        'kas_uraian' => 'Pembayaran dari ' . $customer,
                        'kas_keterangan' => NULL,
                        'kas_tanggal' => $pm['pmb_tanggal'],
                        'kas_jam' => $pm['pmb_jam'],
                        'kas_debet' => $pm['pmb_nominal'],
                        'kas_kredit' => NULL,
                        'kas_jenis' => 'piutang'
                    );
                    $this->db->insert('d_kas', $kas);
                }
                //operasional
                $tgl = date('d/m/Y', strtotime($data['nota_tanggal']));
                foreach ($operasional as $val) {
                    $op = array(
                        'ope_id' => 'OPE' . random_string('alnum', 17),
                        'ope_uraian' => $val['op'],
                        'ope_biaya' => $val['biaya'],
                        'ope_nota_id' => $data['nota_id']
                    );
                    $this->db->insert('d_operasional', $op);
                    $kas = array(
                        'kas_id' => $op['ope_id'],
                        'kas_rekening' => $rekening,
                        'kas_uraian' => 'Operasional ' . $val['op'],
                        'kas_keterangan' => 'Pembelian ke ' . $customer . ' Tanggal ' . $tgl,
                        'kas_tanggal' => $data['nota_tanggal'],
                        'kas_jam' => $data['nota_jam'],
                        'kas_debet' => NULL,
                        'kas_kredit' => $val['biaya'],
                        'kas_jenis' => 'operasional'
                    );
                    $this->db->insert('d_kas', $kas);
                }
                return $data['nota_id'];
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function sspntpj($aColumns, $sWhere, $sLimit, $bulan, $tahun, $gudang)
    {
        $where = "WHERE nota_jenis = 'JUAL' AND nota_asal = '$gudang'";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(nota_tanggal) = $tahun AND MONTH(nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT a.*, d.*, (SELECT GROUP_CONCAT(CONCAT(dtn_jumlah, ' ', prd_satuan, ' - ', prd_nama) SEPARATOR '<br>') FROM d_nota_detail b JOIN m_produk c ON b.dtn_prd_id = c.prd_id WHERE b.dtn_nota_id = a.nota_id) AS detail 
            FROM d_nota a
            LEFT JOIN m_customer d ON a.nota_tujuan = d.cus_id 
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntpj_total($sIndexColumn, $sWhere, $bulan, $tahun, $gudang)
    {
        $where = "WHERE nota_jenis = 'JUAL' AND nota_asal = '$gudang'";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(nota_tanggal) = $tahun AND MONTH(nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_nota a
            LEFT JOIN m_customer d ON a.nota_tujuan = d.cus_id 
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function del_pj($id)
    {
        //        $dt = $this->db->where('nota_id', $id)
        //                ->get('d_nota')->row_array();
        $q = $this->db->where('nota_id', $id)
            ->delete('d_nota');
        if ($q) {
            $this->db->where('dtn_nota_id', $id)->delete('d_nota_detail');
            $this->db->where('stk_nota_id', $id)->delete('d_stok');
            $this->db->where('kas_id', 'JL' . $id)->delete('d_kas');
            $op_ = $this->db->where('ope_nota_id', $id)
                ->from('d_operasional')
                ->get()
                ->result_array();
            $op = array();
            foreach ($op_ as $value) {
                $op[] = $value['ope_id'];
            }
            if (count($op) > 0) {
                $this->db->where_in('kas_id', $op)->delete('d_kas');
            }
            $this->db->where('ope_nota_id', $id)->delete('d_operasional');
            $pm_ = $this->db->where('pmb_nota_id', $id)
                ->from('d_pembayaran')
                ->get()
                ->result_array();
            $pm = $kaspm = array();
            foreach ($pm_ as $value) {
                $pm[] = 'BYRC' . $value['pmb_id'];
            }
            if (count($pm) > 0) {
                $this->db->where_in('kas_id', $pm)->delete('d_kas');
            }
            $this->db->where('pmb_nota_id', $id)->delete('d_pembayaran');
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function sv_requestbarang($data, $detail)
    {
        if ($data['rnota_id'] == '') {
            $this->load->helper('string');
            $data['rnota_id'] = date('ymdhis');
            $sql = $this->db->insert('d_requestnota', $data);
            if ($sql) {
                //rincian & stok
                foreach ($detail as $val) {
                    $id = $data['rnota_id'] . random_string('alnum', 3);
                    $dtl = array(
                        'dtrn_id' => $id,
                        'dtrn_prd_id' => $val['id'],
                        'dtrn_jumlah' => $val['jumlah'],
                        'dtrn_nota_id' => $data['rnota_id']
                    );
                    $this->db->insert('d_requestnota_detail', $dtl);
                }
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function sspntrb($aColumns, $sWhere, $sLimit, $idgudang, $bulan, $tahun)
    {
        $where = "WHERE rnota_tujuan = $idgudang";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(rnota_tanggal) = $tahun AND MONTH(rnota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT a.*, d.gdg_nama as asal, e.gdg_nama as tujuan, (SELECT GROUP_CONCAT(CONCAT(dtrn_jumlah, ' ', prd_satuan, ' - ', prd_nama) SEPARATOR '<br>') FROM d_requestnota_detail b JOIN m_produk c ON b.dtrn_prd_id = c.prd_id WHERE b.dtrn_nota_id = a.rnota_id) AS detail 
            FROM d_requestnota a
            JOIN m_gudang d ON a.rnota_asal = d.gdg_id  
            JOIN m_gudang e ON a.rnota_tujuan = e.gdg_id  
            $where 
            ORDER BY rnota_tanggal asc, rnota_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntrb_total($sIndexColumn, $sWhere, $idgudang, $bulan, $tahun)
    {
        $where = "WHERE rnota_tujuan = $idgudang";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(rnota_tanggal) = $tahun AND MONTH(rnota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_requestnota 
            $where 
            ORDER BY rnota_tanggal asc, rnota_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function sspntvb($aColumns, $sWhere, $sLimit, $idgudang, $bulan, $tahun)
    {
        $where = "WHERE rnota_asal = $idgudang";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(rnota_tanggal) = $tahun AND MONTH(rnota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT a.*, d.gdg_nama as asal, e.gdg_nama as tujuan, (SELECT GROUP_CONCAT(CONCAT(dtrn_jumlah, ' ', prd_satuan, ' - ', prd_nama) SEPARATOR '<br>') FROM d_requestnota_detail b JOIN m_produk c ON b.dtrn_prd_id = c.prd_id WHERE b.dtrn_nota_id = a.rnota_id) AS detail 
            FROM d_requestnota a
            JOIN m_gudang d ON a.rnota_asal = d.gdg_id  
            JOIN m_gudang e ON a.rnota_tujuan = e.gdg_id  
            $where 
            ORDER BY rnota_tanggal asc, rnota_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntvb_total($sIndexColumn, $sWhere, $idgudang, $bulan, $tahun)
    {
        $where = "WHERE rnota_asal = $idgudang";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(rnota_tanggal) = $tahun AND MONTH(rnota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_requestnota 
            $where 
            ORDER BY rnota_tanggal asc, rnota_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function edit_ntrb($id)
    {
        $data = $this->db->select('a.*, b.gdg_nama as asal, c.gdg_nama as tujuan')
            ->from('d_requestnota a')
            ->join('m_gudang b', 'rnota_asal = b.gdg_id')
            ->join('m_gudang c', 'rnota_asal = c.gdg_id')
            ->where('rnota_id', $id)
            ->get()
            ->row_array();
        $data['detail'] = $this->db->from('d_requestnota_detail')
            ->join('m_produk', "dtrn_prd_id = prd_id")
            ->where('dtrn_nota_id', $id)
            ->order_by('prd_kode')
            ->get()
            ->result_array();
        return $data;
    }

    function sv_barangretur($data, $detail, $rekening)
    {
        if ($data['nota_id'] == '') {
            $this->load->helper('string');
            $data['nota_id'] = date('ymdhis');
            $sql = $this->db->insert('d_nota', $data);
            if ($sql) {
                $barang = '';
                //rincian & stok
                foreach ($detail as $val) {
                    $dtl = array(
                        'dtn_id' => $data['nota_id'] . random_string('alnum', 5),
                        'dtn_prd_id' => $val['id'],
                        'dtn_hargabeli' => $val['hb'],
                        'dtn_jumlah' => $val['jumlah'],
                        'dtn_nota_id' => $data['nota_id'],
                        'dtn_kadaluarsa' => $val['kadaluarsa'],
                        'dtn_nobatch' => $val['nobatch']
                    );
                    $this->db->insert('d_nota_detail', $dtl);
                    $stok = array(
                        'stk_id' => $dtl['dtn_id'],
                        'stk_prd_id' => $val['id'],
                        'stk_nota_id' => $data['nota_id'],
                        'stk_tanggal' => $data['nota_tanggal'],
                        'stk_jam' => $data['nota_jam'],
                        'stk_jumlah' => -1 * $val['jumlah'],
                        'stk_gdg_id' => $data['nota_asal'],
                        'stk_kadaluarsa' => $val['kadaluarsa'],
                        'stk_nobatch' => $val['nobatch']
                    );
                    $this->db->insert('d_stok', $stok);
                    $produk = $this->db->get_where('m_produk', array('prd_id' => $val['id']))->row_array();
                    $barang .= $produk['prd_nama'] . ' ' . $val['jumlah'] . ' ' . $produk['prd_satuan'] . ' , ';
                }

                //kas            
                $gdg = $this->db->get_where('m_gudang', array('gdg_id' => $data['nota_asal']))->row_array();
                $sup = $this->db->get_where('m_supplier', array('sup_id' => $data['nota_tujuan']))->row_array();
                $kas = array(
                    'kas_id' => 'RTR' . $data['nota_id'],
                    'kas_rekening' => $rekening,
                    'kas_uraian' => 'Retur barang ' . $gdg['gdg_nama'] . ' ke ' . $sup['sup_nama'] . ' ' . substr($barang, 0, -2),
                    'kas_keterangan' => NULL,
                    'kas_tanggal' => $data['nota_tanggal'],
                    'kas_jam' => $data['nota_jam'],
                    'kas_debet' => NULL,
                    'kas_kredit' => $data['nota_total'],
                    'kas_jenis' => 'retur'
                );
                $this->db->insert('d_kas', $kas);
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function sspntretur($aColumns, $sWhere, $sLimit, $bulan, $tahun, $gudang)
    {
        $where = "WHERE a.nota_jenis = 'RETUR' AND a.nota_asal = $gudang";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(a.nota_tanggal) = $tahun AND MONTH(a.nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT a.*, d.sup_nama, e.nota_id as nota_retur, (SELECT GROUP_CONCAT(CONCAT(dtn_jumlah, ' ', prd_satuan, ' - ', prd_nama) SEPARATOR '<br>') FROM d_nota_detail b JOIN m_produk c ON b.dtn_prd_id = c.prd_id WHERE b.dtn_nota_id = a.nota_id) AS detail 
            FROM d_nota a
            JOIN m_supplier d ON a.nota_tujuan = d.sup_id 
            LEFT JOIN d_nota e ON a.nota_id = e.nota_ref_id 
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntretur_total($sIndexColumn, $sWhere, $bulan, $tahun, $gudang)
    {
        $where = "WHERE a.nota_jenis = 'RETUR' AND a.nota_asal = $gudang";
        if ($tahun == '' && $bulan == '') {
            $where .= '';
        } else {
            $where .= " AND YEAR(a.nota_tanggal) = $tahun AND MONTH(a.nota_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT a.*, d.sup_nama, e.nota_id as nota_retur FROM d_nota  a
            JOIN m_supplier d ON a.nota_tujuan = d.sup_id 
            LEFT JOIN d_nota e ON a.nota_id = e.nota_ref_id 
            $where 
            ORDER BY nota_tanggal desc, nota_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function get_ntretur($id)
    {
        $data = $this->db->select('d_nota.*, m_supplier.*, m_gudang.gdg_nama as tujuan')
            ->from('d_nota')
            ->join('m_supplier', 'nota_tujuan = sup_id')
            ->join('m_gudang', 'nota_asal = gdg_id')
            ->where('nota_id', $id)
            ->get()
            ->row_array();
        $detail = $this->db->from('d_nota_detail')
            ->join('m_produk', 'dtn_prd_id = prd_id')
            ->where('dtn_nota_id', $id)
            ->order_by('prd_kode')
            ->get()
            ->result_array();
        $data['detail'] = $detail;
        $data['status'] = FALSE;
        $data['retur_tanggal'] = NULL;
        $data['retur_jam'] = NULL;
        $cek = $this->db->get_where('d_nota', array('nota_ref_id' => $id));
        if ($cek->num_rows() == 1) {
            $data['status'] = TRUE;
            $dt = $cek->row_array();
            $data['retur_tanggal'] = $dt['nota_tanggal'];
            $data['retur_jam'] = $dt['nota_jam'];
        }
        return $data;
    }

    function del_retur($id)
    {
        $q = $this->db->where('nota_id', $id)
            ->delete('d_nota');
        if ($q) {
            $this->db->where('dtn_nota_id', $id)->delete('d_nota_detail');
            $this->db->where('stk_nota_id', $id)->delete('d_stok');
            $this->db->where('kas_id', 'RTR' . $id)->delete('d_kas');
            //retur back
            $rb = $this->db->get_where('d_nota', array('nota_ref_id' => $id));
            if ($rb->num_rows() == 1) {
                $dtrb = $rb->row_array();
                $idrb = $dtrb['nota_id'];
                $this->db->where('nota_id', $idrb)->delete('d_nota');
                $this->db->where('dtn_nota_id', $idrb)->delete('d_nota_detail');
                $this->db->where('stk_nota_id', $idrb)->delete('d_stok');
                $this->db->where('kas_id', 'RTK' . $idrb)->delete('d_kas');
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function sv_barangreturkembali($data, $detail, $rekening)
    {
        if ($data['nota_id'] == '') {
            $this->load->helper('string');
            $data['nota_id'] = date('ymdhis');
            $sql = $this->db->insert('d_nota', $data);
            if ($sql) {
                $barang = '';
                //rincian & stok
                foreach ($detail as $val) {
                    $dtl = array(
                        'dtn_id' => $data['nota_id'] . random_string('alnum', 5),
                        'dtn_prd_id' => $val['id'],
                        'dtn_hargabeli' => $val['hb'],
                        'dtn_jumlah' => $val['jumlah'],
                        'dtn_nota_id' => $data['nota_id']
                    );
                    $this->db->insert('d_nota_detail', $dtl);
                    $stok = array(
                        'stk_id' => $dtl['dtn_id'],
                        'stk_prd_id' => $val['id'],
                        'stk_nota_id' => $data['nota_id'],
                        'stk_tanggal' => $data['nota_tanggal'],
                        'stk_jam' => $data['nota_jam'],
                        'stk_jumlah' => $val['jumlah'],
                        'stk_gdg_id' => $data['nota_tujuan']
                    );
                    $this->db->insert('d_stok', $stok);
                    $produk = $this->db->get_where('m_produk', array('prd_id' => $val['id']))->row_array();
                    $barang .= $produk['prd_nama'] . ' ' . $val['jumlah'] . ' ' . $produk['prd_satuan'] . ' , ';
                }

                //kas            
                $sup = $this->db->get_where('m_supplier', array('sup_id' => $data['nota_asal']))->row_array();
                $gdg = $this->db->get_where('m_gudang', array('gdg_id' => $data['nota_tujuan']))->row_array();
                $kas = array(
                    'kas_id' => 'RTK' . $data['nota_id'],
                    'kas_rekening' => $rekening,
                    'kas_uraian' => 'Konfirmasi Retur barang kembali ke ' . $gdg['gdg_nama'] . ' dari ' . $sup['sup_nama'] . ' ' . substr($barang, 0, -2),
                    'kas_keterangan' => NULL,
                    'kas_tanggal' => $data['nota_tanggal'],
                    'kas_jam' => $data['nota_jam'],
                    'kas_debet' => $data['nota_total'],
                    'kas_kredit' => NULL,
                    'kas_jenis' => 'retur'
                );
                $this->db->insert('d_kas', $kas);
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}
