<div class="content-wrapper">
    <section class="content-header">
        <h1>Stok Barang <small>Menu untuk melihat stok barang</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Logistik</a></li>
            <li class="active">Stok Barang</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="row <?= ($level == 4) ? 'hidden' : '' ?>" style="margin-bottom: 10px">
                            <div class="col-md-3 col-xs-4 pull-right">
                                <select class="form-control filter" id="gudang">
                                    <?= $optgudang ?>
                                </select> 
                            </div>
                            <label class="col-md-2 col-xs-4 pull-right text-right" style="padding-top: 6px">Filter Data</label>
                        </div>
                        <table id="dt-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Kode</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Satuan</th>
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
<script type="text/javascript">
    $(document).ready(function () {
        $('#gudang').change(function () {
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Stok/ssplist_') ?>/' + $(this).val(),
                "bLengthChange": true,
                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "columns": [{"width": "5%", "sClass": "text-center"}, {"width": "15%", "sClass": "text-center"}, {"width": "55%"}, {"width": "20%", "sClass": "text-right"}, {"width": "5%"}]
            });
        });
        $('#gudang').trigger('change');
    });
</script>