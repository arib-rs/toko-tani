<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Gudang
            <small>Menu untuk melakukan tambah, edit dan hapus data gudang/penyimpanan</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#">Master</a></li>
            <li class="active">Gudang</li>
        </ol>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Form Gudang</h3>
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
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Nama</label>
                                <div class="col-sm-5 col-xs-8">
                                    <input autocomplete="off" type="text" class="form-control" name="nama" id="nama" value="" maxlength="50">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Alamat</label>
                                <div class="col-sm-5 col-xs-8">
                                    <textarea autocomplete="off" class="form-control" name="alamat" id="alamat" maxlength="300"></textarea>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Jenis</label>
                                <div class="col-sm-5 col-xs-8 radio">
                                    <label style="margin-right: 10px">
                                        <input type="radio" name="jenis" id="jenis" value="0" checked="">
                                        Gudang
                                    </label>
                                    <label>
                                        <input type="radio" name="jenis" id="jenis" value="1">
                                        Kios/Toko
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-2 col-sm-offset-2 col-xs-4">Kategori Produk</label>
                                <div class="col-sm-5 col-xs-8">
                                    <select class="select2" name="kategori[]" id="kategori" placeholder="Pilih Kategori Produk" multiple="" style="width: 100%">
                                        <?= $optkategori ?>
                                    </select>
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
                <h3 class="box-title">Daftar Gudang</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table id="dt-gudang" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 3%" class="text-center">No</th>
                                    <th style="width: 30%" class="text-center">Nama</th>
                                    <th style="width: 25%" class="text-center">Alamat</th>
                                    <th style="width: 10%" class="text-center">Jenis</th>
                                    <th style="width: 25%" class="text-center">Kategori Produk</th>
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
        $('#kategori').select2();
        setTable('#dt-gudang');
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('input[name=jenis][value=0]').prop('checked', true);
            $('#form-data').find('.select2').select2('val', '');
            $('#form-data').find('textarea#alamat').val('');
            $('#form-data').find('#btn-save').html('<i class="fa fa-plus"></i> Tambah');
        });
        $('#btn-reset').trigger('click');
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Gudang/save_') ?>',
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
                        reloadTable('#dt-gudang', result.tbody);
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
            url: '<?= base_url('Gudang/edit_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                b.attr('disabled', 'disabled');
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                var form = $('#form-data');
                form.find('#btn-save').html('<i class="fa fa-pencil"></i> Edit');
                form.find('#id').val(result.gdg_id);
                form.find('#nama').val(result.gdg_nama);
                form.find('#nama').focus();
                form.find('#alamat').val(result.gdg_alamat);
                form.find('input:radio[name="jenis"]').filter('[value="' + result.gdg_isjual + '"]').prop('checked', true);
                form.find('#kategori').select2('val', result.produk_kategori);
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
                    url: '<?= base_url('Gudang/delete_') ?>/' + id,
                    dataType: 'JSON',
                    async: false,
                    beforeSend: function () {
                        b.attr('disabled', 'disabled');
                        i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                    },
                    success: function (result) {
                        if (result.ind == 1) {
                            $('#btn-reset').trigger('click');
                            reloadTable('#dt-gudang', result.tbody);
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