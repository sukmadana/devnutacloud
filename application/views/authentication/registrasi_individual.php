<?="<h1>".var_dump($error)."</h1>";?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A Components Mix Bootstarp 3 Admin Dashboard Template">
    <meta name="author" content="Westilian">
    <title>Nuta Cloud - Login Trial</title>
    <link rel="stylesheet" href="<?= base_url(); ?>css/font-awesome.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/animate.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/waves.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/layout.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/components.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/plugins.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/common-styles.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/pages.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/responsive.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/matmix-iconfont.css" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,300,400italic,500,500italic" rel="stylesheet"
          type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet"
          type="text/css">
    <style type="text/css">
        .login-container-no-width {
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 2px rgba(0, 0, 0, 0.3);
            margin: auto;
            padding: 20px;
            text-align: right;
            width: 300px;
        }

        .iconic-input .input-group-addon {
            top: 3px;
        }
    </style>
</head>
<body class="login-page">
<div class="page-container">

    <?php if ($mode != "konfirmasi") { ?>
        <div style="margin: auto;padding: 10px 0;text-align: center;">
            <p style="color:white">Mulai saat ini kami mengubah "Device ID" menjadi "username" dan "password" yang lebih
                familiar dan aman.<br/>
                Silahkan daftarkan diri anda untuk mendapatkan akun nutacloud.</p>
        </div>
    <?php } ?>


    <div class="login-branding">
        <a href="index-2.html"><img src="<?= base_url(); ?>images/logo-large.png" alt="logo"></a>
    </div>

    <?php if ($mode == "form") { ?>
        <div class="login-container-no-width">
            <?php if (count($error) > 0) {
                foreach ($error as $e) {
                    ?><p style="color:red;">
                    <?= $e; ?>
                    </p><?php
                }
                ?>

            <?php } ?>
            <form action="<?= base_url(); ?>authentication/register" method="post" id="form-register-individual">
                <div class="form-group">
                    <div class="input-group iconic-input">
                        <span class="input-group-addon">
                            <span class="input-icon">
                                <i class="fa fa-user"></i>
                            </span>
                        </span>
                        <input type="text" placeholder="Username" class="form-control" name="username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group iconic-input">
                        <span class="input-group-addon">
                            <span class="input-icon">
                                <i class="fa fa-lock"></i>
                            </span>
                        </span>
                        <input type="password" placeholder="Password" class="form-control" name="password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group iconic-input">
                        <span class="input-group-addon">
                            <span class="input-icon">
                                <i class="fa fa-lock"></i>
                            </span>
                        </span>
                        <input type="password" placeholder="Ulangi Password" class="form-control"
                               name="confirmPassword">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group iconic-input">
                        <span class="input-group-addon">
                            <span class="input-icon">
                                <i class="fa fa-envelope"></i>
                            </span>
                        </span>
                        <input type="text" placeholder="Email" class="form-control" name="email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group iconic-input">
                        <span class="input-group-addon">
                            <span class="input-icon">
                                <i class="fa fa-mobile"></i>
                            </span>
                        </span>
                        <input type="text" placeholder="No. Handphone" class="form-control" name="nohp">
                    </div>
                </div>
                <input type="hidden" name="id" value="<?= isset($id) ? $id : $this->input->get('i'); ?>"/>

                <div class="form-group">
                    <div class="input-group" style="display: block">
                        <button class="btn btn-primary btn-block" type="submit" name="submittriger" value="true">
                            Daftar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php } else if ($mode == "konfirmasi") { ?>
        <div class="login-container-no-width" style="text-align: center;width:500px;">
            Kami mengirim email ke <i><?= $email; ?></i><br/>
            untuk konfirmasi pendaftaran akun NutaCloud.<br/>
            Silahkan buka email dan lakukan konfirmasi<br/>
            melalui tombol yang tersedia di email tersebut.
        </div>
    <?php } ?>

    <div class="login-footer">
        &copy; 2015 Nuta Cloud
    </div>

</div>

