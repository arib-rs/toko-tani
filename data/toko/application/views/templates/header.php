<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript">
            var timerStart = Date.now();
        </script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= $info['perusahaan'] ?></title>
        <link rel="shortcut icon" href="<?= config_item('asset') . 'is.ico' ?>">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?= config_item('css') . 'bootstrap.min.css' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'font-awesome.min.css' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'ionicons.min.css' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'font.css' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'AdminLTE.min.css' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'skins/skin-green-light.min.css' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'toastr.min.css' ?>">
        <link rel="stylesheet" href="<?= config_item('css') . 'dataTables.bootstrap.min.css' ?>">
        <?php
        if (isset($css)) {
            foreach ($css as $csshref) {
                echo '<link rel="stylesheet" href="' . base_url($csshref) . '">';
            }
        }
        ?>

        <script src="<?= config_item('js') . 'jQuery-2.1.4.min.js' ?>"></script>
        <script src="<?= config_item('js') . 'bootstrap.min.js' ?>"></script>
        <script src="<?= config_item('js') . 'jquery.slimscroll.min.js' ?>"></script>
        <script src="<?= config_item('js') . 'fastclick.min.js' ?>"></script>
        <script src="<?= config_item('js') . 'app.min.js' ?>"></script>
        <script src="<?= config_item('js') . 'toastr.min.js' ?>"></script>
        <script src="<?= config_item('js') . 'bootbox.js' ?>"></script>
        <script src="<?= config_item('js') . 'jquery.dataTables.min.js' ?>"></script>
        <script src="<?= config_item('js') . 'dataTables.bootstrap.min.js' ?>"></script>
        <?php
        if (isset($js)) {
            foreach ($js as $jshref) {
                echo '<script src="' . base_url($jshref) . '"></script>';
            }
        }
        ?>
        <script>
            var toastrMsg = function (type, text, title) {
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "500",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr[type](text, title);
            };

            var setTable = function (a) {
                var table = $(a).DataTable({
                    'bSort': false,
                    'bFilter': true,
                    'bLengthChange': true,
                    'aLengthMenu': [[10, 30, 50, -1], [10, 30, 50, "All"]],
                    'iDisplayLength': 10,
                    'bAutoWidth': false
                });
                return table;
            };

            var reloadTable = function (a, html) {
                $(a).dataTable().fnDestroy();
                $(a).find('tbody').html(html);
                var table = $(a).DataTable({
                    'bSort': false,
                    'bFilter': true,
                    'bLengthChange': true,
                    'aLengthMenu': [[10, 30, 50, -1], [10, 30, 50, "All"]],
                    'iDisplayLength': 10,
                    'bAutoWidth': false
                });
                return table;
            };
        </script>
        <script>
            $(document).ready(function () {
                if (!$('#<?= $active ?>').parent().hasClass('sidebar-menu')) {
                    $('#<?= $active ?>').parent().parent().addClass('active');
                }
                $('#<?= $active ?>').addClass('active');
            });
        </script>
    </head>
    <!--sidebar-collapse-->
    <body class="hold-transition skin-green-light sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <header class="main-header">
                <a href="#" class="logo">
                    <span class="logo-mini" style="background-color: #f9fafc;"><img src="<?= config_item('asset') . 'is.ico' ?>" class="img-circle" alt="icon"></span>
                    <span class="logo-lg"><b><?= $info['perusahaan'] ?></b></span>
                </a>
                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown user user-menu">
                                <a id="logout-dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span><?= $nama ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img src="<?= config_item('asset') . 'avatar/default.jpg' ?>" class="img-circle" alt="User Image">
                                        <p>
                                            <small><?= $nama ?></small>
                                            <?= $info['perusahaan'] ?>
                                        </p>
                                    </li>
                                    <li class="user-footer">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-6">
                                                <!--<a href="<?= base_url('profil') ?>" class="btn btn-primary btn-flat"><i class="fa fa-user"></i> Profil</a>-->
                                            </div>
                                            <div class="col-md-6 col-xs-6">
                                                <a href="<?= base_url('login/logout') ?>" class="btn btn-default btn-flat pull-right">Logout</a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <aside class="main-sidebar">
                <section class="sidebar">         
                    <ul class="sidebar-menu">
                        <?php if ($level == 0) { ?>
                            <li id="info-menu" class="treeview">
                                <a href="<?= base_url('info') ?>">
                                    <i class="fa fa-info-circle"></i> <span>Info System</span>
                                </a>
                            </li>
                            <li id="customer-menu" class="treeview">
                                <a href="<?= base_url('user') ?>">
                                    <i class="fa fa-user"></i> <span>User</span>
                                </a>
                            </li>
                        <?php } if ($level == 1) { ?>
                            <li id="dashboard-menu" class="treeview">
                                <a href="<?= base_url('dashboard') ?>">
                                    <i class="fa fa-dashcube"></i> <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-cube"></i>
                                    <span>Transaksi</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="barangmasuk-menu"><a href="<?= base_url('barangmasuk') ?>"><i class="fa fa-random"></i> Pembelian</a></li>
                                    <li id="pindahbarang-menu"><a href="<?= base_url('pindahbarang') ?>"><i class="fa fa-exchange"></i> Pindah Barang</a></li>
                                    <li id="penjualan-menu"><a href="<?= base_url('penjualan') ?>"><i class="fa fa-calculator"></i> Penjualan</a></li>
                                    <li id="barangretur-menu"><a href="<?= base_url('barangretur') ?>"><i class="fa fa-truck"></i> Retur</a></li>
                                    <li id="harga-menu"><a href="<?= base_url('produkharga') ?>"><i class="fa fa-signal"></i> Setting Harga</a></li>
                                </ul>
                            </li>
                            <li id="stok-menu" class="treeview">
                                <a href="<?= base_url('stok') ?>">
                                    <i class="fa fa-cubes"></i> <span>Stok</span>
                                </a>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-book"></i>
                                    <span>Laporan</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="kas-menu"><a href="<?= base_url('kas') ?>"><i class="fa fa-circle-o"></i> Kas Bulanan</a></li>
                                    <li id="tutupbuku-menu"><a href="<?= base_url('tutupbuku') ?>"><i class="fa fa-circle-o"></i> Kas Tutup Buku</a></li>
                                    <li id="lapoperasional-menu"><a href="<?= base_url('laporan/operasional') ?>"><i class="fa fa-circle-o"></i> Operasional</a></li>
                                    <li id="lapopname-menu"><a href="<?= base_url('laporan/opname') ?>"><i class="fa fa-circle-o"></i> Opname</a></li>
                                    <!--<li id="lappembelian-menu"><a href="<?= base_url('laporan/pembelian') ?>"><i class="fa fa-circle-o"></i> Pembelian</a></li>-->
                                    <li id="lappenjualan-menu"><a href="<?= base_url('laporan/penjualan') ?>"><i class="fa fa-circle-o"></i> Penjualan (Tunai)</a></li>
                                    <li id="lapppn-menu"><a href="<?= base_url('laporan/ppn') ?>"><i class="fa fa-circle-o"></i> Barang PPN/Non-PPN</a></li>
                                    <li id="lappenjualanproduk-menu"><a href="<?= base_url('laporan/penjualanproduk') ?>"><i class="fa fa-circle-o"></i> Detail Barang</a></li>
                                    <li id="hutang-menu"><a href="<?= base_url('hutang') ?>"><i class="fa fa-circle-o"></i> Hutang ke Supplier</a></li>
                                    <li id="lappiutang-menu"><a href="<?= base_url('piutang') ?>"><i class="fa fa-circle-o"></i> Piutang Customer</a></li>
                                </ul>
                            </li>
                            <li id="master-menu" class="treeview">
                                <a href="#">
                                    <i class="fa fa-database"></i>
                                    <span>Master</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="daerah-menu"><a href="<?= base_url('daerah') ?>"><i class="fa fa-folder"></i> Daerah</a></li>
                                    <li id="supplier-menu"><a href="<?= base_url('supplier') ?>"><i class="fa fa-folder"></i> Supplier</a></li>
                                    <li id="kategori-menu"><a href="<?= base_url('kategori') ?>"><i class="fa fa-folder"></i> Kategori Produk</a></li>
                                    <li id="produk-menu"><a href="<?= base_url('produk') ?>"><i class="fa fa-folder"></i> Produk</a></li>
                                    <li id="gudang-menu"><a href="<?= base_url('gudang') ?>"><i class="fa fa-folder"></i> Gudang</a></li>
                                    <li id="armada-menu"><a href="<?= base_url('armada') ?>"><i class="fa fa-folder"></i> Armada</a></li>
                                    <li id="customer-menu"><a href="<?= base_url('customer') ?>"><i class="fa fa-folder"></i> Customer</a></li>
                                </ul>
                            </li>
                        <?php } elseif ($level == 2) { ?>
                            <li id="dashboard-menu" class="treeview">
                                <a href="<?= base_url('dashboard') ?>">
                                    <i class="fa fa-dashcube"></i> <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-book"></i>
                                    <span>Laporan</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="kas-menu"><a href="<?= base_url('kas') ?>"><i class="fa fa-circle-o"></i> Kas Bulanan</a></li>
                                    <li id="tutupbuku-menu"><a href="<?= base_url('tutupbuku') ?>"><i class="fa fa-circle-o"></i> Kas Tutup Buku</a></li>
                                    <li id="lapoperasional-menu"><a href="<?= base_url('laporan/operasional') ?>"><i class="fa fa-circle-o"></i> Operasional</a></li>
                                    <li id="lapopname-menu"><a href="<?= base_url('laporan/opname') ?>"><i class="fa fa-circle-o"></i> Opname</a></li>
                                    <li id="lappenjualan-menu"><a href="<?= base_url('laporan/penjualan') ?>"><i class="fa fa-circle-o"></i> Penjualan (Tunai)</a></li>
                                    <li id="lappenjualanproduk-menu"><a href="<?= base_url('laporan/penjualanproduk') ?>"><i class="fa fa-circle-o"></i> Detail Barang</a></li>                                    
                                </ul>
                            </li>
                        <?php } elseif ($level == 3) { ?>
                            <li id="dashboard-menu" class="treeview">
                                <a href="<?= base_url('dashboard') ?>">
                                    <i class="fa fa-dashcube"></i> <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-cube"></i>
                                    <span>Transaksi</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="barangmasuk-menu"><a href="<?= base_url('barangmasuk/keu') ?>"><i class="fa fa-random"></i> Pembelian</a></li>
                                    <li id="pindahbarang-menu"><a href="<?= base_url('pindahbarang/keu') ?>"><i class="fa fa-exchange"></i> Pindah Barang</a></li>
                                    <li id="penjualan-menu"><a href="<?= base_url('penjualan') ?>"><i class="fa fa-calculator"></i> Penjualan</a></li>
                                    <li id="barangretur-menu"><a href="<?= base_url('barangretur/keu') ?>"><i class="fa fa-truck"></i> Retur</a></li>
                                    <li id="harga-menu"><a href="<?= base_url('produkharga') ?>"><i class="fa fa-signal"></i> Setting harga</a></li>
                                </ul>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-briefcase"></i>
                                    <span>Keuangan</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="saldo-menu"><a href="<?= base_url('saldo') ?>"><i class="fa fa-circle-o"></i> Set Saldo</a></li>
                                    <li id="pembayaran-menu"><a href="<?= base_url('pembayaran') ?>"><i class="fa fa-circle-o"></i> Pembayaran Hutang</a></li>
                                    <li id="piutang-menu"><a href="<?= base_url('pembayaran/piutang') ?>"><i class="fa fa-circle-o"></i> Pembayaran Piutang</a></li>
                                    <li id="kasoperasional-menu"><a href="<?= base_url('kas/operasional') ?>"><i class="fa fa-circle-o"></i> Operasional</a></li>
                                </ul>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-book"></i>
                                    <span>Kas</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="kasobm-menu"><a href="<?= base_url('kas/obm') ?>"><i class="fa fa-circle-o"></i> OBM</a></li>
                                    <li id="kasprive-menu"><a href="<?= base_url('kas/prive') ?>"><i class="fa fa-circle-o"></i> Prive</a></li>
                                    <li id="kaskaskecil-menu"><a href="<?= base_url('kas/kaskecil') ?>"><i class="fa fa-circle-o"></i> Kas Kecil</a></li>
                                    <li id="kasnotifikasi-menu"><a href="<?= base_url('kas/notifikasi') ?>"><i class="fa fa-circle-o"></i> Notifikasi</a></li>
                                    <li id="kaslainnya-menu"><a href="<?= base_url('kas/lainnya') ?>"><i class="fa fa-circle-o"></i> Lain-lain</a></li>
                                    <li id="kaspajak-menu"><a href="<?= base_url('kas/pajak') ?>"><i class="fa fa-circle-o"></i> Pajak</a></li>
                                </ul>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-book"></i>
                                    <span>Laporan</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li id="kas-menu"><a href="<?= base_url('kas') ?>"><i class="fa fa-circle-o"></i> Kas Bulanan</a></li>
                                    <li id="tutupbuku-menu"><a href="<?= base_url('tutupbuku') ?>"><i class="fa fa-circle-o"></i> Kas Tutup Buku</a></li>
                                    <li id="lapoperasional-menu"><a href="<?= base_url('laporan/operasional') ?>"><i class="fa fa-circle-o"></i> Operasional</a></li>
                                    <li id="lapopname-menu"><a href="<?= base_url('laporan/opname') ?>"><i class="fa fa-circle-o"></i> Opname</a></li>
                                    <li id="lappenjualan-menu"><a href="<?= base_url('laporan/penjualan') ?>"><i class="fa fa-circle-o"></i> Penjualan (Tunai)</a></li>
                                    <li id="lappenjualanproduk-menu"><a href="<?= base_url('laporan/penjualanproduk') ?>"><i class="fa fa-circle-o"></i> Detail Barang</a></li>
                                    <li id="hutang-menu"><a href="<?= base_url('hutang') ?>"><i class="fa fa-circle-o"></i> Hutang ke Supplier</a></li>
                                    <li id="lappiutang-menu"><a href="<?= base_url('piutang') ?>"><i class="fa fa-circle-o"></i> Piutang Customer</a></li>
                                </ul>
                            </li>
                            <li id="customer-menu" class="treeview">
                                <a href="<?= base_url('customer') ?>">
                                    <i class="fa fa-database"></i> <span>Customer</span>
                                </a>
                            </li>
                        <?php } elseif ($level == 4) { ?>
                            <li id="stok-menu" class="treeview">
                                <a href="<?= base_url('stok') ?>">
                                    <i class="fa fa-cubes"></i> <span>Stok</span>
                                </a>
                            </li>
                            <li id="request-menu" class="treeview">
                                <a href="<?= base_url('requestbarang/validasi') ?>">
                                    <i class="fa fa-cube"></i> <span>Request Barang</span>
                                </a>
                            </li>
                            <li id="barangretur-menu" class="treeview">
                                <a href="<?= base_url('barangretur') ?>">
                                    <i class="fa fa-truck"></i> <span>Retur Barang</span>
                                </a>
                            </li>
                        <?php } elseif ($level == 5) { ?>
                            <li id="penjualan-menu" class="treeview">
                                <a href="<?= base_url('penjualan') ?>">
                                    <i class="fa fa-calculator"></i> <span>Penjualan</span>
                                </a>
                            </li>
                            <li id="request-menu" class="treeview">
                                <a href="<?= base_url('requestbarang') ?>">
                                    <i class="fa fa-cube"></i> <span>Request Barang</span>
                                </a>
                            </li>
                            <li id="barangretur-menu" class="treeview">
                                <a href="<?= base_url('barangretur') ?>">
                                    <i class="fa fa-truck"></i> <span>Retur Barang</span>
                                </a>
                            </li>
                            <li id="customer-menu" class="treeview">
                                <a href="<?= base_url('customer') ?>">
                                    <i class="fa fa-database"></i> <span>Customer</span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </section>
            </aside>

