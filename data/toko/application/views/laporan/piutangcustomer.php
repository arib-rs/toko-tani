<div class="content-wrapper">
    <section class="content-header">
        <h1>Laporan Piutang <small>Menu untuk melihat daftar piutang dari customer</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Customer</a></li>
            <li class="active">Piutang</li>
        </ol>
    </section>
    <section class="content">
        <div class="row" style="margin-bottom: 10px">
            <form id="form-filter">
                <div class="col-md-2 col-xs-2">
                    <input type="text" class="form-control text-center" id="tahun" value="<?= date('Y') ?>" readonly="">
                </div>
                <div class="col-md-1 col-xs-2" style="padding-top: 6px">
                    <i id="loader"></i>
                </div>
            </form>
        </div>
        <div class="box">
            <div class="box-body">
                <table id="dt-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="bg-gray-active">
                        <tr>
                            <th style="width: 3%" class="text-center">No</th>
                            <th style="width: 30%" class="text-center">Customer</th>
                            <th style="width: 50%" class="text-center">Detail</th>
                            <th style="width: 17%" class="text-center">Total Piutang</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#tahun').datepicker({
            autoclose: true,
            startView: 'years',
            minViewMode: 'years',
            format: 'yyyy'
        }).on('changeDate', function () {
            var tStart = Date.now();
            var i = $('i#loader'), cls = i.attr('class');
            $.ajax({
                url: '<?= base_url('Piutang/filter_') ?>/' + $(this).val(),
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                },
                success: function (result) {
                    reloadTable('#dt-list', result.tbody);
                }
            }).always(function () {
                i.removeClass().addClass(cls);
            });
            $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
        });
        $('#tahun').trigger('changeDate');
    });
</script>