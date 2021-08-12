<div class="content-wrapper">
    <section class="content-header">
        <h1>
            User
            <small>Menu untuk melakukan tambah, edit dan hapus data User</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#">Master</a></li>
            <li class="active">User</li>
        </ol>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Form User</h3>
                <div class="box-tools pull-right">
                    <button id="collapse" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <form id="form-data">
                            <input type="hidden" class="form-control" id="id" name="id" value="">
                            <input class="form-control" value="" name="usernameold" id="usernameold" type="hidden">
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Nama</label>
                                <div class="col-sm-5 col-xs-8">
                                    <input autocomplete="off" type="text" class="form-control" name="nama" id="nama" value="" maxlength="50">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Sebagai</label>
                                <div class="col-sm-3 col-xs-8">
                                    <select class="form-control" name="level" id="level">
                                        <option value="0">-Pilih-</option>
                                        <option value="1">Administrator</option>
                                        <option value="2">Owner</option>
                                        <option value="3">Bagian Keuangan</option>
                                        <option value="4">Petugas Gudang</option>
                                        <option value="5">Kasir</option>
                                    </select>
                                </div>
                            </div>
                            <div id="filtersebagai" class="row form-group hidden">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Lokasi</label>
                                <div class="col-sm-3 col-xs-8">
                                    <select class="form-control" name="gudang" id="gudang">
                                        <?= $opt ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Username</label>
                                <div class="col-sm-5 col-xs-8">
                                    <input autocomplete="off" type="text" class="form-control" name="username" id="username" value="" maxlength="20">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Password</label>
                                <div class="col-sm-4 col-xs-6">
                                    <input autocomplete="off" type="text" class="form-control" name="password" id="password" value="" maxlength="32">
                                </div>
                                <div class="col-sm-1 col-xs-2">
                                    <a id="btn-generate" class="btn btn-default btn-xs pull-right">Generate</a>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-9 col-xs-12">      
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
                <h3 class="box-title">Daftar User</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table id="dt-user" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 3%" class="text-center">No</th>
                                    <th style="width: 30%" class="text-center">Nama</th>
                                    <th style="width: 15%" class="text-center">Username</th>
                                    <th style="width: 15%" class="text-center">Password</th>
                                    <th style="width: 30%" class="text-center">Bagian</th>
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
        setTable('#dt-user');
        $('#level').change(function () {
            if ($(this).val() == '0' || $(this).val() == '1' || $(this).val() == '2' || $(this).val() == '3') {
                $('#filtersebagai').addClass('hidden');
                $('#gudang').val('0');
            } else {
                $('#filtersebagai').removeClass('hidden');
            }
        });
        $('#gudang').change(function () {
            var level = $("#level").val();
            var isjual = $('#gudang option:selected').data('jual');
            if ((level == '4' && isjual == '1')) {
                toastrMsg('warning', 'Petugas gudang tidak boleh memilih lokasi toko');
                $('#gudang').val('0');
                return true;
            }
            if ((level == '5' && isjual == '0')) {
                toastrMsg('warning', 'Kasir tidak boleh memilih lokasi gudang');
                $('#gudang').val('0');
                return true;
            }
        });
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('select.form-control').val('0');
            $('#form-data').find('#level').trigger('change');
            $('#form-data').find('#btn-save').html('<i class="fa fa-plus"></i> Tambah');
        });
        $('#btn-generate').click(function () {
            var b = $(this);
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            for (var i = 0; i < 6; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            b.attr('disabled', 'disabled');
            $('#password').val(text);
            b.removeAttr('disabled');
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('User/save_') ?>',
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
                        reloadTable('#dt-user', result.tbody);
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
            url: '<?= base_url('User/edit_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                b.attr('disabled', 'disabled');
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                var form = $('#form-data');
                form.find('#btn-save').html('<i class="fa fa-pencil"></i> Edit');
                form.find('#id').val(result.id);
                form.find('#nama').val(result.nama);
                form.find('#nama').focus();
                form.find('#level').val(result.level);
                form.find('#level').trigger('change');
                form.find('#gudang').val(result.gudang);
                form.find('#username').val(result.username);
                form.find('#usernameold').val(result.username);
                form.find('#password').val(result.password);
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
                    url: '<?= base_url('User/delete_') ?>/' + id,
                    dataType: 'JSON',
                    async: false,
                    beforeSend: function () {
                        b.attr('disabled', 'disabled');
                        i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                    },
                    success: function (result) {
                        if (result.ind == 1) {
                            $('#btn-reset').trigger('click');
                            reloadTable('#dt-user', result.tbody);
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