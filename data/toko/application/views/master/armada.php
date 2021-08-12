<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Armada
            <small>Menu untuk melakukan tambah, edit dan hapus data armada</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#">Master</a></li>
            <li class="active">Armada</li>
        </ol>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Form Armada</h3>
                <div class="box-tools pull-right">
                    <button id="collapse" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <form id="form-data">
                            <input type="hidden" class="form-control" id="id" name="id" value="">
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Jenis Angkutan</label>
                                <div class="col-sm-4 col-xs-8">
                                    <input autocomplete="off" type="text" class="form-control" name="jenis" id="jenis" value="" maxlength="50">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Nomor Polisi</label>
                                <div class="col-sm-2 col-xs-8">
                                    <input autocomplete="off" type="text" class="form-control" name="nopol" id="nopol" value="" maxlength="15">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Jatuh tempo</label>
                                <div class="col-sm-2 col-xs-2">
                                    <input autocomplete="off" type="text" class="form-control tanggal" name="stnk" id="stnk" value="" readonly="">
                                    <small>*STNK</small>
                                </div>
                                <div class="col-sm-2 col-xs-2">
                                    <input autocomplete="off" type="text" class="form-control tanggal" name="kir" id="kir" value="" readonly="">
                                    <small>*KIR</small>
                                </div>
                                <div class="col-sm-2 col-xs-2">
                                    <input autocomplete="off" type="text" class="form-control tanggal" name="her" id="her" value="" readonly="">
                                    <small>*HER</small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-10 col-xs-12">      
                                    <button id="btn-save" type="submit" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Tambah</button>                                  
                                    <a id="btn-reset" class="btn btn-default pull-right" style="margin-right: 5px">Batal</a>
                                </div>
                            </div>
                        </form>                
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Armada</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table id="dt-armada" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 3%" class="text-center">No</th>
                                    <th style="width: 40%" class="text-center">Jenis Angkutan</th>
                                    <th style="width: 20%" class="text-center">Nomor Polisi</th>
                                    <th style="width: 10%" class="text-center">STNK</th>
                                    <th style="width: 10%" class="text-center">KIR</th>
                                    <th style="width: 10%" class="text-center">HER</th>
                                    <th style="width: 7%" class="text-center">*</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $tbody ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        setTable('#dt-armada');
        $('.tanggal').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            language: 'id'
        });
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('#btn-save').html('<i class="fa fa-plus"></i> Tambah');
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Armada/save_') ?>',
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
                        $('#btn-reset').trigger('click');
                        form.find('#nama').focus();
                        reloadTable('#dt-armada', result.tbody);
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
    }).on('click', '#btn-edit', function () {
        var tStart = Date.now();
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.attr('name');
        $.ajax({
            url: '<?= base_url('Armada/edit_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                b.attr('disabled', 'disabled');
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                var form = $('#form-data');
                form.find('#btn-save').html('<i class="fa fa-pencil"></i> Edit');
                form.find('#id').val(result.arm_id);
                form.find('#jenis').val(result.arm_jenis);
                form.find('#jenis').focus();
                form.find('#nopol').val(result.arm_nopol);
                form.find('#stnk').datepicker("setDate", result.arm_jtstnk);
                form.find('#kir').datepicker("setDate", result.arm_jtkir);
                form.find('#her').datepicker("setDate", result.arm_jther);
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            },
            error: function () {
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            }
        });
        $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
    }).on('click', '#btn-delete', function () {
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.attr('name');
        bootbox.confirm("Apakah anda akan menghapus data tersebut?", function (vars) {
            if (vars) {
                var tStart = Date.now();
                $.ajax({
                    url: '<?= base_url('Armada/delete_') ?>/' + id,
                    dataType: 'JSON',
                    async: false,
                    beforeSend: function () {
                        b.attr('disabled', 'disabled');
                        i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                    },
                    success: function (result) {
                        if (result.ind == 1) {
                            $('#btn-reset').trigger('click');
                            reloadTable('#dt-armada', result.tbody);
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
            }
        });
    });
</script>