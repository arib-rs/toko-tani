<div class="content-wrapper">
    <section class="content-header">
        <h1>Penjualan</h1>
        <ol class="breadcrumb">
            <li><a href="#">Transaksi</a></li>
            <li class="active">Penjualan</li>
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
                                        <label class="col-md-2 col-xs-4">Customer</label>
                                        <div class="col-md-10 col-xs-8">
                                            <select class="form-control" id="customer" name="customer">
                                                <?= $optcustomer ?>
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
                                                <th class="text-center" style="width: 15%">Harga</th>
                                                <th class="text-center" style="width: 20%">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <!-- <div class="row">
                                        <label class="col-md-2 col-xs-3">Operasional</label>
                                    </div>
                                    <hr style="margin-top: 0; margin-bottom: 10px">
                                    <a id="btn-operasional" class="btn btn-xs pull-right"><i class="fa fa-plus"></i> Tambah Operasional</a>
                                    <table id="dt-op" class="table table-striped table-bordered" cellspacing="0" width="100%" style="margin-bottom: 20px">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 66%">Nama Operasional</th>
                                                <th class="text-center" style="width: 34%">Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?= $trop ?>
                                        </tbody>
                                    </table> -->
                                    <input type="hidden" class="form-control" name="idarmada" value="">
                                    <input type="hidden" class="form-control" value="Armada" name="armada[]" readonly="">
                                    <input type="hidden" class="form-control" value="" name="armada[]" id="armada">
                                    <input type="hidden" class="form-control" name="biayaarmada" value="">

                                    <input type="hidden" class="form-control" name="idsopir" value="">
                                    <input type="hidden" class="form-control" value="Sopir" name="sopir[]" readonly="">
                                    <input type="hidden" class="form-control" value="" name="sopir[]" id="sopir">
                                    <input type="hidden" class="form-control" name="biayasopir" value="">
                                </div>
                                <div class="box-footer">
                                    <div class="row form-group">
                                        <label class="col-md-4 col-md-offset-4 col-xs-4 text-right" style="padding-top: 6px;">Diskon</label>
                                        <div class="col-md-4 col-xs-8">
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                <input type="text" class="form-control text-right money" id="diskon" name="diskon" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group" style="font-size:20px">
                                        <label class="col-md-4 col-md-offset-4 col-xs-4 text-right">Total</label>
                                        <div class="col-md-4 col-xs-8 text-right">
                                            <span id="subtotal" style="font-weight: bold">Rp. 0</span>
                                        </div>
                                    </div>
                                    <div class="row form-group hidden">
                                        <div class="col-md-4 col-md-offset-8 col-xs-8 col-xs-offset-4 checkbox text-right">
                                            <label>
                                                <input type="checkbox" id="ppn" name="ppn" value="<?= $ppn ?>"> PPN <?= $ppn ?>%
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
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
                                    <div id="div-dp" class="row form-group">
                                        <label id="dp-label" class="col-md-4 col-md-offset-4 col-xs-4 text-right" style="padding-top: 6px;">Bayar</label>
                                        <div class="col-md-4 col-xs-8">
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                <input type="text" class="form-control text-right money" id="dp" name="dp" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group" style="font-size:20px">
                                        <label id="sisa-label" class="col-md-4 col-md-offset-4 col-xs-4 text-right">Kembalian</label>
                                        <div class="col-md-4 col-xs-8 text-right">
                                            <span id="sisa" style="font-weight: bold">Rp. 0</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12" style="margin-top: 5px">
                                            <button id="btn-save" type="submit" class="btn btn-primary pull-right"><i class="fa fa-print"></i> Cetak</button>
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
                                <h3 class="box-title">Input Barang</h3>
                            </div>
                            <div class="box-body">
                                <form id="form-search">
                                    <div class="row form-group">
                                        <div class="col-md-9 col-xs-8">
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-search"></i></div>
                                                <input type="hidden" id="barang-id" value="">
                                                <input type="hidden" id="barang-satuan" value="">
                                                <input type="hidden" id="barang-kode" value="">
                                                <input type="hidden" id="barang-hb" value="">
                                                <input type="hidden" id="barang-hj" value="">
                                                <input type="hidden" id="barang-kadaluarsa" value="">
                                                <input type="hidden" id="barang-nobatch" value="">
                                                <input type="text" class="form-control" id="barang-search" placeholder="Input Nama Barang..." value="" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-4">
                                            <div class="input-group">
                                                <input type="hidden" id="barang-stok" value="">
                                                <input type="text" class="form-control" id="barang-qty" placeholder="Qty" value="" autocomplete="off">
                                                <div class="input-group-addon" style="padding: 5px;"><button id="barang-add" class="btn btn-xs btn-success" type="submit"><i class="fa fa-plus"></i></button></div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="box collapsed-box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Daftar Barang</h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6 col-xs-12 pull-right">
                                        <select class="form-control" id="ch-kategori" style="margin-bottom: 10px">
                                            <?= $optkategori ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <table id="dt-produk" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Nama Barang</th>
                                                    <th class="text-center">Stok</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th class="text-center">Harga@</th>
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
                                    <label class="col-md-2 col-xs-4 pull-right text-right" style="padding-top: 6px">Filter Data</label>
                                </div>
                                <table id="dt-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="bg-gray-active">
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Customer</th>
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
    var idgudang = '<?= $gudang ?>';
    $(document).ready(function() {
        $('#tabs a[href="#list"]').click(function(e) {
            e.preventDefault();
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Penjualan/ssplistnota_') ?>/' + idgudang + '/' + $('#tanggalfilter').val(),
                "bLengthChange": true,
                "aLengthMenu": [
                    [10, 30, 50, -1],
                    [10, 30, 50, "All"]
                ],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "aaSorting": [],
                "columns": [{
                    "width": "15%",
                    "sClass": "text-center",
                    "bSortable": false
                }, {
                    "width": "30%",
                    "bSortable": false
                }, {
                    "width": "45%",
                    "bSortable": false
                }, {
                    "width": "10%",
                    "sClass": "text-center",
                    "bSortable": false
                }]
            });
            $(this).tab('show');
        });

        $('#barang-search').typeahead({
            minLength: 3,
            limit: 10,
            displayField: "content",
            source: function(query, process) {
                mp = [];
                $.ajax({
                    url: '<?= base_url('Penjualan/cari_') ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: ({
                        word: query,
                        harga: $('#customer option:selected').data('harga')
                    }),
                    success: function(data) {
                        var dt = data.map(function(prd) {
                            mp[prd.content] = {};
                            mp[prd.content].content = prd.content;
                            mp[prd.content].id = prd.id;
                            mp[prd.content].kode = prd.kode;
                            mp[prd.content].nama = prd.nama;
                            mp[prd.content].satuan = prd.satuan;
                            mp[prd.content].hb = prd.hb;
                            mp[prd.content].hj = prd.hj;
                            mp[prd.content].stok = prd.stok;
                            mp[prd.content].kadaluarsa = prd.kadaluarsa;
                            mp[prd.content].nobatch = prd.nobatch;
                            return prd.content;
                        });
                        process(dt);
                    }
                });
            },
            updater: function(produk) {
                $('#barang-id').val(mp[produk].id);
                $('#barang-satuan').val(mp[produk].satuan);
                $('#barang-kode').val(mp[produk].kode);
                $('#barang-hb').val(mp[produk].hb);
                $('#barang-hj').val(mp[produk].hj);
                $('#barang-stok').val(mp[produk].stok);
                $('#barang-kadaluarsa').val(mp[produk].kadaluarsa);
                $('#barang-nobatch').val(mp[produk].nobatch);
                $('#barang-qty').val('');
                return mp[produk].nama;
            }
        });
        $('#form-search').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var id = form.find('#barang-id').val(),
                qty = form.find('#barang-qty').val();
            if (id == '') {
                toastrMsg('warning', 'Barang tidak boleh kosong');
                return true;
            }
            if (qty == '') {
                toastrMsg('warning', 'Qty Barang tidak boleh kosong');
                return true;
            }
            var tbody = $('#dt-nota').find('tbody');
            if (tbody.find('tr#' + id).length > 0) {
                toastrMsg('error', 'Barang telah ditambahkan pada nota');
            } else {
                var nama = form.find('#barang-search').val(),
                    satuan = form.find('#barang-satuan').val(),
                    kode = form.find('#barang-kode').val(),
                    hb = form.find('#barang-hb').val(),
                    hj = form.find('#barang-hj').val(),
                    stok = form.find('#barang-stok').val(),
                    kadaluarsa = form.find('#barang-kadaluarsa').val(),
                    nobatch = form.find('#barang-nobatch').val();;
                tbody.append('<tr id="' + id + '"><td class="text-center">' + kode + '</td><td><input type="hidden" name="id[]" value="' + id + '"><div class="form-group"><input type="text" id="qty" name="qty[]" class="form-control input-sm text-right" autocomplete="off" value="' + qty + '" maxlenght="15" data-stok="' + stok + '"></div></td><td class="text-center">' + satuan + '</td><td>' + nama + '<i id="btn-remove" class="fa fa-trash text-danger pull-right" style="cursor: pointer; line-height: 21px"></i></td><td><input id="hb" class="form-control" type="hidden" name="hb[]" value="' + hb + '"><input id="hj" class="form-control text-right" type="text" name="hj[]" value="' + hj + '"><input type="hidden" name="kadaluarsa[]" value="' + kadaluarsa + '"><input type="hidden" name="nobatch[]" value="' + nobatch + '"></td><td id="total" class="text-right"></td></tr>');
                tbody.find('tr:last').find('#qty').trigger('keyup');
                $('#diskon').val('');
                $('#dp').val('');
                $('#sisa').html('Rp. 0');
            }
            form.find('input').val('');
            form.find('input#barang-search').focus();
        });
        $('.money').maskMoney({
            precision: 0,
            allowZero: true
        });
        $('#tanggalfilter').datepicker({
            autoclose: true,
            startView: 'months',
            minViewMode: 'months',
            format: 'mm/yyyy',
            language: 'id'
        }).on('changeDate', function() {
            $('#tabs a[href="#list"]').trigger('click');
        });
        $('#tanggal').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            date: new Date()
        });
        $('#dt-produk').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?= base_url('Penjualan/ssplist_') ?>',
            "bLengthChange": true,
            "aLengthMenu": [
                [10, 30, 50, 10, 10, -1],
                [10, 30, 50, 10, 10, "All"]
            ],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{
                "width": "3%",
                "sClass": "text-center"
            }, {
                "width": "40%"
            }, {
                "width": "15%",
                "sClass": "text-center"
            }, {
                "width": "17%",
                "sClass": "text-center"
            }, {
                "width": "15%",
                "sClass": "text-center"
            }, {
                "width": "5%",
                "sClass": "text-center"
            }, {
                "width": "5%",
                "sClass": "text-center"
            }]
        });
        $('#form-data').submit(function(e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function() {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('input#tanggal').data("DateTimePicker").date(new Date());
            $('#form-data').find('select.form-control').val('0');
            $('#ch-kategori').trigger('change');
            $('#form-data').find('input[name=kredit][value=0]').prop('checked', true);
            $('#dp-label').html('Bayar');
            var cb = $('#form-data').find('input[type=checkbox]');
            if (cb.is(':checked')) {
                cb.trigger('click');
            }
            $('#form-data').find('#dt-nota').find('tbody').html('');
            $('#form-data').find('#subtotal').html('Rp. 0');
            $('#form-data').find('#dt-op').find('tbody').html('');
            $.ajax({
                url: '<?= base_url('Penjualan/filtertrop_') ?>',
                dataType: 'JSON',
                async: false,
                beforeSend: function() {
                    $('#form-data').find('#dt-op').find('tbody').html('<tr><td class="text-center" colspan="2"><i class="fa fa-spin fa-spinner"></i> Loading...</td></tr>');
                },
                success: function(result) {
                    $('#form-data').find('#dt-op').find('tbody').html(result.tbody);
                },
                error: function() {
                    $('#form-data').find('#dt-op').find('tbody').html('');
                }
            });
        });
        $('#customer').change(function() {
            $('#form-data').find('#dt-nota').find('tbody').html('');
            $('#form-data').find('#subtotal').html('Rp. 0');
            $('#form-data').find('#sisa').html('Rp. 0');
        });
        $('#ch-kategori').change(function() {
            $('#dt-produk').dataTable().fnDestroy();
            $('#dt-produk').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Penjualan/ssplist_') ?>/' + $(this).val(),
                "bLengthChange": true,
                "aLengthMenu": [
                    [10, 30, 50, 10, 10, -1],
                    [10, 30, 50, 10, 10, "All"]
                ],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "columns": [{
                    "width": "3%",
                    "sClass": "text-center"
                }, {
                    "width": "40%"
                }, {
                    "width": "15%",
                    "sClass": "text-center"
                }, {
                    "width": "17%",
                    "sClass": "text-center"
                }, {
                    "width": "15%",
                    "sClass": "text-center"
                }, {
                    "width": "5%",
                    "sClass": "text-center"
                }, {
                    "width": "5%",
                    "sClass": "text-center"
                }]
            });
        });
        $('#btn-save').click(function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            var form = $('#form-data'),
                dt = form.serializeArray();
            if (form.find('#dt-nota').find('tbody').find('tr').length == 0) {
                toastrMsg('error', 'Belum ada barang');
                return true;
            }
            if ($('#armada').val() == '') {
                dt.push({
                    name: 'noarmada',
                    value: '1'
                });
            } else {
                if ($('#sopir').val() == '') {
                    toastrMsg('error', 'Sopir tidak boleh kosong');
                    return true;
                }
                dt.push({
                    name: 'noarmada',
                    value: '0'
                });
            }
            if ($('#customer').val() == '0' && $('input[name="kredit"]:checked').val() == '1') {
                toastrMsg('error', 'Customer Umum tidak boleh melakukan pembayaran secara Kredit');
                return true;
            }
            if (form.find('#pj').val() == '') {
                toastrMsg('error', 'Penanggung jawab tidak boleh kosong');
                return true;
            }
            var tStart = Date.now();
            $.ajax({
                url: '<?= base_url('Penjualan/save_') ?>',
                dataType: 'JSON',
                type: 'POST',
                data: dt,
                async: false,
                beforeSend: function() {
                    b.attr('disabled', 'disabled');
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function(result) {
                    if (result.ind == 1) {
                        $('#btn-reset').trigger('click');
                        toastrMsg('success', result.msg);
                        if (result.thermal == 0) {
                            //setTimeout(function () {
                            //    window.open('<?= base_url('penjualan/cetaksj') ?>/' + result.id, '_blank');
                            //}, 1500);
                            setTimeout(function() {
                                window.open('<?= base_url('penjualan/cetak') ?>/' + result.id, '_blank');
                            }, 1500);
                        }
                    } else {
                        toastrMsg('error', result.msg);
                    }
                    b.removeAttr('disabled');
                    i.removeClass().addClass(cls);
                },
                error: function() {
                    b.removeAttr('disabled');
                    i.removeClass().addClass(cls);
                }
            });
            $('#load-time').html('<i class="fa fa-clock-o"></i> Function load time : ' + (Date.now() - tStart) + ' millisecond');
        });
        $('#btn-operasional').click(function() {
            $('#form-data').find('#dt-op').find('tbody').append('<tr><td><input type="hidden" class="form-control" name="idop[]" value=""><input type="text" class="form-control" value="" name="op[]"></td><td><input type="hidden" class="form-control" name="biaya[]" value=""></td></tr>');
        });
    }).on('change', 'input[name=kredit]', function() {
        if ($(this).val() == '1') {
            $('#dp-label').html('Down Payment');
            $('#sisa-label').html('Sisa Pembayaran');
        } else {
            $('#dp-label').html('Bayar');
            $('#sisa-label').html('Kembalian');
        }
        $('input#dp').trigger('keyup');
    }).on('click', '#btn-add', function() {
        var b = $(this);
        var id = b.data('id');
        var tbody = $('#dt-nota').find('tbody');
        if (tbody.find('tr#' + id).length > 0) {
            toastrMsg('error', 'Barang telah ditambahkan pada nota');
        } else {
            var hargajual = $('#customer option:selected').data('harga');
            var nama = b.data('nama'),
                satuan = b.data('satuan'),
                kode = b.data('kode'),
                hb = b.data('hb'),
                hj = b.data(hargajual),
                stok = b.data('stok'),
                kadaluarsa = b.data('kadaluarsa'),
                nobatch = b.data('nobatch');
            tbody.append('<tr id="' + id + '"><td class="text-center">' + kode + '</td><td><input type="hidden" name="id[]" value="' + id + '"><div class="form-group"><input type="text" id="qty" name="qty[]" class="form-control input-sm text-right" autocomplete="off" value="" maxlenght="15" data-stok="' + stok + '"></div></td><td class="text-center">' + satuan + '</td><td>' + nama + '<i id="btn-remove" class="fa fa-trash text-danger pull-right" style="cursor: pointer; line-height: 21px"></i></td><td><input id="hb" class="form-control" type="hidden" name="hb[]" value="' + hb + '"><input id="hj" class="form-control text-right" type="text" name="hj[]" value="' + hj + '"></td><td id="total" class="text-right"><input type="hidden" name="kadaluarsa[]" value="' + kadaluarsa + '"><input type="hidden" name="nobatch[]" value="' + nobatch + '"></td></tr>');
            $('#diskon').val('');
            $('#dp').val('');
            $('#sisa').html('Rp. 0');
        }
    }).on('click', '#btn-remove', function() {
        $(this).parent().parent().remove();
    }).on('keyup', '#qty', function() {
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
        var hj = parseFloat(tr.find('input#hj').val());
        var total = hj * qty;
        tr.find('#total').html(total.formatMoney(0, '.', ','));
        var tbody = $(this).parents('tbody:first');
        var subtotal = 0;
        tbody.find('tr').each(function() {
            if ($(this).find('input#qty').val() != '' && $(this).find('input#hj').val() != '') {
                var _qty = parseFloat($(this).find('input#qty').val());
                var _hj = parseFloat($(this).find('input#hj').val());
                subtotal += _hj * _qty;
            }
        });
        $('#diskon').val('');
        $('#form-data').find('#subtotal').html('Rp. ' + subtotal.formatMoney(0, '.', ','));
    }).on('keyup', '#hj', function() {
        var tr = $(this).parents('tr:first');
        tr.find('input#qty').trigger('keyup');
    }).on('keyup', '#diskon', function() {
        var tbody = $('#dt-nota > tbody');
        var subtotal = 0;
        tbody.find('tr').each(function() {
            if ($(this).find('input#qty').val() != '' && $(this).find('input#hj').val() != '') {
                var _qty = parseFloat($(this).find('input#qty').val());
                var _hj = parseFloat($(this).find('input#hj').val());
                subtotal += _hj * _qty;
            }
        });
        var disc = $('#diskon').val(),
            diskon = 0;
        if (disc != '') {
            diskon = parseFloat(disc.replace(/,/g, ''));
        }
        var gtotal = subtotal - diskon;
        $('#form-data').find('#subtotal').html('Rp. ' + gtotal.formatMoney(0, '.', ','));
    }).on('keyup', '#dp', function() {
        var tbody = $('#dt-nota > tbody');
        var subtotal = 0;
        tbody.find('tr').each(function() {
            if ($(this).find('input#qty').val() != '' && $(this).find('input#hj').val() != '') {
                var _qty = parseFloat($(this).find('input#qty').val());
                var _hj = parseFloat($(this).find('input#hj').val());
                subtotal += _hj * _qty;
            }
        });
        var disc = $('#diskon').val(),
            diskon = 0;
        if (disc != '') {
            diskon = parseFloat(disc.replace(/,/g, ''));
        }
        var gtotal = subtotal - diskon;
        var dp = $('#dp').val(),
            dp_ = 0;
        if (dp != '') {
            dp_ = parseFloat(dp.replace(/,/g, ''));
        }
        var sisa = 0;
        if ($('input[name=kredit]:checked').val() == 1) {
            sisa = gtotal - dp_;
        } else {
            sisa = dp_ - gtotal;
        }
        $('#form-data').find('#sisa').html('Rp. ' + sisa.formatMoney(0, '.', ','));
    }).on('keyup', '#barang-qty', function() {
        var fg = $(this).parent();
        var qty = parseFloat(fg.find('input#barang-qty').val());
        var stok = parseFloat(fg.find('input#barang-stok').val());
        if (qty > stok) {
            if (!fg.hasClass('has-error')) {
                fg.addClass('has-error');
                $('#barang-add').prop('disabled', true);
            }
        } else {
            if (fg.hasClass('has-error')) {
                fg.removeClass('has-error');
                $('#barang-add').prop('disabled', false);
            }
        }
    });

    Number.prototype.formatMoney = function(c, d, t) {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "," : d,
            t = t == undefined ? "." : t,
            s = n < 0 ? "-" : "",
            i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
</script>