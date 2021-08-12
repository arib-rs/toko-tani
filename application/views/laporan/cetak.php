<!DOCTYPE html>
<html>

<head>
    <title><?= $info['perusahaan'] ?> | CETAK</title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="<?= config_item('asset') . 'is.ico' ?>">
    <link rel="stylesheet" href="<?= config_item('css') . 'bootstrap.min.css' ?>">
    <?php
    // if ($halnota > 1) {
    //     if ($bagian == '') {
    //         for ($l = $halnota - 1; $l > 0; $l--) {
    ?>
    <!-- <script type="text/javascript">
                    window.open("<?= base_url() ?>penjualan/cetak_extended/<?= $idcetak ?>?p=<?= $l ?>");
                </script> -->
    <?php
    //         }
    //     }
    // }
    ?>
    <script type="text/javascript">
        window.print();
    </script>


</head>

<body style="font-size: 12px;margin:0; padding:0;">
    <?php

    // $cproduk = count($data['detail']);
    // $halnota = ceil($cproduk / 10);
    // $i = $bagian != '' ? $bagian : 0;
    // $no = ($i * 10) + 1;
    $no = 1;
    for ($i = 0; $i < $halnota; $i++) {
        $halaman = $halnota == 1 ? '' : "(" . ($i + 1) . "/" . $halnota . ")";
        ?>
        <!-- height Letter = 27.9 cm -->
        <div class="container-fluid" style="margin:0; padding-top: 20px; padding-right:20px;font-size:10px;height:13.971652838389cm">
            <div class="col-md-8 col-xs-8" style="line-height:1.1;">
                <table style="margin-top: 0px; margin-bottom:2px;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="height:60px; width:65px;padding-right:5px;">
                                <img style="width:100%;height:100%;" src="<?= base_url() ?>/assets/logoonly.png" alt="">
                            </td>
                            <td>
                                <h4 style="margin: 0;font-weight: bold;"><?= $info['perusahaan'] ?></h4>

                                <p style="margin-bottom: 0"><?= $info['deskripsi'] ?></p>
                                <p style="margin-bottom: 0"><?= $info['alamat'] . ', ' . $info['kecamatan'] . ', ' . $info['kabupaten'] ?></p>
                                <p style="margin-bottom: 0"><?= 'Telp. ' . $info['hp'] . ', ' . $info['telepon'] ?> </p>
                                <p style="margin-bottom: 0"><?= 'Email: ' ?> tokotanisamarinda@gmail.com</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table style="margin-top: 0px; margin-bottom:0px;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 10%">Dicetak</td>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 88%"><?= $data['nota_cb'] . ' - ' . date('d/m/Y', strtotime($data['nota_tanggal'])) . ' ' . $data['nota_jam'] ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%">No Nota</td>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 83%"><?= $data['nota_no'] . $halaman ?></td>
                        </tr>
                        <tr>
                            <td style="width: 20%">Tgl. Jatuh Tempo</td>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 78%"><?= date('d/m/Y', strtotime('+30 days', strtotime($data['nota_tanggal']))); ?></td>

                        </tr>
                        <!-- <tr>
                        <td>Jt.Tempo</td>
                        <td>: </td>
                    </tr> -->
                        <!-- <tr>
                        <td>Gudang</td>
                        <td>: <?= $data['asal'] ?></td>
                    </tr> -->
                    </tbody>
                </table>

            </div>
            <div class="col-md-4 col-xs-4" style="line-height:1.1;">
                <!-- <h3 style="margin: 0;">
                <span style="font-style: italic">FAKTUR PENJUALAN</span>
                <span class="pull-right" style="font-weight: bold;"><?= 'FJ-' . $data['nota_id'] ?></span>
            </h3> -->

                <p style="margin-bottom: 0;"><?= $info['kabupaten'] . ', ' . date('d-m-Y', strtotime($data['nota_tanggal'])) ?></p>

                <p style="margin-bottom: 0; margin-top:25px;">Kepada Yth.</p>
                <?php
                    if ($data['nota_tujuan'] > 0) {
                        $pemilik = ($data['cus_iskios']) ? ' - ' . $data['cus_pemilik'] : '';
                        ?>
                    <p style="margin-bottom: 0"><?= $data['cus_nama'] . $pemilik; ?></p>
                    <p style="margin-bottom: 0"><?= $data['cus_alamat'] ?></p>
                    <p style="margin-bottom: 5px"><?= $data['cus_telp'] ?></p>
                <?php } else { ?>
                    <p>Customer Umum</p>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 col-xs-12">
                <h6 class="text-center" style="margin:0 0 2px 0;font-weight:bold">
                    NOTA PENJUALAN
                </h6>
            </div>
            <div class="col-md-12 col-xs-12">
                <table class="" style="margin-bottom: 0px;line-height:1.1">
                    <thead style="border-top: 1px solid #000;">
                        <tr style="margin:0px">
                            <th style="width: 0%; border-bottom: 1px solid #000; padding:0;">NO</th>
                            <th class="text-center" style="width: 3%; border-bottom: 1px solid #000;padding:0;">CEK</th>
                            <!-- <th style="width: 10%; border-bottom: 1px solid #000;">Kode</th> -->
                            <th class="text-center" style="width: 8%; border-bottom: 1px solid #000;padding:0;">QTY</th>
                            <th class="text-center" style="width: 46%; border-bottom: 1px solid #000;padding:0;">NAMA BARANG</th>
                            <!-- <th style="width: 5%; border-bottom: 1px solid #000;">Satuan</th> -->
                            <th class="text-center" style="width: 20%; border-bottom: 1px solid #000;padding:0;">HARGA @</th>
                            <th class="text-center" style="width: 23%; border-bottom: 1px solid #000;padding:0;">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $jstart = ($i * 10) + 0;
                            $jend = ($i + 1) * 10;
                            for ($j = $jstart; $j < $jend; $j++) {
                                $val = $data['detail'][$j];
                                $jumlah = ($val['dtn_jumlah'] == '') ? '' : $val['dtn_jumlah'] * $val['dtn_hargajual'];
                                if (isset($val['prd_kode'])) {
                                    ?>
                                <tr>
                                    <td style="padding:0;"><?= $no ?></td>
                                    <td class="text-center" style="padding:0;">
                                        <input type="checkbox">
                                    </td>
                                    <!-- <td><?= $val['prd_kode'] ?></td> -->
                                    <td style="padding:0;"><?= $this->ascfunc->nf_($val['dtn_jumlah']) . ' ' . $val['prd_satuan'] ?></td>
                                    <td style="padding:0;"><?= $val['prd_nama'] ?></td>
                                    <td style="padding:0;" class="text-right">Rp. <?= $this->ascfunc->nf_($val['dtn_hargajual']) ?></td>
                                    <td style="padding:0;" class="text-right">Rp. <?= $this->ascfunc->nf_($jumlah) ?></td>
                                </tr>
                            <?php
                                        $no++;
                                    } else {
                                        ?>
                                <tr>
                                    <td style="height:19.6px"></td>
                                    <td></td>

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                        <?php
                                }
                            }

                            // foreach ($data['detail'] as $val) {
                            //     $jumlah = ($val['dtn_jumlah'] == '') ? '' : $val['dtn_jumlah'] * $val['dtn_hargajual'];
                            ?>
                        <!-- <tr>
                            <td style="padding:0;"><?= $no ?></td>
                                <td class="text-center" style="padding:0;">
                                    <input type="checkbox">
                                </td>
                                <td><?= $val['prd_kode'] ?></td> *commented section
                                <td style="padding:0;"><?= $this->ascfunc->nf_($val['dtn_jumlah']) . ' ' . $val['prd_satuan'] ?></td>
                                <td style="padding:0;"><?= $val['prd_nama'] ?></td>
                                <td style="padding:0;" class="text-right">Rp. <?= $this->ascfunc->nf_($val['dtn_hargajual']) ?></td>
                                <td style="padding:0;" class="text-right">Rp. <?= $this->ascfunc->nf_($jumlah) ?></td>
                            </tr> -->
                        <?php
                            //     $no++;
                            // }
                            ?>

                    </tbody>
                    <tfoot style="line-height:1.3">
                        <tr style="border-top: 1px solid #000;">
                            <td colspan="4" style="padding:0; height:31px"><?= "Terbilang : <span>\"# " . $data['terbilang'] . " Rupiah #\"</span>"; ?></td>
                            <td class="text-right" style="padding:0;">TOTAL</td>
                            <td class="text-right" style="padding:0;">Rp. <?= $this->ascfunc->nf_($data['total']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="5" style="padding:0;">Diskon</td>
                            <td class="text-right" style="padding:0;">Rp. <?= $this->ascfunc->nf_($data['nota_diskon']) ?></td>
                        </tr>
                        <!--                        <tr>
                            <td class="text-right" colspan="5">PPN(<?= $data['nota_ppn'] . '%' ?>) : Rp.</td>
                            <td class="text-right"><?= $this->ascfunc->nf_($data['ppn']) ?></td>
                        </tr>                    -->
                        <tr>
                            <td colspan="4"></td>
                            <td class="text-right" style="padding:0;">GRAND TOTAL</td>
                            <td class="text-right" style="border-top: 1px solid #000;padding:0;">Rp. <?= $this->ascfunc->nf_($data['grandtotal']) ?></td>
                        </tr>
                        <?php if ($data['nota_iskredit']) { ?>
                            <tr>
                                <td colspan="5" class="text-right" style="padding:0;">Down Payment</td>
                                <td class="text-right" style="padding:0;">Rp.<?= $data['dp'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right" style="padding:0;">Sisa Piutang</td>
                                <td class="text-right" style="border-top: 1px solid #000;padding:0;">Rp. <?= $data['sisa'] ?></td>
                            </tr>
                        <?php } ?>
                    </tfoot>
                </table>
            </div>
            <!-- <div class="clearfix"></div> -->
            <div class="col-md-12 col-xs-12" style=" margin-top:-30px;min-height:110px">
                <div class="col-md-2 col-xs-2 text-center">
                    Penerima<br><br><br>
                    <?= ($data['nota_tujuan'] > 0) ? $data['cus_nama'] . '<br>' . substr($pemilik, 3) : ''; ?>
                </div>
                <!-- <div class="col-md-4 col-xs-4 text-center">
            Dibuat Oleh<br><br><br><br>
            <?= $data['nota_cb'] ?>
        </div> -->
                <!-- <div class="col-md-4 col-xs-4 text-center">
            Hormat Kami<br><br><br><br>
            <?= $data['nota_pj'] ?>
        </div> -->
                <div class="col-md-2 col-xs-2 text-center">
                    Hormat Kami<br><br><br>
                    RANA RAHMI
                    <!-- <? //= //$data['nota_cb'] 
                                ?> -->
                </div>
                <div class="col-md-4 col-xs-4 text-center">
                    <p align="left"><?= $info['perusahaan'] ?><br>
                        <? //= "No. Rek : " . $rek[0]['rek_nomor'] 
                            ?>
                        Bank Mandiri: 148500408888<br>
                        Bank Kaltim: 129183891<br>
                        Bank BCA: 2700423410</p>
                </div>
                <div class="col-md-4"></div>

            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 col-xs-12" style="">
                *) BARANG YANG SUDAH DIBELI TIDAK DAPAT DI TUKAR/DIKEMBALIKAN.
            </div>
        </div>
    <?php
    }
    ?>

</body>

</html>