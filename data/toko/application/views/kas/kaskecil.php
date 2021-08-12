77<div class="content-wrapper">
    <section class="content-header">
        <h1>Kas Kecil</h1>
        <ol class="breadcrumb">
            <li><a href="#">Kas</a></li>
            <li class="active">Kas Kecil</li>
        </ol>
    </section>
    <section class="content">
        <div id="tabs" class="row">
            <div class="col-md-6 col-xs-12">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#form" aria-controls="form" role="tab" data-toggle="tab"><i class="fa fa-file-o"></i> Form</a></li>
                    <li role="presentation"><a href="#list" aria-controls="list" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i> Daftar</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="form">
                    <div class="col-md-6 col-xs-12">
                        <form id="form-data">
                            <div class="box">
                                <div class="box-body">
                                    <input type="hidden" class="form-control" id="id" name="id" value="">
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">No. Rekening</label>
                                        <div class="col-md-10 col-xs-9">
                                            <?= $cbrek ?> 
                                        </div>
                                    </div>        
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">Tanggal</label>
                                        <div class="col-md-4 col-xs-4">
                                            <input type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off">                             
                                        </div>
                                    </div>                            
                                    <div class="row form-group">
                                        <label class="col-md-2 col-xs-3">Uraian</label>
                                        <div class="col-md-10 col-xs-9">
                                            <input type="text" class="form-control" id="uraian" name="uraian" autocomplete="off">                          
                                        </div>
                                    </div>                            
                                    <div class="row form-group" style="margin-bottom: 10px">
                                        <label class="col-md-2 col-xs-2">Debet</label>
                                        <div class="col-md-4 col-xs-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">Rp.</div>
                                                <input type="text" class="form-control text-right" id="debet" name="debet" maxlength="20" autocomplete="off">  
                                            </div>
                                        </div>
                                        <label class="col-md-2 col-xs-2">Kredit</label>
                                        <div class="col-md-4 col-xs-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">Rp.</div>
                                                <input type="text" class="form-control text-right" id="kredit" name="kredit" maxlength="20" autocomplete="off">  
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12" style="margin-top: 5px">
                                            <button id="btn-save" type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Simpan</button>                                  
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
                                    <label class="col-md-2 col-xs-4 pull-right text-right">Filter Data</label>
                                </div>
                                <table id="dt-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Uraian</th>
                                            <th class="text-center">Debet</th>
                                            <th class="text-center">Kredit</th>
                                            <th class="text-center"></th>
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
        $('#tabs a[href="#list"]').click(function (e) {
            e.preventDefault();
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Kas/ssplist_') ?>/kaskecil',
                "bLengthChange": true,
                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "aaSorting": [],
                "columns": [{"width": "20%", "sClass": "text-center", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "20%", "sClass": "text-right", "bSortable": false}, {"width": "20%", "sClass": "text-right", "bSortable": false}, {"width": "10%", "sClass": "text-center", "bSortable": false}]
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
            $('#dt-list').dataTable().fnDestroy();
            $('#dt-list').dataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?= base_url('Kas/ssplist_') ?>/kaskecil/' + $(this).val(),
                "bLengthChange": true,
                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                "iDisplayLength": 10,
                "bAutoWidth": false,
                "aaSorting": [],
                "columns": [{"width": "20%", "sClass": "text-center", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "20%", "sClass": "text-right", "bSortable": false}, {"width": "20%", "sClass": "text-right", "bSortable": false}, {"width": "10%", "sClass": "text-center", "bSortable": false}]
            });
        });
        $('#tanggal').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            date: new Date()
        });
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-reset').click(function () {
            $('#form-data').find('input.form-control').val('');
            $('#form-data').find('input#tanggal').data("DateTimePicker").date(new Date());
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Kas/save_') ?>/kaskecil',
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
    }).on('click', '#btn-edit', function () {
        var tStart = Date.now();
        var b = $(this), i = b.find('i'), cls = i.attr('class'), id = b.data('id');
        $.ajax({
            url: '<?= base_url('Kas/edit_') ?>/' + id,
            dataType: 'JSON',
            async: false,
            beforeSend: function () {
                b.attr('disabled', 'disabled');
                i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
            },
            success: function (result) {
                var form = $('#form-data');
                $('#tabs a[href="#form"]').trigger('click');
                form.find('#id').val(result.kas_id);
                form.find('input#tanggal').data("DateTimePicker").date(result.tanggal);
                form.find('input[name=rekening][value=' + result.kas_rekening + ']').prop('checked', true);
                form.find('#uraian').val(result.kas_uraian);
                form.find('#uraian').focus();
                form.find('#debet').val(result.kas_debet);
                form.find('#kredit').val(result.kas_kredit);
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
                    url: '<?= base_url('Kas/delete_') ?>/' + id,
                    dataType: 'JSON',
                    async: false,
                    beforeSend: function () {
                        b.attr('disabled', 'disabled');
                        i.removeClass().addClass('fa fa-spin fa-circle-o-notch');
                    },
                    success: function (result) {
                        if (result.ind == 1) {
                            $('#btn-reset').trigger('click');
                            $('#dt-list').dataTable().fnDestroy();
                            $('#dt-list').dataTable({
                                "bProcessing": true,
                                "bServerSide": true,
                                "sAjaxSource": '<?= base_url('Kas/ssplist_') ?>/kaskecil',
                                "bLengthChange": true,
                                "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
                                "iDisplayLength": 10,
                                "bAutoWidth": false,
                                "aaSorting": [],
                                "columns": [{"width": "20%", "sClass": "text-center", "bSortable": false}, {"width": "30%", "bSortable": false}, {"width": "20%", "sClass": "text-right", "bSortable": false}, {"width": "20%", "sClass": "text-right", "bSortable": false}, {"width": "10%", "sClass": "text-center", "bSortable": false}]
                            });
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