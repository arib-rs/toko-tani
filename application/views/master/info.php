<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Info
            <small>Menu informasi sistem</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#">Master</a></li>
            <li class="active">Produsen</li>
        </ol>
    </section>
    <section class="content">
        <form id="form-data">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <?= $content ?>             
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="row form-group">
                        <div class="col-sm-12 col-xs-12">      
                            <button id="btn-save" type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Simpan</button>
                        </div>
                    </div> 
                </div>
            </div>
        </form>  
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        setTable('#dt-supplier');
        $('#form-data').submit(function (e) {
            e.preventDefault();
        });
        $('#btn-save').click(function () {
            var tStart = Date.now();
            var b = $(this), i = b.find('i'), cls = i.attr('class');
            var form = $('#form-data'), dt = form.serializeArray();
            $.ajax({
                url: '<?= base_url('Info/save_') ?>',
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
                        toastrMsg('success', result.msg);
                    } else {
                        toastrMsg('error', result.msg);
                        setTimeout(function (){
                            window.location.reload();
                        }, 2000)
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
    });
</script>