<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Nutan Cloud">
    <meta name="author" content="Nuta POS">
    <title>Nuta Cloud - Dashboard</title>
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png');?>" />
    <?php $this->load->view('layouts/css_main'); ?>
    <style type="text/css">
        table.table-bordered th:last-child,
        table.table-bordered td:last-child {
            border-right-width: 1px;
        }
        .no-data {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            width: 300px;
            text-align: center;
        }
    </style>
    <?php if (isAccountExpired()) : ?>   
    <style>
        body.modal-open .main-container {
            -webkit-filter: blur(1px);
            -moz-filter: blur(1px);
            -o-filter: blur(1px);
            -ms-filter: blur(1px);
            filter: blur(1px);
        }
    </style>
    <?php endif;?>
    <script src="<?= base_url(); ?>js/loadcss/loadCSS.js"></script>
</head>
<body>
    <?php $this->load->view('layouts/css_main'); ?>
    <?php if (ENVIRONMENT === "development") { ?>
    <div class="nuta-notif">
        Notifikasi ini muncul karena saat ini anda berada di version <b>Development</b> akan otomatis hilang di
        posisi Staging / Live
    </div>    <?php } ?>
    <div class="page-container list-menu-view">
        <!--Leftbar Start Here -->
        <div class="left-aside desktop-view">
            <div class="left-navigation">
                <?php $this->load->view('layouts/main_menu'); ?>
            </div>
        </div>
        <div class="page-content">
            <!--Topbar Start Here -->
            <header class="top-bar">
                <div class="container-fluid top-nav">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="clearfix top-bar-action">
                                <span class="leftbar-action-mobile waves-effect"><i class="fa fa-bars "></i></span>
                                <span class="leftbar-action desktop waves-effect"><i class="fa fa-bars "></i></span>
                            </div>
                        </div>
                        <div class="col-md-4 responsive-fix top-mid">
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="main-container">
                <?php $this->load->view($page_part); ?>
            </div>
            <?php if (isAccountExpired() && !isset($skipCekExpired)) { ?>
            <div id="myModal" class="modal fade" data-backdrop="static" data-keyboard="false">>
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Pemberitahuan</h4>
                        </div>
                        <div class="modal-body">
                            <p>Masa aktif Nuta anda telah habis. Anda tidak bisa melihat laporan.</p>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-primary" href="<?= base_url('activation/index'); ?>">Aktivasi Sekarang</a>
                        </div>

                    </div>

                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="overlay-mobile"></div>
    <script type="text/javascript">
        window.base_url = '<?= base_url(); ?>';
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jRespond/1.0.0/js/jRespond.min.js"></script>
    <script src="<?= base_url(); ?>js/nav-accordion.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.hoverintent/1.8.0/jquery.hoverIntent.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.6.4/waves.min.js"></script>
    <script src="<?= base_url(); ?>js/smart-resize.min.js"></script>
    <script src="<?= base_url(); ?>js/layout.init.js"></script>
    <script src="<?= base_url(); ?>js/matmix.init.min.js"></script>
    <script src="<?= base_url(); ?>js/retina.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/async/2.1.2/async.min.js"></script>

    <?php if (isAccountExpired()) { ?>
    <script type="text/javascript">
        jQuery(window).on('load', function($) {
            jQuery('#myModal').modal('show');
        });
    </script>
    <?php }
    if (count($js_part) > 0) {
        foreach ($js_part as $js) {
            $this->load->view($js);
        }
    }
    if (count($js_chart) > 0) {
        foreach ($js_chart as $js) {
            $this->load->view($js);
        }
    }
    $current_uri = uri_string();
    $uris = explode('/', $current_uri);
    if (strtolower(trim($uris[0])) != 'perusahaan') {
        ?>

    <?php } ?>
    <?php $this->load->view('layouts/css_main'); ?>
    <?php if ($visibilityMenu['Dashboard']) { ?>
    <script>
        jQuery(document).ready(function($) {
            //        async.parallel([
            js_total_penjualan(),
            js_total_transaksi(),
            js_ratarata_transaksi(),
            js_total_laba(),
            js_total_biaya(),
            js_chart_penjualan_bulan_ini_me(),
            js_chart_pengunjung_bulan_ini_me(),
            js_chart_penjualan_terlaris_me(),
            js_chart_rekap_pembayaran_me(),
            js_chart_outlet_terlaris_me()
        });
    </script>
    <?php } ?>
</body>

</html>
