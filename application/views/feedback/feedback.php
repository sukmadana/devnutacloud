<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A Components Mix Bootstarp 3 Admin Dashboard Template">
    <meta name="author" content="Westilian">
    <title>Nuta Cloud - Feedback</title>
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
    <link rel="stylesheet" href="<?= base_url(); ?>css/jquery.bxslider.css"/>

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
            text-align: center;
        }

        .main-tab {
            margin-left: 0px;
            margin-right: 0px
        }

        .main-tab-content {
            margin: 0;
        }

        .iconic-input .input-group-addon {
            top: 6px;
        }

        .input-sizing-list li {
            border-bottom: solid #A9B1BA 1px;
            padding: 5px 5px;
        }
    </style>
</head>
<body class="login-page">
<div class="page-container">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 login-container-no-width" style="background-color: #226100">

            <div class="block-content" id="loginperusahaan">
                <div class="aside-tab-content">
                    <img src="<?= $imagelarge; ?>"/>
                    <h4 style="color:#fff"><?= $greeting; ?></h4>
                    <a href="<?= $url; ?>" style="color:#FFC01D">Ganti feedback saya <img
                            src="<?= $imagesmall; ?>" style="width: 20px;height:20px"/></a>
                    <div style="background-color: #A9B1BA;height: 1px;margin-bottom: 25px;margin-top: 10px">&nbsp;</div>
                    <form role="form" method="post" action="<?= base_url('feedback/postfeedback'); ?>">
                        <div class="controls">
                            <ul class="input-sizing-list" style="text-align: left;color:#fff">
                                <li>
                                    <input value="Waktu Menunggu" class="i-min-check" type="checkbox"
                                           id="minimal-checkbox-1" name="subject[]">
                                    <label for="minimal-checkbox-1">Waktu Menunggu</label>
                                </li>
                                <li>
                                    <input value="Kualitas" class="i-min-check" type="checkbox" id="minimal-checkbox-2"
                                           name="subject[]">
                                    <label for="minimal-checkbox-2">Kualitas</label>
                                </li>
                                <li>
                                    <input value="Customer Service" class="i-min-check" type="checkbox"
                                           id="minimal-checkbox-3" name="subject[]">
                                    <label for="minimal-checkbox-3">Customer Service</label>
                                </li>
                                <li>
                                    <input value="Lainnya" class="i-min-check" type="checkbox" id="minimal-checkbox-4"
                                           name="subject[]">
                                    <label for="minimal-checkbox-4">Lainnya</label>
                                </li>
                                <li style="border-bottom: none">
                                    <textarea placeholder="Beritahu kami lebih lanjut.."
                                              style="padding:5px;width:100%;min-height: 150px;border:solid 1px #A9B1BA;background-color: transparent"
                                              name="keterangan"></textarea>
                                </li>
                            </ul>
                            <input type="hidden" name="i" value="<?= $deviceid; ?>"/>
                            <input type="hidden" name="e" value="<?= $email; ?>"/>
                            <input type="hidden" name="r" value="<?= base64_encode($response); ?>"/>
                            <input type="hidden" name="s" value="<?= $saletransactionid ?>"/>
                            <input type="submit" class="btn btn-primary" value="Kirim" style="background-color: #FFC01D;border-color: #FFC01D"/>
                        </div>
                    </form>
                </div>

            </div>

        </div>


        <div class="col-md-4"></div>
    </div>

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
<script src="<?= base_url(); ?>js/jquery.bxslider.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {

        var slider = jQuery('#panduanIndividualSlider').bxSlider();
        var slidera = jQuery('#panduanPerusahaanSlider').bxSlider();
        jQuery('#modalPanduanIndividual').on('shown.bs.modal', function () {
            slider.reloadSlider();
        });
        jQuery('#modalPanduanPerusahaan').on('shown.bs.modal', function () {
            slidera.reloadSlider();
        });

    });
</script>

</html>