<div class="content-wrapper">
    <section class="content-header">
        <h1>Rekapitulasi Kas Bulanan</h1>
        <ol class="breadcrumb">
            <li><a href="#">Laporan</a></li>
            <li class="active">Rekap Kas Bulanan</li>
        </ol>
    </section>
    <section class="content">
        <div class="row" style="margin-bottom: 10px">
            <form id="form-filter">
                <label class="col-md-1 col-xs-2" style="padding-top: 6px">No. Rek</label>
                <div class="col-md-6 col-xs-4" style="padding-top: 6px">
                    <?= $cbrek ?>
                </div>
                <div class="col-md-1 col-xs-2">
                    <input id="tanggal" class="form-control text-center" name="tanggal" type="text" value="<?= date('m/Y') ?>" readonly="">
                </div>
                <div class="col-md-2 col-xs-2">
                    <button id="btn-cari" class="btn btn-primary" type="submit" style="width: 100%"><i id="loader" class="fa fa-search"></i> Cari data</button>
                </div>
                <div class="col-md-2 col-xs-2">
                    <button id="btn-excel" class="btn btn-success" type="button" style="width: 50%"><i class="fa fa-file-excel-o"></i> Excel</button>
                    <button id="btn-pdf" class="btn btn-danger" type="button" style="width: 45%"><i class="fa fa-file-pdf-o"></i> PDF</button>
                </div>
            </form>
        </div>
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table id="dt-kuota" class="table table-bordered" style="margin-bottom: 0">
                            <thead class="bg-gray-active">
                                <tr>
                                    <th class="text-center" style="width: 3%">No</th>
                                    <th class="text-center" style="width: 7%">Tanggal</th>
                                    <th class="text-center" style="width: 50%">Uraian</th>
                                    <th class="text-center" style="width: 20%">Debet</th>
                                    <th class="text-center" style="width: 20%">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center"><i class="fa fa-spin fa-spinner"></i> Silahkan tekan tombol 'Cari data' terlebih dahulu...</td>
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
    $(document).ready(function () {
        $('#tanggal').datepicker({
            autoclose: true,
            startView: 'months',
            minViewMode: 'months',
            format: 'mm/yyyy',
            language: 'id'
        });
        $('#form-filter').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-cari').click(function () {
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-filter'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Kas/filterdata_') ?>',
                type: 'POST',
                data: dt,
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    b.attr('disabled', 'disabled');
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function (result) {
                    $('#dt-kuota').find('tbody').html(result.tbody);
                    $('#dt-kuota').find('tfoot').html(result.tfoot);
                }
            }).always(function () {
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            });
        });
        <?php if(isset($_GET['filter'])){ ?>
            var bln = <?= $_GET['filter'] ?>;
            $('#tanggal').val(bln);
            $('#btn-cari').trigger('click');
        <?php } ?>
        $('#btn-excel').click(function () {
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            var form = $('#form-filter'), dt = form.serialize();
            window.open('<?= base_url('Kas/excel_') ?>?' + dt, '_blank');
            i.removeClass().addClass(cls);
        });
        $('#btn-pdf').click(function () {
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            var form = $('#form-filter'), dt = form.serialize();
            window.open('<?= base_url('Kas/pdf_') ?>?' + dt, '_blank');
            i.removeClass().addClass(cls);
        });
    });
</script>