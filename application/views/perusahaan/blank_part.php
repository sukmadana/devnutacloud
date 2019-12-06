<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Nutan Cloud">
    <meta name="author" content="Nuta POS">
    <title>Nuta Cloud Dashboard</title>
    <?php $this->load->view('layouts/css_main'); ?>
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,300,400italic,500,500italic" rel="stylesheet"
          type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet"
          type="text/css">
    <style type="text/css">
        table.table-bordered th:last-child, table.table-bordered td:last-child {
            border-right-width: 1px;
        }
    </style>
    <link rel="stylesheet" href="<?= base_url(); ?>css/jquery.bxslider.css"/>
</head>
<body>
<div class="page-container">
    <div class="page-content">
        <div class="main-container">
            <?php $this->load->view($page_part); ?>
        </div>
    </div>
</div>
<script type="text/javascript">window.base_url = '<?= base_url(); ?>';</script>
<script src="<?= base_url(); ?>js/jquery-1.11.2.min.js"></script>
<script src="<?= base_url(); ?>js/jquery-migrate-1.2.1.min.js"></script>
<script src="<?= base_url(); ?>js/jRespond.min.js"></script>
<script src="<?= base_url(); ?>js/bootstrap.min.js"></script>
<script src="<?= base_url(); ?>js/hoverintent.js"></script>
<script src="<?= base_url(); ?>js/waves.js"></script>

<?php
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
?>

<script src="<?= base_url(); ?>js/smart-resize.js"></script>
<script src="<?= base_url(); ?>js/layout.init.js"></script>
<script src="<?= base_url(); ?>js/matmix.init.js"></script>
<script src="<?= base_url(); ?>js/retina.min.js"></script>
<script src="<?= base_url(); ?>js/jquery.bxslider.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {

        jQuery('#panduanIdPerSlider').bxSlider();

    });
</script>

</body>
</html>