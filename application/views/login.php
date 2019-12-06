<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NutaCloud - Lihat laporan bisnis aplikasi Nuta darimana saja dan kapan saja">
    <title>Nuta Cloud - Login</title>
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png');?>" />
    <link rel="stylesheet" href="<?= base_url(); ?>css/font-awesome.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/auth.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>fonts/segoe-ui/style.css" type="text/css">
</head>

<body class="login-page">
    <?php if (ENVIRONMENT === "development") { ?>
    <div class="row">
        <div class="col-md-12" style="background-color: lightyellow;text-align: center">
            Notifikasi ini muncul karena saat ini anda berada di version <b>Development</b> akan otomatis hilang di
            posisi Staging / Live
        </div>
    </div>
    <?php } ?>
    <div class="container is-login">
        <?php
        //Alert konfirmasi dari email berhasil
        if ($this->input->get('k') == 1) {
            ?>
        <div class="well text-center">
            Selamat anda telah mendapatkan akun nutacloud.<br />Sekarang anda bisa login.
        </div>
        <?php } ?>

        <div class="form__container">
            <form action="<?= base_url(); ?>authentication/auth" method="post" class="form">
                <div class="form__group has-icon <?= ($error == 3) ? 'has-error' : ''; ?>">
                    <div class="form__icon">
                        <i class="fa fa-building"></i>
                    </div>
                    <input type="text" class="form__input" placeholder="Nama Perusahaan" name="idperusahaan" value="<?= $idperusahaan; ?>" />
                    <span class="form__error"><?= ($error == 3) ? $msg : ''; ?></span>
                </div>

                <div class="form__group has-icon <?= ($error == 1) ? 'has-error' : ''; ?>">
                    <div class="form__icon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input type="text" placeholder="Username" class="form__input" name="username" value="<?= $username; ?>" />
                    <span class="form__error"><?= ($error == 1) ? $msg : ''; ?></span>
                </div>

                <div class="form__group has-icon <?= ($error == 2) ? 'has-error' : ''; ?>">
                    <div class="form__icon">
                        <i class="fa fa-lock"></i>
                    </div>
                    <input type="password" class="form__input" name="password" placeholder="Password" autocomplete="off" id="input-password" />
                    <button type="button" class="form__addons js-show-icon">
                        <i class="fa fa-eye" id="icon-mata"></i>
                    </button>
                    <span class="form__error"><?= ($error == 2) ? $msg : ''; ?></span>
                </div>

                <button type="submit" class="btn">Log In</button>

                <p class="cl-grey text-center">
                    Lupa akun nutacloud ? <a href="<?= base_url('account/forgotacc'); ?>">Klik Disini</a>
                </p>
                <?php if ($error == 2) : ?>
                <p class="cl-grey text-center">
                    Lupa password ? <a href="<?= $forgotpwurl; ?>">Klik Disini</a>
                </p>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <script src="<?= base_url(); ?>js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {

            var next_state_input = 'text';
            $('.js-show-icon').on('click', function() {
                $('#input-password').prop('type', next_state_input);
                if (next_state_input == 'text') {
                    next_state_input = 'password';
                    $('#icon-mata').prop('class', 'fa fa-eye-slash');
                } else {
                    next_state_input = 'text';
                    $('#icon-mata').prop('class', 'fa fa-eye');
                }
            });
        });
    </script>
</body>

</html>