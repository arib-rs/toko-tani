<div class="content-wrapper">
    <section class="content-header">
        <h1><span class="pull-left"><a class="text-black" href="<?= base_url('stok') ?>" data-toggle="tooltip" data-placement="right" title="Kembali ke stok barang" style="cursor: pointer"><i class="fa fa-reply"></i></a></span> History Barang <small>Menu untuk melihat history stok setiap barang</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Logistik</a></li>
            <li><a href="#">Stok Barang</a></li>
            <li class="active">History Barang</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Produk : <?= $item_nama ?></h3>
                    </div>
                    <div class="box-body">
                        <input id="barang" type="hidden" value="<?= $item_id ?>">
                        <input id="gudang" type="hidden" value="<?= $gudang_id ?>">
                        <table id="dt-detail" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Penanggung Jawab</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Stok</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    $(document).ready(function () {
        $('#dt-detail').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?= base_url('Stok/ssplistdetail_/' . $item_id . '/' . $gudang_id) ?>',
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "5%", "sClass": "text-center"}, {"width": "45%"}, {"width": "15%"}, {"width": "15%", "sClass": "text-center"}, {"width": "10%", "sClass": "text-center"}, {"width": "10%", "sClass": "text-right"}],
            "order": [[3, 'desc']]
        });
    });
</script>