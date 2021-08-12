<div class="content-wrapper">
    <section class="content-header">
        <h1>Pembayaran Hutang <small>Menu untuk menginput pembayaran dan melihat daftar pembayaran hutang ke supplier</small></h1>
        <ol class="breadcrumb">
            <li><a href="#">Keuangan</a></li>
            <li class="active">Pembayaran Hutang</li>
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
                                        <label class="col-md-2 col-xs-4">Supplier<span class="pull-right"><i id="loader"></i></span></label>
                                        <div class="col-md-10 col-xs-8">
                                            <select class="form-control" id="supplier" name="supplier">
                                                <?= $optsupplier ?>
                                            </select>                                
                                        </div>
                                    </div>                       
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">Sisa Hutang</label>
                                        <div id="total-hutang" class="col-md-4 col-xs-4">-</div>
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
            $('#form-data').find('input#hutang').val('0');
            $('#form-data').find('#total-hutang').html('-');
            $('#form-data').find('input#tanggal').data("DateTimePicker").date(new Date());
            $('#form-data').find('select#supplier').val('0');
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            if (form.find('#supplier').val() == '0') {
                toastrMsg('error', 'Supplier belum dipilih');
                return true;
            }
            $.ajax({
                url: '<?= base_url('Pembayaran/save_') ?>',
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
                "sAjaxSource": '<?= base_url('Pembayaran/ssplistpembayaran_') ?>/' + $('#tanggalfilter').val(),
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
    }).on('change', '#supplier', function () {
        var i = $('#loader'), cls = i.attr('cls');
        $.ajax({
            url: '<?= base_url('Pembayaran/filtersupplier_') ?>/' + $(this).val(),
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                $('#total-hutang').html(result.ftotal);
                $('#hutang').val(result.total);
                i.removeClass().addClass(cls);
            },
            error: function () {
                i.removeClass().addClass(cls);
            }
        });
    }).on('keyup', '#jumlah', function () {
        var fg = $(this).parents('.input-group:first');
        var nominal = parseFloat($(this).val());
        var batas = parseFloat($('input#hutang').val());
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
    }).on('click', '#btn-edit', function () {
        var tStart = Date.now();
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        $.ajax({
            url: '<?= base_url('Pembayaran/edit_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                b.attr('disabled', 'disabled');
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                var form = $('#form-data');
                $('#tabs a[href="#nota"]').trigger('click');
                form.find('#id').val(result.pmb_id);
                form.find('input#hutang').val(result.hutang);
                form.find('#total-hutang').html(result.fhutang);
                form.find('select#supplier').val(result.pmb_sup_id);
                form.find('input[name=rekening][value=' + result.rekening + ']').prop('checked', true);
                form.find('input#tanggal').data("DateTimePicker").date(result.tanggal);
                form.find('#jumlah').val(result.pmb_nominal);
                form.find('#jumlah').focus();
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