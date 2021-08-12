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
                                <input id="kastanggal" class="form-control text-center" name="tanggal" type="text" value="" readonly="">
                            </div>
                            <div class="col-md-10 col-xs-8" style="padding-top: 6px">
                                Rekening&nbsp;&nbsp;&nbsp;
                                <?= $rekening ?>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-md-12 col-xs-12">
                                <div id="grafik-kas"></div>
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
        $('#kastanggal').datepicker({
            autoclose: true,
            startView: 'months',
            minViewMode: 'months',
            format: 'MM yyyy',
            language: 'id'
        }).datepicker("setDate", '<?= date('m/Y') ?>').on('changeDate', function () {
            $('input[type=radio][name=rekening]').trigger('change');
        });
        $('input[type=radio][name=rekening]').change(function () {
            $.ajax({
                url: '<?= base_url('Dashboard/filterkas_') ?>/' + $('#kastanggal').val() + '/' + $(this).val(),
                dataType: 'JSON',
                async: false,
                beforeSend: function () {
                },
                success: function (ss) {
                    $('#grafik-kas').highcharts({
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: 'Arus Kas'
                        },
                        xAxis: {
                            categories: ss.categories
                        },
                        yAxis: {
                            title: {
                                text: 'Rp (Rupiah)'
                            }
                        },
                        plotOptions: {
                            line: {
                                dataLabels: {
                                    enabled: true
                                },
                                enableMouseTracking: false
                            }
                        },
                        series: ss.series
                    });
                },
                error: function () {
                }
            });
        });
        $('input[type=radio][name=rekening]:checked').trigger('change');
    });
</script>