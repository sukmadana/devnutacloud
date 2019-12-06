# README #


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
Pass: b

Database yang dipakai di server dev.nutacloud.com
(root:Lentera1nf)
