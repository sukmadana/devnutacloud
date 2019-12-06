<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Nutan Cloud">
    <meta name="author" content="Nuta POS">
    <title>
        <?php 
            if(isset($title))
            {
                echo $title;
            }
            else{
                echo "Nuta Cloud - Dashboard";
            }
        ?>
    </title>
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png');?>" />
    <link rel="manifest" href="<?= base_url(); ?>js/fbmsg/manifest.json">
    <?php $this->load->view('layouts/css_main'); ?>
    <style type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"></style>
    <style type="text/css">
        table.table-bordered th:last-child, table.table-bordered td:last-child {
            border-right-width: 1px;
        }

        .no-data {
            position: relative;
            bottom: 170px;
            left: 110px;

        }

    </style>
    <?php if (isAccountExpired()) { ?>
    <style type="text/css">
    body.modal-open .main-container {
        -webkit-filter: blur(1px);
        -moz-filter: blur(1px);
        -o-filter: blur(1px);
        -ms-filter: blur(1px);
        filter: blur(1px);
    }
    </style>
    <?php } ?>
</head>
<body>

<?php if (ENVIRONMENT === "development") { ?>
    <div class="nuta-notif">
        Notifikasi ini muncul karena saat ini anda berada di version <b>Development</b> akan otomatis hilang di
        posisi Staging / Live
    </div>
<?php } ?>
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
                    <div class="col-md-6 responsive-fix">
                        <div class="top-aside-right" style="text-align:right;padding-top:15px">
								
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="main-container">
            <?php
            if (isset($tbody)) {
                $this->load->view($page_part, array('tbody' => $tbody));
            } else {
                $this->load->view($page_part);
            } ?>
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
        <?php }  ?>

    </div>
</div>
<script type="text/javascript">window.base_url = '<?= base_url(); ?>';</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jRespond/1.0.0/js/jRespond.min.js"></script>
<script src="<?= base_url(); ?>js/nav-accordion.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.hoverintent/1.8.0/jquery.hoverIntent.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.6.4/waves.min.js"></script>
<script src="<?= base_url(); ?>js/smart-resize.min.js"></script>
<script src="<?= base_url(); ?>js/layout.init.min.js"></script>
<script src="<?= base_url(); ?>js/matmix.init.js"></script>
<script src="<?= base_url(); ?>js/retina.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/async/2.1.2/async.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.3/socket.io.min.js"></script>-->
<!--<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>-->
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js"></script>
<script src="<?=base_url(); ?>js/initfbmsg.js"></script>


<?php if (isAccountExpired()) { ?>
    <script type="text/javascript">
        jQuery(window).on('load', function ($) {
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
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $.fn.dataTable.ext.errMode = 'none';
            $('.dt-table-export').DataTable({
                "iDisplayLength": -1,
                "bInfo": false,
                "bPaginate": false,
                "oLanguage": {
                    "sLengthMenu": ''
                },
                aaSorting: [],
                "dom": '<"row" <"col-md-6"><"col-md-6" <"td-export-toolbar"T>>><"row" <"col-md-6"l><"col-md-6"f>><"row" <"col-md-12"<"td-content"rt>>><"row" <"col-md-6"i><"col-md-6"p>>',
                responsive: true,
                "tableTools": {
                    "sSwfPath": window.base_url + "swf/copy_csv_xls_pdf.swf"
                }
            })
            if ($('#ToolTables_DataTables_Table_0_0').length == 0 && $('#ToolTables_DataTables_Table_0_4').length > 0) {
                var isMobile = false; //initiate as false
                // device detection
                if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
                    isMobile = true;
                }
                if(!isMobile) {
                    //$( ".DTTT_container" ).append( "<p style=\"color:red\">Untuk menampilkan tombol export ke excel :<br>1. Di atas sebelah kirinya alamat nutacloud, tekan tombol kunci secure<br>2. Pilih flash, ubah menjadi always allow on this site<br>3. Tutup Jendela, tekan Reload / Refresh.</p>" );
                    //alert("Untuk menampilkan fungsi export ke excel, di atas sebelah kirinya alamat nutacloud, tekan tombol kunci secure, pilih flash, ubah menjadi allow on this site");
                } else {
                    $( ".DTTT_container" ).append( "<p>Device ini tidak support ekspor ke excel<br>gunakan PC / Laptop untuk ekspor ke excel</p>" );
                }
            }
            if($('#ToolTables_DataTables_Table_0_4').length > 0) {
                $( ".DTTT_container" ).append("<p><a href=\"<?=base_url('cloud/help');?>\" target=\"_BLANK\" class=\"btn btn-default btn-circle\"><i class=\"fa fa-info\" title=\"Tips Export Excel\"></i> Tips Export Excel</a></p>");
                //$( "<div class=\"row\"><div class=\"col-md-10\"></div><div style=\"text-align:right\"class=\"col-md-2\"><a href=\"<?=base_url('cloud/help');?>\" target=\"_BLANK\" class=\"btn btn-default btn-circle\"><i class=\"fa fa-info\" title=\"Tips Export Excel\"></i> Tips Export Excel</a></div><br><br>" ).insertBefore(".table-responsive" );
            }
        });
    </script>
<?php } ?>

<?php
if (isset($js_bootstrap_select)) {
    echo '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>';
}
?>

</body>

</html>
