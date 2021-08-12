<div class="content-wrapper">
    <section class="content-header">
        <h1>Barang Retur <small>Menu untuk menginput retur, melihat daftar dan detail retur ke supplier</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Transaksi</a></li>
            <li class="active">Barang Retur</li>
        </ol>
    </section>
    <section class="content">
        <div id="tabs" class="row">
            <div class="col-md-7 col-xs-12">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#nota" aria-controls="nota" role="tab" data-toggle="tab"><i class="fa fa-file-o"></i> Nota</a></li>
                    <li role="presentation"><a href="#list" aria-controls="list" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i> Daftar Nota</a></li>
                    <li role="presentation"><a href="#dtl" aria-controls="dtl" role="tab" data-toggle="tab"><i class="fa fa-file-o"></i> Detail Nota</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="nota">
                    <div class="col-md-7 col-xs-12">
                        <form id="form-data">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">NOTA <?= $info['perusahaan'] ?></h3>
                                </div>
                                <div class="box-body">
                                    <input type="hidden" class="form-control" name="nid" value="">
                                    <div class="row form-group <?= ($level > 3) ? 'hidden' : '' ?>">
                                        <label class="col-md-2 col-xs-4">Lokasi</label>
                                        <div class="col-md-10 col-xs-8">
                                            <select class="form-control filter" id="gudang" name="gudang">
                                                <?= $optgudang ?>
                                            </select>                                
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-4">Supplier</label>
                                        <div class="col-md-10 col-xs-8">
                                            <select class="form-control filter" id="supplier" name="supplier">
                                                <?= $optsupplier ?>
                                            </select>                                
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">No. Rekening</label>
                                        <div class="col-md-10 col-xs-9">
                                            <?= $cbrek ?> 
                                        </div>
                                    </div>
                                    <div class="row form-group" style="margin-bottom: 10px">
                                        <label class="col-md-2 col-xs-3">Tanggal</label>
                                        <div class="col-md-3 col-xs-4">
                                            <input type="text" class="form-control" id="tanggal" name="tanggal">                             
                                        </div>
                                    </div>
                                    <table id="dt-nota" class="table table-striped table-bordered" cellspacing="0" width="100%" style="margin-bottom: 20px">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 10%">Kode</th>
                                                <th class="text-center" style="width: 10%">Qty</th>
                                                <th class="text-center" style="width: 5%">Satuan</th>
                                                <th class="text-center" style="width: 40%">Nama Barang</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>                                    
                                </div>
                                <div class="box-footer">
                                    <div class="row form-group hidden">
                                        <div class="col-md-4 col-md-offset-8 col-xs-8 col-xs-offset-4 checkbox text-right">
                                            <label>
                                                <input type="checkbox" id="ppn" name="ppn" value="<?= $ppn ?>"> PPN <?= $ppn ?>%
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row form-group hidden">
                                        <label class="col-md-4 col-md-offset-4 col-xs-4 text-right" style="padding-top: 11px;">Pembayaran</label>
                                        <div class="col-md-4 col-xs-8 radio text-right">
                                            <label style="margin-right: 10px">
                                                <input type="radio" name="kredit" value="0" checked="">
                                                Lunas
                                            </label>
                                            <label>
                                                <input type="radio" name="kredit" value="1">
                                                Kredit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-4 col-md-offset-4 col-xs-4 text-right">Penanggung Jawab</label>
                                        <div class="col-md-4 col-xs-8">
                                            <input type="text" class="form-control" id="pj" name="pj" autocomplete="off" maxlength="50">                               
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12" style="margin-top: 5px">
                                            <button id="btn-save" type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Simpan</button>                                  
                                            <a id="btn-reset" class="btn btn-default pull-right" style="margin-right: 5px">Batal</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5 col-xs-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Daftar Barang</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <table id="dt-produk" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Nama Barang</th>
                                                    <th class="text-center">Stok</th>
                                                    <th class="text-center">Satuan</th>
													<th class="text-center">Expired</th>
                                                    <th class="text-center">*</th>
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
                <div role="tabpanel" class="tab-pane" id="list">
                    <div class="col-md-12 col-xs-12">
                        <div class="box">
                            <div class="box-body">
                                <div class="row" style="margin-bottom: 10px">
                                    <div class="col-md-2 col-xs-4 pull-right">
                                        <input id="tanggalfilter" class="form-control text-center" name="tanggal" type="text" value="" readonly="">
                                    </div>
                                    <div class="col-md-2 col-xs-4 pull-right <?= ($level > 3) ? 'hidden' : '' ?>">
                                        <select class="form-control" id="gudangfilter">
                                            <?= $gudang ?>
                                        </select>
                                    </div>
                                    <label class="col-md-2 col-xs-4 pull-right text-right" style="padding-top: 6px">Filter Data</label>
                                </div>
                                <table id="dt-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="bg-gray-active">
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Supplier</th>
                                            <th class="text-center">Detail</th>
                                            <th class="text-center">*</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="dtl">
                    <div class="col-md-12 col-xs-12">
                        <div class="box">
                            <div id="detail" class="box-body"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    var level = '<?= $level ?>';
    $(document).ready(function () {
        $('#tabs a[href="#list"]').click(function (e) {
            e.preventDefault();
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Barangretur/ssplistnota_') ?>/' + $('#gudangfilter').val() + '/' + $('#tanggalfilter').val(),
                "bLengthChange": true,
                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "aaSorting": [],
                "columns": [{"width": "15%", "sClass": "text-center", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "45%", "bSortable": false}, {"width": "10%", "sClass": "text-center", "bSortable": false}]
            });
            $(this).tab('show');
        });
        $('#gudangfilter').change(function () {
            $('#tabs a[href="#list"]').trigger('click');
        });
        $('#tanggalfilter').datepicker({
            autoclose: true,
            startView: 'months',
            minViewMode: 'months',
            format: 'mm/yyyy',
            language: 'id'
        }).on('changeDate', function () {
            $('#tabs a[href="#list"]').trigger('click');
        });
        $('#tabs a[href="#dtl"]').click(function (e) {
            e.preventDefault();
            $('#detail').html('Belum ada nota yang dipilih...');
            $(this).tab('show');
        });
        $('#tanggal').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            date: new Date()
        });
        $('#dt-produk').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?= base_url('Barangretur/ssplist_') ?>',
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50,10, -1], [10, 30, 50,10, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "50%"}, {"width": "15%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-center"},{"width": "15%", "sClass": "text-center"}, {"width": "7%", "sClass": "text-center"}]
        });
        $('#pj').typeahead({
            minLength: 3,
            limit: 10,
            displayField: "content",
            source: function (query, process) {
                mp = [];
                $.ajax({
                    url: '<?= base_url('Barangretur/cari_') ?>',
                    type: 'POST',
                    dataType: 'JSON', data: 'word=' + query,
                    success: function (data) {
                        var dt = data.map(function (prd) {
                            mp[prd.content] = {};
                            mp[prd.content].content = prd.content;
                            return prd.content;
                        });
                        process(dt);
                    }
                });
            },
            updater: function (produk) {
                return mp[produk].content;
            }
        });
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('input#tanggal').data("DateTimePicker").date(new Date());
            if (level <= 3) {
                $('#form-data').find('select#gudang').val('0');
            }
            $('#form-data').find('select#supplier').val('0');
            $('#form-data').find('#supplier').trigger('change');
            $('#form-data').find('input[name=kredit][value=0]').prop('checked', true);
            var cb = $('#form-data').find('input[type=checkbox]');
            if (cb.is(':checked')) {
                cb.trigger('click');
            }
            $('#form-data').find('#dt-nota').find('tbody').html('');
        });
        $('#btn-save').click(function () {
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            if (form.find('#dt-nota').find('tbody').find('tr').length == 0) {
                toastrMsg('error', 'Belum ada barang');
                return true;
            }
            if (form.find('#pj').val() == '') {
                toastrMsg('error', 'Penanggung jawab tidak boleh kosong');
                return true;
            }
            var tStart = Date.now();
            $.ajax({
                url: '<?= base_url('Barangretur/save_') ?>',
                dataType: 'JSON',
                type: 'POST',
                data: dt,
                async: false, beforeSend: function () {
                    b.attr('disabled', 'disabled');
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function (result) {
                    if (result.ind == 1) {
                        $('#btn-reset').trigger('click');
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
    }).on('click', '#btn-add', function () {
        var b = $(this);
        var id = b.data('id');
        var tbody = $('#dt-nota').find('tbody');
        if (tbody.find('tr#' + id).length > 0) {
            toastrMsg('error', 'Barang telah ditambahkan pada nota');
        } else {
            var nama = b.data('nama'), satuan = b.data('satuan'), kode = b.data('kode'), hb = b.data('hb'), stok = b.data('stok'),kadaluarsa = b.data('kadaluarsa'),nobatch = b.data('nobatch');
            tbody.append('<tr id="' + id + '"><td class="text-center">' + kode + '</td><td><input type="hidden" name="id[]" value="' + id + '"><input id="hb" class="form-control" type="hidden" name="hb[]" value="' + hb + '"><div class="form-group"><input type="text" id="qty" name="qty[]" class="form-control input-sm text-right" autocomplete="off" value="" maxlenght="15" data-stok="' + stok + '"></div></td><td class="text-center">' + satuan + '</td><td>' + nama + '<i id="btn-remove" class="fa fa-trash text-danger pull-right" style="cursor: pointer; line-height: 21px"></i><input type="hidden" name="kadaluarsa[]" value="' + kadaluarsa + '"><input type="hidden" name="nobatch[]" value="' + nobatch + '"></td></tr>');
        }
    }).on('click', '#btn-remove', function () {
        $(this).parent().parent().remove();
    }).on('keyup', '#qty', function () {
        var tr = $(this).parents('tr:first');
        var fg = $(this).parent();
        var qty = parseFloat(tr.find('input#qty').val());
        var stok = parseFloat(tr.find('input#qty').data('stok'));
        if (qty > stok) {
            if (!fg.hasClass('has-error')) {
                fg.addClass('has-error');
                $('#btn-save').prop('disabled', true);
            }
        } else {
            if (fg.hasClass('has-error')) {
                fg.removeClass('has-error');
                $('#btn-save').prop('disabled', false);
            }
        }
    }).on('change', '.filter', function () {
        var tStart = Date.now();
        $('#form-data').find('#dt-nota').find('tbody').html('');
        $('#form-data').find('#subtotal').html('Rp. 0');
        $('#dt-produk').dataTable().fnDestroy();
        $('#dt-produk').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?= base_url('Barangretur/ssplist_') ?>/' + $('#supplier').val() + '/' + $('#gudang').val(),
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50,10, -1], [10, 30, 50,10, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "50%"}, {"width": "15%", "sClass": "text-right"}, {"width": "10%", "sClass": "text-center"},{"width": "15%", "sClass": "text-center"}, {"width": "7%", "sClass": "text-center"}]
        });
        $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
    }).on('click', '#btn-select', function () {
        var tStart = Date.now();
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        $.ajax({
            url: '<?= base_url('Barangretur/getnota_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                b.attr('disabled', 'disabled');
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (rs) {
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
                $('#tabs a[href="#dtl"]').tab('show');
                $('#detail').html(rs.content);
            },
            error: function () {
                b.removeAttr('disabled');
                i.removeClass().addClass(cls);
            }
        });
        $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
    }).on('click', '#btn-delete', function () {
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        bootbox.confirm("Apakah anda akan menghapus data tersebut?", function (vars) {
            if (vars) {
                var tStart = Date.now();
                $.ajax({
                    url: '<?= base_url('Barangretur/delete_') ?>/' + id,
                    dataType: 'JSON',
                    async: false,
                    beforeSend: function () {
                        i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                    },
                    success: function (result) {
                        if (result.ind == 1) {
                            toastrMsg('success', result.msg);
                            $('#tabs a[href="#list"]').trigger('click');
                        } else {
                            toastrMsg('error', result.msg);
                        }
                        i.removeClass().addClass(cls);
                    },
                    error: function () {
                        i.removeClass().addClass(cls);
                    }
                });
                $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
            }
        });
    }).on('click', '#btn-konfirmasi', function () {
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        $.ajax({
            url: '<?= base_url('Barangretur/modaledit_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                var mdl = $(result.modal);
                mdl.modal({
                    backdrop: 'static',
                    keydrop: false
                }).on('hidden.bs.modal', function () {
                    $(this).remove();
                });
                mdl.find('#form-update').submit(function (e) {
                    e.preventDefault();
                });
                mdl.find('#tanggal').datetimepicker({
                    format: 'DD/MM/YYYY HH:mm',
                    date: new Date()
                });
                mdl.find('#btn-update').click(function () {
                    var b = $(this), i = b.find('i'), cls = i.attr('class');
                    var form = mdl.find('#form-update'), dt = form.serializeArray();
                    var tStart = Date.now();
                    $.ajax({
                        url: '<?= base_url('Barangretur/savekonfirmasi_') ?>',
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
                                $('#form-data').find('#supplier').trigger('change');
                                $('#tabs a[href="#list"]').trigger('click');
                                mdl.modal('hide');
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
                i.removeClass().addClass(cls);
            },
            error: function () {
                i.removeClass().addClass(cls);
            }
        });
    });

    Number.prototype.formatMoney = function (c, d, t) {
        var n = this,
                c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d,
                t = t == undefined ? "." : t,
                s = n < 0 ? "-" : "",
                i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
</script>