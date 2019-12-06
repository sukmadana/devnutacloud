<html ng-app="mbcloud">
    <head>
        <link rel="stylesheet" href="<?= base_url(); ?>css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= base_url(); ?>css/animate.min.css">
        <link rel="stylesheet" href="<?= base_url(); ?>css/mbcloud.css">
        <script src="<?= base_url(); ?>js/jquery.min.js"></script>
        <script src="<?= base_url(); ?>js/bootstrap.min.js"></script>
        <script src="<?= base_url(); ?>js/angular.min.js"></script>
        <script src="<?= base_url(); ?>js/angular-animate.min.js"></script>

        <style type="text/css">
            body{background:url("<?= base_url(); ?>bg.png") repeat-x center center fixed;
                 -webkit-background-size: contain;
                 -moz-background-size: contain;
                 -o-background-size: contain;
                 background-size: contain;}

            .absolute-center {
                margin-left:auto;
                margin-right:auto;
                width: 100%;
                height: 50%;

            }
            #content-wrapper{width:90%;margin:0 auto;}
            ul{list-style:none}
            li{padding:10px;}

            .sample-show-hide {

            }

            .sample-show-hide.ng-enter {
                animation: fadeInDown 0.5s;
                -webkit-animation:fadeInDown 0.5s;
            }
            .sample-show-hide.ng-move {
                /*      animation: fadeIn 1s;*/
            }
            .sample-show-hide.ng-leave{
                /*animation: fadeOut 0.5s;*/
                opacity:0;
            }
            .vis-hidden{
                /*                visibility: hidden;*/
                opacity:0;  
                transition:opacity 0.1s linear;
                -webkit-transition:opacity 0.1s linear;

            }
        </style>
        
                <script type="text/javascript">
                    var MBCloud = angular.module('mbcloud', ['ngAnimate']);
                    MBCloud.controller('menuCtrl', function ($scope) {
                    var baseurl = '<?= base_url(); ?>';
                            $scope.menu = [
<?php foreach ($laporan as $key => $value) { ?>
                                {title: '<?= $key; ?>'
                                        , submenu: [
    <?php foreach ($laporan[$key] as $t) { ?>
                                            {title: '<?= $t; ?>', link: '#', image: baseurl + '<?=$this->config->item('gambar'.$key);?>'},
    <?php } ?>
                                        ],image: baseurl + '<?=$this->config->item('gambar'.$key);?>'},
<?php } ?>
                            ];
                            $scope.selectedindex = 0;
                            $scope.selectedmenu = $scope.menu[$scope.selectedindex];
                            $scope.nextmenu = function () {
                            if ($scope.selectedindex < $scope.menu.length - 1) {
                            $scope.selectedindex++;
                            }
                            $scope.selectedmenu = $scope.menu[$scope.selectedindex];
                            }
                    $scope.prevmenu = function () {
                    if ($scope.selectedindex > 0) {
                    $scope.selectedindex--;
                    }

                    $scope.selectedmenu = $scope.menu[$scope.selectedindex];
                    }
                    });
        </script>
    </head>
    <body>
        <div id="content-wrapper"  ng-controller="menuCtrl">
            <div class="header-wrapper" >
                <div style="margin: 20px auto; width:294px;">
                    <a href="#"  ng-click="prevmenu()" style="display:block;float:left;margin:25px auto;padding-right:50px;" ng-class="{
                            'vis-hidden'
                            : selectedindex == 0}">
                        <!--<i class="glyphicon glyphicon-menu-left" style="margin-top:-75px;color:#8EA2B6"></i>-->
                        <img src="<?= base_url(); ?>left.png"/>
                    </a>
                    <a href='<?= base_url(); ?>index.php/mokas/reportarusuang' style="display:block;float:left;"><img ng-src="{{selectedmenu.image}}" style="display: block;margin:0 auto"/><span style="color:#fff;display:block;margin:0 auto;text-align: center">{{selectedmenu.title}}</span></a>
                    <a href="#" ng-click="nextmenu()" style="font-size:40px;display:block;float:left;margin:25px auto;padding-left: 50px;" ng-class="{
                            'vis-hidden'
                            : selectedindex == menu.length - 1}">
                        <!--<i class="glyphicon glyphicon-menu-right" style="margin-top:-75px;color:#8EA2B6"></i>-->
                        <img src="<?= base_url(); ?>right.png"/>
                    </a>
                </div>
            </div><br/><br/>
            <hr style="display: block; height: 1px;
                border: 0; border-top: 1px solid #ccc;
                margin-left: 0;margin-right:0; padding: 0;clear:both;margin-top:7em">
            <div  class="absolute-center">
                <div class="row">
                    <div class="col-md-4 sample-show-hide" ng-repeat="x in selectedmenu.submenu"  style="padding:10px 10px;">
                        <a  href='{{x.link}}'><img ng-src="{{selectedmenu.image}}" style="display: inline-block;width:50px;height:50px"/><span style="color:#fff;display:inline-block;margin-left:10px;">{{x.title}}</span></a>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>