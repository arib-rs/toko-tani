<div class="content-wrapper">
    <section class="content-header">
        <h1>Rekapitulasi Penjualan Admin/Sales <small>Menu untuk melihat daftar penjualan per admin</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Laporan</a></li>
            <li class="active">Rekap Penjualan Admin/Sales</li>
        </ol>
    </section>
    <section class="content">
        <div class="row" style="margin-bottom: 10px">
            <form id="form-filter">
                <label class="col-md-1 col-xs-2" style="padding-top: 6px">Filter Data</label>
                <div class="col-md-3 col-xs-4">
                    <select class="form-control" id="admin" name="admin">
                        <?= $optadmin ?>
                    </select>
                </div>
                <div class="col-md-2 col-xs-2">
                    <input id="tanggal" class="form-control text-center" name="tanggal" type="text" value="" readonly="" placeholder="Periode">
                </div>
                <div class="col-md-2 col-xs-2">
                    <button id="btn-cari" class="btn btn-primary" type="submit" style="width: 100%"><i id="loader" class="fa fa-search"></i> Cari data</button>
                </div>
                <div class="col-md-2 col-xs-2">
                    <button id="btn-pdf" class="btn btn-danger" type="button" style="width: 45%"><i class="fa fa-file-pdf-o"></i> PDF</button>
                    <button id="btn-excel" class="btn btn-success" type="button" style="width: 50%"><i class="fa fa-file-excel-o"></i> Excel</button>
                </div>
            </form>
        </div>
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table id="dt-table" class="table table-bordered" style="margin-bottom: 0">
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
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center"><i class="fa fa-spin fa-spinner"></i> Silahkan tekan tombol 'Cari data' terlebih dahulu...</td>
                                </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tanggal').daterangepicker({
            format: 'DD/MM/YYYY'
        });
        $('#form-filter').submit(function(e) {
            e.preventDefault();
        });
        $('#btn-cari').click(function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            var form = $('#form-filter'),
                dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Laporan/adminsalesfilter_') ?>',
                type: 'POST',
                data: dt,
                dataType: 'JSON',
                async: false,
                beforeSend: function() {
                    b.attr('disabled', 'disabled');
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function(result) {
                    $('#dt-table').find('tbody').html(result.tbody);
                    $('#dt-table').find('tfoot').html(result.tfoot);
                }
            }).always(function() {
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            });
        });
        $('#btn-pdf').click(function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            var form = $('#form-filter'),
                dt = form.serialize();
            window.open('<?= base_url('Laporan/adminsalespdf_') ?>?' + dt, '_blank');
            i.removeClass().addClass(cls);
        });
        $('#btn-excel').click(function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            var form = $('#form-filter'),
                dt = form.serialize();
            window.open('<?= base_url('Laporan/adminsalesexcel_') ?>?' + dt, '_blank');
            i.removeClass().addClass(cls);
        });
    });
</script>