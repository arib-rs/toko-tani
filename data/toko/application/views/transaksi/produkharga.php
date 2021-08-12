<div class="content-wrapper">
    <section class="content-header">
        <h1>Setting Harga <small>Menu untuk melakukan setting harga tiap produk</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Transaksi</a></li>
            <li class="active">Setting harga</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Form Setting Harga</h3>
            </div>
            <div class="box-body">
                <form id="form-data">
                    <input type="hidden" class="form-control" id="id" name="id" value="">
                    <div class="row form-group">
                        <label class="col-md-2 col-xs-4">Nama Produk</label>
                        <div class="col-md-6 col-xs-8">
                            <input type="text" class="form-control" id="nama" placeholder="" autocomplete="off">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-2 col-xs-4">Harga Beli</label>
                        <div class="col-md-2 col-xs-8">
                            <input type="text" class="form-control" name="hb" id="hb" placeholder="" autocomplete="off" maxlength="11">
                        </div>
                        <div class="col-md-2 col-xs-8" style="padding-left: 33px;">
                            <label class="checkbox">
                                <input type="checkbox" name="isppn" id="isppn" value="1">
                                PPN
                            </label>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-2 col-xs-4">Harga Eceran</label>
                        <div class="col-md-2 col-xs-8">
                            <input type="text" class="form-control" name="he" id="he" placeholder="" autocomplete="off" maxlength="11">
                        </div>
                        <label class="col-md-2 col-xs-4">Harga Grosir</label>
                        <div class="col-md-2 col-xs-8">
                            <input type="text" class="form-control" name="hg" id="hg" placeholder="" autocomplete="off" maxlength="11">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-2 col-xs-4">Harga Anggota</label>
                        <div class="col-md-2 col-xs-8">
                            <input type="text" class="form-control" name="hm" id="hm" placeholder="" autocomplete="off" maxlength="11">
                        </div>
                        <label class="col-md-2 col-xs-4">Harga Khusus</label>
                        <div class="col-md-2 col-xs-8">
                            <input type="text" class="form-control" name="hk" id="hk" placeholder="" autocomplete="off" maxlength="11">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-offset-10 col-md-2 col-xs-offset-8 col-xs-4">
                            <button id="btn-save" type="submit" class="btn btn-success pull-right" style="margin-left: 3px" disabled=""><i class="fa fa-pencil"></i> Simpan</button>
                            <a id="btn-reset" class="btn btn-default pull-right">Batal</a>
                        </div>
                    </div>                              
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Produk</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 col-xs-4 pull-right">
                        <select class="form-control" id="ch-kategori" style="margin-bottom: 10px">
                            <?= $opt ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table id="dt-produk" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Produk</th>
                                    <th class="text-center">Harga Beli</th>
                                    <th class="text-center">Harga Eceran</th>
                                    <th class="text-center">Harga Grosir</th>
                                    <th class="text-center">Harga Anggota</th>
                                    <th class="text-center">Harga Khusus</th>
                                    <th class="text-center">*</th>
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
        $('#dt-produk').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?= base_url('Produkharga/ssplist_') ?>',
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "40%"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "7%", "sClass": "text-center"}]
        });
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('#nama').val('');
            $('#form-data').find('#btn-save').prop('disabled', true);
            var cb = $('#form-data').find('#isppn');
            if (cb.is(':checked')) {
                cb.trigger('click');
            }
        });
        $('#nama').typeahead({
            minLength: 3,
            limit: 10,
            displayField: "content",
            source: function (query, process) {
                mp = [];
                $.ajax({
                    url: '<?= base_url('Produkharga/cari_') ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: 'word=' + query,
                    success: function (data) {
                        var dt = data.map(function (prd) {
                            mp[prd.content] = {};
                            mp[prd.content].id = prd.id;
                            mp[prd.content].hb = prd.hb;
                            mp[prd.content].he = prd.he;
                            mp[prd.content].hg = prd.hg;
                            mp[prd.content].hk = prd.hk;
                            mp[prd.content].hm = prd.hm;
                            mp[prd.content].isppn = prd.isppn;
                            mp[prd.content].content = prd.content;
                            return prd.content;
                        });
                        process(dt);
                    }
                });
            },
            updater: function (produk) {
                var form = $('#form-data');
                form.find('#id').val(mp[produk].id);
                form.find('#hb').val(mp[produk].hb);
                form.find('#hb').focus();
                form.find('#he').val(mp[produk].he);
                form.find('#hg').val(mp[produk].hg);
                form.find('#hm').val(mp[produk].hm);
                form.find('#hk').val(mp[produk].hk);
                var isppn = (mp[produk].isppn == 1) ? true : false;
                form.find('#isppn').prop('checked', isppn);
                form.find('#he').trigger('keyup');
                return mp[produk].content;
            }
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Produkharga/save_') ?>',
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
                        $('#ch-kategori').val(result.kategori);
                        $('#ch-kategori').trigger('change');
                        toastrMsg('success', result.msg);
                    } else {
                        toastrMsg('error', result.msg);
                        b.removeAttr('disabled');
                    }
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
            url: '<?= base_url('Produkharga/edit_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                b.attr('disabled', 'disabled');
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                var form = $('#form-data');
                form.find('#id').val(result.prd_id);
                var kode = (result.prd_kode != '') ? '[' + result.prd_kode + '] ' : '';
                var satuan = (result.prd_satuan != '') ? ' /' + result.prd_satuan : '';
                form.find('#nama').val(kode + result.prd_nama + satuan);
                form.find('#hb').val(result.prd_hargabeli);
                form.find('#hb').focus();
                form.find('#he').val(result.prd_hargaecer);
                form.find('#hg').val(result.prd_hargagrosir);
                form.find('#hm').val(result.prd_hargamember);
                form.find('#hk').val(result.prd_hargakhusus);
                var isppn = (result.prd_isppn == 1) ? true : false;
                form.find('#isppn').prop('checked', isppn);
                form.find('#he').trigger('keyup');
                form.find('#btn-save').prop('disabled', false);
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            },
            error: function () {
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            }
        });
        $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
    }).on('change', '#ch-kategori', function () {
        var tStart = Date.now();
        $('#dt-produk').dataTable().fnDestroy();
        $('#dt-produk').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?= base_url('Produkharga/ssplist_') ?>/' + $(this).val(),
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "40%"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-right"}, {"width": "7%", "sClass": "text-center"}]
        });
        $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
    }).on('keyup', '#he', function () {
		return true;
        //var fg = $(this).parent();
        //if ($('input#hb').val() == '' || $('input#he').val() == '') {
        //    return true;
        //}
        //var hb = parseFloat($('input#hb').val());
        //var he = parseFloat($('input#he').val());
        //var markup = (he - hb) / hb * 100;
        //if (markup > 10) {
        //    if (!fg.hasClass('has-error')) {
        //        fg.addClass('has-error');
        //    }
        //} else {
        //    if (fg.hasClass('has-error')) {
        //        fg.removeClass('has-error');
        //    }
        //}
    }).on('keyup', '#hb', function () {
        $('input#he').trigger('keyup');
    });
</script>