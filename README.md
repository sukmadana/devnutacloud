# README #

## Config file yang perlu disesuaikan

    application/application/config/config.php

 - $config['base_url'] = 'www.nutacloud.com;
 - $config['composer_autoload'] = 'vendor/autoload.php';
 - $config['sess_save_path'] = APPPATH.'nutacloud_sessions';
 - $config['ws_base_url'] = 'http://ws.nutacloud.com/';
 - $config['api_base_url'] = 'http://api.nutacloud.com/';
 - $config['css_versions'] = '1';

## untuk php7

   

 **index.php**

  tambahkan ob_start di awal line setelah php tag(<?php)
	

    <?php
    ob_start();




**system/core/Exceptions.php**
  Ubah line 190  dari

    public function show_exception(Exception $exception)
	
menjadi

    public function show_exception($exception)




untuk login dev.nuta, pake ini:
Perusahaan : Rahmat Mob 2
User: Rahmat
Pass: a

Database yang dipakai di server dev.nutacloud.com
(root:Lentera1nf)