<!DOCTYPE html>
<html>
    <head>
        <title><?= $info['perusahaan'] ?> | CETAK</title>
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <link rel="shortcut icon" href="<?= config_item('asset') . 'is.ico' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'bootstrap.min.css' ?>">
        <script type="text/javascript">
            window.print();
        </script>
    </head>

    <body>
        <div class="container-fluid" style="margin-top: 20px">
            <div class="col-md-7 col-xs-7">
                <h4 style="margin: 0;font-weight: bold;"><?= $info['perusahaan'] ?></h4>
                <p style="margin-bottom: 0"><?= $info['alamat'] . '-' . $info['kecamatan'] ?></p>
                <p style="margin-bottom: 0"><?= $info['hp'] . ', ' . $info['telepon'] ?></p>
                <p><?= $info['kabupaten'] ?></p>

                <table style="margin-top: 20px; width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 20%">Tanggal</td>
                            <td style="width: 80%">: <?= date('d/m/Y', strtotime($data['nota_tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <td>Jt.Tempo</td>
                            <td>: </td>
                        </tr>
                        <tr>
                            <td>Gudang</td>
                            <td>: <?= $data['asal'] ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="col-md-5 col-xs-5">
                <h3 style="margin: 0;"><span style="font-style: italic">FAKTUR PENJUALAN</span> <span class="pull-right" style="font-weight: bold;"><?= 'FJ-'.$data['nota_id'] ?></span></h3>
                <br>
                <p style="margin-bottom: 0; margin-top: 35px; font-weight: bold">Customer</p>
                <?php
                if ($data['nota_tujuan'] > 0) {
                    $pemilik = ($data['cus_iskios']) ? ' - ' . $data['cus_pemilik'] : '';
                    ?>
                    <p style="margin-bottom: 0;"><?= $data['cus_nama'] . $pemilik; ?></p>
                    <p style="margin-bottom: 0"><?= $data['cus_alamat'] ?></p>
                    <p><?= $data['cus_telp'] ?></p>
                <?php } else { ?>
                    <p>Umum</p>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 col-xs-12">
                <table class="table table-responsive" style="margin-bottom: 20px">
                    <thead style="border-top: 1px solid #000;">
                        <tr>
                            <th style="width: 10%; border-bottom: 1px solid #000;">Kode</th>
                            <th style="width: 35%; border-bottom: 1px solid #000;">Nama Barang</th>
                            <th class="text-right" style="width: 5%; border-bottom: 1px solid #000;">Qty</th>
                            <th style="width: 5%; border-bottom: 1px solid #000;">Satuan</th>
                            <th class="text-right" style="width: 15%; border-bottom: 1px solid #000;">@Harga</th>
                            <th class="text-right" style="width: 20%; border-bottom: 1px solid #000;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data['detail'] as $val) {
                            $jumlah = ($val['dtn_jumlah'] == '') ? '' : $val['dtn_jumlah'] * $val['dtn_hargajual'];
                            ?>
                            <tr>
                                <td><?= $val['prd_kode'] ?></td>
                                <td><?= $val['prd_nama'] ?></td>
                                <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_jumlah']) ?></td>
                                <td><?= $val['prd_satuan'] ?></td>
                                <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_hargajual']) ?></td>
                                <td class="text-right"><?= $this->ascfunc->nf_($jumlah) ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-right" colspan="5" style="border-top: 1px solid #000;">TOTAL = Rp.</td>
                            <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($data['total']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="5" style="border-top: 1px solid #000;">Diskon = Rp.</td>
                            <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($data['nota_diskon']) ?></td>
                        </tr>
<!--                        <tr>
                            <td class="text-right" colspan="5">PPN(<?= $data['nota_ppn'] . '%' ?>) = Rp.</td>
                            <td class="text-right"><?= $this->ascfunc->nf_($data['ppn']) ?></td>
                        </tr>                    -->
                        <tr>
                            <td class="text-right" colspan="5" style="border-top: 1px solid #000;">GRAND TOTAL = Rp.</td>
                            <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($data['grandtotal']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="border-top: 1px solid #000; font-size: 17px;"><?= 'Terbilang : <span style="font-style: italic;">' . $data['terbilang'] . ' Rupiah</span>'; ?></td>
                        </tr>
                        <?php if ($data['nota_iskredit']) { ?>
                            <tr>
                                <td colspan="5" class="text-right" style="border-top: none">Down Payment  = Rp.</td>
                                <td class="text-right" style="border-top: none"><?= $data['dp'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right" style="border-top: none">Sisa Piutang  = Rp.</td>
                                <td class="text-right" style="border-top: 1px solid #000;"><?= $data['sisa'] ?></td>
                            </tr>
                        <?php } ?>
                    </tfoot>
                </table>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4 col-xs-4 text-center">
                Dibuat Oleh<br><br><br><br>
                <?= $data['nota_cb'] ?>
            </div>
            <div class="col-md-4 col-xs-4 text-center">
                Penerima<br><br><br><br>
                <?= ($data['nota_tujuan'] > 0) ? $data['cus_nama'] . '<br>' . substr($pemilik, 3) : ''; ?>
            </div>
            <div class="col-md-4 col-xs-4 text-center">
                Hormat Kami<br><br><br><br>
                <?= $data['nota_pj'] ?>
            </div>
        </div>
    </body>
</html>
