<div class="content-wrapper">
    <section class="content-header">
        <h1>Pindah Barang <small>Menu untuk melihat daftar dan detail perpindahan barang dari gudang ke toko/kios</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Transaksi</a></li>
            <li class="active">Pindah Barang</li>
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
                "sAjaxSource": '<?= base_url('Pindahbarang/ssplistnota_') ?>',
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
        $('#tabs a[href="#dtl"]').click(function (e) {
            e.preventDefault();
            $('#detail').html('Belum ada nota yang dipilih...');
            $(this).tab('show');
        });
        $('#tanggalfilter').datepicker({
            autoclose: true,
            startView: 'months',
            minViewMode: 'months',
            format: 'mm/yyyy',
            language: 'id'
        }).on('changeDate', function () {
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Pindahbarang/ssplistnota_') ?>/' + $(this).val(),
                "bLengthChange": true,
                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "aaSorting": [],
                "columns": [{"width": "20%", "sClass": "text-center", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "20%", "sClass": "text-center", "bSortable": false}]
            });
        });
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
    }).on('click', '#btn-select', function () {
        var tStart = Date.now();
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        $.ajax({
            url: '<?= base_url('Pindahbarang/getnota_') ?>/' + id,
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
    }).on('click', '#btn-edit', function () {
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        $.ajax({
            url: '<?= base_url('Pindahbarang/modaledit_') ?>/' + id,
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
                mdl.find('#form-data').submit(function (e) {
                    e.preventDefault();
                });
                mdl.find('#btn-update').click(function () {
                    var b = $(this), i = b.find('i'), cls = i.attr('class');
                    var form = mdl.find('#form-data'), dt = form.serializeArray();
                    if (mdl.find('#armada').val() == '' || mdl.find('#armada').val() == undefined) {
                        dt.push({name: 'noarmada', value: '1'});
                    } else {
                        if (mdl.find('#sopir').val() == '') {
                            toastrMsg('error', 'Sopir tidak boleh kosong');
                            return true;
                        }
                        dt.push({name: 'noarmada', value: '0'});
                    }
                    var tStart = Date.now();
                    $.ajax({
                        url: '<?= base_url('Pindahbarang/save_') ?>',
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