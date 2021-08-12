<div class="content-wrapper">
    <section class="content-header">
        <h1>Request Barang <small>Menu untuk menginput request barang dari gudang ke toko/kios</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Transaksi</a></li>
            <li class="active">Request Barang</li>
        </ol>
    </section>
    <section class="content">
        <div id="tabs" class="row">
            <div class="col-md-7 col-xs-12">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#nota" aria-controls="nota" role="tab" data-toggle="tab"><i class="fa fa-file-o"></i> Nota</a></li>
                    <li role="presentation"><a href="#list" aria-controls="list" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i> Daftar Nota</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="nota">
                    <div class="col-md-7 col-xs-12">
                        <form id="form-data">
                            <div class="box">
                                <div class="box-body">
                                    <input type="hidden" class="form-control" name="nid" value="">
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-4">Gudang</label>
                                        <div class="col-md-10 col-xs-8">
                                            <select class="form-control filter" id="gudang" name="gudang">
                                                <?= $optgudang ?>
                                            </select>                                
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-4">Tujuan</label>
                                        <div class="col-md-10 col-xs-8">
                                            <?= $gudang ?>
                                            <select class="form-control filter hidden" id="tujuan" name="tujuan">
                                                <?= $optgudangtujuan ?>
                                            </select>                                
                                        </div>
                                    </div>
                                    <div class="row form-group hidden">
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
                                                <th class="text-center" style="width: 75%">Nama Barang</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="box-footer">
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
                                    <label class="col-md-2 col-xs-4 pull-right text-right" style="padding-top: 6px">Filter Data</label>
                                </div>
                                <table id="dt-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="bg-gray-active">
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Keterangan</th>
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
    $(document).ready(function () {
        $('#tabs a[href="#list"]').click(function (e) {
            e.preventDefault();
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Requestbarang/ssplistnota_') ?>/' + $('#tanggalfilter').val(),
                "bLengthChange": true,
                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "aaSorting": [],
                "columns": [{"width": "15%", "sClass": "text-center", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "45%", "bSortable": false}, {"width": "10%", "sClass": "text-center", "bSortable": false}]
            });
            $(this).tab('show');
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
            "sAjaxSource": '<?= base_url('Requestbarang/ssplist_') ?>',
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "60%"}, {"width": "20%", "sClass": "text-center"}, {"width": "17%", "sClass": "text-center"}]
        });
        $('#pj').typeahead({
            minLength: 3,
            limit: 10,
            displayField: "content",
            source: function (query, process) {
                mp = [];
                $.ajax({
                    url: '<?= base_url('Requestbarang/cari_') ?>',
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
            $('#form-data').find('select#gudang').val('0');
            $('#form-data').find('#gudang').trigger('change');
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
                url: '<?= base_url('Requestbarang/save_') ?>',
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
            var nama = b.data('nama'), satuan = b.data('satuan'), kode = b.data('kode'), stok = b.data('stok');
            tbody.append('<tr id="' + id + '"><td class="text-center">' + kode + '</td><td><input type="hidden" name="id[]" value="' + id + '"><div class="form-group"><input type="text" id="qty" name="qty[]" class="form-control input-sm text-right" autocomplete="off" value="" maxlenght="15" data-stok="' + stok + '"></div></td><td class="text-center">' + satuan + '</td><td>' + nama + '<i id="btn-remove" class="fa fa-trash text-danger pull-right" style="cursor: pointer; line-height: 21px"></i></td></tr>');
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
        if ($('#tujuan').val() != 0 && ($('#gudang').val() == $('#tujuan').val())) {
            toastrMsg('error', 'Pindah barang tidak boleh dilakukan pada lokasi yang sama');
            $('#tujuan').val('0');
            return true;
        }
        var tStart = Date.now();
        $('#form-data').find('#dt-nota').find('tbody').html('');
        $('#dt-produk').dataTable().fnDestroy();
        $('#dt-produk').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?= base_url('Requestbarang/ssplist_') ?>/' + $('#gudang').val() + '/' + $('#tujuan').val(),
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "60%"}, {"width": "20%", "sClass": "text-center"}, {"width": "17%", "sClass": "text-center"}]
        });
        $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
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