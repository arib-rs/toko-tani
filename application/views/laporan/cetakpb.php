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

                <table style="margin-top: 20px; margin-bottom: 10px; width: 100%;">
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
                <h3 style="margin: 0;"><span style="font-style: italic">SURAT JALAN</span> <span class="pull-right" style="font-weight: bold;"><?= $data['nota_id'] ?></span></h3>
                <br>
                <p style="margin-bottom: 0; margin-top: 35px; font-weight: bold;">TUJUAN</p>
                <p style="margin-bottom: 0;"><?= $data['gdg_nama'] ?></p>
                <p><?= $data['gdg_alamat'] ?></p>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 col-xs-12">
                <table class="table table-responsive" style="margin-bottom: 20px">
                    <thead style="border-top: 1px solid #000;">
                        <tr>
                            <th style="width: 10%; border-bottom: 1px solid #000;">Kode</th>
                            <th style="width: 70%; border-bottom: 1px solid #000;">Nama Barang</th>
                            <th class="text-right" style="width: 10%; border-bottom: 1px solid #000;">Qty</th>
                            <th style="width: 10%; border-bottom: 1px solid #000;">Satuan</th>
                        </tr>
                    </thead>
                    <tbody style="border-bottom: 1px solid #000;">
                        <?php
                        foreach ($data['detail'] as $val) {
                            ?>
                            <tr>
                                <td><?= $val['prd_kode'] ?></td>
                                <td><?= $val['prd_nama'] ?></td>
                                <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_jumlah']) ?></td>
                                <td><?= $val['prd_satuan'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4 col-xs-4 text-center">
                Dibuat Oleh<br><br><br><br>
                (<?= $data['nota_cb'] ?>)
            </div>
            <div class="col-md-4 col-xs-4 text-center">
            </div>
            <div class="col-md-4 col-xs-4 text-center">
                Diketahui Oleh<br><br><br><br>
                (<?= $data['nota_pj'] ?>)
            </div>
        </div>
    </body>
</html>
