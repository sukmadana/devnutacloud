<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NutaCloud - Lihat laporan bisnis aplikasi Nuta darimana saja dan kapan saja">
    <meta name="author" content="Westilian">
    <title>Nuta Cloud - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.1/css/font-awesome.min.css"
          type="text/css">
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
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.11/angular.js"></script>


</head>
<body class="login-page" ng-app="myApp" ng-controller="myCtrl">
<div class="page-container">
    <div class="login-branding">
        <a href="index-2.html"><img src="<?= base_url(); ?>images/logo-large.png" alt="logo"
                                    style="width: 100px;height: 71px;"></a>
    </div>
    {{asu}}
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 login-container-no-width">
            <div class="block-content" id="loginperusahaan">
                <div class="aside-tab-content">
                    <h4>Isi sesuai spesifikasi tablet anda.</h4>
                    <form>
                        <div class="form-group">

                            <div class="input-group">
                                <label span class="input-group-addon" style="min-width: 150px">Versi Android</label>
                                <select class="form-control" ng-model="androidVersion"
                                        ng-options="v.api as v.name +' ( '+ v.version +' )' for v in androidVersions">

                                </select>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="input-group">
                                <label span class="input-group-addon" style="min-width: 150px">Ukuran Tablet</label>
                                <input type="number" placeholder="Ukuran Fisik" class="form-control"
                                       ng-model="physicalSize"/>
                                <span class="input-group-addon" style="min-width: 60px">inc</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" style="min-width: 150px">Lebar Layar</span>
                                <input type="number" placeholder="Width Pixel" class="form-control"
                                       ng-model="widthPixel"/>
                                <span class="input-group-addon" style="min-width: 60px">pixel</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" style="min-width: 150px">Tinggi Layar</span>
                                <input type="number" placeholder="Height Pixel" class="form-control"
                                       ng-model="heightPixel"/>
                                <span class="input-group-addon" style="min-width: 60px">pixel</span>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="input-group">
                                <span span class="input-group-addon" style="min-width: 150px">Kerapatan Layar</span>
                                <input type="number" class="form-control"
                                       ng-model="ppi"/>
                                <span class="input-group-addon" style="min-width: 60px">ppi</span>
                            </div>
                        </div>
                        <div
                            class="iconic-w-horizontal {{(isDiagonalSupport && isRatioSupport && isAndroidVersionSupport) ? 'w_bg_green':'w_bg_deep_orange'}}  light-text">
                            <a href="#" class="ico-block">
                                <span><i class="fa {{(isDiagonalSupport && isRatioSupport && isAndroidVersionSupport ) ? 'fa-check':'fa-remove'}} "
                                         font-size="20px"></i></span>
                            </a>
                            <div class="w-meta-info">
                                <span class="w-meta-value">{{(isDiagonalSupport && isRatioSupport && isAndroidVersionSupport) ? 'Support':'Tidak support'}} Nuta</span>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="col-md-4"></div>
    </div>

    <div class="login-footer">
        &copy; <?= date('Y'); ?> NutaCloud

    </div>

