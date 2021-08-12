<html>

<head>
    <title><?= $info['perusahaan'] ?></title>
</head>
<style>
    .table-f,
    .table-f th,
    .table-f td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .table-f thead,
    .table-f tfoot {
        background: #b5bbc8;
        font-weight: bold;
    }

    .table-c,
    .table-c th,
    .table-c td {
        border: none;
    }

    html {
        margin: 15px 20px 50px;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-bold {
        font-weight: bold;
    }
</style>

<body>
    <div class="text-right">
        <img src="<?= config_item('asset') . 'logo.png' ?>" height="80px" />
    </div>
    <h5 style="text-align: center">LAPORAN PENJUALAN</h5>
    <h6 style="text-align: center">Periode : <?= $tanggal ?><br>Admin/sales : <?= $admin ?></h6>
    <table class="table-f" style="font-size: 10px; margin-left: auto; margin-right: auto; width: 100%">
        <thead class="bg-gray-active">
            <tr>
                <th class="text-center" style="width: 3%">No.</th>
                <th class="text-center" style="width: 7%">Tanggal</th>
                <th class="text-center" style="width: 13%">Customer</th>
                <th class="text-center" style="width: 13%">No. Nota</th>
                <th class="text-center" style="width: 15%">Nama Barang</th>
                <th class="text-center" style="width: 10%">Jumlah</th>
                <th class="text-center" style="width: 10%">Harga Jual</th>
                <th class="text-center" style="width: 15%">Total</th>
                <th class="text-center" style="width: 10%">Status Pembayaran</th>
            </tr>
        </thead>
        <tbody><?= $tbody ?></tbody>
        <tfoot class="bg-gray-active"><?= $tfoot ?></tfoot>
    </table>
</body>

</html>