<script src="<?= base_url(); ?>js/jquery-1.11.2.min.js"></script>
<script src="<?= base_url(); ?>js/jquery-migrate-1.2.1.min.js"></script>
<script src="<?= base_url(); ?>js/jRespond.min.js"></script>
<script src="<?= base_url(); ?>js/bootstrap.min.js"></script>
<script src="<?= base_url(); ?>js/nav-accordion.js"></script>
<script src="<?= base_url(); ?>js/hoverintent.js"></script>
<script src="<?= base_url(); ?>js/waves.js"></script>
<script src="<?= base_url(); ?>js/switchery.js"></script>
<script src="<?= base_url(); ?>js/jquery.loadmask.js"></script>
<script src="<?= base_url(); ?>js/icheck.js"></script>
<script src="<?= base_url(); ?>js/bootbox.js"></script>
<script src="<?= base_url(); ?>js/animation.js"></script>
<script src="<?= base_url(); ?>js/colorpicker.js"></script>
<script src="<?= base_url(); ?>js/bootstrap-datepicker.js"></script>
<script src="<?= base_url(); ?>js/floatlabels.js"></script>
<script src="<?= base_url(); ?>js/smart-resize.js"></script>
<script src="<?= base_url(); ?>js/layout.init.js"></script>
<script src="<?= base_url(); ?>js/matmix.init.js"></script>
<script src="<?= base_url(); ?>js/retina.min.js"></script>
<script src="<?= base_url(); ?>js/form/bootstrapValidator.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        if ($.fn.bootstrapValidator) {
            $('#form-register-individual')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    fields: {
                        username: {
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {
                                    message: 'Username tidak boleh kosong.'
                                },
                                stringLength: {
                                    min: 4,
                                    max: 50,
                                    message: 'Username minimal 4 huruf dan maksimal 50 huruf.'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]+$/,
                                    message: 'Username hanya bisa diisi huruf dan angka.'
                                },
                                "remote": {
                                    url: '<?=base_url() . 'ajax/validateusernameindividual';?>',
                                    type: "post",
                                    data: {
                                        username: function () {
                                            return $('#form-register-individual :input[name="username"]').val();
                                        }
                                    },
                                    message: 'Username sudah dipakai.'
                                },
                            }
                        },
                        password: {
                            validators: {
                                notEmpty: {
                                    message: 'Password tidak boleh kosong.'
                                }
                            }
                        },
                        confirmPassword: {
                            validators: {
                                notEmpty: {
                                    message: 'Ketik kembali password.'
                                },
                                identical: {
                                    field: 'password',
                                    message: 'Password tidak sama.'
                                }
                            }
                        },
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'Email tidak boleh kosong.'
                                },
                                emailAddress: {
                                    message: 'Format email tidak valid.'
                                },
                                "remote": {
                                    url: '<?=base_url() . 'ajax/validateemailindividual';?>',
                                    type: "post",
                                    data: {
                                        email: function () {
                                            return $('#form-register-individual :input[name="email"]').val();
                                        }
                                    },
                                    message: 'Email sudah dipakai.'
                                },
                            }
                        },
                        nohp: {
                            validators: {
                                notEmpty: {
                                    message: 'No. Handphone tidak boleh kosong.'
                                },
                                regexp: {
                                    regexp: /^[0-9]+$/,
                                    message: 'Isi dengan angka contoh: 089676008545.'
                                },
                                stringLength: {
                                    min: 8,
                                    max: 12,
                                    message: 'Minimal 8 angka dan maksimal 12 angka.'
                                }
                            }
                        },
                    }
                }).on('success.form.bv', function (e) {
//                    e.preventDefault(); // Prevent the form from submitting
//                    alert('I am going to do other things');
//
//                    // ... Do whatever you want
//
//                    // If you want to submit the form, use the defaultSubmit() method
//                    // http://bootstrapvalidator.com/api/#default-submit
                $('#form-register-individual').bootstrapValidator('defaultSubmit');
            });
        }
    });
</script>

</html>
