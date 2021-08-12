<div class="content-wrapper">
    <section class="content-header">
        <h1>Penjualan <small>Menu untuk melihat daftar dan detail penjualan ke customer</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Transaksi</a></li>
            <li class="active">Penjualan</li>
        </ol>
    </section>
    <section class="content">
        <div id="tabs" class="row">
            <div class="col-md-4 col-xs-12">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#list" aria-controls="list" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i> Daftar Nota</a></li>
                    <li role="presentation"><a href="#dtl" aria-controls="dtl" role="tab" data-toggle="tab"><i class="fa fa-file-o"></i> Detail Nota</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="list">
                    <div class="col-md-12 col-xs-12">
                        <div class="box">
                            <div class="box-body">
                                <div class="row" style="margin-bottom: 10px">
                                    <div class="col-md-2 col-xs-4 pull-right">
                                        <input id="tanggalfilter" class="form-control text-center" name="tanggal" type="text" value="" readonly="">
                                    </div>
                                    <div class="col-md-2 col-xs-4 pull-right">
                                        <select class="form-control" id="gudang" name="gudang">
                                            <?= $gudang ?>
                                        </select>
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
    $(document).ready(function () {
        $('#tabs a[href="#list"]').click(function (e) {
            e.preventDefault();
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Penjualan/ssplistnota_') ?>/' + $('#gudang').val() + '/' + $('#tanggalfilter').val(),
                "bLengthChange": true,
                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "aaSorting": [],
                "columns": [{"width": "15%", "sClass": "text-center", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "45%", "bSortable": false}, {"width": "10%", "sClass": "text-center", "bSortable": false}]
            });
            $(this).tab('show');
        });
        $('#tabs a[href="#list"]').trigger('click');
        $('#tanggalfilter').datepicker({
            autoclose: true,
            startView: 'months',
            minViewMode: 'months',
            format: 'mm/yyyy',
            language: 'id'
        }).on('changeDate', function () {
            $('#tabs a[href="#list"]').trigger('click');
        });
        $('#gudang').change(function () {
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
            "sAjaxSource": '<?= base_url('Penjualan/ssplist_') ?>',
            "bLengthChange": true,
            "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
            "iDisplayLength": 10,
            "bAutoWidth": false,
            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "70%"}, {"width": "20%", "sClass": "text-right"}, {"width": "7%", "sClass": "text-center"}]
        });
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('input#tanggal').data("DateTimePicker").date(new Date());
            $('#form-data').find('select.form-control').val('0');
            $('#form-data').find('#supplier').trigger('change');
            $('#form-data').find('input[name=kredit][value=0]').prop('checked', true);
            var cb = $('#form-data').find('input[type=checkbox]');
            if (cb.is(':checked')) {
                cb.trigger('click');
            }
            $('#form-data').find('#dt-nota').find('tbody').html('');
            $('#form-data').find('#dt-op').find('tbody').html('');
            $.ajax({
                url: '<?= base_url('Penjualan/filtertrop_') ?>',
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                    $('#form-data').find('#dt-op').find('tbody').html('<tr><td class="text-center" colspan="2"><i class="fa fa-spin fa-spinner"></i> Loading...</td></tr>');
                },
                success: function (result) {
                    $('#form-data').find('#dt-op').find('tbody').html(result.tbody);
                },
                error: function () {
                    $('#form-data').find('#dt-op').find('tbody').html('');
                }
            });
        });
        $('#customer').change(function () {
            $('#form-data').find('#dt-nota').find('tbody').html('');
        });
        $('#btn-save').click(function () {
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            if (form.find('#dt-nota').find('tbody').find('tr').length == 0) {
                toastrMsg('error', 'Belum ada barang');
                return true;
            }
            if ($('#armada').val() == '') {
                dt.push({name: 'noarmada', value: '1'});
            } else {
                if ($('#sopir').val() == '') {
                    toastrMsg('error', 'Sopir tidak boleh kosong');
                    return true;
                }
                dt.push({name: 'noarmada', value: '0'});
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
                async: false, beforeSend: function () {
                    b.attr('disabled', 'disabled');
                    i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                },
                success: function (result) {
                    if (result.ind == 1) {
                        $('#btn-reset').trigger('click');
                        toastrMsg('success', result.msg);
                        $('#dt-produk').dataTable().fnDestroy();
                        $('#dt-produk').dataTable({
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": '<?= base_url('Penjualan/ssplist_') ?>',
                            "bLengthChange": true,
                            "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                            "iDisplayLength": 10,
                            "bAutoWidth": false,
                            "columns": [{"width": "3%", "sClass": "text-center"}, {"width": "70%"}, {"width": "20%", "sClass": "text-right"}, {"width": "7%", "sClass": "text-center"}]
                        });
                        setTimeout(function () {
                            window.open('<?= base_url('penjualan/cetak') ?>/' + result.id, '_blank');
                        }, 1500);
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
        $('#btn-operasional').click(function () {
            $('#form-data').find('#dt-op').find('tbody').append('<tr><td><input type="hidden" class="form-control" name="idop[]" value=""><input type="text" class="form-control" value="" name="op[]"></td><td><input type="hidden" class="form-control" name="biaya[]" value=""></td></tr>');
        });
    }).on('click', '#btn-add', function () {
        var b = $(this);
        var id = b.data('id');
        var tbody = $('#dt-nota').find('tbody');
        if (tbody.find('tr#' + id).length > 0) {
            toastrMsg('error', 'Barang telah ditambahkan pada nota');
        } else {
            var hargajual = $('#customer option:selected').data('harga');
            var nama = b.data('nama'), satuan = b.data('satuan'), kode = b.data('kode'), hb = b.data('hb'), hj = b.data(hargajual), stok = b.data('stok');
            tbody.append('<tr id="' + id + '"><td class="text-center">' + kode + '</td><td><input type="hidden" name="id[]" value="' + id + '"><div class="form-group"><input type="text" id="qty" name="qty[]" class="form-control input-sm text-right" autocomplete="off" value="" maxlenght="15" data-stok="' + stok + '"></div></td><td class="text-center">' + satuan + '</td><td>' + nama + '<i id="btn-remove" class="fa fa-trash text-danger pull-right" style="cursor: pointer; line-height: 21px"></i></td><td><input id="hb" class="form-control" type="hidden" name="hb[]" value="' + hb + '"><input id="hj" class="form-control text-right" type="text" name="hj[]" value="' + hj + '"></td><td id="total" class="text-right"></td></tr>');
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
        var hj = parseFloat(tr.find('input#hj').val());
        var total = hj * qty;
        tr.find('#total').html(total.formatMoney(2, '.', ','));
    }).on('keyup', '#hj', function () {
        var tr = $(this).parents('tr:first');
        tr.find('input#qty').trigger('keyup');
    }).on('click', '#btn-select', function () {
        var tStart = Date.now();
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        $.ajax({
            url: '<?= base_url('Penjualan/getnota_') ?>/' + id,
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
                    url: '<?= base_url('Penjualan/delete_') ?>/' + id,
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