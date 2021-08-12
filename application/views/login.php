<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $info['perusahaan'] ?> | Login</title>
    <link rel="shortcut icon" href="<?= config_item('asset') . 'is.ico' ?>">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= config_item('css') . 'bootstrap.min.css' ?>">
    <link rel="stylesheet" href="<?= config_item('css') . 'font-awesome.min.css' ?>">
    <link rel="stylesheet" href="<?= config_item('css') . 'AdminLTE.min.css' ?>">
</head>
<style>
    body {
        background-image: url('<?= config_item('asset') . 'bg.jpg' ?>');
        background-size: 100% auto;
    }
</style>

<body class="hold-transition">
    <div class="col-md-12 col-xs-12 text-center bg-success" style="opacity: 0.9">
        <h2 class="text-bold"><?= $info['perusahaan'] ?></h2>
        <h4><?= $info['deskripsi'] ?></h4>
        <h4 class="text-center"><i class="fa fa-map-marker"></i> <?= $info['alamat'] . ', ' . $info['kecamatan'] . ', ' . $info['kabupaten'] ?></h4>
        <h5 class="text-center"><i class="fa fa-phone"></i> <?= $info['telepon'] ?> | <?= $info['hp'] ?></h5>
        <h5 class="text-center"><b>Version <?= $version ?></b></h5>
    </div>
    <div class="login-box">
        <div class="login-box-body" style="background: none">
            <p class="login-box-msg ">&nbsp;</p>
            <form id="form-data">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                        <input id="username" name="username" class="form-control" autocomplete="off" type="text" placeholder="Username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                        <input id="password" name="password" class="form-control" autocomplete="off" type="password" placeholder="Password">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-md-offset-8 col-xs-6 col-xs-offset-6">
                        <button type="submit" class="btn btn-success btn-flat btn-block text-bold">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= config_item('js') . 'jQuery-2.1.4.min.js' ?>"></script>
    <script src="<?= config_item('js') . 'bootstrap.min.js' ?>"></script>
    <script src="<?= config_item('js') . 'jquery.slimscroll.min.js' ?>"></script>
    <script src="<?= config_item('js') . 'fastclick.min.js' ?>"></script>
    <script src="<?= config_item('js') . 'app.min.js' ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#form-data').submit(function(e) {
                e.preventDefault();
                var form = $('#form-data');
                var dt = form.serialize();
                $.ajax({
                    url: '<?= base_url('Login/auth') ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: dt,
                    async: false,
                    beforeSend: function() {
                        form.find('#submit').prop("disabled", true);
                        alert_('<font color="blue"><i class="fa fa-spin fa-spinner"></i> Mohon tunggu beberapa saat...</font>');
                    },
                    success: function(result) {
                        alert_(result.msg);
                        if (result.ind == 1) {
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            form.find('.form-control').val('');
                        }
                    },
                    error: function() {
                        alert_('<font color="darkred"><i class="fa fa-times"></i> Terjadi kesalahan pada koneksi!</font>');
                    }
                }).always(function() {
                    form.find('#submit').prop("disabled", false);
                });
            });
        });

        function alert_(msg) {
            $('.login-box-msg').html($(msg));
            setTimeout(function() {
                $('.login-box-msg').html('&nbsp;');
            }, 2000);
        }
    </script>
</body>

</html>