<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Set Saldo
            <small>Menu untuk melakukan setting saldo pada awal tahun</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#">Keuangan</a></li>
            <li class="active">Set Saldo</li>
        </ol>
    </section>
    <section class="content">
        <div id="tabs" class="row">
            <div class="col-md-4 col-xs-12">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#nota" aria-controls="nota" role="tab" data-toggle="tab"><i class="fa fa-file-o"></i> Form</a></li>
                    <li role="presentation"><a href="#list" aria-controls="list" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i> Daftar</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="nota">
                    <div class="col-md-8 col-xs-12">
                        <form id="form-data">
                            <div class="box">
                                <div class="box-body">
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">Tahun</label>
                                        <div class="col-md-2 col-xs-4">
                                            <input type="text" class="form-control" id="tahun" name="tahun" value="<?= date('Y') ?>" readonly="">                             
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">No. Rekening</label>
                                        <div class="col-md-10 col-xs-9">
                                            <?= $cbrek ?> 
                                        </div>
                                    </div>
                                    <table id="dt-form" class="table table-striped table-bordered">
                                        <thead class="bg-gray-active">
                                            <tr>
                                                <th colspan="2" class="text-center">Saldo Awal</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="width: 25%">Saldo (+)</th>
                                                <th class="text-center" style="width: 25%">Saldo (-)</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12" style="margin-top: 5px">
                                            <button id="btn-save" type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Simpan</button>                                  
                                            <a id="btn-reset" class="btn btn-default pull-right" style="margin-right: 5px">Batal</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>                    
                </div>
                <div role="tabpanel" class="tab-pane" id="list">
                    <div class="col-md-8 col-xs-12">
                        <div class="box">
                            <div class="box-body">
                                <table id="dt-list" class="table table-bordered">
                                    <thead class="bg-gray-active">
                                        <tr>
                                            <th rowspan="2" class="text-center" style="width: 3%; vertical-align: middle">No</th>
                                            <th rowspan="2" class="text-center" style="width: 47%; vertical-align: middle">Tahun</th>
                                            <th colspan="2" class="text-center">Saldo Awal</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="width: 25%">Saldo (+)</th>
                                            <th class="text-center" style="width: 25%">Saldo (-)</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#tabs a[href="#list"]').click(function (e) {
            e.preventDefault();
            var tStart = Date.now();
            $.ajax({
                url: '<?= base_url('Saldo/filter_') ?>',
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    $('#dt-list').find('tbody').html('<tr><td class="text-center" colspan="2"><i class="fa fa-spin fa-spinner"></i> Loading...</td></tr>');
                },
                success: function (result) {
                    $('#dt-list').find('tbody').html(result.tr);
                },
                error: function () {
                    $('#dt-list').find('tbody').html('');
                }
            });
            $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
            $(this).tab('show');
        });
        $('#tahun').datepicker({
            autoclose: true,
            startView: 'years',
            minViewMode: 'years',
            format: 'yyyy'
        }).on('changeDate', function () {
            var tStart = Date.now();
            $.ajax({
                url: '<?= base_url('Saldo/filterform_') ?>/' + $(this).val() + '/' + $('input[name=rekening]:checked').val(),
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    $('#dt-form').find('tbody').html('<tr><td class="text-center" colspan="2"><i class="fa fa-spin fa-spinner"></i> Loading...</td></tr>');
                },
                success: function (result) {
                    $('#dt-form').find('tbody').html(result.tr);
                },
                error: function () {
                    $('#dt-form').find('tbody').html('');
                }
            });
            $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
        });
        $('input[name=rekening]').click(function () {
            var tStart = Date.now();
            $.ajax({
                url: '<?= base_url('Saldo/filterform_') ?>/' + $('#tahun').val() + '/' + $(this).val(),
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    $('#dt-form').find('tbody').html('<tr><td class="text-center" colspan="2"><i class="fa fa-spin fa-spinner"></i> Loading...</td></tr>');
                },
                success: function (result) {
                    $('#dt-form').find('tbody').html(result.tr);
                },
                error: function () {
                    $('#dt-form').find('tbody').html('');
                }
            });
            $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
        });
        $('#tahun').trigger('changeDate');
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input#tahun').datepicker("setDate", '<?= date('Y') ?>');
            $('#form-data').find('input#tahun').trigger('changeDate');
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Saldo/save_') ?>',
                dataType: 'JSON',
                type: 'POST',
                data: dt,
                async: false,
                beforeSend: function () {
                    b.attr('disabled', 'disabled');
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function (result) {
                    if (result.ind == 1) {
                        toastrMsg('success', result.msg);
                    } else {
                        toastrMsg('error', result.msg);
                    }
                    b.removeAttr('disabled');
                    i.removeClass().addClass(cls);
                },
                error: function () {
                    b.removeAttr('disabled');
                    i.removeClass().addClass(cls);
                }
            });
            $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
        });
        $('#btn-cari').click(function () {
            var btn = $(this), i = btn.find('i'), cls = i.attr('class');
            $.ajax({
                url: '<?= base_url('Saldokios/filter_') ?>/' + $('#filterkecamatan').val() + '/' + $('#filtertahun').val(),
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function (result) {
                    $('#dt-list').find('tbody').html(result.tr);
                    i.removeClass().addClass(cls);
                },
                error: function () {
                    i.removeClass().addClass(cls);
                }
            });
        });
    });
</script>