</div>
</body>
<script src="<?= base_url(); ?>js/jquery-1.11.2.min.js"></script>
<script src="<?= base_url(); ?>js/jquery-migrate-1.2.1.min.js"></script>
<script src="<?= base_url(); ?>js/jRespond.min.js"></script>
<script src="<?= base_url(); ?>js/bootstrap.min.js"></script>
<script src="<?= base_url(); ?>js/animation.js"></script>
<script src="<?= base_url(); ?>js/smart-resize.js"></script>
<script src="<?= base_url(); ?>js/layout.init.js"></script>
<script src="<?= base_url(); ?>js/matmix.init.js"></script>
<script src="<?= base_url(); ?>js/retina.min.js"></script>
<script>
    var app = angular.module('myApp', []);
    app.controller('myCtrl', function ($scope) {
        $scope.diagonal = 0;
        $scope.widthPixel = 0;
        $scope.heightPixel = 0;
        $scope.ppi = 0;
        $scope.diagonalOS = 0;
        $scope.screenRatio = 0;
        $scope.physicalSize = 0;
        $scope.isDiagonalSupport = false;
        $scope.isRatioSupport = false;
        $scope.androidVersion = 0;
        $scope.androidVersions = [
            {api: 0, version: "-", name: "Silahkan pilih versi android"},
            {api: 1, version: "1.0", name: "Alpha"},
            {api: 2, version: "1.1", name: "Beta"},
            {api: 3, version: "1.5", name: "Cupcake"},
            {api: 4, version: "1.6", name: "Donut"},
            {api: 5, version: "2.0", name: "Eclair"},
            {api: 6, version: "2.0.1", name: "Eclair"},
            {api: 7, version: "2.1", name: "Eclair"},
            {api: 8, version: "2.2", name: "Froyo"},
            {api: 9, version: "2.3", name: "Gingerbread"},
            {api: 10, version: "2.3.3", name: "Gingerbread"},
            {api: 11, version: "3.0", name: "Honeycomb"},
            {api: 12, version: "3.1", name: "Honeycomb"},
            {api: 13, version: "3.2", name: "Honeycomb"},
            {api: 14, version: "4.0", name: "Ice Cream Sandwich"},
            {api: 15, version: "4.0.3", name: "Ice Cream Sandwich"},
            {api: 16, version: "4.1", name: "Jelly Bean"},
            {api: 17, version: "4.2", name: "Jelly Bean"},
            {api: 18, version: "4.3", name: "Jelly Bean"},
            {api: 19, version: "4.4", name: "Kitkat"},
            {api: 20, version: "4.4W", name: "Kitkat"},
            {api: 21, version: "5.0", name: "Lollipop"},
            {api: 22, version: "5.1", name: "Lollipop"},
            {api: 23, version: "6.0", name: "Marshmallow"},
            {api: 24, version: "7.0", name: "Nougat"},
            {api: 25, version: "7.1", name: "Nougat"}
        ];
        $scope.isAndroidVersionSupport = false;
        $scope.$watch('widthPixel', function (newValue, oldValue) {
            var widthKuadrat = $scope.widthPixel * $scope.widthPixel;
            var heightKuadrat = $scope.heightPixel * $scope.heightPixel;
            $scope.diagonal = Math.sqrt(widthKuadrat + heightKuadrat);
            var isWidthBiggerThanHeight = ($scope.widthPixel>$scope.heightPixel);
            if(isWidthBiggerThanHeight){
				$scope.screenRatio = $scope.heightPixel / $scope.widthPixel;
            }else{
            	$scope.screenRatio = $scope.widthPixel / $scope.heightPixel;
        	}
        });
        $scope.$watch('heightPixel', function (newValue, oldValue) {
            var widthKuadrat = $scope.widthPixel * $scope.widthPixel;
            var heightKuadrat = $scope.heightPixel * $scope.heightPixel;
            $scope.diagonal = Math.sqrt(widthKuadrat + heightKuadrat);
            var isWidthBiggerThanHeight = ($scope.widthPixel>$scope.heightPixel);
            if(isWidthBiggerThanHeight){
				$scope.screenRatio = $scope.heightPixel / $scope.widthPixel;
            }else{
            	$scope.screenRatio = $scope.widthPixel / $scope.heightPixel;
        	}
        });
        $scope.$watch('diagonal', function (newValue, oldValue) {
            $scope.ppi = Math.round($scope.diagonal / $scope.physicalSize);
            $scope.diagonalOS = Math.round($scope.diagonal / $scope.ppi);
        });
        $scope.$watch('physicalSize', function (newValue, oldValue) {
            $scope.ppi = Math.round($scope.diagonal / $scope.physicalSize);
        });
        $scope.$watch('ppi', function (newValue, oldValue) {
            $scope.diagonalOS = Math.round($scope.diagonal / $scope.ppi);
        });
        $scope.$watch('diagonalOS', function (newValue, oldValue) {
            $scope.isDiagonalSupport = ($scope.diagonalOS >= 7);
        });
        $scope.$watch('screenRatio', function (newValue, oldValue) {
        	console.log($scope.screenRatio);	
            $scope.isRatioSupport = ($scope.screenRatio <= 0.65);
        });
        $scope.$watch('androidVersion', function (newValue, oldValue) {
            $scope.isAndroidVersionSupport = ($scope.androidVersion >= 15);
        });


    });
</script>
</html>