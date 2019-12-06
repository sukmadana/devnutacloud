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
    <div class="nuta-notif">
        Notifikasi ini muncul karena saat ini anda berada di version <b>Development</b> akan otomatis hilang di
        posisi Staging / Live
    </div>
    <?php } ?>

    <div class="container is-medium">
        <?php if ($state === 'firststep') : ?>
        <p class="text-center title">Masukan email yang Anda gunakan saat daftar Nuta</p>
        <?php elseif ($state === 'secondstep') : ?>
        <p class="text-center title">
            Ada beberapa perusahaan yang menggunakan email ini. <br> Silahkan pilih perusahaan yang Anda maksud.
        </p>
        <?php elseif ($state === 'finish') : ?>
        <p class="text-center title">
            Kami telah mengirim <b>Informasi Akun</b> ke email <b><?= $email; ?></b> <br>
            Silahkan cek email tersebut.
        </p>
        <?php endif; ?>

        <div class="form__container">
            <form method="post" action="" id="forgotaccountform" class="form">
                <?php if ($state === 'firststep') : ?>
                <div class="form__group <?= ($error == 1) ? 'has-error' : ''; ?>">
                    <input type="text" placeholder="Email" class="form__input" name="email" value="<?= $email; ?>" required />
                    <span class="form__error"><?= ($error == 1) ? $msg : ''; ?></span>
                </div>
                <input class="btn" type="submit" name="actionbutton" value="Kirim" />
                <p class="cl-grey text-center">
                    <a href="<?= base_url('/'); ?>">Kembali</a>
                </p>
                <?php elseif ($state === 'secondstep') : ?>
                <div class="form__group">
                    <select class="form__select" name="perusahaan">
                        <?php foreach ($options_perusahaan as $p) { ?>
                        <option value="<?= $p; ?>"><?= $p; ?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" value="<?= $email; ?>" name="email" />
                    <br />
                </div>
                <input class="btn" type="submit" name="actionbutton" value="Pilih" />
                <p class="cl-grey text-center">
                    <a href="<?= base_url('/'); ?>">Kembali</a>
                </p>
                <?php elseif ($state === 'finish') : ?>
                <a href="<?= base_url('/'); ?>" class="btn is-block">Log In</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
