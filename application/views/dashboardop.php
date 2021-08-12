<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Dashboard
        </h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashcube"></i> Dashboard</li>
        </ol>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-body" style="min-height: 460px">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <div class="col-md-2 col-xs-4">
                                <input id="tanggal" class="form-control text-center" name="tanggal" type="text" value="" readonly="">
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-md-12 col-xs-12">
                                <table class="table table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30%">Kecamatan</th>
                                            <th class="text-center" style="width: 70%">Pengiriman</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pengiriman">

                                    </tbody>
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
        $('#tanggal').datepicker({
            autoclose: true,
            format: 'dd MM yyyy',
            language: 'id'
        }).datepicker("setDate", '<?= date('d/m/Y') ?>').on('changeDate', function () {
            $.ajax({
                url: '<?= base_url('Dashboard/pengiriman_') ?>/' + $(this).val(),
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                },
                success: function (ss) {
                    $('#pengiriman').html(ss.content);
                },
                error: function () {
                }
            });
        });
        $('#tanggal').trigger('changeDate');
    });
</script>