<div class="content-wrapper">
    <section class="content-header">
        <h1>Detail Barang (Rekapitulasi Customer) <small>Menu untuk melihat daftar penjualan per barang</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Laporan</a></li>
            <li class="active">Detail Barang</li>
        </ol>
    </section>
    <section class="content">
        <div class="row" style="margin-bottom: 10px">
            <form id="form-filter">
                <label class="col-md-1 col-xs-2" style="padding-top: 6px">Filter Data</label>
                <div class="col-md-3 col-xs-4">
                    <input type="hidden" id="id" name="id" class="form-control" value="">
                    <input type="text" autocomplete="off" id="produk" value="" class="form-control"> 
                </div>
                <div class="col-md-2 col-xs-2">
                    <input id="tanggal" class="form-control text-center" name="tanggal" type="text" value="" readonly="" placeholder="Periode">
                </div>
                <div class="col-md-2 col-xs-2">
                    <button id="btn-cari" class="btn btn-primary" type="submit" style="width: 100%"><i id="loader" class="fa fa-search"></i> Cari data</button>
                </div>
                <div class="col-md-2 col-xs-2">
                    <button id="btn-pdf" class="btn btn-danger" type="button" style="width: 45%"><i class="fa fa-file-pdf-o"></i> PDF</button>
                </div>
            </form>
        </div>
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table id="div-detail" class="table" style="font-size: 17px; font-weight: bold; margin-bottom: 10px;">
                            <tbody></tbody>
                        </table>
                        <table id="dt-table" class="table table-bordered" style="margin-bottom: 0">
                            <thead class="bg-gray-active">
                                <tr>
                                    <th class="text-center" style="width: 3%">No</th>
                                    <th class="text-center" style="width: 40%">Nama Barang</th>
                                    <th class="text-center" style="width: 7%">Tanggal</th>
                                    <th class="text-center" style="width: 10%">Barang Terjual</th>
                                    <th class="text-center" style="width: 20%">Customer</th>
                                    <th class="text-center" style="width: 20%">Total Pendapatan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center"><i class="fa fa-spin fa-spinner"></i> Silahkan tekan tombol 'Cari data' terlebih dahulu...</td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="div-rekap" class="table" style="font-size: 17px; font-weight: bold; margin-top: 10px;">
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
        $('#produk').typeahead({
            minLength: 3,
            limit: 10,
            displayField: "content",
            source: function (query, process) {
                mp = [];
                $.ajax({
                    url: '<?= base_url('Laporan/cari_') ?>',
                    type: 'POST',
                    dataType: 'JSON', data: 'word=' + query,
                    success: function (data) {
                        var dt = data.map(function (prd) {
                            mp[prd.content] = {};
                            mp[prd.content].id = prd.id;
                            mp[prd.content].content = prd.content;
                            return prd.content;
                        });
                        process(dt);
                    }
                });
            },
            updater: function (produk) {
                $('#id').val(mp[produk].id);
                return mp[produk].content;
            }
        });
        $('#tanggal').daterangepicker({
            format: 'DD/MM/YYYY'
        });
        $('#form-filter').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-cari').click(function () {
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-filter'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Laporan/penjualanprodukfilter_') ?>',
                type: 'POST',
                data: dt,
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    b.attr('disabled', 'disabled');
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function (result) {
                    $('#div-detail').find('tbody').html(result.detail);
                    $('#dt-table').find('tbody').html(result.tbody);
                    $('#div-rekap').find('tbody').html(result.rekap);
                }
            }).always(function () {
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            });
        });
        $('#btn-pdf').click(function () {
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            var form = $('#form-filter'), dt = form.serialize();
            window.open('<?= base_url('Laporan/penjualanprodukpdf_') ?>?' + dt, '_blank');
            i.removeClass().addClass(cls);
        });
    });
</script>