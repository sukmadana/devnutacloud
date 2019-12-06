<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A Components Mix Bootstarp 3 Admin Dashboard Template">
    <meta name="author" content="Westilian">
    <title>Nuta Cloud - Login Trial Dev</title>
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png');?>" />
    <link rel="stylesheet" href="<?= base_url(); ?>css/font-awesome.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/auth.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>fonts/segoe-ui/style.css" type="text/css">
</head>

<body>
    <?php if (ENVIRONMENT === "development") { ?>
    <div class="row">
        <div class="col-md-12" style="background-color: lightyellow;text-align: center">
            Notifikasi ini muncul karena saat ini anda berada di version <b>Development</b> akan otomatis hilang di
            posisi Staging / Live
        </div>
    </div>
    <?php } ?>

    <div class="container is-medium">

        <p class="text-center title">
            Kami telah mengirim <b>password</b> ke email <b><?= $email; ?></b> <br>
            Silahkan cek email tersebut.
        </p>
        <div class="form__container">
            <a href="<?= base_url('/'); ?>" class="btn is-block">Log In</a>
        </div>
    </div>
</body>
</html>