<div class="content-wrapper">
    <section class="content-header">
        <h1>Pembayaran Piutang <small>Menu untuk menginput pembayaran dan melihat daftar pembayaran piutang dari customer</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Keuangan</a></li>
            <li class="active">Pembayaran Piutang</li>
        </ol>
    </section>
    <section class="content">
        <div id="tabs" class="row">
            <div class="col-md-4 col-xs-12">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#nota" aria-controls="nota" role="tab" data-toggle="tab"><i class="fa fa-file-o"></i> Nota</a></li>
                    <li role="presentation"><a href="#list" aria-controls="list" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i> Daftar Nota</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="nota">
                    <div class="col-md-8 col-xs-12">
                        <form id="form-data">
                            <div class="box">
                                <div class="box-body">
                                    <input type="hidden" class="form-control" id="id" name="id" value="">
                                    <input type="hidden" class="form-control" id="hutang" value="0">
                                    <input type="hidden" name="jenis" value="supplier">
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-4">Customer<span class="pull-right"><i id="loader"></i></span></label>
                                        <div class="col-md-10 col-xs-8">
                                            <select class="form-control" id="customer" name="customer">
                                                <?= $optcustomer ?>
                                            </select>                                
                                        </div>
                                    </div>                       
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">Daftar Nota</label>
                                        <div id="daftar-nota" class="col-md-10 col-xs-9"><div class="radio"><label><input type="radio" name="idnota" value="0" data-sisa="0" checked="">-Pilih Nota-</label></div></div>
                                    </div>   
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">No. Rekening</label>
                                        <div class="col-md-10 col-xs-9">
                                            <?= $cbrek ?> 
                                        </div>
                                    </div>        
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">Tanggal</label>
                                        <div class="col-md-4 col-xs-4">
                                            <input type="text" class="form-control" id="tanggal" name="tanggal">                             
                                        </div>
                                    </div>                              
                                    <div class="row form-group" style="margin-bottom: 10px">
                                        <label class="col-md-2 col-xs-3">Nominal</label>
                                        <div class="col-md-4 col-xs-9">
                                            <div class="input-group">
                                                <div class="input-group-addon">Rp.</div>
                                                <input type="text" class="form-control text-right" id="jumlah" name="jumlah" maxlength="20" autocomplete="off">  
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="box-footer">
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
                                            <th class="text-center">Supplier</th>
                                            <th class="text-center">Nominal</th>
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
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#tanggal').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            date: new Date()
        });
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('#daftar-nota').html('<div class="radio"><label><input type="radio" name="idnota" value="0" data-sisa="0" checked="">-Pilih Nota-</label></div>');
            $('#form-data').find('input#tanggal').data("DateTimePicker").date(new Date());
            $('#form-data').find('select#customer').val('0');
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            if (form.find('#customer').val() == '0') {
                toastrMsg('error', 'Customer belum dipilih');
                return true;
            }
            if (form.find('input[name=idnota]:checked').val() == '0') {
                toastrMsg('error', 'Nota belum dipilih');
                return true;
            }
            if (form.find('#jumlah').val() == '') {
                toastrMsg('error', 'Nominal tidak boleh kosong');
                return true;
            }
            $.ajax({
                url: '<?= base_url('Pembayaran/psave_') ?>',
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
        $('#tabs a[href="#list"]').click(function (e) {
            e.preventDefault();
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Pembayaran/ssplistpiutang_') ?>/' + $('#tanggalfilter').val(),
                "bLengthChange": true,
                "aLengthMenu": [[25, 50, -1], [25, 50, "All"]],
                "iDisplayLength": 25,
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
    }).on('change', '#customer', function () {
        var i = $('#loader'), cls = i.attr('cls');
        $.ajax({
            url: '<?= base_url('Pembayaran/filtercustomer_') ?>/' + $(this).val(),
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                $('#daftar-nota').html(result.div);
                i.removeClass().addClass(cls);
            },
            error: function () {
                i.removeClass().addClass(cls);
            }
        });
    }).on('change', 'input[name=idnota]', function () {
        var fg = $('#jumlah').parents('.input-group:first');
        $('#jumlah').val('');
        if (fg.hasClass('has-error')) {
            fg.removeClass('has-error');
            $('#btn-save').prop('disabled', false);
        }
    }).on('keyup', '#jumlah', function () {
        var fg = $(this).parents('.input-group:first');
        var nominal = parseFloat($(this).val());
        var batas = parseFloat($('input[name=idnota]:checked').data('sisa'));
        if (nominal > batas) {
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
    }).on('click', '#btn-delete', function () {
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        bootbox.confirm("Apakah anda akan menghapus data tersebut?", function (vars) {
            if (vars) {
                var tStart = Date.now();
                $.ajax({
                    url: '<?= base_url('Pembayaran/delete_') ?>/' + id,
                    dataType: 'JSON',
                    async: false,
                    beforeSend: function () {
                        b.attr('disabled', 'disabled');
                        i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                    },
                    success: function (result) {
                        if (result.ind == 1) {
                            $('#btn-reset').trigger('click');
                            $('#tabs a[href="#list"]').trigger('click');
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